<?php
include('is_logged.php'); // Verifica que el usuario esté logueado

// Validaciones del lado del servidor
if (empty($_POST['mod_id'])) {
	$errors[] = "ID vacío";
} elseif (empty($_POST['mod_codigo'])) {
	$errors[] = "Código vacío";
} elseif (empty($_POST['mod_nombre'])) {
	$errors[] = "Nombre del producto vacío";
} elseif ($_POST['mod_categoria'] === "") {
	$errors[] = "Selecciona la categoría del producto";
} elseif (empty($_POST['mod_precio'])) {
	$errors[] = "Precio de venta vacío";
} else {
	require_once("../config/db.php");
	require_once("../config/conexion.php");

	// Sanitización y asignación de variables
	$codigo     = trim($_POST["mod_codigo"]);
	$nombre     = trim($_POST["mod_nombre"]);
	$categoria  = intval($_POST['mod_categoria']);
	$stock      = intval($_POST['mod_stock']);
	$precio     = floatval($_POST['mod_precio']);
	$id_producto = intval($_POST['mod_id']);

	// Consulta preparada
	$sql = "UPDATE products 
            SET codigo_producto = ?, nombre_producto = ?, id_categoria = ?, precio_producto = ?, stock = ?
            WHERE id_producto = ?";
	$stmt = $con->prepare($sql);
	$stmt->bind_param("ssiddi", $codigo, $nombre, $categoria, $precio, $stock, $id_producto);

	if ($stmt->execute()) {
		$messages[] = "Producto actualizado satisfactoriamente.";
	} else {
		$errors[] = "Error al actualizar el producto: " . $stmt->error;
	}

	$stmt->close();
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
