<?php
// Script para crear el usuario administrador por defecto
require_once("config/db.php");

// Verificación mínima de versión PHP
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Este sistema no funciona en versiones de PHP menores a 5.3.7");
} elseif (version_compare(PHP_VERSION, '5.5.0', '<')) {
    require_once("libraries/password_compatibility_library.php");
}

// Conectar a la base de datos
$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}

$con->set_charset("utf8");

// Datos del administrador por defecto
$admin_email = "admin@benga.com";
$admin_username = "admin";
$admin_firstname = "Administrador";
$admin_password = "Admin123!"; // Cumple con todos los requisitos de seguridad

// Verificar si ya existe un usuario administrador
$stmt = $con->prepare("SELECT user_id FROM users WHERE user_email = ? OR user_name = ? LIMIT 1");
$stmt->bind_param("ss", $admin_email, $admin_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    echo "El usuario administrador ya existe.\n";
    echo "Email: $admin_email\n";
    echo "Usuario: $admin_username\n";
    echo "Contraseña: $admin_password\n";
} else {
    // Crear hash de la contraseña
    $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);
    
    // Insertar usuario administrador
    $stmt = $con->prepare("INSERT INTO users (user_name, user_email, firstname, user_password_hash, date_added) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $admin_username, $admin_email, $admin_firstname, $password_hash);
    
    if ($stmt->execute()) {
        echo "Usuario administrador creado exitosamente.\n";
        echo "Email: $admin_email\n";
        echo "Usuario: $admin_username\n";
        echo "Contraseña: $admin_password\n";
        echo "\n¡IMPORTANTE! Cambia esta contraseña después del primer inicio de sesión.\n";
    } else {
        echo "Error al crear el usuario administrador: " . $stmt->error . "\n";
    }
}

$stmt->close();
$con->close();
?>
