<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    header("location: login.php");
    exit;
}

/* Conexión a base de datos */
require_once("config/db.php");
require_once("config/conexion.php");

$active_productos = "active";
$title = "Inventario | Simple Stock";
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
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-search me-2"></i>Consultar inventario
                            </h5>
                        </div>
                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#nuevoProducto">
                            <i class="bi bi-plus-circle me-1"></i>Agregar
                        </button>
                    </div>
                    <div class="card-body">

                        <?php
                        include("modal/registro_productos.php");
                        include("modal/editar_productos.php");
                        ?>
                        <form class="row g-3" role="form" id="datos">

                            <div class="col-md-4">
                                <label for="q" class="form-label">Filtrar por código o nombre</label>
                                <input type="text" class="form-control" id="q" placeholder="Código o nombre del producto" onkeyup="load(1);">
                            </div>

                            <div class="col-md-4">
                                <label for="id_categoria" class="form-label">Filtrar por categoría</label>
                                <select class="form-select" name="id_categoria" id="id_categoria" onchange="load(1);">
                                    <option value="">Selecciona una categoría</option>
                                    <?php
                                    $query_categoria = mysqli_query($con, "SELECT * FROM categorias ORDER BY nombre_categoria");
                                    while ($rw = mysqli_fetch_array($query_categoria)) {
                                        echo '<option value="' . $rw['id_categoria'] . '">' . $rw['nombre_categoria'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-12 text-center">
                                <span id="loader"></span>
                            </div>

                            <hr class="my-4">

                            <div class="col-12">
                                <div id="resultados"></div><!-- Datos AJAX -->
                            </div>

                            <div class="col-12">
                                <div class="outer_div"></div><!-- Datos AJAX -->
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </main>

        <?php include("footer.php"); ?>
    </div>

    <?php include("modal/cambiar_password.php"); ?>

    <!-- Scripts -->
    <script type="text/javascript" src="js/productos.js"></script>
    <script>
        function load(page = 1) {
            let q = $("#q").val();
            let id_categoria = $("#id_categoria").val();
            $("#loader").fadeIn('slow');
            $.ajax({
                url: './ajax/buscar_productos.php',
                method: 'GET',
                data: { q: q, id_categoria: id_categoria, page: page },
                success: function(data) {
                    $(".outer_div").html(data);
                    $("#loader").fadeOut('slow');
                }
            });
        }

        function eliminar(id) {
            let q = $("#q").val();
            let id_categoria = $("#id_categoria").val();
            $.ajax({
                type: "GET",
                url: "./ajax/buscar_productos.php",
                data: { id: id, q: q, id_categoria: id_categoria },
                beforeSend: function() {
                    $("#resultados").html("Mensaje: Cargando...");
                },
                success: function(datos) {
                    $("#resultados").html(datos);
                    load(1);
                }
            });
        }

        $(document).ready(function() {
            load(1);

            <?php if (isset($_GET['delete'])): ?>
            eliminar(<?php echo intval($_GET['delete']); ?>);
            <?php endif; ?>

            // Submit nuevo producto
            $("#guardar_producto").off('submit').on('submit', function(event) {
                event.preventDefault();
                $('#guardar_datos').attr("disabled", true);
                const parametros = $(this).serialize();
                $.ajax({
                    type: "POST",
                    url: "ajax/nuevo_producto.php",
                    data: parametros,
                    beforeSend: function() {
                        $("#resultados_ajax_productos").html("Mensaje: Cargando...");
                    },
                    success: function(datos) {
                        $("#resultados_ajax_productos").html(datos);
                        $('#guardar_datos').attr("disabled", false);
                        load(1);
                        if (datos.indexOf('alert-success') > -1) {
                            setTimeout(function() {
                                const modalEl = document.getElementById('nuevoProducto');
                                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                                modal.hide();
                                document.getElementById('guardar_producto').reset();
                                // Limpiar backdrop y clase modal-open si persiste
                                setTimeout(function() {
                                    $(".modal-backdrop").remove();
                                    $("body").removeClass("modal-open").css("overflow", "");
                                }, 500);
                            }, 1200);
                        }
                    },
                    error: function() {
                        $("#resultados_ajax_productos").html('<div class="alert alert-danger">Error al procesar la solicitud</div>');
                        $('#guardar_datos').attr("disabled", false);
                    }
                });
            });
        });
    </script>
</body>
</html>
