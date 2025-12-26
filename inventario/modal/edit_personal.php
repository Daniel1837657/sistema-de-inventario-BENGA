<!-- Modal para editar información personal -->
<div class="modal fade" id="editPersonalModal" tabindex="-1" aria-labelledby="editPersonalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPersonalModalLabel">
                    <i class="bi bi-person-gear me-2"></i>Editar Información Personal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_personal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstname" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" 
                                   value="<?php echo htmlspecialchars($user_data['firstname']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastname" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" 
                                   value="<?php echo htmlspecialchars($user_data['lastname']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_name" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="user_name" name="user_name" 
                                   value="<?php echo htmlspecialchars($user_data['user_name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="user_email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="user_email" name="user_email" 
                                   value="<?php echo htmlspecialchars($user_data['user_email']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo isset($user_data['phone']) ? htmlspecialchars($user_data['phone']) : ''; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">Cargo</label>
                            <input type="text" class="form-control" id="position" name="position" 
                                   value="<?php echo isset($user_data['position']) ? htmlspecialchars($user_data['position']) : ''; ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#editPersonalForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: './ajax/update_personal_info.php',
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                $('#personal-message').html('<div class="alert alert-info"><i class="bi bi-hourglass-split me-2"></i>Actualizando información...</div>');
            },
            success: function(response) {
                console.log('Personal info response:', response);
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        $('#personal-message').html('<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>' + result.message + '</div>');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        $('#personal-message').html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>' + result.message + '</div>');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    $('#personal-message').html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error en la respuesta del servidor: ' + response + '</div>');
                }
            },
            error: function() {
                $('#personal-message').html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error al actualizar la información</div>');
            }
        });
    });
});
</script>
