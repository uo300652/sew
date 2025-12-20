-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 11:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uo300652_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `facilitadorobservaciones`
--

CREATE TABLE `facilitadorobservaciones` (
  `observador_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `observador_comentarios` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilitadorobservaciones`
--

INSERT INTO `facilitadorobservaciones` (`observador_id`, `usuario_id`, `observador_comentarios`) VALUES
(1, 1, 'El usuario tuvo dificultades a la hora de buscar la temperatura media el último día de entramiento debido a que toda la informacion se mostraba como una cadena de texto separada con comas sin un formato visual claro que distinga las diferentes partes'),
(2, 2, 'Tuvo algo de dificultad a la hora de encontrar la hora del amanecer del circuito, buscando inicialmente en la propia pestaña de circuito para finalmente darse cuenta de que la información se encontraba en la pestaña meteorología. Descubrió que la información se encontraba en meteorología navegando hasta encontrarlo en vez de usar el menú de ayuda.'),
(3, 3, 'Al usuario le costó entender el funcionamiento de la página, no entendía que para navegar debía usar el menú de navegación. Buscaba algún enlace o botón al que acceder. Una vez que entendió el funcionamiento del menú de ayuda logró responder las preguntas sin ningún contratiempo. La única respuesta que le costó encontrar fue la hora del amanacer del día de la carrera la cual buscó inicialmente en la pestaña de circuito hasta que finalmente se acabó llegando a la página de meteorología dando con la respuesta correcta.'),
(4, 4, 'Problemas inciales al entender como funciona la navegación de la página. Una vez entendido cómo fucniona el menú de navegación encontró la información pertinente sin ninguna dificultad. Lo que más le costo encontrar fue la información del circuito abriendo el InfoCircuitoHTML, pero tras consultar la sección de ayuda logró entender la funcionalidad y encontrar la información sin ningún problema'),
(5, 5, 'Al principio le costó entender el menú de navegación. Una vez que entendió como funcionaba navegó sin problemas y encontró todas las respuestas.'),
(6, 6, 'Navegación fluida. No encontró ningún problema a la hora de buscar la información. Se apoyó en el menú de ayuda y para encontrar las cosas que le causaban dudas.'),
(7, 7, 'No tuvo ningún problema a la hora de navegar por la página, encontró todas las respuestas sin ningún problema. La respuesta que más le costó encontrar fue la definición de pole, es decir, el glosario con términos de MotoGP.');

-- --------------------------------------------------------

--
-- Table structure for table `testresultados`
--

CREATE TABLE `testresultados` (
  `test_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `dispositivo_electronico` enum('ordenador','tablet','movil') NOT NULL,
  `tiempo_segundos` int(10) UNSIGNED NOT NULL,
  `completado` int(11) DEFAULT NULL CHECK (`completado` = 0 or `completado` = 1),
  `usuario_comentarios` text DEFAULT NULL,
  `usuario_sugerencias` text DEFAULT NULL,
  `puntuacion` int(11) DEFAULT NULL CHECK (`puntuacion` between 0 and 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testresultados`
--

INSERT INTO `testresultados` (`test_id`, `usuario_id`, `dispositivo_electronico`, `tiempo_segundos`, `completado`, `usuario_comentarios`, `usuario_sugerencias`, `puntuacion`) VALUES
(1, 1, 'tablet', 333, 1, 'Dificultad a la hora de encontrar los datos referidos a la meteorología', 'Mejorar la forma en la que se muestran los datos meteorologicos para que sea menos ambiguo y mas claro', 7),
(2, 2, 'tablet', 502, 1, 'La página está muy bien estructurada, permite indagar e interactuar a través de los diversos mecanismos multimedia que presenta. Es clara e intuitiva.', 'Indicar la información contenida en los archivos con denominación HTML, SVG, KML.', 9),
(3, 3, 'ordenador', 700, 1, 'al inicio, entender la mecánica de las preguntas y la forma de búsqueda', 'explicar al inicio, como se debe proceder para la búsqueda de las respuestas a las distintas preguntas', 7),
(4, 4, 'ordenador', 1378, 1, 'NAVEGACIÓN INTUITIVA. UNA VEZ CONOCIDO EL FUNCIONAMIENTO, AUMENTA LA RAPIDEZ PARA RESPONDER A LAS PREGUNTAS.', 'MAYOR SENCILLEZ PARA VISUALIZAR LAS CARACTERÍSTICAS DEL CIRCUITO SOBRE TODO PENSANDO EN USUARIOS MENOS EXPERTOS', 9),
(5, 5, 'tablet', 958, 1, 'Aunque me faltaban las gafas, creo que la página me parece muy intuitiva.', 'Todo correcto a mi entender', 9),
(6, 6, 'movil', 394, 1, 'Página muy bien hecha y un contenido muy amplio y la navegación fue muy intuitiva.', 'Ninguna. Todo muy bien explicado.', 10),
(7, 7, 'movil', 486, 1, 'La navegación me ha parecido muy intuitiva y las secciones están bien organizadas, de forma clara y ordenada. La información que contiene es completa y precisa.', 'Ninguna', 10);

-- --------------------------------------------------------

--
-- Table structure for table `usuariotests`
--

CREATE TABLE `usuariotests` (
  `usuario_id` int(11) NOT NULL,
  `profesion` text NOT NULL,
  `edad` int(11) NOT NULL,
  `genero` enum('Hombre','Mujer','Otro') NOT NULL,
  `pericia_informatica` int(11) DEFAULT NULL CHECK (`pericia_informatica` between 0 and 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuariotests`
--

INSERT INTO `usuariotests` (`usuario_id`, `profesion`, `edad`, `genero`, `pericia_informatica`) VALUES
(1, 'Estudiante software', 20, 'Hombre', 10),
(2, 'Catedrática Lengua castellana y Literatura', 60, 'Mujer', 7),
(3, 'abogado', 66, 'Hombre', 6),
(4, 'ENFERMERA ', 63, 'Mujer', 5),
(5, 'FUNCIONARIO JUBILADO', 70, 'Hombre', 6),
(6, 'Estudiante derecho', 20, 'Mujer', 7),
(7, 'Abogada', 35, 'Mujer', 7);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `facilitadorobservaciones`
--
ALTER TABLE `facilitadorobservaciones`
  ADD PRIMARY KEY (`observador_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `testresultados`
--
ALTER TABLE `testresultados`
  ADD PRIMARY KEY (`test_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `usuariotests`
--
ALTER TABLE `usuariotests`
  ADD PRIMARY KEY (`usuario_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `facilitadorobservaciones`
--
ALTER TABLE `facilitadorobservaciones`
  MODIFY `observador_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `testresultados`
--
ALTER TABLE `testresultados`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `usuariotests`
--
ALTER TABLE `usuariotests`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `facilitadorobservaciones`
--
ALTER TABLE `facilitadorobservaciones`
  ADD CONSTRAINT `facilitadorobservaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuariotests` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `testresultados`
--
ALTER TABLE `testresultados`
  ADD CONSTRAINT `testresultados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuariotests` (`usuario_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
