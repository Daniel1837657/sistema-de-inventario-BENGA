<?php if (isset($con)) : ?>
<!-- Modal -->
<div class="modal fade" id="myModal2" tabindex="-1" aria-labelledby="editProductLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="editProductLabel">
          <i class="bi bi-pencil-square me-2"></i>Editar producto
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form method="post" id="editar_producto" name="editar_producto" novalidate>
        <div class="modal-body">
          <div id="resultados_ajax2" role="alert" aria-live="polite"></div>

          <div class="row g-3">
            <div class="col-md-6">
              <label for="mod_codigo" class="form-label">Código <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="mod_codigo" name="mod_codigo" placeholder="Código del producto" required />
              <input type="hidden" name="mod_id" id="mod_id" />
            </div>

            <div class="col-md-6">
              <label for="mod_categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
              <select class="form-select" name="mod_categoria" id="mod_categoria" required>
                <option value="">Selecciona una categoría</option>
                <?php 
                  $query_categoria = mysqli_query($con, "SELECT * FROM categorias ORDER BY nombre_categoria");
                  while ($rw = mysqli_fetch_array($query_categoria)) {
                    echo '<option value="' . htmlspecialchars($rw['id_categoria']) . '">' . htmlspecialchars($rw['nombre_categoria']) . '</option>';
                  }
                ?>
              </select>
            </div>

            <div class="col-12">
              <label for="mod_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
              <textarea class="form-control" id="mod_nombre" name="mod_nombre" rows="3" placeholder="Nombre del producto" required></textarea>
            </div>

            <div class="col-md-6">
              <label for="mod_precio" class="form-label">Precio <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" step="0.01" class="form-control" id="mod_precio" name="mod_precio" placeholder="0.00" required />
              </div>
            </div>

            <div class="col-md-6">
              <label for="mod_stock" class="form-label">Stock</label>
              <input type="number" min="0" class="form-control" id="mod_stock" name="mod_stock" placeholder="Inventario inicial" readonly />
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
