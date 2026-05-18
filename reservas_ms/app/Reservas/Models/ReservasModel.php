<?php
  namespace App\Reservas\Models;

  use App\Config\Database;
  use PDO;

  class ReservasModel {
    private PDO $db;

    public function __construct() {
      $this->db = Database::connect();
    }

    public function getAll(): array {
      return $this->db->query('SELECT * FROM reservas ORDER BY id DESC')->fetchAll();
    }

    public function getById(int $id): array|false {
      $stmt = $this->db->prepare('SELECT * FROM reservas WHERE id = :id');
      $stmt->execute([':id' => $id]);
      return $stmt->fetch();
    }

    public function getByVehiculo(int $vehiculoId): array {
      $stmt = $this->db->prepare('SELECT * FROM reservas WHERE vehiculo_id = :vid ORDER BY id DESC');
      $stmt->execute([':vid' => $vehiculoId]);
      return $stmt->fetchAll();
    }

    public function getByCliente(int $clienteId): array {
      $stmt = $this->db->prepare('SELECT * FROM reservas WHERE cliente_id = :cid ORDER BY id DESC');
      $stmt->execute([':cid' => $clienteId]);
      return $stmt->fetchAll();
    }

    public function getActivas(): array {
      $stmt = $this->db->prepare("SELECT * FROM reservas WHERE estado = 'activa'");
      $stmt->execute();
      return $stmt->fetchAll();
    }

    public function checkSolapamiento(int $vehiculoId, string $inicio, string $fin): bool {
      $sql  = "SELECT COUNT(*) FROM reservas
               WHERE vehiculo_id = :vid AND estado = 'activa'
                 AND (:inicio <= fecha_fin AND :fin >= fecha_inicio)";
      $stmt = $this->db->prepare($sql);
      $stmt->execute([':vid' => $vehiculoId, ':inicio' => $inicio, ':fin' => $fin]);
      return (int)$stmt->fetchColumn() > 0;
    }

    public function create(array $data): int {
      $sql  = 'INSERT INTO reservas (vehiculo_id, cliente_id, fecha_inicio, fecha_fin, total)
               VALUES (:vehiculo_id, :cliente_id, :fecha_inicio, :fecha_fin, :total)';
      $stmt = $this->db->prepare($sql);
      $stmt->execute([
        ':vehiculo_id'  => $data['vehiculo_id'],
        ':cliente_id'   => $data['cliente_id'],
        ':fecha_inicio' => $data['fecha_inicio'],
        ':fecha_fin'    => $data['fecha_fin'],
        ':total'        => $data['total'],
      ]);
      return (int)$this->db->lastInsertId();
    }

    public function updateEstado(int $id, string $estado): bool {
      $stmt = $this->db->prepare('UPDATE reservas SET estado = :estado WHERE id = :id');
      return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }
  }