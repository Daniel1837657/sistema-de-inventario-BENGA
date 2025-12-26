<?php
include('is_logged.php'); // Verifica que el usuario esté logueado

// Validación de entrada
if (empty($_POST['mod_id'])) {
	$errors[] = "ID vacío";
} elseif (empty($_POST['mod_nombre'])) {
	$errors[] = "Nombre vacío";
} else {
	require_once("../config/db.php"); // Variables de conexión
	require_once("../config/conexion.php"); // Conexión a la base de datos

	// Limpieza de datos
	$id_categoria = intval($_POST['mod_id']);
	$nombre = mysqli_real_escape_string($con, strip_tags($_POST["mod_nombre"], ENT_QUOTES));
	$descripcion = mysqli_real_escape_string($con, strip_tags($_POST["mod_descripcion"], ENT_QUOTES));

	// Uso de consulta preparada para mayor seguridad
	$stmt = $con->prepare("UPDATE categorias SET nombre_categoria = ?, descripcion_categoria = ? WHERE id_categoria = ?");
	$stmt->bind_param("ssi", $nombre, $descripcion, $id_categoria);

	if ($stmt->execute()) {
		$messages[] = "Categoría actualizada satisfactoriamente.";
	} else {
		$errors[] = "Error al actualizar: " . $stmt->error;
	}

	$stmt->close();
}

// Mensajes de error
if (!empty($errors)) {
	echo '<div class="alert alert-danger" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>Error!</strong> ' . implode("<br>", $errors) . '
        </div>';
}

// Mensajes de éxito
if (!empty($messages)) {
	echo '<div class="alert alert-success" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>¡Bien hecho!</strong> ' . implode("<br>", $messages) . '
        </div>';
}
