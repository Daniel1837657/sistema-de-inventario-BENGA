<?php
// Script para crear solo las tablas necesarias
require_once("config/db.php");

// Conectar a MySQL
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}

echo "<h2>Creando Base de Datos y Tablas BENGA</h2>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 5px;'>";

// Crear base de datos
$sql = "CREATE DATABASE IF NOT EXISTS simple_stock";
if ($connection->query($sql) === TRUE) {
    echo "✅ Base de datos 'simple_stock' creada correctamente<br>";
} else {
    echo "❌ Error creando base de datos: " . $connection->error . "<br>";
}

// Seleccionar base de datos
$connection->select_db("simple_stock");

// Crear tabla usuarios
$sql = "CREATE TABLE IF NOT EXISTS users (
    user_id int(11) NOT NULL AUTO_INCREMENT,
    user_name varchar(64) COLLATE utf8_spanish_ci NOT NULL,
    user_password_hash varchar(255) COLLATE utf8_spanish_ci NOT NULL,
    user_email varchar(64) COLLATE utf8_spanish_ci NOT NULL,
    date_added datetime NOT NULL,
    firstname varchar(50) COLLATE utf8_spanish_ci NOT NULL,
    lastname varchar(50) COLLATE utf8_spanish_ci NOT NULL,
    PRIMARY KEY (user_id),
    UNIQUE KEY user_name (user_name),
    UNIQUE KEY user_email (user_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci";

if ($connection->query($sql) === TRUE) {
    echo "✅ Tabla 'users' creada correctamente<br>";
} else {
    echo "❌ Error creando tabla users: " . $connection->error . "<br>";
}

// Crear tabla categorias
$sql = "CREATE TABLE IF NOT EXISTS categorias (
    id_categoria int(11) NOT NULL AUTO_INCREMENT,
    nombre_categoria varchar(255) COLLATE utf8_spanish_ci NOT NULL,
    descripcion_categoria text COLLATE utf8_spanish_ci,
    estado_categoria tinyint(4) NOT NULL DEFAULT '1',
    date_added datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci";

if ($connection->query($sql) === TRUE) {
    echo "✅ Tabla 'categorias' creada correctamente<br>";
} else {
    echo "❌ Error creando tabla categorias: " . $connection->error . "<br>";
}

// Crear tabla productos
$sql = "CREATE TABLE IF NOT EXISTS productos (
    id_producto int(11) NOT NULL AUTO_INCREMENT,
    codigo_producto varchar(20) COLLATE utf8_spanish_ci NOT NULL,
    nombre_producto varchar(255) COLLATE utf8_spanish_ci NOT NULL,
    descripcion text COLLATE utf8_spanish_ci,
    id_categoria int(11) NOT NULL,
    precio_producto decimal(11,2) NOT NULL,
    stock int(11) NOT NULL DEFAULT '0',
    date_added datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_producto),
    UNIQUE KEY codigo_producto (codigo_producto),
    KEY fk_categoria (id_categoria),
    CONSTRAINT fk_categoria FOREIGN KEY (id_categoria) REFERENCES categorias (id_categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci";

if ($connection->query($sql) === TRUE) {
    echo "✅ Tabla 'productos' creada correctamente<br>";
} else {
    echo "❌ Error creando tabla productos: " . $connection->error . "<br>";
}

// Crear tabla movimientos_stock
$sql = "CREATE TABLE IF NOT EXISTS movimientos_stock (
    id_movimiento int(11) NOT NULL AUTO_INCREMENT,
    id_producto int(11) NOT NULL,
    tipo_movimiento enum('entrada','salida') COLLATE utf8_spanish_ci NOT NULL,
    cantidad int(11) NOT NULL,
    motivo varchar(255) COLLATE utf8_spanish_ci NOT NULL,
    fecha_movimiento datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id int(11) NOT NULL,
    PRIMARY KEY (id_movimiento),
    KEY fk_producto_mov (id_producto),
    KEY fk_usuario_mov (usuario_id),
    CONSTRAINT fk_producto_mov FOREIGN KEY (id_producto) REFERENCES productos (id_producto),
    CONSTRAINT fk_usuario_mov FOREIGN KEY (usuario_id) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci";

if ($connection->query($sql) === TRUE) {
    echo "✅ Tabla 'movimientos_stock' creada correctamente<br>";
} else {
    echo "❌ Error creando tabla movimientos_stock: " . $connection->error . "<br>";
}

// Insertar usuario admin por defecto
$admin_email = 'admin@benga.com';
$admin_password = password_hash('Admin123!', PASSWORD_DEFAULT);

$sql = "INSERT IGNORE INTO users (user_name, user_password_hash, user_email, date_added, firstname, lastname) 
        VALUES ('admin', '$admin_password', '$admin_email', NOW(), 'Admin', 'BENGA')";

if ($connection->query($sql) === TRUE) {
    echo "✅ Usuario admin creado: admin@benga.com / Admin123!<br>";
} else {
    echo "❌ Error creando usuario admin: " . $connection->error . "<br>";
}

// Insertar categoría por defecto
$sql = "INSERT IGNORE INTO categorias (nombre_categoria, descripcion_categoria) 
        VALUES ('General', 'Categoría general para productos')";

if ($connection->query($sql) === TRUE) {
    echo "✅ Categoría 'General' creada<br>";
} else {
    echo "❌ Error creando categoría: " . $connection->error . "<br>";
}

// Insertar productos de ejemplo
$sql = "INSERT IGNORE INTO productos (codigo_producto, nombre_producto, descripcion, id_categoria, precio_producto, stock) VALUES
        ('PROD001', 'Producto Ejemplo 1', 'Descripción del producto 1', 1, 100.00, 50),
        ('PROD002', 'Producto Ejemplo 2', 'Descripción del producto 2', 1, 150.00, 30),
        ('PROD003', 'Producto Ejemplo 3', 'Descripción del producto 3', 1, 200.00, 25)";

if ($connection->query($sql) === TRUE) {
    echo "✅ Productos de ejemplo creados<br>";
} else {
    echo "❌ Error creando productos: " . $connection->error . "<br>";
}

echo "</div>";
echo "<br><h3>✅ ¡Base de datos configurada correctamente!</h3>";
echo "<p><a href='movimientos.php' class='btn btn-primary'>Ir a Movimientos</a></p>";

$connection->close();
?>
