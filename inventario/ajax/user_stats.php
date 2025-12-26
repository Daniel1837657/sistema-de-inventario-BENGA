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

$user_id = $_SESSION['user_id'];

// Total productos en el sistema
$total_productos = 0;
$query = "SELECT COUNT(*) as total FROM productos";
$result = $connection->query($query);
if ($result && $row = $result->fetch_assoc()) {
    $total_productos = (int)$row['total'];
}

// Total movimientos registrados por el usuario
$total_movimientos = 0;
$query = "SELECT COUNT(*) as total FROM movimientos_stock WHERE usuario_id = ?";
$stmt = $connection->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        $total_movimientos = (int)$row['total'];
    }
}

// Días activo (desde registro)
$dias_activo = 0;
$query = "SELECT DATEDIFF(NOW(), date_added) as dias FROM users WHERE user_id = ?";
$stmt = $connection->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        $dias_activo = max(1, (int)$row['dias']); // Mínimo 1 día
    }
}

$stats = [
    'total_productos' => $total_productos,
    'total_movimientos' => $total_movimientos,
    'dias_activo' => $dias_activo
];

$connection->close();

header('Content-Type: application/json');
echo json_encode($stats);
?>
