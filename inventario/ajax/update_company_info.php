<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    exit(json_encode(['success' => false, 'message' => 'No autorizado']));
}

require_once("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(['success' => false, 'message' => 'Método no permitido']));
}

$company_name = trim($_POST['company_name']);
$nit = trim($_POST['nit']);
$address = trim($_POST['address']);
$phone = trim($_POST['phone']);
$email = trim($_POST['email']);
$website = trim($_POST['website']);
$industry = trim($_POST['industry']);
$description = trim($_POST['description']);

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($connection->connect_error) {
    exit(json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']));
}

// Crear tabla company_info si no existe
$create_table = "CREATE TABLE IF NOT EXISTS `company_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(200) DEFAULT NULL,
  `nit` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `industry` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$connection->query($create_table);

// Verificar si ya existe un registro
$check_query = "SELECT id FROM company_info LIMIT 1";
$result = $connection->query($check_query);

if ($result && $result->num_rows > 0) {
    // Actualizar registro existente
    $row = $result->fetch_assoc();
    $company_id = $row['id'];
    
    $query = "UPDATE company_info SET company_name = ?, nit = ?, address = ?, phone = ?, email = ?, website = ?, industry = ?, description = ? WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ssssssssi", $company_name, $nit, $address, $phone, $email, $website, $industry, $description, $company_id);
} else {
    // Crear nuevo registro
    $query = "INSERT INTO company_info (company_name, nit, address, phone, email, website, industry, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ssssssss", $company_name, $nit, $address, $phone, $email, $website, $industry, $description);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Información de la empresa actualizada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la información de la empresa: ' . $connection->error]);
}

$connection->close();
?>
