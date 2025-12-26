<?php
if (isset($con)) {
?>
<!-- Modal -->
<div class="modal fade" id="nuevoProducto" tabindex="-1" aria-labelledby="nuevoProductoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="nuevoProductoLabel">
          <i class="bi bi-plus-circle me-2"></i>Agregar nuevo producto
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <form method="post" id="guardar_producto" name="guardar_producto" novalidate>
          <div id="resultados_ajax_productos"></div>

          <div class="row g-3">
            <div class="col-md-6">
              <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Código del producto" required aria-required="true" />
              <div class="invalid-feedback" id="codigoError">El código es obligatorio.</div>
            </div>

            <div class="col-md-6">
              <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
              <select class="form-select" id="categoria" name="categoria" required aria-required="true">
                <option value="">Selecciona una categoría</option>
                <?php
                $query_categoria = mysqli_query($con, "SELECT * FROM categorias ORDER BY nombre_categoria");
                while ($rw = mysqli_fetch_array($query_categoria)) {
                  echo '<option value="' . $rw['id_categoria'] . '">' . htmlspecialchars($rw['nombre_categoria']) . '</option>';
                }
                ?>
              </select>
              <div class="invalid-feedback" id="categoriaError">La categoría es obligatoria.</div>
            </div>

            <div class="col-12">
              <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
              <textarea class="form-control" id="nombre" name="nombre" placeholder="Nombre del producto" maxlength="255" required aria-required="true" rows="3"></textarea>
              <div class="invalid-feedback" id="nombreError">El nombre es obligatorio.</div>
            </div>

            <div class="col-md-6">
              <label for="precio" class="form-label">Precio <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="text" class="form-control" id="precio" name="precio" placeholder="0.00" maxlength="15" required aria-required="true" />
              <div class="invalid-feedback" id="precioError">El precio es obligatorio y debe ser mayor a 0.</div>
              </div>
            </div>

            <div class="col-md-6">
              <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
              <input type="number" min="0" class="form-control" id="stock" name="stock" placeholder="0" maxlength="8" required aria-required="true" />
              <div class="invalid-feedback" id="stockError">El stock es obligatorio y debe ser mayor o igual a 0.</div>
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
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('guardar_producto');
  if (form) {
    form.addEventListener('submit', function(e) {
      let valid = true;
      // Código
      const codigo = document.getElementById('codigo');
      if (!codigo.value.trim()) {
        codigo.classList.add('is-invalid');
        document.getElementById('codigoError').style.display = 'block';
        valid = false;
      } else {
        codigo.classList.remove('is-invalid');
        document.getElementById('codigoError').style.display = 'none';
      }
      // Categoría
      const categoria = document.getElementById('categoria');
      if (!categoria.value) {
        categoria.classList.add('is-invalid');
        document.getElementById('categoriaError').style.display = 'block';
        valid = false;
      } else {
        categoria.classList.remove('is-invalid');
        document.getElementById('categoriaError').style.display = 'none';
      }
      // Nombre
      const nombre = document.getElementById('nombre');
      if (!nombre.value.trim()) {
        nombre.classList.add('is-invalid');
        document.getElementById('nombreError').style.display = 'block';
        valid = false;
      } else {
        nombre.classList.remove('is-invalid');
        document.getElementById('nombreError').style.display = 'none';
      }
      // Precio
      const precio = document.getElementById('precio');
      if (!precio.value.trim() || isNaN(precio.value) || parseFloat(precio.value) <= 0) {
        precio.classList.add('is-invalid');
        document.getElementById('precioError').style.display = 'block';
        valid = false;
      } else {
        precio.classList.remove('is-invalid');
        document.getElementById('precioError').style.display = 'none';
      }
      // Stock
      const stock = document.getElementById('stock');
      if (stock.value === '' || isNaN(stock.value) || parseInt(stock.value) < 0) {
        stock.classList.add('is-invalid');
        document.getElementById('stockError').style.display = 'block';
        valid = false;
      } else {
        stock.classList.remove('is-invalid');
        document.getElementById('stockError').style.display = 'none';
      }
      if (!valid) {
        e.preventDefault();
      }
    });
  }
});
</script>

<?php
}
?>
