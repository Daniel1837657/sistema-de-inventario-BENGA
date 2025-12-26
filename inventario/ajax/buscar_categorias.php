<?php
// -------------------------
// Autor: Daniel Perdomo
// -------------------------

include('is_logged.php'); // Verifica que el usuario está logueado
require_once("../config/db.php");
require_once("../config/conexion.php");

$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';

if (isset($_GET['id'])) {
	$id_categoria = (int) $_GET['id'];

	// Verificar si existen productos vinculados
	$stmt = $con->prepare("SELECT COUNT(*) FROM products WHERE id_categoria = ?");
	$stmt->bind_param("i", $id_categoria);
	$stmt->execute();
	$stmt->bind_result($count);
	$stmt->fetch();
	$stmt->close();

	if ($count == 0) {
		// Eliminar categoría
		$stmt = $con->prepare("DELETE FROM categorias WHERE id_categoria = ?");
		$stmt->bind_param("i", $id_categoria);

		if ($stmt->execute()) {
			echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>¡Éxito!</strong> Categoría eliminada exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
		} else {
			echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Error!</strong> Lo siento, algo ha salido mal. Intenta nuevamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
		}
		$stmt->close();
	} else {
		echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Advertencia!</strong> No se pudo eliminar esta categoría. Existen productos vinculados.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
	}
}

if ($action == 'ajax') {
	$q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';
	$q = "%$q%";

	$aColumns = array('nombre_categoria');
	$sTable = "categorias";
	$sWhere = "";

	if (!empty($_REQUEST['q'])) {
		$sWhereParts = [];
		foreach ($aColumns as $col) {
			$sWhereParts[] = "$col LIKE ?";
		}
		$sWhere = "WHERE (" . implode(" OR ", $sWhereParts) . ")";
	}

	include 'pagination.php';
	$output = '';

	$page      = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
	$per_page  = 10;
	$adjacents = 4;
	$offset    = ($page - 1) * $per_page;

	// Contar registros
	$count_sql = "SELECT COUNT(*) AS numrows FROM $sTable $sWhere";
	$stmt = $con->prepare($count_sql);

	if (!empty($_REQUEST['q'])) {
		$stmt->bind_param(str_repeat("s", count($aColumns)), ...array_fill(0, count($aColumns), $q));
	}
	$stmt->execute();
	$stmt->bind_result($numrows);
	$stmt->fetch();
	$stmt->close();

	$total_pages = ceil($numrows / $per_page);
	$reload = './categorias.php';
	
	// Generar paginación
	if ($total_pages > 1) {
		$output = paginate($reload, $page, $total_pages, $adjacents);
	}

	// Obtener datos
	$sql = "SELECT * FROM $sTable $sWhere ORDER BY nombre_categoria LIMIT ?, ?";
	$stmt = $con->prepare($sql);

	if (!empty($_REQUEST['q'])) {
		$params = array_merge(array_fill(0, count($aColumns), $q), [$offset, $per_page]);
		$types = str_repeat("s", count($aColumns)) . "ii";
		$stmt->bind_param($types, ...$params);
	} else {
		$stmt->bind_param("ii", $offset, $per_page);
	}

	$stmt->execute();
	$result = $stmt->get_result();

	if ($numrows > 0) {
?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead class="table-light">
					<tr>
						<th><i class="bi bi-tag me-2"></i>Nombre de la Categoría</th>
						<th class="text-center"><i class="bi bi-gear me-2"></i>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php
					while ($row = $result->fetch_assoc()) {
					?>
						<tr>
							<td>
								<span class="fw-medium"><?php echo htmlspecialchars($row['nombre_categoria']); ?></span>
							</td>
							<td class="text-center">
								<a href="#" onclick="editarCategoria('<?php echo $row['id_categoria']; ?>', '<?php echo htmlspecialchars($row['nombre_categoria']); ?>')" class="btn btn-sm btn-primary me-1">
									<i class="bi bi-pencil-square me-1"></i>Editar
								</a>
								<a href="#" onclick="eliminarCategoria('<?php echo $row['id_categoria']; ?>')" class="btn btn-sm btn-danger">
									<i class="bi bi-trash me-1"></i>Eliminar
								</a>
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>

		<div class="d-flex justify-content-center mt-3">
			<?php echo $output; ?>
		</div>

<?php
	} else {
		echo '<div class="alert alert-info text-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Información:</strong> No se encontraron categorías que coincidan con tu búsqueda.
              </div>';
	}
	$stmt->close();
}
?>