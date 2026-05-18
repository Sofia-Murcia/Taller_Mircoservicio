<?php
  namespace App\Vehiculos\Models;

  use App\Config\Database;
  use PDO;

  class VehiculosModel {
    private PDO $db;

    public function __construct() {
      $this->db = Database::connect();
    }

    public function getAll(): array {
      $stmt = $this->db->query('SELECT * FROM vehiculos ORDER BY id DESC');
      return $stmt->fetchAll();
    }

    public function getDisponibles(): array {
      $stmt = $this->db->prepare('SELECT * FROM vehiculos WHERE estado = :estado');
      $stmt->execute([':estado' => 'disponible']);
      return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
      $stmt = $this->db->prepare('SELECT * FROM vehiculos WHERE id = :id');
      $stmt->execute([':id' => $id]);
      return $stmt->fetch();
    }

    public function create(array $data): int {
      $sql = 'INSERT INTO vehiculos (marca, modelo, anio, categoria, placa, precio_dia)
              VALUES (:marca, :modelo, :anio, :categoria, :placa, :precio_dia)';
      $stmt = $this->db->prepare($sql);
      $stmt->execute([
        ':marca'      => $data['marca'],
        ':modelo'     => $data['modelo'],
        ':anio'       => $data['anio'],
        ':categoria'  => $data['categoria'],
        ':placa'      => $data['placa'],
        ':precio_dia' => $data['precio_dia'],
      ]);
      return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
      $fields = [];
      $params = [':id' => $id];
      $allowed = ['marca', 'modelo', 'anio', 'categoria', 'placa', 'precio_dia', 'estado'];
      foreach ($allowed as $field) {
        if (isset($data[$field])) {
          $fields[] = "$field = :$field";
          $params[":$field"] = $data[$field];
        }
      }
      if (empty($fields)) return false;
      $sql  = 'UPDATE vehiculos SET ' . implode(', ', $fields) . ' WHERE id = :id';
      $stmt = $this->db->prepare($sql);
      return $stmt->execute($params);
    }

    public function updateEstado(int $id, string $estado): bool {
      $stmt = $this->db->prepare('UPDATE vehiculos SET estado = :estado WHERE id = :id');
      return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public function delete(int $id): bool {
      $stmt = $this->db->prepare('DELETE FROM vehiculos WHERE id = :id');
      return $stmt->execute([':id' => $id]);
    }
  }