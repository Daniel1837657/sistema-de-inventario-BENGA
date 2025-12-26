<footer class="py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0">
                    &copy; <?php echo date('Y'); ?> - Daniel FelipePerdomo Hernández.
                    <a href="http://danielperdomo.pw/" target="_blank" rel="noopener noreferrer" class="text-decoration-underline">
                        Sitios Web y Sistemas
                    </a>
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">Sistema de Inventario BENGA</p>
            </div>
        </div>
    </div>
</footer>

<!-- jQuery (versión moderna compatible con Bootstrap 5) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
    crossorigin="anonymous"></script>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>

<!-- Script para arreglar modales -->
<script src="js/modal-fix.js?v=<?php echo time(); ?>"></script>

<script>
// Rutina universal para limpiar backdrop y restaurar body al cerrar cualquier modal
document.addEventListener('DOMContentLoaded', function() {
    // Escucha el evento global de cierre de modales Bootstrap
    document.querySelectorAll('.modal').forEach(function(modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function() {
            // Elimina cualquier backdrop residual
            document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                backdrop.remove();
            });
            // Restaura el estado del body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
        });
    });
});
</script>

