<?php
if (isset($con)) {
?>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="nuevoUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="nuevoUsuarioLabel">
                    <i class="bi bi-person-plus me-2"></i>Agregar nuevo usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form method="post" id="guardar_usuario" name="guardar_usuario" novalidate>
                    <div id="resultados_ajax"></div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="firstname" class="form-label">Nombres <span class="text-danger">*</span>
                                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Nombres" required />
                            </label>
                        </div>

                        <div class="col-md-6">
                            <label for="lastname" class="form-label">Apellidos <span class="text-danger">*</span>
                                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Apellidos" required />
                            </label>
                        </div>

                        <div class="col-md-6">
                            <label for="user_name" class="form-label">Usuario <span class="text-danger">*</span>
                                <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Usuario" required />
                            </label>
                        </div>

                        <div class="col-md-6">
                            <label for="user_email" class="form-label">Email <span class="text-danger">*</span>
                                <input type="email" class="form-control" id="user_email" name="user_email" placeholder="correo@ejemplo.com" required />
                            </label>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contraseña <span class="text-danger">*</span>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="user_password_new" name="user_password_new" placeholder="Contraseña" required />
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword1" aria-label="Mostrar/Ocultar contraseña">
                                        <i class="bi bi-eye" id="eyeIcon1"></i>
                                    </button>
                                </div>
                            </label>
                            <small class="form-text text-muted">Mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Repite contraseña <span class="text-danger">*</span>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="user_password_repeat" name="user_password_repeat" placeholder="Repite contraseña" required />
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword2" aria-label="Mostrar/Ocultar contraseña">
                                        <i class="bi bi-eye" id="eyeIcon2"></i>
                                    </button>
                                </div>
                            </label>
                        </div>
                    </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cerrar
                </button>
                <button type="submit" class="btn btn-primary" id="guardar_datos">
                    <i class="bi bi-check-circle me-1"></i>Guardar datos
                </button>
            </div>

                </form>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility for user registration modal
    document.getElementById('togglePassword1').addEventListener('click', function() {
        const password = document.getElementById('user_password_new');
        const eyeIcon = document.getElementById('eyeIcon1');
        if (password.type === 'password') {
            password.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            password.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });
    document.getElementById('togglePassword2').addEventListener('click', function() {
        const password = document.getElementById('user_password_repeat');
        const eyeIcon = document.getElementById('eyeIcon2');
        if (password.type === 'password') {
            password.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            password.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });
    // Si tienes AJAX para guardar usuario, cierra el modal solo si sigue abierto
    // Ejemplo:
    // var modalElement = document.getElementById('myModal');
    // var modal = bootstrap.Modal.getInstance(modalElement);
    // if (modal && modalElement.classList.contains('show')) {
    //     modal.hide();
    // }
</script>
<?php
}
?>
