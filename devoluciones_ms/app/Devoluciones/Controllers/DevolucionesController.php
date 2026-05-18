<?php
  namespace App\Devoluciones\Controllers;

  use App\Devoluciones\Models\DevolucionesModel;
  use App\Devoluciones\Views\DevolucionesView;

  class DevolucionesController {
    private DevolucionesModel $model;

    public function __construct() {
      $this->model = new DevolucionesModel();
    }

    public function handle(string $method, ?int $id): void {
      match($method) {
        'GET'  => $this->get($id),
        'POST' => $this->create(),
        default => DevolucionesView::error('Metodo no permitido', 405),
      };
    }

    private function get(?int $id): void {
      if (isset($_GET['reserva_id'])) {
        DevolucionesView::success($this->model->getByReserva((int)$_GET['reserva_id']));
        return;
      }
      DevolucionesView::success($this->model->getAll());
    }

    private function create(): void {
      $data     = json_decode(file_get_contents('php://input'), true);
      $required = ['reserva_id', 'fecha_devolucion'];
      foreach ($required as $field) {
        if (empty($data[$field])) { DevolucionesView::error("Campo requerido: $field", 422); return; }
      }

      // Verificar que la reserva exista y este activa (llamada HTTP a reservas_ms)
      $reservaUrl  = "http://localhost/reservas_ms/reservas?id={$data['reserva_id']}";
      $reservaResp = json_decode(file_get_contents($reservaUrl), true);
      if (empty($reservaResp['data'])) {
        DevolucionesView::error('Reserva no encontrada', 404); return;
      }
      $reserva = $reservaResp['data'];
      if ($reserva['estado'] !== 'activa') {
        DevolucionesView::error('La reserva no esta activa', 400); return;
      }

      // Registrar devolucion
      $id = $this->model->create($data);

      // Actualizar estado reserva a finalizada
      $ctxPut = stream_context_create([
        'http' => [
          'method'  => 'PUT',
          'header'  => 'Content-Type: application/json',
          'content' => json_encode(['estado' => 'finalizada']),
        ],
      ]);
      file_get_contents("http://localhost/reservas_ms/reservas?id={$data['reserva_id']}", false, $ctxPut);

      // Actualizar estado vehiculo a disponible
      file_get_contents(
        "http://localhost/vehiculos_ms/vehiculos?id={$reserva['vehiculo_id']}",
        false,
        stream_context_create([
          'http' => [
            'method'  => 'PUT',
            'header'  => 'Content-Type: application/json',
            'content' => json_encode(['estado' => 'disponible']),
          ],
        ])
      );

      DevolucionesView::success(['id' => $id, 'message' => 'Devolucion registrada'], 201);
    }
  }