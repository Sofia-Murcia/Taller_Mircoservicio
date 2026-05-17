CREATE DATABASE IF NOT EXISTS clientes_db;
USE clientes_db;

CREATE TABLE IF NOT EXISTS `clientes` (
  `id`               int(11) NOT NULL AUTO_INCREMENT,
  `nombre`           varchar(150) NOT NULL,
  `telefono`         varchar(50) DEFAULT NULL,
  `correo`           varchar(100) DEFAULT NULL,
  `numero_licencia`  varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;