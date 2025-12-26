'use strict';

$(document).ready(function () {
    load(1);
});

function load(page) {
    const q = $("#q").val();
    $("#loader").fadeIn('slow');
    $.ajax({
        url: './ajax/buscar_usuarios.php',
        data: { action: 'ajax', page: page, q: q },
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
    if (confirm("Realmente deseas eliminar el usuario")) {
        $.ajax({
            type: "GET",
            url: "./ajax/buscar_usuarios.php",
            data: { id: id, q: q },
            beforeSend: function () {
                $("#resultados").html("Mensaje: Cargando...");
            },
            success: function (datos) {
                $("#resultados").html(datos);
                load(1);
            },
            error: function () {
                $("#resultados").html('<span style="color:red;">Error al eliminar el usuario.</span>');
            }
        });
    }
}

function editarUsuario(id, firstname, lastname, user_name, user_email, user_phone) {
    document.getElementById('firstname2').value = firstname;
    document.getElementById('lastname2').value = lastname;
    document.getElementById('user_name2').value = user_name;
    document.getElementById('user_email2').value = user_email;
    document.getElementById('mod_id').value = id;
    
    const modal = new bootstrap.Modal(document.getElementById('myModal2'));
    modal.show();
}

function cambiarPassword(id) {
    document.getElementById('mod_id').value = id;
    const modal = new bootstrap.Modal(document.getElementById('myModal3'));
    modal.show();
}

function eliminarUsuario(id) {
    if (confirm("¿Realmente deseas eliminar este usuario?")) {
        const q = $("#q").val();
        $.ajax({
            type: "GET",
            url: "./ajax/buscar_usuarios.php",
            data: { id: id, q: q },
            beforeSend: function () {
                $("#resultados").html("Mensaje: Cargando...");
            },
            success: function (datos) {
                $("#resultados").html(datos);
                load(1);
            },
            error: function () {
                $("#resultados").html('<span style="color:red;">Error al eliminar el usuario.</span>');
            }
        });
    }
}

$("#guardar_usuario").submit(function (event) {
    event.preventDefault();
    $('#guardar_datos').attr("disabled", true);
    const parametros = $(this).serialize();
    $.ajax({
        type: "POST",
        url: "ajax/nuevo_usuario.php",
        data: parametros,
        beforeSend: function () {
            $("#resultados_ajax").html("Mensaje: Cargando...");
        },
        success: function (datos) {
            $("#resultados_ajax").html(datos);
            $('#guardar_datos').attr("disabled", false);
            load(1);
            if (datos.indexOf('alert-success') > -1) {
                setTimeout(function() {
                    const modalEl = document.getElementById('myModal');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.hide();
                    document.getElementById('guardar_usuario').reset();
                        // Bootstrap gestiona el backdrop y la clase modal-open automáticamente
                }, 1200);
            }
        },
        error: function() {
            $("#resultados_ajax").html('<div class="alert alert-danger">Error al procesar la solicitud</div>');
            $('#guardar_datos').attr("disabled", false);
        }
    });
});

$("#editar_usuario").submit(function (event) {
    event.preventDefault();
    $('#actualizar_datos').attr("disabled", true);
    const parametros = $(this).serialize();
    $.ajax({
        type: "POST",
        url: "ajax/editar_usuario.php",
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

$("#editar_password").submit(function (event) {
    event.preventDefault();
    $('#actualizar_datos3').attr("disabled", true);
    const parametros = $(this).serialize();
    $.ajax({
        type: "POST",
        url: "ajax/editar_password.php",
        data: parametros,
        beforeSend: function () {
            $("#resultados_ajax3").html("Mensaje: Cargando...");
        },
        success: function (datos) {
            $("#resultados_ajax3").html(datos);
            $('#actualizar_datos3').attr("disabled", false);
            if (datos.indexOf('alert-success') > -1) {
                setTimeout(function() {
                    const modalEl = document.getElementById('myModal3');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.hide();
                    document.getElementById('editar_password').reset();
                        // Bootstrap gestiona el backdrop y la clase modal-open automáticamente
                }, 1200);
            }
        },
        error: function() {
            $("#resultados_ajax3").html('<div class="alert alert-danger">Error al procesar la solicitud</div>');
            $('#actualizar_datos3').attr("disabled", false);
        }
    });
});
