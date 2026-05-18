<?php
  namespace App\Reservas\Controllers;

  use App\Reservas\Models\ReservasModel;
use App\Reservas\View\ReservasView as ViewReservasView;
use App\Reservas\Views\ReservasView;

  class ReservasController {
    private ReservasModel $model;

    public function __construct() {
      $this->model = new ReservasModel();
    }

    public function handle(string $method, ?int $id): void {
      match($method) {
        'GET'    => $this->get($id),
        'POST'   => $this->create(),
        'PUT'    => $this->update($id),
        default  => ReservasView:: error('Metodo no permitido', 405),
      };
    }

    private function get(?int $id): void {
      if ($id) {
        $reserva = $this->model->getById($id);
        if (!$reserva) { ReservasView::error('Reserva no encontrada', 404); return; }
        ReservasView::success($reserva);
        return;
      }
      if (isset($_GET['vehiculo_id'])) {
        ReservasView::success($this->model->getByVehiculo((int)$_GET['vehiculo_id']));
        return;
      }
      if (isset($_GET['cliente_id'])) {
        ReservasView::success($this->model->getByCliente((int)$_GET['cliente_id']));
        return;
      }
      if (isset($_GET['estado']) && $_GET['estado'] === 'activa') {
        ReservasView::success($this->model->getActivas());
        return;
      }
      ReservasView::success($this->model->getAll());
    }

    private function create(): void {
      $data     = json_decode(file_get_contents('php://input'), true);
      $required = ['vehiculo_id', 'cliente_id', 'fecha_inicio', 'fecha_fin'];
      foreach ($required as $field) {
        if (empty($data[$field])) { ReservasView::error("Campo requerido: $field", 422); return; }
      }

      // Validar que fecha_fin > fecha_inicio
      if (strtotime($data['fecha_fin']) <= strtotime($data['fecha_inicio'])) {
        ReservasView::error('fecha_fin debe ser mayor que fecha_inicio', 400);
        return;
      }

      // Verificar vehiculo existe y esta disponible (llamada HTTP a vehiculos_ms)
      $vehiculoUrl  = "http://localhost/vehiculos_ms/vehiculos?id={$data['vehiculo_id']}";
      $vehiculoResp = json_decode(file_get_contents($vehiculoUrl), true);
      if (empty($vehiculoResp['data'])) {
        ReservasView::error('Vehiculo no encontrado', 404); return;
      }
      $vehiculo = $vehiculoResp['data'];
      if ($vehiculo['estado'] !== 'disponible') {
        ReservasView::error('El vehiculo no esta disponible', 409); return;
      }

      // Verificar cliente existe (llamada HTTP a clientes_ms)
      $clienteUrl  = "http://localhost/clientes_ms/clientes?id={$data['cliente_id']}";
      $clienteResp = json_decode(file_get_contents($clienteUrl), true);
      if (empty($clienteResp['data'])) {
        ReservasView::error('Cliente no encontrado', 404); return;
      }

      // Verificar solapamiento de fechas
      if ($this->model->checkSolapamiento($data['vehiculo_id'], $data['fecha_inicio'], $data['fecha_fin'])) {
        ReservasView::error('El vehiculo ya tiene una reserva activa en ese rango de fechas', 409);
        return;
      }

      // Calcular total
      $dias          = (strtotime($data['fecha_fin']) - strtotime($data['fecha_inicio'])) / 86400;
      $data['total'] = $dias * $vehiculo['precio_dia'];

      // Crear reserva
      $id = $this->model->create($data);

      // Actualizar estado vehiculo a alquilado (llamada HTTP PUT a vehiculos_ms)
      $ctx = stream_context_create([
        'http' => [
          'method'  => 'PUT',
          'header'  => 'Content-Type: application/json',
          'content' => json_encode(['estado' => 'alquilado']),
        ],
      ]);
      file_get_contents("http://localhost/vehiculos_ms/vehiculos?id={$data['vehiculo_id']}", false, $ctx);

      ReservasView::success(['id' => $id, 'total' => $data['total'], 'message' => 'Reserva creada'], 201);
    }

    private function update(?int $id): void {
      if (!$id) { ReservasView::error('ID requerido', 400); return; }
      $data = json_decode(file_get_contents('php://input'), true);
      if (empty($data['estado'])) { ReservasView::error('Campo requerido: estado', 422); return; }
      $ok = $this->model->updateEstado($id, $data['estado']);
      $ok ? ReservasView::success(['message' => 'Estado de reserva actualizado'])
          : ReservasView::error('No se pudo actualizar', 500);
    }
  }