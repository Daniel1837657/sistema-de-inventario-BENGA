-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-12-2016 a las 11:38:12
-- Versión del servidor: 5.6.26
-- Versión de PHP: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Estructura de tabla para la tabla `categorias`
CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(255) NOT NULL,
  `descripcion_categoria` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Datos iniciales para `categorias`
INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `descripcion_categoria`, `date_added`) VALUES
(1, 'Repuestos', 'Equipos para el hogar', '2016-12-19 00:00:00'),
(4, 'Equipos', 'Equipos stihl', '2016-12-19 21:06:37'),
(5, 'Accesorios', 'Accesorios stihl', '2016-12-19 21:06:39');

-- Estructura de tabla para la tabla `products`
CREATE TABLE IF NOT EXISTS `products` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_producto` char(20) NOT NULL UNIQUE,
  `nombre_producto` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `precio_producto` decimal(15,2) NOT NULL COMMENT 'Precios con dos decimales',
  `stock` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  PRIMARY KEY (`id_producto`),
  CONSTRAINT `fk_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Estructura de tabla para la tabla `historial`
CREATE TABLE IF NOT EXISTS `historial` (
  `id_historial` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `nota` varchar(255) NOT NULL,
  `referencia` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL,
  PRIMARY KEY (`id_historial`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `fk_id_producto` FOREIGN KEY (`id_producto`) REFERENCES `products` (`id_producto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Estructura de tabla para la tabla `users`
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `firstname` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL UNIQUE COMMENT 'user''s name, unique',
  `user_password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL UNIQUE COMMENT 'user''s email, unique',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user data';

-- Datos iniciales para `users`
INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `user_name`, `user_password_hash`, `user_email`, `date_added`) VALUES
(1, 'Daniel', 'Perdomo', 'admin', '$2y$10$MPVHzZ2ZPOWmtUUGCq3RXu31OTB.jo7M9LZ7PmPQYmgETSNn19ejO', 'admin@admin.com', '2016-12-19 15:06:00');

-- Estructura de tabla para la tabla `pdf_files`
CREATE TABLE IF NOT EXISTS `pdf_files` (
  `id_pdf` int(11) NOT NULL AUTO_INCREMENT,
  `pdf` LONGBLOB NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `dependencia` varchar(255) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pdf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Consulta para mostrar los precios formateados en formato colombiano (ejemplo)
/*
SELECT 
  id_producto, 
  codigo_producto, 
  nombre_producto, 
  FORMAT(precio_producto, 2, 'de_DE') AS precio_formateado -- Ajustar según servidor
FROM `products`;
*/
