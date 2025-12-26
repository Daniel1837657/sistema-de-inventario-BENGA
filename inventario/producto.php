<?php
/*-------------------------
Autor: Daniel Felipe Perdomo Hernández
---------------------------*/

session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
	header("location: login.php");
	exit;
}

/* Connect To Database*/
require_once("config/db.php");
require_once("config/conexion.php");
include("funciones.php");

$active_productos = "active";
$active_clientes = "";
$active_usuarios = "";
$title = "Producto | Simple Stock";

// Manejo de agregar stock
if (isset($_POST['quantity']) && isset($_POST['fecha_ingreso'])) {
	$quantity = intval($_POST['quantity']);
	$reference = isset($_POST['reference']) ? mysqli_real_escape_string($con, strip_tags($_POST["reference"], ENT_QUOTES)) : '';
	$precio_unitario = isset($_POST['precio_unitario']) ? floatval($_POST['precio_unitario']) : 0;
	$fecha_ingreso = mysqli_real_escape_string($con, $_POST['fecha_ingreso']);
	$proveedor = isset($_POST['proveedor']) ? mysqli_real_escape_string($con, strip_tags($_POST["proveedor"], ENT_QUOTES)) : '';
	
	$id_producto = intval($_GET['id']);
	$user_id = $_SESSION['user_id'];
	$firstname = $_SESSION['firstname'];
	
	// Crear nota detallada
	$nota = "$firstname agregó $quantity producto(s) al inventario";
	if (!empty($proveedor)) {
		$nota .= " - Proveedor: $proveedor";
	}
	if ($precio_unitario > 0) {
		$nota .= " - Precio: $" . number_format($precio_unitario, 2);
	}
	if (!empty($reference)) {
		$nota .= " - Ref: $reference";
	}
	
	$fecha = date("Y-m-d H:i:s");

	// Validar datos
	if ($quantity < 1) {
		$error = 1;
		$error_msg = "La cantidad debe ser mayor a 0";
	} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_ingreso)) {
		$error = 1;
		$error_msg = "Formato de fecha inválido";
	} else {
	if (guardar_historial($con, $id_producto, $user_id, $fecha, $nota, $reference, $quantity) && agregar_stock($con, $id_producto, $quantity)) {
			$message = 1;
		} else {
			$error = 1;
			$error_msg = "Error al procesar la solicitud";
		}
	}
}

// Manejo de eliminar stock
if (isset($_POST['reference_remove']) and isset($_POST['quantity_remove'])) {
	$quantity = intval($_POST['quantity_remove']);
	$reference = mysqli_real_escape_string($con, strip_tags($_POST["reference_remove"], ENT_QUOTES));
	$id_producto = intval($_GET['id']);
	$user_id = $_SESSION['user_id'];
	$firstname = $_SESSION['firstname'];
	$nota = "$firstname eliminó $quantity producto(s) del inventario";
	$fecha = date("Y-m-d H:i:s");

	if (guardar_historial($con, $id_producto, $user_id, $fecha, $nota, $reference, $quantity) && eliminar_stock($con, $id_producto, $quantity) == 1) {
		$message = 1;
	} else {
		$error = 1;
	}
}

