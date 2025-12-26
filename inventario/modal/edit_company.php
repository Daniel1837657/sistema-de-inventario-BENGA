<!-- Modal para editar información de la empresa -->
<div class="modal fade" id="editCompanyModal" tabindex="-1" aria-labelledby="editCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCompanyModalLabel">
                    <i class="bi bi-building-gear me-2"></i>Editar Información de la Empresa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_company">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="company_name" class="form-label">Nombre de la Empresa</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" 
                                   value="<?php echo $company_data && !empty($company_data['company_name']) ? htmlspecialchars($company_data['company_name']) : ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="nit" class="form-label">NIT</label>
                            <input type="text" class="form-control" id="nit" name="nit" 
                                   value="<?php echo $company_data && !empty($company_data['nit']) ? htmlspecialchars($company_data['nit']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Dirección</label>
                        <textarea class="form-control" id="address" name="address" rows="2"><?php echo $company_data && !empty($company_data['address']) ? htmlspecialchars($company_data['address']) : ''; ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo $company_data && !empty($company_data['phone']) ? htmlspecialchars($company_data['phone']) : ''; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo $company_data && !empty($company_data['email']) ? htmlspecialchars($company_data['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">Sitio Web</label>
                            <input type="url" class="form-control" id="website" name="website" 
                                   value="<?php echo $company_data && !empty($company_data['website']) ? htmlspecialchars($company_data['website']) : ''; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="industry" class="form-label">Industria/Sector</label>
                            <select class="form-select" id="industry" name="industry">
                                <option value="">Seleccionar...</option>
                                <option value="retail" <?php echo $company_data && $company_data['industry'] == 'retail' ? 'selected' : ''; ?>>Retail/Comercio</option>
                                <option value="manufacturing" <?php echo $company_data && $company_data['industry'] == 'manufacturing' ? 'selected' : ''; ?>>Manufactura</option>
                                <option value="services" <?php echo $company_data && $company_data['industry'] == 'services' ? 'selected' : ''; ?>>Servicios</option>
                                <option value="technology" <?php echo $company_data && $company_data['industry'] == 'technology' ? 'selected' : ''; ?>>Tecnología</option>
                                <option value="healthcare" <?php echo $company_data && $company_data['industry'] == 'healthcare' ? 'selected' : ''; ?>>Salud</option>
                                <option value="education" <?php echo $company_data && $company_data['industry'] == 'education' ? 'selected' : ''; ?>>Educación</option>
                                <option value="food" <?php echo $company_data && $company_data['industry'] == 'food' ? 'selected' : ''; ?>>Alimentos</option>
                                <option value="other" <?php echo $company_data && $company_data['industry'] == 'other' ? 'selected' : ''; ?>>Otro</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo $company_data && !empty($company_data['description']) ? htmlspecialchars($company_data['description']) : ''; ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#editCompanyForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: './ajax/update_company_info.php',
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                $('#company-message').html('<div class="alert alert-info"><i class="bi bi-hourglass-split me-2"></i>Actualizando información de la empresa...</div>');
            },
            success: function(response) {
                console.log('Company info response:', response);
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        $('#company-message').html('<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>' + result.message + '</div>');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        $('#company-message').html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>' + result.message + '</div>');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    $('#company-message').html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error en la respuesta del servidor: ' + response + '</div>');
                }
            },
            error: function() {
                $('#company-message').html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error al actualizar la información</div>');
            }
        });
    });
});
</script>
