<!-- Modal para subir imagen de perfil -->
<div class="modal fade" id="uploadImageModal" tabindex="-1" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadImageModalLabel">
                    <i class="bi bi-camera me-2"></i>Cambiar Foto de Perfil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img src="<?php echo !empty($user_data['profile_image']) ? 'uploads/profiles/' . htmlspecialchars($user_data['profile_image'], ENT_QUOTES, 'UTF-8') : 'https://via.placeholder.com/150x150/667eea/ffffff?text=IMG'; ?>" 
                             alt="Vista previa" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #dee2e6;">
                    </div>
                    
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Seleccionar imagen</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" required>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>Subir Imagen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Vista previa de imagen
    $('#profileImage').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Subir imagen
    $('#uploadImageForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const progressBar = $('#upload-progress .progress-bar');
        
        $('#upload-progress').show();
        $('#upload-message').empty();
        
        $.ajax({
            url: './ajax/upload_profile_image.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        const percentComplete = (evt.loaded / evt.total) * 100;
                        progressBar.css('width', percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                console.log('Upload response:', response);
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        $('#upload-message').html('<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>' + result.message + '</div>');
                        // Actualizar imagen inmediatamente
                        if (result.image_url) {
                            $('.profile-avatar').attr('src', result.image_url + '?t=' + new Date().getTime());
                            $('#imagePreview').attr('src', result.image_url + '?t=' + new Date().getTime());
                        }
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $('#upload-message').html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>' + result.message + '</div>');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    $('#upload-message').html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error en la respuesta del servidor</div>');
                }
                $('#upload-progress').hide();
            },
            error: function() {
                $('#upload-message').html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error al subir la imagen</div>');
                $('#upload-progress').hide();
            }
        });
    });
});
</script>