// Cargar datos del producto
if (isset($_GET['id'])) {
	$id_producto = intval($_GET['id']);
	$query = mysqli_query($con, "SELECT * FROM products WHERE id_producto = '$id_producto'");
	$row = mysqli_fetch_array($query);
} else {
	die("Producto no existe");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("head.php"); ?>
</head>

<body>
	<?php
	include("navbar.php");
	include("modal/agregar_stock.php");
	include("modal/eliminar_stock.php");
	include("modal/editar_productos.php");
	?>

	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<div class="card">
				<div class="card-body">
						<div class="row">
							<div class="col-md-4 offset-md-2 text-center">
								<img class="item-img img-fluid" src="img/stock.png" alt="">
								<br>
								<a href="#" class="btn btn-danger" onclick="eliminar('<?php echo $row['id_producto']; ?>')" title="Eliminar">
									<i class="bi bi-trash"></i> Eliminar
								</a>
								<a href="#myModal2" data-bs-toggle="modal" data-codigo='<?php echo $row['codigo_producto']; ?>'
									data-nombre='<?php echo $row['nombre_producto']; ?>'
									data-categoria='<?php echo $row['id_categoria'] ?>'
									data-precio='<?php echo $row['precio_producto'] ?>'
									data-stock='<?php echo $row['stock']; ?>'
									data-id='<?php echo $row['id_producto']; ?>'
									class="btn btn-info" title="Editar">
									<i class="bi bi-pencil"></i> Editar
								</a>
							</div>

							<div class="col-md-4 text-start">
								<div class="row margin-btm-20">
									<div class="col-12">
										<span class="item-title"><?php echo $row['nombre_producto']; ?></span>
									</div>
									<div class="col-12 mb-2">
										<span class="item-number"><?php echo $row['codigo_producto']; ?></span>
									</div>
									<div class="col-12">
										<span class="current-stock">Stock disponible</span>
									</div>
									<div class="col-12 mb-2">
										<span class="item-quantity"><?php echo number_format($row['stock']); ?></span>
									</div>

									<div class="col-12">
										<span class="current-stock">Precio venta</span>
									</div>
									<div class="col-12">
										<span class="item-price">$ <?php echo number_format($row['precio_producto']); ?> COP</span>
									</div>

									<div class="col-6 col-md-4">
										<a href="" data-bs-toggle="modal" data-bs-target="#add-stock"><img width="100px" src="img/stock-in.png"></a>
									</div>
									<div class="col-6 col-md-4">
										<a href="" data-bs-toggle="modal" data-bs-target="#remove-stock"><img width="100px" src="img/stock-out.png"></a>
									</div>
									<div class="col-12 mb-2"></div>
								</div>
							</div>
						</div>

						<br>

						<div class="row">
							<div class="col-md-8 offset-md-2 text-start">
								<div class="row">
									<?php if (isset($message)) { ?>
										<div class="alert alert-success alert-dismissible" role="alert">
											<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
											<strong>Aviso!</strong> Datos procesados exitosamente.
										</div>
									<?php } ?>
									<?php if (isset($error)) { ?>
										<div class="alert alert-danger alert-dismissible" role="alert">
											<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
											<strong>Error!</strong> <?php echo isset($error_msg) ? $error_msg : 'No se pudo procesar los datos.'; ?>
										</div>
									<?php } ?>

									<table class='table table-bordered'>
										<tr>
											<th class='text-center' colspan=4>HISTORIAL DE INVENTARIO</th>
										</tr>
										<tr>
											<td>Fecha</td>
											<td>Hora</td>
											<td>Descripción</td>
											<td class='text-center'>Total</td>
										</tr>
										<?php
										$query_historial = mysqli_query($con, "SELECT * FROM historial WHERE id_producto='$id_producto'");
										while ($row_hist = mysqli_fetch_array($query_historial)) {
										?>
											<tr>
												<td><?php echo date('d/m/Y', strtotime($row_hist['fecha'])); ?></td>
												<td><?php echo date('H:i:s', strtotime($row_hist['fecha'])); ?></td>
												<td><?php echo $row_hist['nota']; ?></td>
												<td class='text-center'><?php echo number_format($row_hist['cantidad']); ?></td>
											</tr>
										<?php
										}
										?>
									</table>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

	</div>

	<?php
	include("footer.php");
	?>
	<script type="text/javascript" src="js/productos.js"></script>
	<style>
		.invalid-feedback {
			color: #dc3545;
			font-size: 0.875em;
			margin-top: 0.25rem;
		}
		.is-invalid {
			border-color: #dc3545;
		}
		.form-text {
			color: #6c757d;
			font-size: 0.875em;
		}
	</style>
</body>

</html>

<script>
	// Validación y envío del formulario de agregar stock
	$("#add_stock_form").submit(function(event) {
		event.preventDefault();
		
		// Validar cantidad
		var quantity = parseInt($("#quantity").val());
		if (!quantity || quantity < 1) {
			$("#quantityError").text("La cantidad debe ser mayor o igual a 1").show();
			$("#quantity").addClass("is-invalid");
			return false;
		} else {
			$("#quantityError").hide();
			$("#quantity").removeClass("is-invalid");
		}

		// Validar fecha
		var fechaIngreso = $("#fecha_ingreso").val();
		if (!fechaIngreso) {
			alert("La fecha de ingreso es requerida");
			return false;
		}

		$('#btn_guardar_stock').attr("disabled", true).html('<i class="bi bi-arrow-clockwise"></i> Guardando...');

		var parametros = $(this).serialize();
		$.ajax({
			type: "POST",
			url: window.location.href,
			data: parametros,
			success: function(datos) {
				location.reload();
			},
			error: function() {
				alert("Error al procesar la solicitud");
				$('#btn_guardar_stock').attr("disabled", false).html('<i class="bi bi-plus-circle me-1"></i>Agregar Stock');
			}
		});
	});

	$("#editar_producto").submit(function(event) {
		$('#actualizar_datos').attr("disabled", true);

		var parametros = $(this).serialize();
		$.ajax({
			type: "POST",
			url: "ajax/editar_producto.php",
			data: parametros,
			beforeSend: function(objeto) {
				$("#resultados_ajax2").html("Mensaje: Cargando...");
			},
			success: function(datos) {
				$("#resultados_ajax2").html(datos);
				$('#actualizar_datos').attr("disabled", false);
			}
		});
		event.preventDefault();
	})

	// Reset del modal de agregar stock
	$('#add-stock').on('show.bs.modal', function(event) {
		var form = $('#add_stock_form')[0];
		form.reset();
		
		// Restablecer fecha actual
		$('#fecha_ingreso').val('<?php echo date('Y-m-d'); ?>');
		
		// Limpiar errores de validación
		$('.is-invalid').removeClass('is-invalid');
		$('.invalid-feedback').hide();
		
		// Restablecer botón
		$('#btn_guardar_stock').attr("disabled", false).html('<i class="bi bi-plus-circle me-1"></i>Agregar Stock');
	});

	$('#myModal2').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget);
		var codigo = button.data('codigo');
		var nombre = button.data('nombre');
		var categoria = button.data('categoria');
		var precio = button.data('precio');
		var stock = button.data('stock');
		var id = button.data('id');

		var modal = $(this);
		modal.find('.modal-body #mod_codigo').val(codigo);
		modal.find('.modal-body #mod_nombre').val(nombre);
		modal.find('.modal-body #mod_categoria').val(categoria);
		modal.find('.modal-body #mod_precio').val(precio);
		modal.find('.modal-body #mod_stock').val(stock);
		modal.find('.modal-body #mod_id').val(id);
	})
</script>