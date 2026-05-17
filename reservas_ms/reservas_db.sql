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