<?php
include('is_logged.php'); // Verifica que el usuario está logueado

// Validación del lado del servidor
if (empty($_POST['nombre'])) {
    $errors[] = "El campo 'Nombre' está vacío.";
} else {
    // Conexión a la base de datos
    require_once("../config/db.php");
    require_once("../config/conexion.php");

    // Escapar y sanitizar entradas
    $nombre      = mysqli_real_escape_string($con, strip_tags($_POST["nombre"], ENT_QUOTES));
    $descripcion = mysqli_real_escape_string($con, strip_tags($_POST["descripcion"] ?? '', ENT_QUOTES));
    $date_added  = date("Y-m-d H:i:s");

    // Uso de sentencia preparada para mayor seguridad
    $stmt = $con->prepare("INSERT INTO categorias (nombre_categoria, descripcion_categoria, date_added) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $descripcion, $date_added);

    if ($stmt->execute()) {
        $messages[] = "La categoría ha sido ingresada satisfactoriamente.";
    } else {
        $errors[] = "Error al guardar: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}

// Mostrar errores
if (!empty($errors)) {
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Error:</strong>
        <?php echo implode("<br>", $errors); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
}

// Mostrar mensajes
if (!empty($messages)) {
    ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <strong>¡Bien hecho!</strong>
        <?php echo implode("<br>", $messages); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
}
?>
