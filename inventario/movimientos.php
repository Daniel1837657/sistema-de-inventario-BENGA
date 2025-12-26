<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
	header("location: login.php");
	exit;
}

require_once("config/db.php");
require_once("config/conexion.php");

$active_movimientos = "active";
$title = "Movimientos de Stock | BENGA";
$producto_id = isset($_GET['producto']) ? intval($_GET['producto']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<?php include("head.php"); ?>

<body>
	<div class="main-container">
		<?php include("navbar.php"); ?>

		<main class="main-content">
			<div class="container my-4">
				<!-- Dashboard de Estadísticas -->
				<div class="row mb-4">
					<div class="col-12">
						<div class="card shadow-sm border-0">
							<div class="card-header bg-gradient-primary text-white">
								<h5 class="mb-0">
									<i class="bi bi-graph-up me-2"></i>Dashboard de Movimientos
								</h5>
							</div>
							<div class="card-body">
								<div class="row g-4">
									<!-- Métricas principales -->
									<div class="col-lg-3 col-md-6">
										<div class="card bg-success text-white h-100">
											<div class="card-body text-center">
												<i class="bi bi-arrow-up-circle-fill fs-1 mb-2"></i>
												<h3 id="total-entradas" class="mb-1">0</h3>
												<p class="mb-0 small">Total Entradas</p>
											</div>
										</div>
									</div>
									<div class="col-lg-3 col-md-6">
										<div class="card bg-danger text-white h-100">
											<div class="card-body text-center">
												<i class="bi bi-arrow-down-circle-fill fs-1 mb-2"></i>
												<h3 id="total-salidas" class="mb-1">0</h3>
												<p class="mb-0 small">Total Salidas</p>
											</div>
										</div>
									</div>
									<div class="col-lg-3 col-md-6">
										<div class="card bg-primary text-white h-100">
											<div class="card-body text-center">
												<i class="bi bi-clipboard-data-fill fs-1 mb-2"></i>
												<h3 id="movimientos-hoy" class="mb-1">0</h3>
												<p class="mb-0 small">Movimientos Hoy</p>
											</div>
										</div>
									</div>
									<div class="col-lg-3 col-md-6">
										<div class="card bg-warning text-dark h-100">
											<div class="card-body text-center">
												<i class="bi bi-calendar-week-fill fs-1 mb-2"></i>
												<h3 id="movimientos-semana" class="mb-1">0</h3>
												<p class="mb-0 small">Esta Semana</p>
											</div>
										</div>
									</div>
								</div>

								<!-- Gráficos -->
								<div class="row mt-4">
									<div class="col-lg-8">
										<div class="card">
											<div class="card-header">
												<h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Movimientos por Día (Últimos 7 días)</h6>
											</div>
											<div class="card-body">
												<canvas id="movimientosChart" height="100"></canvas>
											</div>
										</div>
									</div>
									<div class="col-lg-4">
										<div class="card">
											<div class="card-header">
												<h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Tipos de Movimiento</h6>
											</div>
											<div class="card-body">
												<canvas id="tiposChart" height="200"></canvas>
											</div>
										</div>
									</div>
								</div>

								<!-- Top productos con más movimientos -->
								<div class="row mt-4">
									<div class="col-12">
										<div class="card">
											<div class="card-header">
												<h6 class="mb-0"><i class="bi bi-trophy me-2"></i>Productos con Más Movimientos (Últimos 30 días)</h6>
											</div>
											<div class="card-body">
												<div id="top-productos"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Filtros y tabla de movimientos -->
				<div class="card shadow-sm border-0">
					<div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
						<h5 class="mb-0">
							<i class="bi bi-clock-history me-2"></i>Historial de Movimientos
						</h5>
						<div>
							<button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#nuevoMovimiento">
								<i class="bi bi-plus-circle me-1"></i>Registrar Movimiento
							</button>
						</div>
					</div>
					<div class="card-body">
						<?php include("modal/registro_movimiento.php"); ?>
						
						<form class="row g-3" id="filtros_movimientos">
							<div class="col-md-3">
								<label for="producto_filtro" class="form-label">Producto</label>
								<select class="form-select" id="producto_filtro" onchange="loadMovimientos();">
									<option value="">Todos los productos</option>
									<?php
									$query_productos = mysqli_query($con, "SELECT id_producto, codigo_producto, nombre_producto FROM productos ORDER BY nombre_producto");
									while ($row = mysqli_fetch_array($query_productos)) {
										$selected = ($producto_id == $row['id_producto']) ? 'selected' : '';
										echo '<option value="' . $row['id_producto'] . '" ' . $selected . '>' . 
											 htmlspecialchars($row['codigo_producto'] . ' - ' . $row['nombre_producto']) . '</option>';
									}
									?>
								</select>
							</div>

							<div class="col-md-2">
								<label for="tipo_movimiento" class="form-label">Tipo</label>
								<select class="form-select" id="tipo_movimiento" onchange="loadMovimientos();">
									<option value="">Todos</option>
									<option value="entrada">Entradas</option>
									<option value="salida">Salidas</option>
								</select>
							</div>

							<div class="col-md-2">
								<label for="fecha_desde" class="form-label">Desde</label>
								<input type="date" class="form-control" id="fecha_desde" onchange="loadMovimientos();">
							</div>

							<div class="col-md-2">
								<label for="fecha_hasta" class="form-label">Hasta</label>
								<input type="date" class="form-control" id="fecha_hasta" onchange="loadMovimientos();">
							</div>

							<div class="col-md-3">
								<label class="form-label">&nbsp;</label>
								<div class="d-grid">
									<button type="button" class="btn btn-info" onclick="loadMovimientos();">
										<i class="bi bi-search me-1"></i>Buscar
									</button>
								</div>
							</div>
						</form>

						<hr class="my-4">

						<div id="movimientos-resultados"></div>
					</div>
				</div>
			</div>
		</main>

		<?php include("footer.php"); ?>
	</div>

	<!-- Chart.js -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	
	<script>
		let movimientosChart, tiposChart;

		$(document).ready(function() {
			loadDashboard();
			loadMovimientos();
		});

		function loadDashboard() {
			$.ajax({
				url: './ajax/dashboard_movimientos.php',
				dataType: 'json',
				success: function(data) {
					console.log('Dashboard data:', data); // Debug
					
					// Actualizar métricas con formato de números
					$('#total-entradas').text(data.total_entradas.toLocaleString());
					$('#total-salidas').text(data.total_salidas.toLocaleString());
					$('#movimientos-hoy').text(data.movimientos_hoy.toLocaleString());
					$('#movimientos-semana').text(data.movimientos_semana.toLocaleString());
					
					// Crear gráfico de movimientos por día
					createMovimientosChart(data.movimientos_por_dia);
					
					// Crear gráfico de tipos
					createTiposChart(data.tipos_movimiento);
					
					// Mostrar top productos
					showTopProductos(data.top_productos);
				},
				error: function(xhr, status, error) {
					console.error('Error al cargar dashboard:', error);
					console.error('Response:', xhr.responseText);
					// Mostrar números por defecto
					$('#total-entradas').text('0');
					$('#total-salidas').text('0');
					$('#movimientos-hoy').text('0');
					$('#movimientos-semana').text('0');
				}
			});
		}

		function createMovimientosChart(data) {
			const ctx = document.getElementById('movimientosChart').getContext('2d');
			
			if (movimientosChart) {
				movimientosChart.destroy();
			}
			
			movimientosChart = new Chart(ctx, {
				type: 'line',
				data: {
					labels: data.labels,
					datasets: [{
						label: 'Entradas',
						data: data.entradas,
						borderColor: '#667eea',
						backgroundColor: 'rgba(102, 126, 234, 0.1)',
						tension: 0.4
					}, {
						label: 'Salidas',
						data: data.salidas,
						borderColor: '#764ba2',
						backgroundColor: 'rgba(118, 75, 162, 0.1)',
						tension: 0.4
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: {
							position: 'top',
						}
					},
					scales: {
						y: {
							beginAtZero: true,
							ticks: {
								stepSize: 1
							}
						}
					}
				}
			});
		}

		function createTiposChart(data) {
			const ctx = document.getElementById('tiposChart').getContext('2d');
			
			if (tiposChart) {
				tiposChart.destroy();
			}
			
			tiposChart = new Chart(ctx, {
				type: 'doughnut',
				data: {
					labels: ['Entradas', 'Salidas'],
					datasets: [{
						data: [data.entradas, data.salidas],
						backgroundColor: ['#667eea', '#764ba2'],
						borderWidth: 2,
						borderColor: '#fff'
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: {
							position: 'bottom'
						}
					}
				}
			});
		}

		function showTopProductos(productos) {
			let html = '<div class="row">';
			
			productos.forEach((producto, index) => {
				const badgeClass = index === 0 ? 'bg-warning' : index === 1 ? 'bg-secondary' : 'bg-success';
				const icon = index === 0 ? 'bi-trophy-fill' : index === 1 ? 'bi-award-fill' : 'bi-star-fill';
				
				html += `
					<div class="col-md-4 mb-3">
						<div class="card h-100">
							<div class="card-body text-center">
								<i class="bi ${icon} fs-2 text-warning mb-2"></i>
								<h6 class="card-title">${producto.codigo_producto}</h6>
								<p class="card-text small text-muted">${producto.nombre_producto}</p>
								<span class="badge ${badgeClass}">${producto.total_movimientos} movimientos</span>
							</div>
						</div>
					</div>
				`;
			});
			
			html += '</div>';
			$('#top-productos').html(html);
		}

		function loadMovimientos() {
			var producto = $("#producto_filtro").val();
			var tipo = $("#tipo_movimiento").val();
			var fecha_desde = $("#fecha_desde").val();
			var fecha_hasta = $("#fecha_hasta").val();
			
			$.ajax({
				url: './ajax/buscar_movimientos.php',
				data: { 
					producto: producto,
					tipo: tipo,
					fecha_desde: fecha_desde,
					fecha_hasta: fecha_hasta
				},
				beforeSend: function() {
					$("#movimientos-resultados").html('<div class="text-center"><div class="spinner-border text-info" role="status"><span class="visually-hidden">Cargando...</span></div></div>');
				},
				success: function(data) {
					$("#movimientos-resultados").html(data);
					// Recargar dashboard después de cambios
					loadDashboard();
				},
				error: function() {
					$("#movimientos-resultados").html('<div class="alert alert-danger">Error al cargar los movimientos</div>');
				}
			});
		}

		// Actualizar dashboard cada 5 minutos
		setInterval(loadDashboard, 300000);
	</script>

	<?php include("modal/cambiar_password.php"); ?>
</body>

</html>
