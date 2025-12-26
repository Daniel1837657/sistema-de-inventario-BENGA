<?php if (isset($con)) : ?>
	<!-- Modal -->
	<div class="modal fade" id="myModal2" tabindex="-1" aria-labelledby="editCategoryLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">

				<div class="modal-header bg-warning text-dark">
					<h5 class="modal-title" id="editCategoryLabel">
						<i class="bi bi-pencil-square me-2"></i>Editar categoría
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
				</div>

				<form method="post" id="editar_categoria" name="editar_categoria" novalidate>
					<div class="modal-body">
						<div id="resultados_ajax2" role="alert" aria-live="polite"></div>

						<div class="row g-3">
							<div class="col-12">
								<label for="mod_nombre" class="form-label">
									Nombre <span class="text-danger">*</span>
								</label>
								<input type="text" class="form-control" id="mod_nombre" name="mod_nombre" required placeholder="Nombre de la categoría">
								<input type="hidden" name="mod_id" id="mod_id">
							</div>

							<div class="col-12">
								<label for="mod_descripcion" class="form-label">
									Descripción
								</label>
								<textarea class="form-control" id="mod_descripcion" name="mod_descripcion" rows="3" placeholder="Descripción de la categoría"></textarea>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
							<i class="bi bi-x-circle me-1"></i>Cerrar
						</button>
						<button type="submit" class="btn btn-warning" id="actualizar_datos">
							<i class="bi bi-check-circle me-1"></i>Actualizar datos
						</button>
					</div>
				</form>

			</div>
		</div>
	</div>
<?php endif; ?>