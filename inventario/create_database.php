<?php
// Script simple para crear la base de datos y ejecutar el setup
require_once("config/db.php");

// Conectar a MySQL sin seleccionar base de datos
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}

// Crear base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($connection->query($sql)) {
    echo "Base de datos '" . DB_NAME . "' creada o ya existe.<br>";
} else {
    echo "Error creando base de datos: " . $connection->error . "<br>";
}

// Seleccionar la base de datos
$connection->select_db(DB_NAME);

// Crear tabla users
$sql = "CREATE TABLE IF NOT EXISTS `users` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($connection->query($sql)) {
    echo "Tabla 'users' creada correctamente.<br>";
} else {
    echo "Error creando tabla users: " . $connection->error . "<br>";
}

// Crear tabla categorias
$sql = "CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(60) NOT NULL,
  `descripcion_categoria` text,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($connection->query($sql)) {
    echo "Tabla 'categorias' creada correctamente.<br>";
} else {
    echo "Error creando tabla categorias: " . $connection->error . "<br>";
}

// Crear tabla productos
$sql = "CREATE TABLE IF NOT EXISTS `productos` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($connection->query($sql)) {
    echo "Tabla 'productos' creada correctamente.<br>";
} else {
    echo "Error creando tabla productos: " . $connection->error . "<br>";
}

// Crear tabla movimientos_stock
$sql = "CREATE TABLE IF NOT EXISTS `movimientos_stock` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($connection->query($sql)) {
    echo "Tabla 'movimientos_stock' creada correctamente.<br>";
} else {
    echo "Error creando tabla movimientos_stock: " . $connection->error . "<br>";
}

// Insertar usuario admin
$sql = "INSERT IGNORE INTO `users` (`user_name`, `user_password_hash`, `user_email`, `firstname`, `lastname`, `date_added`) 
        VALUES ('admin', '$2y$10\$8K1p/wgyQ1Vp92JRiCM5.eUiUFvzsItMtpjflFmy.tZycXAjDOpTi', 'admin@benga.com', 'Administrador', 'Sistema', NOW())";

if ($connection->query($sql)) {
    echo "Usuario admin creado correctamente.<br>";
} else {
    echo "Error creando usuario admin: " . $connection->error . "<br>";
}

// Insertar categorías
$categorias = [
    ['Electrónicos', 'Productos electrónicos y tecnológicos'],
    ['Ropa', 'Prendas de vestir y accesorios'],
    ['Hogar', 'Artículos para el hogar'],
    ['Deportes', 'Equipos y accesorios deportivos'],
    ['Libros', 'Libros y material de lectura']
];

foreach ($categorias as $cat) {
    $sql = "INSERT IGNORE INTO `categorias` (`nombre_categoria`, `descripcion_categoria`) VALUES (?, ?)";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ss", $cat[0], $cat[1]);
    $stmt->execute();
}
echo "Categorías creadas correctamente.<br>";

// Insertar productos de ejemplo
$productos = [
    ['LAPTOP001', 'Laptop HP Pavilion', 'Laptop HP Pavilion 15 pulgadas', 15999.99, 10, 2, '2025-12-31', '123456789012', 'HP Inc.'],
    ['MOUSE001', 'Mouse Inalámbrico', 'Mouse inalámbrico ergonómico', 299.99, 25, 5, NULL, '123456789013', 'Logitech'],
    ['CAMISA001', 'Camisa Formal', 'Camisa formal de algodón', 599.99, 15, 3, NULL, '123456789014', 'Textiles SA'],
    ['MESA001', 'Mesa de Centro', 'Mesa de centro de madera', 2499.99, 5, 1, NULL, '123456789015', 'Muebles Modernos'],
    ['BALON001', 'Balón de Fútbol', 'Balón oficial FIFA', 899.99, 20, 5, NULL, '123456789016', 'Sports Pro']
];

foreach ($productos as $index => $prod) {
    $categoria_id = $index + 1; // Asignar categoría secuencialmente
    $sql = "INSERT IGNORE INTO `productos` (`codigo_producto`, `nombre_producto`, `descripcion_producto`, `precio_producto`, `stock_producto`, `stock_minimo`, `fecha_vencimiento`, `codigo_barras`, `proveedor`, `id_categoria`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("sssdiiissi", $prod[0], $prod[1], $prod[2], $prod[3], $prod[4], $prod[5], $prod[6], $prod[7], $prod[8], $categoria_id);
    $stmt->execute();
}
echo "Productos de ejemplo creados correctamente.<br>";

echo "<br><strong style='color: green;'>¡Base de datos configurada exitosamente!</strong><br>";
echo "<br><strong>Credenciales de acceso:</strong><br>";
echo "Usuario: admin@benga.com<br>";
echo "Contraseña: Admin123!<br>";
echo "<br><a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al Login</a>";

$connection->close();
?>
