<?php
session_start();

// Control de acceso más claro y seguro
if (empty($_SESSION['user_login_status']) || $_SESSION['user_login_status'] !== 1) {
    header("Location: login.php");
    exit;
}

// Carga configuraciones y conexión (ideal que lancen excepciones si fallan)
require_once "config/db.php";
require_once "config/conexion.php";

$active_categoria = "active";
$title = "BENGA | Control de Inventario";

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
                            <i class="bi bi-tags me-2"></i>Gestionar Categorías
                        </h5>
                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#nuevoCliente">
                            <i class="bi bi-plus-circle me-1"></i>Nueva Categoría
                        </button>
                    </div>
                    <div class="card-body">
                        <?php
                        include("modal/registro_categorias.php");
                        include("modal/editar_categorias.php");
                        include("modal/cambiar_password.php");
                        ?>
                        <form id="datos_cotizacion" class="row g-3" role="form" autocomplete="off" onsubmit="return false;">
                            <div class="col-md-4">
                                <label for="q" class="form-label">Nombre de la categoría</label>
                                <input
                                    type="search"
                                    class="form-control"
                                    id="q"
                                    name="q"
                                    placeholder="Buscar categorías..."
                                    oninput="load(1);"
                                    aria-label="Buscar categorías"
                                    autocomplete="off"
                                    autofocus
                                >
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
                                <span id="loader" aria-live="polite"></span>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div id="resultados" role="region" aria-live="polite"></div>
                        <div class="outer_div"></div>
                    </div>
                </div>
            </div>
        </main>

        <?php include("footer.php"); ?>
    </div>

    <script src="js/categorias.js"></script>
</body>
</html>
