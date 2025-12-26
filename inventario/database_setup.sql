-- Script SQL para configurar la base de datos del sistema de inventario
-- Ejecutar este script para crear las tablas necesarias

CREATE DATABASE IF NOT EXISTS simple_stock;
USE simple_stock;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(64) NOT NULL UNIQUE,
    user_email VARCHAR(254) NOT NULL UNIQUE,
    firstname VARCHAR(100) NOT NULL,
    user_password_hash VARCHAR(255) NOT NULL,
    date_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_active TINYINT(1) NOT NULL DEFAULT 1,
    INDEX idx_email (user_email),
    INDEX idx_username (user_name)
);

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(255) NOT NULL,
    descripcion_categoria TEXT,
    date_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo_producto VARCHAR(100) NOT NULL UNIQUE,
    nombre_producto VARCHAR(255) NOT NULL,
    descripcion TEXT,
    id_categoria INT,
    precio_producto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock_producto INT NOT NULL DEFAULT 0,
    date_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE SET NULL
);

-- Insertar usuario administrador por defecto
INSERT IGNORE INTO users (user_name, user_email, firstname, user_password_hash, date_added) 
VALUES ('admin', 'admin@benga.com', 'Administrador', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW());
-- Contraseña por defecto: Admin123!

-- Insertar categorías de ejemplo
INSERT IGNORE INTO categorias (nombre_categoria, descripcion_categoria) VALUES
('Electrónicos', 'Productos electrónicos y tecnológicos'),
('Oficina', 'Suministros y equipos de oficina'),
('Hogar', 'Artículos para el hogar');

-- Insertar productos de ejemplo
INSERT IGNORE INTO productos (codigo_producto, nombre_producto, descripcion, id_categoria, precio_producto, stock_producto) VALUES
('ELEC001', 'Laptop HP', 'Laptop HP Pavilion 15 pulgadas', 1, 15000.00, 10),
('OFIC001', 'Silla de Oficina', 'Silla ergonómica para oficina', 2, 2500.00, 25),
('HOGAR001', 'Aspiradora', 'Aspiradora vertical sin bolsa', 3, 3500.00, 8);
