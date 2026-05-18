<?php
  namespace App\Config;

  use PDO;

  class Database {
    private static ?PDO $pdo = null;

    public static function connect(): PDO {
      if (!self::$pdo) {
        $dsn = 'mysql:host=localhost;dbname=clientes_db;charset=utf8mb4';
        self::$pdo = new PDO($dsn, 'root', '', [
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
      }
      return self::$pdo;
    }
  }