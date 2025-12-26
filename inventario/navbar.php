<?php if (isset($title)): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
	<div class="container">
		<a class="navbar-brand fw-bold" href="#">
			<a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
				<img src="img/BENGALogo.jpg" alt="BENGA Logo" style="height:32px;width:auto;margin-right:12px;vertical-align:middle;">BENGA
			</a>
		</a>
		<button
			class="navbar-toggler"
			type="button"
			data-bs-toggle="collapse"
			data-bs-target="#navbar-main"
			aria-controls="navbar-main"
			aria-expanded="false"
			aria-label="Toggle navigation"
		>
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbar-main">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
				<li class="nav-item">
					<a class="nav-link <?php if (isset($active_productos)) { echo $active_productos; } ?>" href="stock.php">
						<i class="bi bi-box-seam me-2"></i>Inventario
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php if (isset($active_movimientos)) { echo $active_movimientos; } ?>" href="movimientos.php">
						<i class="bi bi-clock-history me-2"></i>Movimientos
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php if (isset($active_categoria)) { echo $active_categoria; } ?>" href="categorias.php">
						<i class="bi bi-tags me-2"></i>Categorías
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php if (isset($active_usuarios)) { echo $active_usuarios; } ?>" href="usuarios.php">
						<i class="bi bi-people me-2"></i>Usuarios
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="acerca.php">
						<i class="bi bi-info-circle me-2"></i>Acerca de
					</a>
				</li>
			</ul>
			<ul class="navbar-nav">
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="bi bi-person-circle me-1"></i><?php echo isset($_SESSION['firstname']) ? htmlspecialchars($_SESSION['firstname']) : 'Usuario'; ?>
					</a>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" href="perfil.php">
							<i class="bi bi-person-circle me-2"></i>Ver Perfil
						</a></li>
						<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#myModal3">
							<i class="bi bi-key me-2"></i>Cambiar Contraseña
						</a></li>
						<li><hr class="dropdown-divider"></li>
						<li><a class="dropdown-item text-danger" href="logout.php">
							<i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
						</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</nav>
<?php endif; ?>
