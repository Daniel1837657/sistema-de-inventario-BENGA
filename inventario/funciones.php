<?php
function get_row($con, string $table, string $row, string $id, $equal) {
    // Preparar query para evitar inyección SQL
    $stmt = $con->prepare("SELECT $row FROM $table WHERE $id = ? LIMIT 1");
    if (!$stmt) return null; // falla la preparación
    
    $stmt->bind_param('s', $equal);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $rowData = $result->fetch_assoc()) {
        return $rowData[$row] ?? null;
    }
    return null;
}

function guardar_historial($con, int $id_producto, int $user_id, string $fecha, string $nota, string $reference, int $quantity): bool {
    $sql = "INSERT INTO historial (id_producto, user_id, fecha, nota, referencia, cantidad) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    if (!$stmt) return false;

    $stmt->bind_param("iisssi", $id_producto, $user_id, $fecha, $nota, $reference, $quantity);
    return $stmt->execute();
}

function agregar_stock($con, int $id_producto, int $quantity): bool {
    $sql = "UPDATE products SET stock = stock + ? WHERE id_producto = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) return false;

    $stmt->bind_param("ii", $quantity, $id_producto);
    return $stmt->execute();
}

function eliminar_stock($con, int $id_producto, int $quantity): bool {
    $sql = "UPDATE products SET stock = stock - ? WHERE id_producto = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) return false;

    $stmt->bind_param("ii", $quantity, $id_producto);
    return $stmt->execute();
}
?>
