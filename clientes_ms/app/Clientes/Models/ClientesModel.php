<?php
  namespace App\Clientes\Models;

  use App\Config\Database;
  use PDO;

  class ClientesModel {
    private PDO $db;

    public function __construct() {
      $this->db = Database::connect();
    }

    public function getAll(): array {
      return $this->db->query('SELECT * FROM clientes ORDER BY id DESC')->fetchAll();
    }

    public function getById(int $id): array|false {
      $stmt = $this->db->prepare('SELECT * FROM clientes WHERE id = :id');
      $stmt->execute([':id' => $id]);
      return $stmt->fetch();
    }

    public function create(array $data): int {
      $sql  = 'INSERT INTO clientes (nombre, email, telefono, licencia)
               VALUES (:nombre, :email, :telefono, :licencia)';
      $stmt = $this->db->prepare($sql);
      $stmt->execute([
        ':nombre'   => $data['nombre'],
        ':email'    => $data['email'],
        ':telefono' => $data['telefono'] ?? null,
        ':licencia' => $data['licencia'],
      ]);
      return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
      $fields  = [];
      $params  = [':id' => $id];
      $allowed = ['nombre', 'email', 'telefono', 'licencia'];
      foreach ($allowed as $field) {
        if (isset($data[$field])) {
          $fields[]        = "$field = :$field";
          $params[":$field"] = $data[$field];
        }
      }
      if (empty($fields)) return false;
      $stmt = $this->db->prepare('UPDATE clientes SET ' . implode(', ', $fields) . ' WHERE id = :id');
      return $stmt->execute($params);
    }

    public function delete(int $id): bool {
      $stmt = $this->db->prepare('DELETE FROM clientes WHERE id = :id');
      return $stmt->execute([':id' => $id]);
    }
  }