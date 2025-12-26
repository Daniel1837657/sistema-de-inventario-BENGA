<?php
// Verificación mínima de versión PHP
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Simple PHP Login no funciona en versiones de PHP menores a 5.3.7");
} elseif (version_compare(PHP_VERSION, '5.5.0', '<')) {
    require_once("libraries/password_compatibility_library.php");
}

// Carga configuración de base de datos y clase de login
require_once("config/db.php");
require_once("classes/Login.php");

$login = new Login();

// Procesar logout si es una petición POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $login->doLogout();
    header("Location: login.php");
    exit;
}

// Si no es POST, mostrar confirmación
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BENGA | Cerrar Sesión</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card card-container text-center">
            <i class="bi bi-box-arrow-right display-1 text-primary mb-3"></i>
            <h4 class="mb-4">¿Cerrar Sesión?</h4>
            <p class="text-muted mb-4">¿Estás seguro de que deseas cerrar tu sesión?</p>
            
            <form method="post" action="logout.php" class="d-inline">
                <button type="submit" name="logout" class="btn btn-danger me-2">
                    <i class="bi bi-box-arrow-right me-1"></i>Sí, Cerrar Sesión
                </button>
            </form>
            
            <a href="stock.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Cancelar
            </a>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
