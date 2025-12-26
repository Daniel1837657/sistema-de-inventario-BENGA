<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    exit(json_encode(['error' => 'No autorizado']));
}

require_once("../config/db.php");

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($connection->connect_error) {
    exit(json_encode(['error' => 'Error de conexión']));
}

$connection->set_charset("utf8");

// Métricas principales
$total_entradas = 0;
$total_salidas = 0;
$movimientos_hoy = 0;
$movimientos_semana = 0;

// Verificar si existe la tabla movimientos_stock, si no usar historial
$table_exists = false;
$check_table = $connection->query("SHOW TABLES LIKE 'movimientos_stock'");
if ($check_table && $check_table->num_rows > 0) {
    $table_exists = true;
}

if ($table_exists) {
    // Total entradas
    $query = "SELECT COALESCE(SUM(cantidad), 0) as total FROM movimientos_stock WHERE tipo_movimiento = 'entrada'";
    $result = $connection->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $total_entradas = (int)$row['total'];
    }

    // Total salidas
    $query = "SELECT COALESCE(SUM(cantidad), 0) as total FROM movimientos_stock WHERE tipo_movimiento = 'salida'";
    $result = $connection->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $total_salidas = (int)$row['total'];
    }

    // Movimientos hoy
    $query = "SELECT COUNT(*) as total FROM movimientos_stock WHERE DATE(fecha_movimiento) = CURDATE()";
    $result = $connection->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $movimientos_hoy = (int)$row['total'];
    }

    // Movimientos esta semana
    $query = "SELECT COUNT(*) as total FROM movimientos_stock WHERE YEARWEEK(fecha_movimiento, 1) = YEARWEEK(CURDATE(), 1)";
    $result = $connection->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $movimientos_semana = (int)$row['total'];
    }
} else {
    // Usar tabla historial como alternativa
    $query = "SELECT COALESCE(SUM(cantidad), 0) as total FROM historial WHERE nota LIKE '%agregó%'";
    $result = $connection->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $total_entradas = (int)$row['total'];
    }

    $query = "SELECT COALESCE(SUM(cantidad), 0) as total FROM historial WHERE nota LIKE '%eliminó%'";
    $result = $connection->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $total_salidas = (int)$row['total'];
    }

    // Movimientos hoy
    $query = "SELECT COUNT(*) as total FROM historial WHERE DATE(fecha) = CURDATE()";
    $result = $connection->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $movimientos_hoy = (int)$row['total'];
    }

    // Movimientos esta semana
    $query = "SELECT COUNT(*) as total FROM historial WHERE YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)";
    $result = $connection->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $movimientos_semana = (int)$row['total'];
    }
}

// Movimientos por día (últimos 7 días)
$movimientos_por_dia = [
    'labels' => [],
    'entradas' => [],
    'salidas' => []
];

for ($i = 6; $i >= 0; $i--) {
    $fecha = date('Y-m-d', strtotime("-$i days"));
    $fecha_label = date('d/m', strtotime("-$i days"));
    
    $movimientos_por_dia['labels'][] = $fecha_label;
    
    if ($table_exists) {
        // Entradas del día
        $query = "SELECT COALESCE(COUNT(*), 0) as total FROM movimientos_stock 
                  WHERE DATE(fecha_movimiento) = '$fecha' AND tipo_movimiento = 'entrada'";
        $result = $connection->query($query);
        $entradas = $result ? (int)$result->fetch_assoc()['total'] : 0;
        $movimientos_por_dia['entradas'][] = $entradas;
        
        // Salidas del día
        $query = "SELECT COALESCE(COUNT(*), 0) as total FROM movimientos_stock 
                  WHERE DATE(fecha_movimiento) = '$fecha' AND tipo_movimiento = 'salida'";
        $result = $connection->query($query);
        $salidas = $result ? (int)$result->fetch_assoc()['total'] : 0;
        $movimientos_por_dia['salidas'][] = $salidas;
    } else {
        // Usar historial
        $query = "SELECT COALESCE(COUNT(*), 0) as total FROM historial 
                  WHERE DATE(fecha) = '$fecha' AND nota LIKE '%agregó%'";
        $result = $connection->query($query);
        $entradas = $result ? (int)$result->fetch_assoc()['total'] : 0;
        $movimientos_por_dia['entradas'][] = $entradas;
        
        $query = "SELECT COALESCE(COUNT(*), 0) as total FROM historial 
                  WHERE DATE(fecha) = '$fecha' AND nota LIKE '%eliminó%'";
        $result = $connection->query($query);
        $salidas = $result ? (int)$result->fetch_assoc()['total'] : 0;
        $movimientos_por_dia['salidas'][] = $salidas;
    }
}

