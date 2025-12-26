<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
	header("location: login.php");
	exit;
}

/* Connect To Database*/
require_once("config/db.php"); //Contiene las variables de configuracion para conectar a la base de datos
require_once("config/conexion.php"); //Contiene funcion que conecta a la base de datos

$active_usuarios = "active";
$title = "Usuarios | Simple Stock";
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<?php include("head.php"); ?>
</head>

<body>
	<div class="main-container">
		<?php include("navbar.php"); ?>

		<main class="main-content">
			<div class="container my-4">
				<div class="card shadow-sm border-0">
					<div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
						<h5 class="mb-0">
							<i class="bi bi-people me-2"></i>Gestionar Usuarios
						</h5>
						<button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#myModal">
							<i class="bi bi-person-plus me-1"></i>Nuevo Usuario
						</button>
					</div>
					<div class="card-body">
						<?php
						include("modal/registro_usuarios.php");
						include("modal/editar_usuarios.php");
						include("modal/cambiar_password.php");
						?>
						<form class="row g-3" id="datos_cotizacion" role="form">
							<div class="col-md-4">
								<label for="q" class="form-label">Nombre del usuario</label>
								<input type="text" class="form-control" id="q" placeholder="Buscar usuarios..." onkeyup="load(1);">
							</div>
							<div class="col-md-3">
								<label class="form-label">&nbsp;</label>
								<div class="d-grid">
									<button type="button" class="btn btn-primary" onclick="load(1);">
										<i class="bi bi-search me-1"></i>Buscar
									</button>
								</div>
							</div>
							<div class="col-12 text-center">
								<span id="loader"></span>
							</div>
						</form>

						<hr class="my-4">

						<div id="resultados"></div><!-- Carga los datos ajax -->
						<div class='outer_div'></div><!-- Carga los datos ajax -->
					</div>
				</div>
			</div>
		</main>

		<?php include("footer.php"); ?>
	</div>

	<script type="text/javascript" src="js/usuarios.js"></script>
</body>

</html>
