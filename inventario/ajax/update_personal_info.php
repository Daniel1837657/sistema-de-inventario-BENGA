<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    exit(json_encode(['success' => false, 'message' => 'No autorizado']));
}

require_once("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(['success' => false, 'message' => 'Método no permitido']));
}

$user_id = $_SESSION['user_id'];
$firstname = trim($_POST['firstname']);
$lastname = trim($_POST['lastname']);
$user_name = trim($_POST['user_name']);
$user_email = trim($_POST['user_email']);
$phone = trim($_POST['phone']);
$position = trim($_POST['position']);

// Validaciones
if (empty($firstname) || empty($lastname) || empty($user_name) || empty($user_email)) {
    exit(json_encode(['success' => false, 'message' => 'Los campos nombre, apellido, usuario y email son obligatorios']));
}

if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
    exit(json_encode(['success' => false, 'message' => 'Email no válido']));
}

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($connection->connect_error) {
    exit(json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']));
}

// Verificar si las columnas existen, si no, agregarlas
$check_columns = $connection->query("SHOW COLUMNS FROM users LIKE 'phone'");
if ($check_columns->num_rows == 0) {
    $connection->query("ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL");
}

$check_columns = $connection->query("SHOW COLUMNS FROM users LIKE 'position'");
if ($check_columns->num_rows == 0) {
    $connection->query("ALTER TABLE users ADD COLUMN position VARCHAR(100) DEFAULT NULL");
}

// Verificar si el usuario o email ya existen (excluyendo el usuario actual)
$query = "SELECT user_id FROM users WHERE (user_name = ? OR user_email = ?) AND user_id != ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("ssi", $user_name, $user_email, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    exit(json_encode(['success' => false, 'message' => 'El usuario o email ya están en uso']));
}

// Actualizar información
$query = "UPDATE users SET firstname = ?, lastname = ?, user_name = ?, user_email = ?, phone = ?, position = ? WHERE user_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("ssssssi", $firstname, $lastname, $user_name, $user_email, $phone, $position, $user_id);

if ($stmt->execute()) {
    // Actualizar variables de sesión
    $_SESSION['firstname'] = $firstname;
    $_SESSION['user_name'] = $user_name;
    $_SESSION['user_email'] = $user_email;
    
    echo json_encode(['success' => true, 'message' => 'Información personal actualizada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la información: ' . $connection->error]);
}

$connection->close();
?>
