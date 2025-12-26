<?php
// Script para crear tabla movimientos_stock
require_once("config/db.php");

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}

echo "<h2>Creando Tabla Movimientos Stock</h2>";

// Crear tabla movimientos_stock
$sql = "CREATE TABLE IF NOT EXISTS movimientos_stock (
    id_movimiento int(11) NOT NULL AUTO_INCREMENT,
    id_producto int(11) NOT NULL,
    tipo_movimiento enum('entrada','salida') NOT NULL,
    cantidad int(11) NOT NULL,
    motivo varchar(255) NOT NULL,
    fecha_movimiento datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id int(11) NOT NULL,
    PRIMARY KEY (id_movimiento),
    KEY fk_producto_mov (id_producto),
    KEY fk_usuario_mov (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

if ($connection->query($sql) === TRUE) {
    echo "✅ Tabla 'movimientos_stock' creada exitosamente<br>";
    
    // Insertar movimientos de ejemplo
    $insert = "INSERT IGNORE INTO movimientos_stock (id_producto, tipo_movimiento, cantidad, motivo, usuario_id) VALUES
               (1, 'entrada', 20, 'Compra inicial', 1),
               (2, 'entrada', 15, 'Reposición stock', 1),
               (1, 'salida', 5, 'Venta cliente', 1),
               (3, 'entrada', 10, 'Inventario inicial', 1),
               (2, 'salida', 3, 'Venta mostrador', 1)";
    
    if ($connection->query($insert) === TRUE) {
        echo "✅ Movimientos de ejemplo insertados<br>";
    }
    
} else {
    echo "❌ Error: " . $connection->error . "<br>";
}

// Verificar que la tabla existe
$check = $connection->query("SHOW TABLES LIKE 'movimientos_stock'");
if ($check->num_rows > 0) {
    echo "✅ Tabla 'movimientos_stock' confirmada<br>";
    
    $count = $connection->query("SELECT COUNT(*) as total FROM movimientos_stock");
    $row = $count->fetch_assoc();
    echo "✅ Movimientos en tabla: " . $row['total'] . "<br>";
    
} else {
    echo "❌ Tabla 'movimientos_stock' NO encontrada<br>";
}

echo "<br><a href='movimientos.php'>🔄 Probar Movimientos</a>";

$connection->close();
?>
