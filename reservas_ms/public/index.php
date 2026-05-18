<?php
  require_once __DIR__ . '/../vendor/autoload.php';

  use App\Middlewares\CorsMiddleware;
  use App\Reservas\Controllers\ReservasController;

  CorsMiddleware::handle();

  $method = $_SERVER['REQUEST_METHOD'];
  $id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

  $controller = new ReservasController();
  $controller->handle($method, $id);