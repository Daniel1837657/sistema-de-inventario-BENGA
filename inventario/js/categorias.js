'use strict';

$(document).ready(function () {
	load(1);
});

function load(page) {
	const q = $("#q").val();
	$("#loader").fadeIn('slow');
	$.ajax({
		url: './ajax/buscar_categorias.php?action=ajax&page=' + page + '&q=' + q,
		beforeSend: function (objeto) {
			$('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
		},
		success: function (data) {
			$(".outer_div").html(data).fadeIn('slow');
			$('#loader').html('');
		}
	})
}

function eliminar(id) {
	const q = $("#q").val();
	if (confirm("Realmente deseas eliminar la categoría")) {
		$.ajax({
			type: "GET",
			url: "./ajax/buscar_categorias.php",
			data: "id=" + id, "q": q,
			beforeSend: function (objeto) {
				$("#resultados").html("Mensaje: Cargando...");
			},
			success: function (datos) {
				$("#resultados").html(datos);
				load(1);
			}
		});
	}
}

$("#guardar_categoria").submit(function (event) {
	event.preventDefault();
	$('#guardar_datos').attr("disabled", true);
	const parametros = $(this).serialize();
	$.ajax({
		type: "POST",
		url: "ajax/nueva_categoria.php",
		data: parametros,
		beforeSend: function (objeto) {
			$("#resultados_ajax").html("Mensaje: Cargando...");
		},
		success: function (datos) {
			$("#resultados_ajax").html(datos);
			$('#guardar_datos').attr("disabled", false);
			load(1);
			if (datos.indexOf('alert-success') > -1) {
				setTimeout(function() {
					const modalEl = document.getElementById('nuevoCliente');
					const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
					modal.hide();
					document.getElementById('guardar_categoria').reset();
						// Bootstrap gestiona el backdrop y la clase modal-open automáticamente
				}, 1200);
			}
		},
		error: function(xhr, status, error) {
			$("#resultados_ajax").html('<div class="alert alert-danger">Error al procesar la solicitud</div>');
			$('#guardar_datos').attr("disabled", false);
		}
	});
})

$("#editar_categoria").submit(function (event) {
	event.preventDefault();
	$('#actualizar_datos').attr("disabled", true);
	const parametros = $(this).serialize();
	$.ajax({
		type: "POST",
		url: "ajax/editar_categoria.php",
		data: parametros,
		beforeSend: function (objeto) {
			$("#resultados_ajax2").html("Mensaje: Cargando...");
		},
		success: function (datos) {
			$("#resultados_ajax2").html(datos);
			$('#actualizar_datos').attr("disabled", false);
			load(1);
			if (datos.indexOf('alert-success') > -1) {
				setTimeout(function() {
					const modalEl = document.getElementById('myModal2');
					const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
					modal.hide();
						// Bootstrap gestiona el backdrop y la clase modal-open automáticamente
				}, 1200);
			}
		}
	});
})

$('#myModal2').on('show.bs.modal', function (event) {
	const button = $(event.relatedTarget);
	const nombre = button.data('nombre');
	const descripcion = button.data('descripcion');
	const id = button.data('id');
	const modal = $(this);
	modal.find('.modal-body #mod_nombre').val(nombre);
	modal.find('.modal-body #mod_descripcion').val(descripcion);
	modal.find('.modal-body #mod_id').val(id);
})
