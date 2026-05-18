<?php
  namespace App\Devoluciones\Models;

  use App\Config\Database;
  use PDO;

  class DevolucionesModel {
    private PDO $db;

    public function __construct() {
      $this->db = Database::connect();
    }

    public function getAll(): array {
      return $this->db->query('SELECT * FROM devoluciones ORDER BY id DESC')->fetchAll();
    }

    public function getByReserva(int $reservaId): array {
      $stmt = $this->db->prepare('SELECT * FROM devoluciones WHERE reserva_id = :rid');
      $stmt->execute([':rid' => $reservaId]);
      return $stmt->fetchAll();
    }

    public function create(array $data): int {
      $sql  = 'INSERT INTO devoluciones (reserva_id, fecha_devolucion, observaciones)
               VALUES (:reserva_id, :fecha_devolucion, :observaciones)';
      $stmt = $this->db->prepare($sql);
      $stmt->execute([
        ':reserva_id'       => $data['reserva_id'],
        ':fecha_devolucion' => $data['fecha_devolucion'],
        ':observaciones'    => $data['observaciones'] ?? null,
      ]);
      return (int)$this->db->lastInsertId();
    }
  }