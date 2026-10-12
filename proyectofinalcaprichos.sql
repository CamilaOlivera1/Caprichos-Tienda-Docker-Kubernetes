-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-07-2025 a las 09:14:39
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
-- Base de datos: `proyectofinalcaprichos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(3, 'Caprichos Children'),
(2, 'Caprichos Men'),
(1, 'Caprichos Women');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `envio_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `medioPago_id` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `usuario_id`, `envio_id`, `total`, `medioPago_id`, `fecha`) VALUES
(1, NULL, 2, 42000.00, 1, '2024-12-19 02:25:12'),
(2, NULL, 1, 17000.00, 1, '2024-12-19 02:33:39'),
(3, NULL, 2, 28000.00, 2, '2024-12-19 02:42:40'),
(15, 8, 1, 28000.00, 2, '2024-12-19 18:00:28'),
(16, 8, NULL, 0.00, NULL, '2024-12-19 18:24:37'),
(17, 8, NULL, 0.00, NULL, '2024-12-19 18:33:37'),
(18, 8, 1, 42000.00, 2, '2024-12-19 18:58:23'),
(19, 2, 1, 26000.00, 2, '2024-12-19 19:01:25'),
(22, 10, 1, 12000.00, 2, '2024-12-20 00:44:31'),
(24, 13, 1, 26000.00, 1, '2025-07-09 04:20:07'),
(25, 13, 1, 50000.00, 2, '2025-07-10 03:20:58'),
(26, 13, 2, 14000.00, 2, '2025-07-11 04:26:44'),
(27, 13, 3, 28000.00, 1, '2025-07-12 04:42:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envios`
--

CREATE TABLE `envios` (
  `id` int(11) NOT NULL,
  `destino` varchar(100) NOT NULL,
  `costo` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `envios`
--

INSERT INTO `envios` (`id`, `destino`, `costo`) VALUES
(1, 'Junín', 3000.00),
(2, 'San Martín', 3000.00),
(3, 'Rivadavia', 3000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medioPago`
--

CREATE TABLE `medioPago` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medioPago`
--

INSERT INTO `medioPago` (`id`, `nombre`) VALUES
(1, 'Tarjeta Crédito'),
(2, 'Tarjeta Débito');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `imagen`, `stock`, `categoria_id`) VALUES
(1, 'Remera Negra Mujer', 'Remera color negro con frutillas', 12000.00, 'remera negra.jpg', 1, 1),
(2, 'Remera Hombre', 'Remera deportiva color blanco', 14000.00, 'remera blanca hom.jpg', 5, 2),
(3, 'vestido Mujer', 'Vestido veraniego color rojo', 25000.00, 'vestido rojo.jpg', 2, 1),
(4, 'Falda Mujer', 'Falda larga elegante con detalles floreados', 30000.00, 'faldaFloreada.jpeg', 2, 1),
(5, 'Monito Mujer', 'Monito elegante color negro', 26000.00, 'monito.jpeg', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_comprados`
--

CREATE TABLE `productos_comprados` (
  `id` int(11) NOT NULL,
  `compra_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_comprados`
--

INSERT INTO `productos_comprados` (`id`, `compra_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, NULL, 1, 25000.00, 25000.00),
(2, 1, NULL, 1, 14000.00, 14000.00),
(3, 2, NULL, 1, 14000.00, 14000.00),
(4, 3, NULL, 1, 25000.00, 25000.00),
(9, 15, NULL, 1, 25000.00, 25000.00),
(10, 16, NULL, 1, 14000.00, 14000.00),
(11, 17, NULL, 1, 26000.00, 26000.00),
(12, 18, 4, 1, 30000.00, 30000.00),
(13, 18, 1, 1, 12000.00, 12000.00),
(14, 19, 1, 1, 12000.00, 12000.00),
(15, 19, 2, 1, 14000.00, 14000.00),
(18, 22, 1, 1, 12000.00, 12000.00),
(20, 24, 5, 1, 26000.00, 26000.00),
(21, 25, 3, 2, 25000.00, 50000.00),
(22, 26, 2, 1, 14000.00, 14000.00),
(23, 27, 2, 2, 14000.00, 28000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro`
--

CREATE TABLE `registro` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `apellido` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro`
--

INSERT INTO `registro` (`id`, `nombre`, `apellido`, `email`, `contraseña`, `fecha_registro`) VALUES
(2, 'Agustín', 'López', 'agustin@gmail.com', '$2y$10$/sjSPtzOe9nO6GorW0ySVeDwp6xQiMzxUaDNCr0xMhV3Dv/bIHdgO', '2024-12-16 03:07:50'),
(4, 'Muñeca', 'Rivera', 'muneca@gmail.com', '$2y$10$jY.4eXlBQn8uT32bK37jquy7oi95nG9CC/GCmlX6ZCjMsN9ekTlfC', '2024-12-16 05:41:20'),
(7, 'Ruth', 'Martínez', 'ruth@gmail.com', '$2y$10$7Vb7zqjS0YlIkO86cQ/qeu4bNntKeDZ4LLKtVNpjJmbwev7JDS22C', '2024-12-17 17:33:59'),
(8, 'Muñeca', 'Olivera', 'mumuu@gmail.com', '$2y$10$I6fHG1DjPhF2MUfjietQbu2WBm13gWYaRi6j6N.F6LatcoT60BIRy', '2024-12-17 17:37:59'),
(9, 'Camila Aldana', 'Olivera', 'cami@gmail.com', '$2y$10$fOPjXoR9DtCFcxG8jkK5CeYSVfBlQxfX9zvZAReQtR/FIS9QjAdke', '2024-12-19 20:46:19'),
(10, 'isa', 'Lemos', 'isa@gmail.com', '$2y$10$5k.5b3cXi7TfdX0F3xj9KunfhV5X9U7fREdzq71DdL7X18/ql66u.', '2024-12-20 00:41:13'),
(13, 'Camila', 'Olivera', 'camiluolivera2506@gmail.com', '$2y$10$CdfkG4kxajlV7uGBbJvUQ.fcCCZ8clg8AXD60fP/5iH7fO1632NwC', '2025-07-09 03:36:39');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `envio_id` (`envio_id`),
  ADD KEY `medioPago_id` (`medioPago_id`);

--
-- Indices de la tabla `envios`
--
ALTER TABLE `envios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `medioPago`
--
ALTER TABLE `medioPago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `productos_comprados`
--
ALTER TABLE `productos_comprados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compra_id` (`compra_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `registro`
--
ALTER TABLE `registro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `envios`
--
ALTER TABLE `envios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `medioPago`
--
ALTER TABLE `medioPago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `productos_comprados`
--
ALTER TABLE `productos_comprados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `registro`
--
ALTER TABLE `registro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `registro` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`envio_id`) REFERENCES `envios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `compras_ibfk_3` FOREIGN KEY (`medioPago_id`) REFERENCES `medioPago` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `productos_comprados`
--
ALTER TABLE `productos_comprados`
  ADD CONSTRAINT `productos_comprados_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `productos_comprados_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
