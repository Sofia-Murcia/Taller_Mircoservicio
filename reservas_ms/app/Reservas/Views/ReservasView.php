<?php
  namespace App\Reservas\Views;

  class ReservasView {
    public static function success(mixed $data, int $code = 200): void {
      http_response_code($code);
      echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public static function error(string $message, int $code = 400): void {
      http_response_code($code);
      echo json_encode(['status' => 'error', 'message' => $message]);
    }
  }