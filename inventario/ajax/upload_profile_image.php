<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    exit(json_encode(['success' => false, 'message' => 'No autorizado']));
}

require_once("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['profileImage'])) {
    exit(json_encode(['success' => false, 'message' => 'No se recibió ninguna imagen']));
}

$user_id = $_SESSION['user_id'];
$file = $_FILES['profileImage'];

// Validar archivo
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 2 * 1024 * 1024; // 2MB

if (!in_array($file['type'], $allowed_types)) {
    exit(json_encode(['success' => false, 'message' => 'Formato de imagen no válido. Use JPG, PNG o GIF']));
}

if ($file['size'] > $max_size) {
    exit(json_encode(['success' => false, 'message' => 'La imagen es muy grande. Máximo 2MB']));
}

// Crear directorio si no existe
$upload_dir = '../uploads/profiles/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generar nombre único
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
$filepath = $upload_dir . $filename;

// Mover archivo
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    exit(json_encode(['success' => false, 'message' => 'Error al subir la imagen']));
}

// Actualizar base de datos
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($connection->connect_error) {
    exit(json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']));
}

// Primero verificar si la columna existe, si no, agregarla
$check_column = $connection->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
if ($check_column->num_rows == 0) {
    $connection->query("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL");
}

// Eliminar imagen anterior si existe
$query = "SELECT profile_image FROM users WHERE user_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$old_data = $result->fetch_assoc();

if ($old_data && !empty($old_data['profile_image'])) {
    $old_file = $upload_dir . $old_data['profile_image'];
    if (file_exists($old_file)) {
        unlink($old_file);
    }
}

// Actualizar con nueva imagen
$query = "UPDATE users SET profile_image = ? WHERE user_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("si", $filename, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Imagen actualizada correctamente',
        'filename' => $filename,
        'image_url' => 'uploads/profiles/' . $filename
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la base de datos: ' . $connection->error]);
}

$connection->close();
?>
