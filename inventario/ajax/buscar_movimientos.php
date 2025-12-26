<?php
include('is_logged.php');
require_once("../config/db.php");
require_once("../config/conexion.php");

$producto_id = isset($_REQUEST['producto']) ? intval($_REQUEST['producto']) : 0;
$tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : '';
$fecha_desde = isset($_REQUEST['fecha_desde']) ? $_REQUEST['fecha_desde'] : '';
$fecha_hasta = isset($_REQUEST['fecha_hasta']) ? $_REQUEST['fecha_hasta'] : '';

// Construir consulta
$sql = "SELECT m.*, p.codigo_producto, p.nombre_producto, u.firstname, u.lastname 
        FROM movimientos_stock m 
        LEFT JOIN productos p ON m.id_producto = p.id_producto 
        LEFT JOIN users u ON m.usuario_id = u.user_id 
        WHERE 1=1";

$params = [];
$types = "";

if ($producto_id > 0) {
    $sql .= " AND m.id_producto = ?";
    $params[] = $producto_id;
    $types .= "i";
}

if (!empty($tipo)) {
    $sql .= " AND m.tipo_movimiento = ?";
    $params[] = $tipo;
    $types .= "s";
}

if (!empty($fecha_desde)) {
    $sql .= " AND DATE(m.fecha_movimiento) >= ?";
    $params[] = $fecha_desde;
    $types .= "s";
}

if (!empty($fecha_hasta)) {
    $sql .= " AND DATE(m.fecha_movimiento) <= ?";
    $params[] = $fecha_hasta;
    $types .= "s";
}

$sql .= " ORDER BY m.fecha_movimiento DESC LIMIT 100";

$stmt = $con->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-info">
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Motivo</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>';
    
    while ($row = $result->fetch_assoc()) {
        $tipo_class = $row['tipo_movimiento'] == 'entrada' ? 'text-success' : 'text-danger';
        $tipo_icono = $row['tipo_movimiento'] == 'entrada' ? 'bi-arrow-up-circle' : 'bi-arrow-down-circle';
        $signo = $row['tipo_movimiento'] == 'entrada' ? '+' : '-';
        
        echo '<tr>
                <td>' . date('d/m/Y H:i', strtotime($row['fecha_movimiento'])) . '</td>
                <td>
                    <strong>' . htmlspecialchars($row['codigo_producto']) . '</strong><br>
                    <small class="text-muted">' . htmlspecialchars($row['nombre_producto']) . '</small>
                </td>
                <td>
                    <span class="badge bg-' . ($row['tipo_movimiento'] == 'entrada' ? 'success' : 'danger') . '">
                        <i class="bi ' . $tipo_icono . ' me-1"></i>' . ucfirst($row['tipo_movimiento']) . '
                    </span>
                </td>
                <td class="' . $tipo_class . ' fw-bold">' . $signo . $row['cantidad'] . '</td>
                <td>' . htmlspecialchars($row['motivo']) . '</td>
                <td>' . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . '</td>
              </tr>';
    }
    
    echo '</tbody></table></div>';
    
    // Resumen
    $sql_resumen = "SELECT 
        SUM(CASE WHEN tipo_movimiento = 'entrada' THEN cantidad ELSE 0 END) as total_entradas,
        SUM(CASE WHEN tipo_movimiento = 'salida' THEN cantidad ELSE 0 END) as total_salidas,
        COUNT(*) as total_movimientos
        FROM movimientos_stock m WHERE 1=1";
    
    if ($producto_id > 0) {
        $sql_resumen .= " AND m.id_producto = $producto_id";
    }
    if (!empty($tipo)) {
        $sql_resumen .= " AND m.tipo_movimiento = '$tipo'";
    }
    if (!empty($fecha_desde)) {
        $sql_resumen .= " AND DATE(m.fecha_movimiento) >= '$fecha_desde'";
    }
    if (!empty($fecha_hasta)) {
        $sql_resumen .= " AND DATE(m.fecha_movimiento) <= '$fecha_hasta'";
    }
    
    $resumen = mysqli_query($con, $sql_resumen);
    $stats = mysqli_fetch_assoc($resumen);
    
    echo '<div class="row mt-3">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5>' . $stats['total_entradas'] . '</h5>
                        <small>Total Entradas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h5>' . $stats['total_salidas'] . '</h5>
                        <small>Total Salidas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5>' . ($stats['total_entradas'] - $stats['total_salidas']) . '</h5>
                        <small>Diferencia</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h5>' . $stats['total_movimientos'] . '</h5>
                        <small>Total Movimientos</small>
                    </div>
                </div>
            </div>
          </div>';
} else {
    echo '<div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            No se encontraron movimientos con los filtros aplicados.
          </div>';
}

$stmt->close();
?>
