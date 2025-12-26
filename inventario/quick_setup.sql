-- Script rápido para crear base de datos y tablas
CREATE DATABASE IF NOT EXISTS simple_stock CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE simple_stock;

-- Tabla users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL,
  `user_password_hash` varchar(255) NOT NULL,
  `user_email` varchar(64) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(60) NOT NULL,
  `descripcion_categoria` text,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_producto` varchar(50) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `descripcion_producto` text,
  `precio_producto` decimal(10,2) NOT NULL,
  `stock_producto` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 5,
  `fecha_vencimiento` date DEFAULT NULL,
  `codigo_barras` varchar(50) DEFAULT NULL,
  `proveedor` varchar(100) DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `codigo_producto` (`codigo_producto`),
  KEY `id_categoria` (`id_categoria`),
  FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla movimientos_stock
CREATE TABLE IF NOT EXISTS `movimientos_stock` (
  `id_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada','salida') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` varchar(200) NOT NULL,
  `observaciones` text,
  `usuario_id` int(11) NOT NULL,
  `fecha_movimiento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_movimiento`),
  KEY `idx_producto` (`id_producto`),
  KEY `idx_fecha` (`fecha_movimiento`),
  KEY `idx_usuario` (`usuario_id`),
  FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE,
  FOREIGN KEY (`usuario_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar usuario admin
INSERT IGNORE INTO `users` (`user_name`, `user_password_hash`, `user_email`, `firstname`, `lastname`, `date_added`) 
VALUES ('admin', '$2y$10$8K1p/wgyQ1Vp92JRiCM5.eUiUFvzsItMtpjflFmy.tZycXAjDOpTi', 'admin@benga.com', 'Administrador', 'Sistema', NOW());

-- Insertar categorías
INSERT IGNORE INTO `categorias` (`nombre_categoria`, `descripcion_categoria`) VALUES
('Electrónicos', 'Productos electrónicos y tecnológicos'),
('Ropa', 'Prendas de vestir y accesorios'),
('Hogar', 'Artículos para el hogar'),
('Deportes', 'Equipos y accesorios deportivos'),
('Libros', 'Libros y material de lectura');

-- Insertar productos de ejemplo
INSERT IGNORE INTO `productos` (`codigo_producto`, `nombre_producto`, `descripcion_producto`, `precio_producto`, `stock_producto`, `stock_minimo`, `fecha_vencimiento`, `codigo_barras`, `proveedor`, `id_categoria`) VALUES
('LAPTOP001', 'Laptop HP Pavilion', 'Laptop HP Pavilion 15 pulgadas', 15999.99, 10, 2, '2025-12-31', '123456789012', 'HP Inc.', 1),
('MOUSE001', 'Mouse Inalámbrico', 'Mouse inalámbrico ergonómico', 299.99, 25, 5, NULL, '123456789013', 'Logitech', 1),
('CAMISA001', 'Camisa Formal', 'Camisa formal de algodón', 599.99, 15, 3, NULL, '123456789014', 'Textiles SA', 2),
('MESA001', 'Mesa de Centro', 'Mesa de centro de madera', 2499.99, 5, 1, NULL, '123456789015', 'Muebles Modernos', 3),
('BALON001', 'Balón de Fútbol', 'Balón oficial FIFA', 899.99, 20, 5, NULL, '123456789016', 'Sports Pro', 4);

-- Insertar movimientos iniciales
INSERT IGNORE INTO `movimientos_stock` (`id_producto`, `tipo_movimiento`, `cantidad`, `motivo`, `usuario_id`) VALUES
(1, 'entrada', 10, 'Stock inicial', 1),
(2, 'entrada', 25, 'Stock inicial', 1),
(3, 'entrada', 15, 'Stock inicial', 1),
(4, 'entrada', 5, 'Stock inicial', 1),
(5, 'entrada', 20, 'Stock inicial', 1);
