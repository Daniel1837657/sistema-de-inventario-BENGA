<?php
include('is_logged.php'); // Verifica que el usuario esté logueado

/* Conexión a la base de datos */
require_once("../config/db.php");
require_once("../config/conexion.php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// ELIMINAR USUARIO
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$user_id = intval($_GET['id']);

	// No permitir borrar el administrador
	if ($user_id === 1) {
		echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Advertencia!</strong> No se puede eliminar el usuario administrador.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
	} else {
		// Verificar que el usuario exista
		$stmt = $con->prepare("SELECT * FROM users WHERE user_id = ?");
		$stmt->bind_param("i", $user_id);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			// Eliminar usuario
			$stmt_del = $con->prepare("DELETE FROM users WHERE user_id = ?");
			$stmt_del->bind_param("i", $user_id);
			if ($stmt_del->execute()) {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>¡Éxito!</strong> Usuario eliminado exitosamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
			} else {
				echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Error!</strong> Lo siento, algo salió mal al eliminar.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
			}
			$stmt_del->close();
		} else {
			echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Advertencia!</strong> El usuario no existe.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
		}
		$stmt->close();
	}
}

// LISTAR USUARIOS (AJAX)
if ($action == 'ajax') {
	$q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';
	$sWhere = "";
	if ($q != "") {
		$q_like = "%" . $q . "%";
		$sWhere = "WHERE firstname LIKE ? OR lastname LIKE ?";
	}

	include 'pagination.php'; // Contiene paginación
	$output = '';

	$page = (isset($_REQUEST['page']) && is_numeric($_REQUEST['page'])) ? intval($_REQUEST['page']) : 1;
	$per_page = 10;
	$adjacents = 4;
	$offset = ($page - 1) * $per_page;

	// Contar total de filas
	if ($q != "") {
		$stmt_count = $con->prepare("SELECT COUNT(*) AS numrows FROM users $sWhere");
		$stmt_count->bind_param("ss", $q_like, $q_like);
	} else {
		$stmt_count = $con->prepare("SELECT COUNT(*) AS numrows FROM users");
	}
	$stmt_count->execute();
	$stmt_count->bind_result($numrows);
	$stmt_count->fetch();
	$stmt_count->close();

	$total_pages = ceil($numrows / $per_page);
	$reload = './usuarios.php';
	
	// Generar paginación
	if ($total_pages > 1) {
		$output = paginate($reload, $page, $total_pages, $adjacents);
	}

	// Obtener datos paginados
	if ($q != "") {
		$stmt_data = $con->prepare("SELECT * FROM users $sWhere LIMIT ?, ?");
		$stmt_data->bind_param("ssii", $q_like, $q_like, $offset, $per_page);
	} else {
		$stmt_data = $con->prepare("SELECT * FROM users LIMIT ?, ?");
		$stmt_data->bind_param("ii", $offset, $per_page);
	}
	$stmt_data->execute();
	$result_data = $stmt_data->get_result();

	if ($numrows > 0) {
?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead class="table-light">
					<tr>
						<th><i class="bi bi-person me-2"></i>Nombre</th>
						<th><i class="bi bi-person-badge me-2"></i>Usuario</th>
						<th><i class="bi bi-envelope me-2"></i>Email</th>
						<th><i class="bi bi-telephone me-2"></i>Teléfono</th>
						<th class="text-center"><i class="bi bi-gear me-2"></i>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php
					while ($row = $result_data->fetch_assoc()) {
					?>
						<tr>
							<td>
								<span class="fw-medium"><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></span>
							</td>
							<td><?php echo htmlspecialchars($row['user_name']); ?></td>
							<td><?php echo htmlspecialchars($row['user_email']); ?></td>
							<td><?php echo isset($row['user_phone']) ? htmlspecialchars($row['user_phone']) : 'N/A'; ?></td>
							<td class="text-center">
								<a href="#" onclick="editarUsuario('<?php echo $row['user_id']; ?>', '<?php echo htmlspecialchars($row['firstname']); ?>', '<?php echo htmlspecialchars($row['lastname']); ?>', '<?php echo htmlspecialchars($row['user_name']); ?>', '<?php echo htmlspecialchars($row['user_email']); ?>', '<?php echo isset($row['user_phone']) ? htmlspecialchars($row['user_phone']) : ''; ?>')" class="btn btn-sm btn-primary me-1">
									<i class="bi bi-pencil-square me-1"></i>Editar
								</a>
								<a href="#" onclick="cambiarPassword('<?php echo $row['user_id']; ?>')" class="btn btn-sm btn-warning me-1">
									<i class="bi bi-key me-1"></i>Password
								</a>
								<?php if ($row['user_id'] != 1): ?>
								<a href="#" onclick="eliminarUsuario('<?php echo $row['user_id']; ?>')" class="btn btn-sm btn-danger">
									<i class="bi bi-trash me-1"></i>Eliminar
								</a>
								<?php endif; ?>
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
                <strong>Información:</strong> No se encontraron usuarios que coincidan con tu búsqueda.
              </div>';
	}
	$stmt_data->close();
}
?>