<?php
  namespace App\Vehiculos\Controllers;

  use App\Vehiculos\Models\VehiculosModel;
  use App\Vehiculos\View\VehiculosView;

  class VehiculosController {
    private VehiculosModel $model;

    public function __construct() {
      $this->model = new VehiculosModel();
    }

    public function handle(string $method, ?int $id): void {
      match($method) {
        'GET'    => $this->get($id),
        'POST'   => $this->create(),
        'PUT'    => $this->update($id),
        'DELETE' => $this->delete($id),
        default  => VehiculosView::error('Metodo no permitido', 405),
      };
    }

    private function get(?int $id): void {
      if ($id) {
        $vehiculo = $this->model->getById($id);
        if (!$vehiculo) { VehiculosView::error('Vehiculo no encontrado', 404); return; }
        VehiculosView::success($vehiculo);
        return;
      }
      if (isset($_GET['disponibles']) && $_GET['disponibles'] === '1') {
        VehiculosView::success($this->model->getDisponibles());
        return;
      }
      VehiculosView::success($this->model->getAll());
    }

    private function create(): void {
      $data     = json_decode(file_get_contents('php://input'), true);
      $required = ['marca', 'modelo', 'anio', 'categoria', 'placa', 'precio_dia'];
      foreach ($required as $field) {
        if (empty($data[$field])) { VehiculosView::error("Campo requerido: $field", 422); return; }
      }
      try {
        $id = $this->model->create($data);
        VehiculosView::success(['id' => $id, 'message' => 'Vehiculo creado'], 201);
      } catch (\PDOException $e) {
        $code = $e->getCode() === '23000' ? 409 : 500;
        VehiculosView::error('Error: ' . $e->getMessage(), $code);
      }
    }

    private function update(?int $id): void {
      if (!$id) { VehiculosView::error('ID requerido', 400); return; }
      $data = json_decode(file_get_contents('php://input'), true);
      if (empty($data)) { VehiculosView::error('Sin datos para actualizar', 400); return; }
      $ok = $this->model->update($id, $data);
      $ok ? VehiculosView::success(['message' => 'Vehiculo actualizado'])
          : VehiculosView::error('No se pudo actualizar', 500);
    }

    private function delete(?int $id): void {
      if (!$id) { VehiculosView::error('ID requerido', 400); return; }
      if (!$this->model->getById($id)) { VehiculosView::error('Vehiculo no encontrado', 404); return; }
      $ok = $this->model->delete($id);
      $ok ? VehiculosView::success(['message' => 'Vehiculo eliminado'])
          : VehiculosView::error('No se pudo eliminar', 500);
    }
  }