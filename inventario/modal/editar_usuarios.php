<?php if (isset($con)) : ?>
	<!-- Modal -->
	<div class="modal fade" id="myModal2" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">

				<div class="modal-header bg-warning text-dark">
					<h5 class="modal-title" id="editUserLabel">
						<i class="bi bi-person-gear me-2"></i>Editar usuario
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
				</div>

				<form method="post" id="editar_usuario" name="editar_usuario" novalidate>
					<div class="modal-body">
						<div id="resultados_ajax2" role="alert" aria-live="polite"></div>

						<div class="row g-3">
							<div class="col-md-6">
								<label for="firstname2" class="form-label">Nombres <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="firstname2" name="firstname2" placeholder="Nombres" required />
								<input type="hidden" id="mod_id" name="mod_id" />
							</div>

							<div class="col-md-6">
								<label for="lastname2" class="form-label">Apellidos <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="lastname2" name="lastname2" placeholder="Apellidos" required />
							</div>

							<div class="col-md-6">
								<label for="user_name2" class="form-label">Usuario <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="user_name2" name="user_name2" placeholder="Usuario" pattern="[a-zA-Z0-9]{2,64}" title="Sólo letras y números, 2-64 caracteres" required />
							</div>

							<div class="col-md-6">
								<label for="user_email2" class="form-label">Email <span class="text-danger">*</span></label>
								<input type="email" class="form-control" id="user_email2" name="user_email2" placeholder="Correo electrónico" required />
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-warning" id="actualizar_datos">
							<i class="bi bi-check-circle me-1"></i>Actualizar datos
						</button>
					</div>
				</form>

			</div>
		</div>
	</div>
<?php endif; ?>