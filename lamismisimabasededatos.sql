-- lamismisimabasededatos.sql - CORREGIDO para XAMPP/phpMyAdmin
-- Basado en tu dump original del 15-09-2026, con FIX del FK invertido
-- y defaults para que register.php funcione sin problemas.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS=0;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Si ya existe la BD, no falla
CREATE DATABASE IF NOT EXISTS `lamismisimabasededatos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `lamismisimabasededatos`;

DROP TABLE IF EXISTS `xp_usuario`;
DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id_usuario` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `pass_hash` varchar(255) NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `xp_usuario` (
  `id_usuario` int(10) NOT NULL,
  `nivel` int(10) NOT NULL DEFAULT 1,
  `xp_necesaria` int(10) NOT NULL DEFAULT 500,
  `xp_ganada` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_usuario`),
  CONSTRAINT `fk_xp_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
