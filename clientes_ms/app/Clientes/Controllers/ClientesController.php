<?php
  namespace App\Clientes\Controllers;

  use App\Clientes\Models\ClientesModel;
  use App\Clientes\Views\ClientesView;

  class ClientesController {
    private ClientesModel $model;

    public function __construct() {
      $this->model = new ClientesModel();
    }

    public function handle(string $method, ?int $id): void {
      match($method) {
        'GET'    => $this->get($id),
        'POST'   => $this->create(),
        'PUT'    => $this->update($id),
        'DELETE' => $this->delete($id),
        default  => ClientesView::error('Metodo no permitido', 405),
      };
      
    }

    private function get(?int $id): void {
      if ($id) {
        $cliente = $this->model->getById($id);
        if (!$cliente) { ClientesView::error('Cliente no encontrado', 404); return; }
        ClientesView::success($cliente);
        return;
      }
      ClientesView::success($this->model->getAll());
    }

    private function create(): void {
      $data     = json_decode(file_get_contents('php://input'), true);
      $required = ['nombre', 'email', 'licencia'];
      foreach ($required as $field) {
        if (empty($data[$field])) { ClientesView::error("Campo requerido: $field", 422); return; }
      }
      try {
        $id = $this->model->create($data);
        ClientesView::success(['id' => $id, 'message' => 'Cliente creado'], 201);
      } catch (\PDOException $e) {
        $code = $e->getCode() === '23000' ? 409 : 500;
        ClientesView::error('Error: ' . $e->getMessage(), $code);
      }
    }

    private function update(?int $id): void {
      if (!$id) { ClientesView::error('ID requerido', 400); return; }
      $data = json_decode(file_get_contents('php://input'), true);
      if (empty($data)) { ClientesView::error('Sin datos para actualizar', 400); return; }
      $ok = $this->model->update($id, $data);
      $ok ? ClientesView::success(['message' => 'Cliente actualizado'])
          : ClientesView::error('No se pudo actualizar', 500);
    }

    private function delete(?int $id): void {
      if (!$id) { ClientesView::error('ID requerido', 400); return; }
      if (!$this->model->getById($id)) { ClientesView::error('Cliente no encontrado', 404); return; }
      $ok = $this->model->delete($id);
      $ok ? ClientesView::success(['message' => 'Cliente eliminado'])
          : ClientesView::error('No se pudo eliminar', 500);
    }
  }