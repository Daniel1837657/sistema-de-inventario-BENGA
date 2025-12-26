// Función para abrir el modal de agregar producto de forma segura
'use strict';

$(document).ready(function () {
    load(1);
});

function load(page) {
    const q = $("#q").val();
    const id_categoria = $("#id_categoria").val();
    const parametros = { action: 'ajax', page: page, q: q, id_categoria: id_categoria };

    $("#loader").fadeIn('slow');

    $.ajax({
        url: './ajax/buscar_productos.php',
        data: parametros,
        beforeSend: function () {
            $('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
        },
        success: function (data) {
            $(".outer_div").html(data).fadeIn('slow');
            $('#loader').html('');
        },
        error: function () {
            $('#loader').html('<span style="color:red;">Error al cargar los datos.</span>');
        }
    });
}

function eliminar(id) {
    const q = $("#q").val();

    if (confirm("Realmente deseas eliminar el producto")) {
        $.ajax({
            type: "GET",
            url: "./ajax/buscar_productos.php",
            data: { id: id, q: q },
            beforeSend: function () {
                $("#resultados").html("Mensaje: Cargando...");
            },
            success: function (datos) {
                $("#resultados").html(datos);
                load(1);
            },
            error: function () {
                $("#resultados").html('<span style="color:red;">Error al eliminar el producto.</span>');
            }
        });
    }
}

$("#editar_producto").submit(function (event) {
    event.preventDefault();
    $('#actualizar_datos').attr("disabled", true);
    const parametros = $(this).serialize();
    $.ajax({
        type: "POST",
        url: "ajax/editar_producto.php",
        data: parametros,
        beforeSend: function () {
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
        },
        error: function() {
            $("#resultados_ajax2").html('<div class="alert alert-danger">Error al procesar la solicitud</div>');
            $('#actualizar_datos').attr("disabled", false);
        }
    });
});
