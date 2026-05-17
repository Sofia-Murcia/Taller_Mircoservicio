CREATE DATABASE IF NOT EXISTS vehiculos_db;
USE vehiculos_db;

CREATE TABLE IF NOT EXISTS `vehiculos` (
  `id`        int(11) NOT NULL AUTO_INCREMENT,
  `marca`     varchar(100) NOT NULL,
  `modelo`    varchar(100) NOT NULL,
  `anio`      year NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `estado`    enum('disponible','alquilado','mantenimiento') DEFAULT 'disponible',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;