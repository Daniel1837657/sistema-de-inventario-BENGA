<?php
include('is_logged.php');
require_once("../config/db.php");
require_once("../config/conexion.php");

$errors = [];
$messages = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validaciones
    if (empty($_POST['producto_mov'])) {
        $errors[] = "Debe seleccionar un producto";
    } elseif (empty($_POST['tipo_mov'])) {
        $errors[] = "Debe seleccionar el tipo de movimiento";
    } elseif (empty($_POST['cantidad_mov']) || $_POST['cantidad_mov'] <= 0) {
        $errors[] = "La cantidad debe ser mayor a 0";
    } elseif (empty($_POST['motivo_mov'])) {
        $errors[] = "Debe especificar el motivo";
    } else {
        $id_producto = intval($_POST['producto_mov']);
        $tipo_movimiento = $_POST['tipo_mov'];
        $cantidad = intval($_POST['cantidad_mov']);
        $motivo = mysqli_real_escape_string($con, $_POST['motivo_mov']);
        $observaciones = isset($_POST['observaciones_mov']) ? mysqli_real_escape_string($con, $_POST['observaciones_mov']) : '';
        $usuario_id = $_SESSION['user_id'];
        
        // Verificar stock actual si es salida
        if ($tipo_movimiento == 'salida') {
            $sql_stock = "SELECT stock FROM productos WHERE id_producto = ?";
            $stmt_stock = $con->prepare($sql_stock);
            $stmt_stock->bind_param("i", $id_producto);
            $stmt_stock->execute();
            $result_stock = $stmt_stock->get_result();
            $producto = $result_stock->fetch_assoc();
            
            if ($producto['stock'] < $cantidad) {
                $errors[] = "Stock insuficiente. Stock actual: " . $producto['stock'];
            }
            $stmt_stock->close();
        }
        
        if (empty($errors)) {
            // Iniciar transacción
            $con->begin_transaction();
            
            try {
                // Registrar movimiento
                $sql_mov = "INSERT INTO movimientos_stock (id_producto, tipo_movimiento, cantidad, motivo, observaciones, fecha_movimiento, usuario_id) 
                           VALUES (?, ?, ?, ?, ?, NOW(), ?)";
                $stmt_mov = $con->prepare($sql_mov);
                $stmt_mov->bind_param("isissi", $id_producto, $tipo_movimiento, $cantidad, $motivo, $observaciones, $usuario_id);
                $stmt_mov->execute();
                
                // Actualizar stock
                if ($tipo_movimiento == 'entrada') {
                    $sql_update = "UPDATE productos SET stock = stock + ? WHERE id_producto = ?";
                } else {
                    $sql_update = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
                }
                
                $stmt_update = $con->prepare($sql_update);
                $stmt_update->bind_param("ii", $cantidad, $id_producto);
                $stmt_update->execute();
                
                $con->commit();
                $messages[] = "Movimiento registrado correctamente";
                
                $stmt_mov->close();
                $stmt_update->close();
                
            } catch (Exception $e) {
                $con->rollback();
                $errors[] = "Error al registrar el movimiento: " . $e->getMessage();
            }
        }
    }
}

// Mostrar mensajes
if (!empty($errors)) {
    echo '<div class="alert alert-danger" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            <strong>Error!</strong> ' . implode("<br>", $errors) . '</div>';
}

if (!empty($messages)) {
    echo '<div class="alert alert-success" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            <strong>¡Éxito!</strong> ' . implode("<br>", $messages) . '</div>';
}
?>
