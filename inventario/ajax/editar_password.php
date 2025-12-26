<?php
include('is_logged.php'); // Verifica que el usuario esté logueado

// Comprobación de versión PHP
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
	exit("Este sistema no funciona en PHP menor a 5.3.7");
} elseif (version_compare(PHP_VERSION, '5.5.0', '<')) {
	// Compatibilidad con password_hash en PHP < 5.5
	require_once("../libraries/password_compatibility_library.php");
}

// Incluir validador de contraseñas
require_once("../classes/PasswordValidator.php");

// Validaciones
if (empty($_POST['user_id_mod'])) {
	$errors[] = "ID vacío";
} elseif (empty($_POST['user_password_new3']) || empty($_POST['user_password_repeat3'])) {
	$errors[] = "Contraseña vacía";
} elseif ($_POST['user_password_new3'] !== $_POST['user_password_repeat3']) {
	$errors[] = "La contraseña y su repetición no coinciden";
} else {
	// Validar fortaleza de la contraseña
	$password_errors = PasswordValidator::validate($_POST['user_password_new3']);
	if (!empty($password_errors)) {
		$errors = array_merge($errors ?? [], $password_errors);
	} else {
		require_once("../config/db.php");
		require_once("../config/conexion.php");

		$user_id = intval($_POST['user_id_mod']);
		$user_password = $_POST['user_password_new3'];

		// Encriptar contraseña
		$user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);

		// Consulta preparada
		$stmt = $con->prepare("UPDATE users SET user_password_hash = ? WHERE user_id = ?");
		$stmt->bind_param("si", $user_password_hash, $user_id);

		if ($stmt->execute()) {
			$messages[] = "Contraseña modificada con éxito.";
		} else {
			$errors[] = "Error al modificar la contraseña: " . $stmt->error;
		}

		$stmt->close();
	}
}

// Mostrar errores
if (!empty($errors)) {
	echo '<div class="alert alert-danger" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>Error!</strong> ' . implode("<br>", $errors) . '
        </div>';
}

// Mostrar mensajes de éxito
if (!empty($messages)) {
	echo '<div class="alert alert-success" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>¡Bien hecho!</strong> ' . implode("<br>", $messages) . '
        </div>';
}
