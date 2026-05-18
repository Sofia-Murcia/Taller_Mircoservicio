<?php
  require_once __DIR__ . '/../vendor/autoload.php';

  use App\Middlewares\CorsMiddleware;
  use App\Clientes\Controllers\ClientesController;

  CorsMiddleware::handle();

  $method = $_SERVER['REQUEST_METHOD'];
  $id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

  $controller = new ClientesController();
  $controller->handle($method, $id);