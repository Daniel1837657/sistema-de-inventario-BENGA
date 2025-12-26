<?php
if (isset($con)) {
?>
<!-- Modal -->
<div class="modal fade" id="nuevoCliente" tabindex="-1" aria-labelledby="nuevoClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="nuevoClienteLabel">
                    <i class="bi bi-plus-circle me-2"></i>Agregar nueva categoría
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form method="post" id="guardar_categoria" name="guardar_categoria" novalidate>
                    <div id="resultados_ajax"></div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nombre" id="nombre_label" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre de la categoría" required aria-labelledby="nombre_label">
                        </div>

                        <div class="col-12">
                            <label for="descripcion" id="descripcion_label" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="255" placeholder="Descripción opcional de la categoría" aria-labelledby="descripcion_label"></textarea>
                        </div>
                    </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cerrar
                </button>
                <button type="submit" class="btn btn-success" id="guardar_datos">
                    <i class="bi bi-check-circle me-1"></i>Guardar datos
                </button>
            </div>

                </form>
        </div>
    </div>
</div>

<script>
// AJAX seguro para guardar categoría
document.getElementById('guardar_categoria').addEventListener('submit', function(e) {
    e.preventDefault(); // evita recargar la página
    let form = e.target;
    let formData = new FormData(form);
    fetch('ajax_guardar_categoria.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('resultados_ajax').innerHTML = data;
        form.reset();
        // Cerrar el modal solo si sigue abierto
        var modalElement = document.getElementById('nuevoCliente');
        var modal = bootstrap.Modal.getInstance(modalElement);
        if (modal && modalElement.classList.contains('show')) {
            modal.hide();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('resultados_ajax').innerHTML = '<div class="alert alert-danger">Ocurrió un error al guardar.</div>';
    });
});
</script>
<?php
}
?>
