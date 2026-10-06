-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-10-2026 a las 21:19:52
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `lamismisimabasededatos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(10) NOT NULL,
  `username` varchar(20) NOT NULL,
  `pass_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `username`, `pass_hash`) VALUES
(1, 'usuariotest1', '$2y$10$91ZEjnBFiPBOF4Lylawuq.V7IHCRWHNRvdna.dLlP6IdekD4Dp4bS'),
(2, 'holamundo', '$2y$10$hz93Ip52qrXwsxT2tDWFZeGPfD5vBzVq1gM3rIondwqrhRewbphoq'),
(3, 'usuarioconxp', '$2y$10$0UF4FIF1vqtn9RbxKPlBXu2Gby81fKcjzEsEsda.iTgeZQSj.wXom'),
(4, 'usuarioconxp2', '$2y$10$3jylo7POmI48EmETeN8FneMDgMxzhxhuDQ2w7W2/I63rarSdRal9O'),
(5, 'usuarioconxp2', '$2y$10$5S/KLDi/QPWsLOh5Ih18oeqO6rXgNJtmMt5d/BTpNlJeWKNZNBnDq'),
(6, 'usuarioconxp3', '$2y$10$tVW3/s123mvgkGfIIjqqEOrzC84pGmgydHgeDwOV8pKPjaZrEXP1u');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `xp_usuario`
--

CREATE TABLE `xp_usuario` (
  `xp_id` int(10) NOT NULL,
  `usuario_asocc` int(10) NOT NULL,
  `nivel` int(10) NOT NULL DEFAULT 1,
  `xp_necesaria` int(10) NOT NULL DEFAULT 500,
  `xp_ganada` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `xp_usuario`
--

INSERT INTO `xp_usuario` (`xp_id`, `usuario_asocc`, `nivel`, `xp_necesaria`, `xp_ganada`) VALUES
(1, 6, 1, 500, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `xp_usuario`
--
ALTER TABLE `xp_usuario`
  ADD PRIMARY KEY (`xp_id`),
  ADD KEY `usuario_asocc` (`usuario_asocc`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `xp_usuario`
--
ALTER TABLE `xp_usuario`
  MODIFY `xp_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `xp_usuario`
--
ALTER TABLE `xp_usuario`
  ADD CONSTRAINT `xp_usuario_ibfk_1` FOREIGN KEY (`usuario_asocc`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
