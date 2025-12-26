<?php
include('is_logged.php'); // Verifica sesión activa

// Revisa versión mínima PHP
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, no puedes usar este código en PHP menor a 5.3.7.");
} elseif (version_compare(PHP_VERSION, '5.5.0', '<')) {
    require_once("../libraries/password_compatibility_library.php");
}

// Inicializa arrays para errores y mensajes
$errors = [];
$messages = [];

// Validación básica
if (empty($_POST['firstname'])) {
    $errors[] = "Nombres vacíos";
} elseif (empty($_POST['lastname'])) {
    $errors[] = "Apellidos vacíos";
} elseif (empty($_POST['user_name'])) {
    $errors[] = "Nombre de usuario vacío";
} elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
    $errors[] = "Contraseña vacía";
} elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
    $errors[] = "La contraseña y la repetición no coinciden";
} elseif (strlen($_POST['user_password_new']) < 6) {
    $errors[] = "La contraseña debe tener mínimo 6 caracteres";
} elseif (strlen($_POST['user_name']) < 2 || strlen($_POST['user_name']) > 64) {
    $errors[] = "Nombre de usuario debe tener entre 2 y 64 caracteres";
} elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])) {
    $errors[] = "Nombre de usuario inválido: sólo letras y números permitidos";
} elseif (empty($_POST['user_email'])) {
    $errors[] = "Correo electrónico vacío";
} elseif (strlen($_POST['user_email']) > 64) {
    $errors[] = "Correo electrónico demasiado largo";
} elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Formato de correo electrónico inválido";
} else {
    require_once("../config/db.php");
    require_once("../config/conexion.php");

    // Limpieza segura
    $firstname = mysqli_real_escape_string($con, strip_tags($_POST["firstname"], ENT_QUOTES));
    $lastname  = mysqli_real_escape_string($con, strip_tags($_POST["lastname"], ENT_QUOTES));
    $user_name = mysqli_real_escape_string($con, strip_tags($_POST["user_name"], ENT_QUOTES));
    $user_email= mysqli_real_escape_string($con, strip_tags($_POST["user_email"], ENT_QUOTES));
    $user_password = $_POST['user_password_new'];
    $date_added = date("Y-m-d H:i:s");

    // Hashea la contraseña
    $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);

    // Validar si usuario o email ya existen con sentencia preparada
    $stmt = $con->prepare("SELECT user_id FROM users WHERE user_name = ? OR user_email = ? LIMIT 1");
    $stmt->bind_param("ss", $user_name, $user_email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "El nombre de usuario o correo electrónico ya está en uso.";
    } else {
        // Inserción segura
        $stmt_insert = $con->prepare("INSERT INTO users (firstname, lastname, user_name, user_password_hash, user_email, date_added) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("ssssss", $firstname, $lastname, $user_name, $user_password_hash, $user_email, $date_added);
        
        if ($stmt_insert->execute()) {
            $messages[] = "Cuenta creada con éxito.";
        } else {
            $errors[] = "Falló el registro, inténtalo de nuevo.";
        }
        $stmt_insert->close();
    }
    $stmt->close();
}

// Mostrar errores
if (!empty($errors)) {
    echo '<div class="alert alert-danger" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            <strong>Error!</strong> ' . implode("<br>", $errors) . '</div>';
}

// Mostrar mensajes
if (!empty($messages)) {
    echo '<div class="alert alert-success" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            <strong>¡Bien hecho!</strong> ' . implode("<br>", $messages) . '</div>';
}
?>
