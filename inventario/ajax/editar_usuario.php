<?php
include('is_logged.php'); // Verifica que el usuario que intenta acceder está logueado

// Verificación de versión mínima de PHP
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Lo sentimos, este sistema no funciona con una versión de PHP menor a 5.3.7.");
} elseif (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // Compatibilidad con versiones antiguas (añade funciones de hash modernas)
    require_once("../libraries/password_compatibility_library.php");
}

// Inicializa arrays para errores y mensajes
$errors   = [];
$messages = [];

// Validaciones de entrada
if (empty($_POST['firstname2'])) {
    $errors[] = "Nombres vacíos";
} elseif (empty($_POST['lastname2'])) {
    $errors[] = "Apellidos vacíos";
} elseif (empty($_POST['user_name2'])) {
    $errors[] = "Nombre de usuario vacío";
} elseif (strlen($_POST['user_name2']) > 64 || strlen($_POST['user_name2']) < 2) {
    $errors[] = "El nombre de usuario debe tener entre 2 y 64 caracteres";
} elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name2'])) {
    $errors[] = "El nombre de usuario solo puede contener letras y números (2 a 64 caracteres)";
} elseif (empty($_POST['user_email2'])) {
    $errors[] = "El correo electrónico no puede estar vacío";
} elseif (strlen($_POST['user_email2']) > 64) {
    $errors[] = "El correo electrónico no puede exceder 64 caracteres";
} elseif (!filter_var($_POST['user_email2'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Formato de correo electrónico inválido";
} else {
    // Conexión a base de datos
    require_once("../config/db.php");
    require_once("../config/conexion.php");

    // Escapar entradas
    $firstname  = mysqli_real_escape_string($con, strip_tags($_POST["firstname2"], ENT_QUOTES));
    $lastname   = mysqli_real_escape_string($con, strip_tags($_POST["lastname2"], ENT_QUOTES));
    $user_name  = mysqli_real_escape_string($con, strip_tags($_POST["user_name2"], ENT_QUOTES));
    $user_email = mysqli_real_escape_string($con, strip_tags($_POST["user_email2"], ENT_QUOTES));
    $user_id    = intval($_POST['mod_id']);

    // Actualización con prepared statement (más seguro)
    $stmt = $con->prepare("UPDATE users SET firstname=?, lastname=?, user_name=?, user_email=? WHERE user_id=?");
    $stmt->bind_param("ssssi", $firstname, $lastname, $user_name, $user_email, $user_id);

    if ($stmt->execute()) {
        $messages[] = "La cuenta ha sido modificada con éxito.";
    } else {
        $errors[] = "Error al modificar la cuenta: " . $stmt->error;
    }
    $stmt->close();
}

// Mostrar mensajes
if (!empty($errors)) {
    echo '<div class="alert alert-danger" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            <strong>Error!</strong> ' . implode("<br>", $errors) . '
        </div>';
}

if (!empty($messages)) {
    echo '<div class="alert alert-success" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            <strong>¡Bien hecho!</strong> ' . implode("<br>", $messages) . '
        </div>';
}
?>
