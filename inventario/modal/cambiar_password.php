<?php if (isset($con)) : ?>
	<!-- Modal -->
	<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="changePasswordLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">

				<div class="modal-header">
					<h5 class="modal-title" id="changePasswordLabel">
						<i class="bi bi-key"></i> Cambiar contraseña
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
				</div>

				<form method="post" id="editar_password" name="editar_password" novalidate>
					<div class="modal-body">
						<div id="resultados_ajax3" role="alert" aria-live="polite"></div>

						<div class="mb-3">
							<label for="user_password_new3" class="form-label">
								Nueva contraseña <span class="text-danger">*</span>
							</label>
							<input
								type="password"
								class="form-control"
								id="user_password_new3"
								name="user_password_new3"
								placeholder="Nueva contraseña"
								required
								aria-required="true">
							<input type="hidden" id="user_id_mod" name="user_id_mod" />
							<div class="form-text">
								<strong>Requisitos:</strong> Mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.
							</div>
						</div>

						<div class="mb-3">
							<label for="user_password_repeat3" class="form-label">
								Repite contraseña <span class="text-danger">*</span>
							</label>
							<input
								type="password"
								class="form-control"
								id="user_password_repeat3"
								name="user_password_repeat3"
								placeholder="Repite contraseña"
								pattern=".{6,}"
								required
								aria-required="true">
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Cerrar modal">Cerrar</button>
						<button type="submit" class="btn btn-primary" id="actualizar_datos3">Cambiar contraseña</button>
					</div>
				</form>

			</div>
		</div>
	</div>
<?php endif; ?>