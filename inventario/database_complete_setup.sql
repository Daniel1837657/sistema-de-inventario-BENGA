-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS `simple_stock` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `simple_stock`;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `user_phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(255) NOT NULL,
  `descripcion_categoria` text,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_producto` varchar(50) NOT NULL,
  `nombre_producto` text NOT NULL,
  `precio_producto` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `id_categoria` int(11) NOT NULL,
  `stock_minimo` int(11) DEFAULT 10,
  `fecha_vencimiento` date NULL,
  `codigo_barras` varchar(50) NULL,
  `proveedor` varchar(255) NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `codigo_producto` (`codigo_producto`),
  KEY `idx_categoria` (`id_categoria`),
  KEY `idx_stock` (`stock`),
  KEY `idx_codigo_barras` (`codigo_barras`),
  KEY `idx_vencimiento` (`fecha_vencimiento`),
  FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla para movimientos de stock
CREATE TABLE IF NOT EXISTS `movimientos_stock` (
  `id_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada','salida') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `observaciones` text,
  `fecha_movimiento` datetime NOT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY (`id_movimiento`),
  KEY `idx_producto` (`id_producto`),
  KEY `idx_fecha` (`fecha_movimiento`),
  KEY `idx_usuario` (`usuario_id`),
  FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE,
  FOREIGN KEY (`usuario_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar usuario administrador por defecto
INSERT IGNORE INTO `users` (`user_name`, `user_password_hash`, `user_email`, `firstname`, `lastname`, `date_added`) 
VALUES ('admin', '$2y$10$8K1p/wgyQ1Vp92JRiCM5.eUiUFvzsItMtpjflFmy.tZycXAjDOpTi', 'admin@benga.com', 'Administrador', 'Sistema', NOW());

-- Insertar categorías de ejemplo
INSERT IGNORE INTO `categorias` (`nombre_categoria`, `descripcion_categoria`) VALUES
('Electrónicos', 'Productos electrónicos y tecnológicos'),
('Ropa', 'Prendas de vestir y accesorios'),
('Hogar', 'Artículos para el hogar'),
('Deportes', 'Equipos y accesorios deportivos'),
('Libros', 'Libros y material de lectura');

-- Insertar productos de ejemplo
INSERT IGNORE INTO `productos` (`codigo_producto`, `nombre_producto`, `precio_producto`, `stock`, `id_categoria`, `stock_minimo`) VALUES
('ELEC001', 'Smartphone Samsung Galaxy', 299.99, 15, 1, 5),
('ELEC002', 'Laptop HP Pavilion', 599.99, 8, 1, 3),
('ROPA001', 'Camiseta Polo', 29.99, 25, 2, 10),
('ROPA002', 'Jeans Levis', 79.99, 12, 2, 8),
('HOGAR001', 'Cafetera Eléctrica', 89.99, 6, 3, 5),
('HOGAR002', 'Aspiradora Robot', 199.99, 4, 3, 2),
('DEP001', 'Balón de Fútbol', 24.99, 20, 4, 15),
('DEP002', 'Raqueta de Tenis', 149.99, 7, 4, 5),
('LIB001', 'El Quijote', 19.99, 30, 5, 20),
('LIB002', 'Cien Años de Soledad', 24.99, 18, 5, 15);

-- Insertar movimientos iniciales de stock
INSERT IGNORE INTO `movimientos_stock` (`id_producto`, `tipo_movimiento`, `cantidad`, `motivo`, `fecha_movimiento`, `usuario_id`) 
SELECT 
    p.id_producto,
    'entrada',
    p.stock,
    'Stock inicial del sistema',
    NOW(),
    1
FROM productos p 
WHERE p.stock > 0;
