// Script para estabilizar modales Bootstrap 5 - Anti-parpadeo
$(document).ready(function() {
    // Solo enfocar el primer input visible al mostrar el modal
    $('.modal').on('shown.bs.modal', function() {
        const firstInput = $(this).find('input:visible:first');
        if (firstInput.length) {
            firstInput.focus();
        }
    });
    // Permitir cierre al hacer click en el fondo, usando Bootstrap
    $(document).on('click', '.modal', function(e) {
        if (e.target === this) {
            const modal = bootstrap.Modal.getInstance(this);
            if (modal) {
                modal.hide();
            }
        }
    });
});
