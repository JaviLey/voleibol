-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para ligas_voleibol_chiapas
CREATE DATABASE IF NOT EXISTS `ligas_voleibol_chiapas` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `ligas_voleibol_chiapas`;

-- Volcando estructura para tabla ligas_voleibol_chiapas.equipos
CREATE TABLE IF NOT EXISTS `equipos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_liga` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `nombre_equipo` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `rama` enum('varonil','femenil') NOT NULL,
  `categoria` enum('Libre','1ra. Division','2da. Division') NOT NULL,
  `validado` tinyint(1) DEFAULT 0,
  `comprobante_inscripcion` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `referencia_pago` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_liga` (`id_liga`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `equipos_ibfk_1` FOREIGN KEY (`id_liga`) REFERENCES `ligas` (`id`),
  CONSTRAINT `equipos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Volcando estructura para tabla ligas_voleibol_chiapas.jugadores
CREATE TABLE IF NOT EXISTS `jugadores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_equipo` int(11) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `curp` varchar(18) DEFAULT NULL,
  `numero_sired` varchar(50) DEFAULT NULL,
  `estatura` decimal(3,2) DEFAULT NULL,
  `domicilio` text DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `numero_playera` int(11) DEFAULT NULL,
  `firma` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `curp` (`curp`),
  KEY `id_equipo` (`id_equipo`),
  CONSTRAINT `jugadores_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla ligas_voleibol_chiapas.jugadores: ~0 rows (aproximadamente)

-- Volcando estructura para tabla ligas_voleibol_chiapas.ligas
CREATE TABLE IF NOT EXISTS `ligas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `fondo_cedula` varchar(255) DEFAULT NULL,
  `logo_fmvb` varchar(255) DEFAULT NULL,
  `logo_avech` varchar(255) DEFAULT NULL,
  `activa` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla ligas_voleibol_chiapas.ligas: ~1 rows (aproximadamente)
INSERT INTO `ligas` (`id`, `nombre`, `logo`, `fondo_cedula`, `logo_fmvb`, `logo_avech`, `activa`, `fecha_creacion`) VALUES
	(1, 'Liga Municipal de Voleibol OMA', NULL, NULL, NULL, NULL, 1, '2025-08-23 23:26:25');

-- Volcando estructura para tabla ligas_voleibol_chiapas.pagos_credenciales
CREATE TABLE IF NOT EXISTS `pagos_credenciales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_equipo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `metodo_pago` enum('transferencia','deposito','oxxo','efectivo') NOT NULL,
  `comprobante` varchar(255) NOT NULL,
  `validado` tinyint(1) DEFAULT 0,
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `fecha_validacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_equipo` (`id_equipo`),
  CONSTRAINT `pagos_credenciales_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla ligas_voleibol_chiapas.pagos_credenciales: ~0 rows (aproximadamente)

-- Volcando estructura para tabla ligas_voleibol_chiapas.personal_tecnico
CREATE TABLE IF NOT EXISTS `personal_tecnico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_equipo` int(11) NOT NULL,
  `tipo` enum('medico','auxiliar_medico','delegado','representante','entrenador','auxiliar') NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `curp` varchar(18) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `firma` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_equipo` (`id_equipo`),
  CONSTRAINT `personal_tecnico_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla ligas_voleibol_chiapas.personal_tecnico: ~0 rows (aproximadamente)

-- Volcando estructura para tabla ligas_voleibol_chiapas.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `tipo` enum('admin','delegado') DEFAULT 'delegado',
  `activo` tinyint(1) DEFAULT 0,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla ligas_voleibol_chiapas.usuarios: ~4 rows (aproximadamente)
INSERT INTO `usuarios` (`id`, `email`, `password`, `nombre_completo`, `telefono`, `direccion`, `tipo`, `activo`, `fecha_registro`) VALUES
	(1, 'admin@voleibolchiapas.com', '$2y$10$E.uDo1mTKGdlTeYf3e3b7e22lTzCw4/kzXe7qsUe.PV4GVTMg0iPa', 'Administrador Principal', '9611234567', 'Tuxtla Gutiérrez, Chiapas', 'admin', 1, '2025-08-23 23:26:25');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
