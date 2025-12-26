<?php
// Script directo para crear tabla productos
require_once("config/db.php");

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}

echo "<h2>Verificando y Creando Tabla Productos</h2>";

// Verificar si existe la base de datos
$db_check = "CREATE DATABASE IF NOT EXISTS simple_stock";
$connection->query($db_check);
$connection->select_db("simple_stock");

// Crear tabla productos directamente
$sql = "CREATE TABLE IF NOT EXISTS productos (
    id_producto int(11) NOT NULL AUTO_INCREMENT,
    codigo_producto varchar(20) NOT NULL,
    nombre_producto varchar(255) NOT NULL,
    descripcion text,
    precio_producto decimal(11,2) NOT NULL DEFAULT 0.00,
    stock int(11) NOT NULL DEFAULT 0,
    date_added datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_producto),
    UNIQUE KEY codigo_producto (codigo_producto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

if ($connection->query($sql) === TRUE) {
    echo "✅ Tabla 'productos' creada exitosamente<br>";
    
    // Insertar productos de prueba
    $insert = "INSERT IGNORE INTO productos (codigo_producto, nombre_producto, descripcion, precio_producto, stock) VALUES
               ('PROD001', 'Producto Test 1', 'Producto de prueba 1', 100.00, 50),
               ('PROD002', 'Producto Test 2', 'Producto de prueba 2', 150.00, 30),
               ('PROD003', 'Producto Test 3', 'Producto de prueba 3', 200.00, 25)";
    
    if ($connection->query($insert) === TRUE) {
        echo "✅ Productos de prueba insertados<br>";
    }
    
} else {
    echo "❌ Error: " . $connection->error . "<br>";
}

// Verificar que la tabla existe
$check = $connection->query("SHOW TABLES LIKE 'productos'");
if ($check->num_rows > 0) {
    echo "✅ Tabla 'productos' confirmada en base de datos<br>";
    
    $count = $connection->query("SELECT COUNT(*) as total FROM productos");
    $row = $count->fetch_assoc();
    echo "✅ Productos en tabla: " . $row['total'] . "<br>";
    
} else {
    echo "❌ Tabla 'productos' NO encontrada<br>";
}

echo "<br><a href='movimientos.php'>🔄 Probar Movimientos</a>";

$connection->close();
?>
