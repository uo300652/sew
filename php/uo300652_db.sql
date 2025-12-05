-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-11-2025 a las 16:35:51
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `uo300652_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facilitadorobservaciones`
--

CREATE TABLE `facilitadorobservaciones` (
  `observacion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `comentarios` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `testresultados`
--

CREATE TABLE `testresultados` (
  `test_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `dispositivo_electronico` enum('ordenador','tableta','telefono') NOT NULL,
  `tiempo_segundos` int(10) UNSIGNED NOT NULL,
  `completado` tinyint(1) NOT NULL,
  `usuario_comentarios` text DEFAULT NULL,
  `usuario_sugerencias` text DEFAULT NULL,
  `puntuacion` int(11) NOT NULL CHECK (`puntuacion` between 0 and 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuariotests`
--

CREATE TABLE `usuariotests` (
  `usuario_id` int(11) NOT NULL,
  `profesion` text NOT NULL,
  `edad` int(11) NOT NULL CHECK (`edad` >= 0),
  `genero` enum('Hombre','Mujer','Otro') NOT NULL,
  `percia_informatica` int(11) DEFAULT NULL CHECK (`percia_informatica` between 0 and 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `facilitadorobservaciones`
--
ALTER TABLE `facilitadorobservaciones`
  ADD PRIMARY KEY (`observacion_id`),
  ADD KEY `fk_observaciones_user` (`usuario_id`);

--
-- Indices de la tabla `testresultados`
--
ALTER TABLE `testresultados`
  ADD PRIMARY KEY (`test_id`),
  ADD KEY `fk_testresult_user` (`usuario_id`);

--
-- Indices de la tabla `usuariotests`
--
ALTER TABLE `usuariotests`
  ADD PRIMARY KEY (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `facilitadorobservaciones`
--
ALTER TABLE `facilitadorobservaciones`
  MODIFY `observacion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `testresultados`
--
ALTER TABLE `testresultados`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuariotests`
--
ALTER TABLE `usuariotests`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `facilitadorobservaciones`
--
ALTER TABLE `facilitadorobservaciones`
  ADD CONSTRAINT `fk_observaciones_user` FOREIGN KEY (`usuario_id`) REFERENCES `usuariotests` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `testresultados`
--
ALTER TABLE `testresultados`
  ADD CONSTRAINT `fk_testresult_user` FOREIGN KEY (`usuario_id`) REFERENCES `usuariotests` (`usuario_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
