<?php
include('is_logged.php'); // Verifica que el usuario está logueado

// Validación del lado del servidor
if (empty($_POST['codigo'])) {
    $errors[] = "El campo 'Código' está vacío.";
} elseif (empty($_POST['nombre'])) {
    $errors[] = "El campo 'Nombre del producto' está vacío.";
} elseif ($_POST['stock'] === "") {
    $errors[] = "El campo 'Stock' está vacío.";
} elseif (empty($_POST['precio'])) {
    $errors[] = "El campo 'Precio de venta' está vacío.";
} else {
    require_once("../config/db.php");
    require_once("../config/conexion.php");
    include("../funciones.php");

    // Sanitizar entradas
    $codigo        = mysqli_real_escape_string($con, strip_tags($_POST["codigo"], ENT_QUOTES));
    $nombre        = mysqli_real_escape_string($con, strip_tags($_POST["nombre"], ENT_QUOTES));
    $stock         = intval($_POST['stock']);
    $id_categoria  = intval($_POST['categoria']);
    $precio_venta  = floatval($_POST['precio']);
    $date_added    = date("Y-m-d H:i:s");

    // Verificar si el código ya existe (sentencia preparada)
    $stmt_check = $con->prepare("SELECT id_producto FROM products WHERE codigo_producto = ?");
    $stmt_check->bind_param("s", $codigo);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $errors[] = "Este código de producto ya fue registrado.";
    } else {
        // Insertar producto (sentencia preparada)
        $stmt_insert = $con->prepare("
            INSERT INTO products 
            (codigo_producto, nombre_producto, date_added, precio_producto, stock, id_categoria) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt_insert->bind_param("sssdis", $codigo, $nombre, $date_added, $precio_venta, $stock, $id_categoria);

        if ($stmt_insert->execute()) {
            $messages[] = "El producto ha sido ingresado satisfactoriamente.";
            
            // Guardar historial
            $id_producto = get_row($con, 'products', 'id_producto', 'codigo_producto', $codigo);
            $user_id     = $_SESSION['user_id'];
            $firstname   = $_SESSION['firstname'];
            $nota        = "$firstname agregó $stock producto(s) al inventario";
            guardar_historial($con, $id_producto, $user_id, $date_added, $nota, $codigo, $stock);
        } else {
            $errors[] = "Error al guardar el producto: " . htmlspecialchars($stmt_insert->error);
        }

        $stmt_insert->close();
    }
    $stmt_check->close();
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