// Tipos de movimiento (para gráfico circular)
if ($table_exists) {
    $query_entradas = "SELECT COUNT(*) as total FROM movimientos_stock WHERE tipo_movimiento = 'entrada'";
    $query_salidas = "SELECT COUNT(*) as total FROM movimientos_stock WHERE tipo_movimiento = 'salida'";
    
    $result_entradas = $connection->query($query_entradas);
    $result_salidas = $connection->query($query_salidas);
    
    $tipos_movimiento = [
        'entradas' => $result_entradas ? (int)$result_entradas->fetch_assoc()['total'] : 0,
        'salidas' => $result_salidas ? (int)$result_salidas->fetch_assoc()['total'] : 0
    ];
} else {
    $query_entradas = "SELECT COUNT(*) as total FROM historial WHERE nota LIKE '%agregó%'";
    $query_salidas = "SELECT COUNT(*) as total FROM historial WHERE nota LIKE '%eliminó%'";
    
    $result_entradas = $connection->query($query_entradas);
    $result_salidas = $connection->query($query_salidas);
    
    $tipos_movimiento = [
        'entradas' => $result_entradas ? (int)$result_entradas->fetch_assoc()['total'] : 0,
        'salidas' => $result_salidas ? (int)$result_salidas->fetch_assoc()['total'] : 0
    ];
}

// Top productos con más movimientos (últimos 30 días)
$top_productos = [];
if ($table_exists) {
    $query = "SELECT p.codigo_producto, p.nombre_producto, COUNT(m.id_movimiento) as total_movimientos
              FROM productos p
              INNER JOIN movimientos_stock m ON p.id_producto = m.id_producto
              WHERE m.fecha_movimiento >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
              GROUP BY p.id_producto, p.codigo_producto, p.nombre_producto
              ORDER BY total_movimientos DESC
              LIMIT 3";
} else {
    $query = "SELECT p.codigo_producto, p.nombre_producto, COUNT(h.id) as total_movimientos
              FROM products p
              INNER JOIN historial h ON p.id_producto = h.id_producto
              WHERE h.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
              GROUP BY p.id_producto, p.codigo_producto, p.nombre_producto
              ORDER BY total_movimientos DESC
              LIMIT 3";
}

$result = $connection->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $top_productos[] = [
            'codigo_producto' => $row['codigo_producto'],
            'nombre_producto' => $row['nombre_producto'],
            'total_movimientos' => (int)$row['total_movimientos']
        ];
    }
}

// Si no hay productos con movimientos, mostrar productos por defecto
if (empty($top_productos)) {
    $query = "SELECT codigo_producto, nombre_producto FROM products ORDER BY nombre_producto LIMIT 3";
    $result = $connection->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $top_productos[] = [
                'codigo_producto' => $row['codigo_producto'],
                'nombre_producto' => $row['nombre_producto'],
                'total_movimientos' => 0
            ];
        }
    }
}

$dashboard_data = [
    'total_entradas' => $total_entradas,
    'total_salidas' => $total_salidas,
    'movimientos_hoy' => $movimientos_hoy,
    'movimientos_semana' => $movimientos_semana,
    'movimientos_por_dia' => $movimientos_por_dia,
    'tipos_movimiento' => $tipos_movimiento,
    'top_productos' => $top_productos
];

$connection->close();

header('Content-Type: application/json');
echo json_encode($dashboard_data);
?>
