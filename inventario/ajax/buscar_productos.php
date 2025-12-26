<?php
/*-------------------------
	Autor: Daniel Perdomo
	---------------------------*/

require_once('is_logged.php'); // Verifica que el usuario está logueado
require_once("../config/db.php");
require_once("../config/conexion.php");
require_once("../funciones.php");

$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';

/* ELIMINAR PRODUCTO */
if (!empty($_GET['id'])) {
	$id_producto = intval($_GET['id']);
	$stmt = $con->prepare("DELETE FROM products WHERE id_producto = ?");
	$stmt->bind_param("i", $id_producto);
	if ($stmt->execute()) {
		echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
			<i class="bi bi-check-circle me-2"></i>
			<strong>¡Éxito!</strong> Producto eliminado exitosamente.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>';
	} else {
		echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<i class="bi bi-exclamation-triangle me-2"></i>
			<strong>Error!</strong> Algo ha salido mal, intenta nuevamente.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>';
	}
	$stmt->close();
}

/* LISTAR PRODUCTOS (AJAX) */
if ($action === 'ajax') {
	$q            = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';
	$id_categoria = isset($_REQUEST['id_categoria']) ? intval($_REQUEST['id_categoria']) : 0;

	$aColumns = ['codigo_producto', 'nombre_producto'];
	$sTable   = "products";
	$sWhere   = "";

	if (!empty($q)) {
		$q_escaped = mysqli_real_escape_string($con, strip_tags($q, ENT_QUOTES));
		$sWhere = "WHERE (" . implode(" LIKE '%$q_escaped%' OR ", $aColumns) . " LIKE '%$q_escaped%')";
	}

	if ($id_categoria > 0) {
		$sWhere .= ($sWhere ? " AND " : "WHERE ") . "id_categoria = $id_categoria";
	}

	$sWhere .= " ORDER BY id_producto DESC";

	include 'pagination.php';
	$page        = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	$per_page    = 18;
	$adjacents   = 4;
	$offset      = ($page - 1) * $per_page;

	// Contar registros
	$count_query = mysqli_query($con, "SELECT COUNT(*) AS numrows FROM $sTable $sWhere");
	$numrows     = $count_query ? (int)mysqli_fetch_assoc($count_query)['numrows'] : 0;
	$total_pages = ceil($numrows / $per_page);
	$reload      = './stock.php';

	// Obtener datos
	$sql   = "SELECT * FROM $sTable $sWhere LIMIT $offset, $per_page";
	$query = mysqli_query($con, $sql);

	if ($numrows > 0 && $query) {
		$nums = 1;
		while ($row = mysqli_fetch_assoc($query)) {
			$id_producto     = $row['id_producto'];
			$codigo_producto = htmlspecialchars($row['codigo_producto'], ENT_QUOTES, 'UTF-8');
			$nombre_producto = htmlspecialchars($row['nombre_producto'], ENT_QUOTES, 'UTF-8');
			$stock           = (float)$row['stock'];
			
			// Determinar el estado del stock
			$stock_class = 'success';
			$stock_icon = 'bi-check-circle';
			if ($stock < 10) {
				$stock_class = 'danger';
				$stock_icon = 'bi-exclamation-triangle';
			} elseif ($stock < 50) {
				$stock_class = 'warning';
				$stock_icon = 'bi-exclamation-circle';
			}
?>
			<div class="col-lg-2 col-md-3 col-sm-6 col-12 mb-3">
				<div class="card h-100 shadow-sm stock-item">
					<div class="card-body text-center p-3">
						<div class="position-relative mb-2">
							<img class="img-fluid" src="img/stock.png" alt="<?php echo $nombre_producto; ?>" style="max-height: 80px;">
							<span class="badge bg-<?php echo $stock_class; ?> position-absolute top-0 end-0" title="Cantidad actual">
								<i class="<?php echo $stock_icon; ?> me-1"></i><?php echo number_format($stock, 2); ?>
							</span>
						</div>
						<h6 class="card-title fw-bold text-truncate" title="<?php echo $nombre_producto; ?>">
							<?php echo $nombre_producto; ?>
						</h6>
						<p class="card-text small text-muted"><?php echo $codigo_producto; ?></p>
						<div class="d-grid gap-1">
							<a href="producto.php?id=<?php echo $id_producto; ?>" class="btn btn-sm btn-outline-primary">
								<i class="bi bi-eye me-1"></i>Ver
							</a>
							<a href="#" onclick="editarProducto('<?php echo $id_producto; ?>')" class="btn btn-sm btn-outline-warning">
								<i class="bi bi-pencil-square me-1"></i>Editar
							</a>
							<a href="#" onclick="eliminarProducto('<?php echo $id_producto; ?>')" class="btn btn-sm btn-outline-danger">
								<i class="bi bi-trash me-1"></i>Eliminar
							</a>
						</div>
					</div>
				</div>
			</div>
<?php
			if ($nums % 6 == 0) echo "<div class='clearfix'></div>";
			$nums++;
		}
		echo "<div class='clearfix'></div>
				<div class='row text-center'>
					<div class='col-12'>" . paginate($reload, $page, $total_pages, $adjacents) . "</div>
				</div>";
	} else {
		echo '<div class="col-12">
				<div class="alert alert-info text-center" role="alert">
					<i class="bi bi-info-circle me-2"></i>
					<strong>Información:</strong> No se encontraron productos que coincidan con tu búsqueda.
				</div>
			</div>';
	}
}
?>