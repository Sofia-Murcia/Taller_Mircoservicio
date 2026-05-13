-- ============================================================
-- Bases de datos para microservicios - Alquiler de Vehículos
-- Ejecutar en phpMyAdmin o MySQL antes de levantar los servicios
-- ============================================================

-- 1. vehiculos_ms
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

-- 2. clientes_ms
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

-- 3. reservas_ms
-- NOTA: sin FOREIGN KEY, cada servicio tiene su propia BD
CREATE DATABASE IF NOT EXISTS reservas_db;
USE reservas_db;

CREATE TABLE IF NOT EXISTS `reservas` (
  `id`           int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id`   int(11) NOT NULL,
  `vehiculo_id`  int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin`    date NOT NULL,
  `estado`       enum('activa','completada','cancelada') DEFAULT 'activa',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
