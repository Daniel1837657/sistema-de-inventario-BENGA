<!-- Modal -->
<div id="add-stock" class="modal fade" tabindex="-1" aria-labelledby="addStockLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addStockLabel">
          <i class="bi bi-plus-circle me-2"></i>Agregar Stock
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      
      <form method="post" action="" name="add_stock" id="add_stock_form" novalidate>
        <div class="modal-body">

          <div class="row g-3">
            <div class="col-12">
              <label for="producto_info" class="form-label">Producto</label>
              <input
                type="text"
                name="producto_info"
                class="form-control"
                id="producto_info"
                value="<?php echo isset($row['nombre_producto']) ? htmlspecialchars($row['nombre_producto'] . ' (' . $row['codigo_producto'] . ')', ENT_QUOTES, 'UTF-8') : ''; ?>"
                readonly
                aria-describedby="productoHelp">
              <div id="productoHelp" class="form-text">Producto seleccionado para agregar stock</div>
            </div>

            <div class="col-md-6">
              <label for="quantity" class="form-label">Cantidad <span class="text-danger">*</span></label>
              <input
                type="number"
                min="1"
                step="1"
                name="quantity"
                class="form-control"
                id="quantity"
                placeholder="Cantidad a agregar"
                required
                aria-required="true"
                aria-describedby="quantityHelp quantityError">
              <div id="quantityHelp" class="form-text">Ingrese un número mayor o igual a 1</div>
              <div id="quantityError" class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
              <label for="precio_unitario" class="form-label">Precio Unitario</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input
                  type="number"
                  min="0"
                  step="0.01"
                  name="precio_unitario"
                  class="form-control"
                  id="precio_unitario"
                  placeholder="0.00"
                  aria-describedby="precioHelp">
                <span class="input-group-text">COP</span>
              </div>
              <div id="precioHelp" class="form-text">Precio unitario del producto (opcional)</div>
            </div>

            <div class="col-md-6">
              <label for="fecha_ingreso" class="form-label">Fecha Ingreso <span class="text-danger">*</span></label>
              <input
                type="date"
                name="fecha_ingreso"
                class="form-control"
                id="fecha_ingreso"
                value="<?php echo date('Y-m-d'); ?>"
                required
                aria-required="true"
                aria-describedby="fechaHelp">
              <div id="fechaHelp" class="form-text">Fecha de ingreso del stock</div>
            </div>

            <div class="col-md-6">
              <label for="proveedor" class="form-label">Proveedor</label>
              <input
                type="text"
                name="proveedor"
                class="form-control"
                id="proveedor"
                placeholder="Nombre del proveedor"
                maxlength="100"
                aria-describedby="proveedorHelp">
              <div id="proveedorHelp" class="form-text">Proveedor del producto (opcional)</div>
            </div>

            <div class="col-12">
              <label for="reference" class="form-label">Referencia</label>
              <input
                type="text"
                name="reference"
                class="form-control"
                id="reference"
                placeholder="Referencia o nota adicional"
                maxlength="100"
                aria-describedby="referenceHelp">
              <div id="referenceHelp" class="form-text">Referencia o nota adicional (opcional)</div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Cerrar modal">
            <i class="bi bi-x-circle me-1"></i>Cerrar
          </button>
          <button type="submit" class="btn btn-primary" id="btn_guardar_stock">
            <i class="bi bi-plus-circle me-1"></i>Agregar Stock
          </button>
        </div>
      </form>
    </div>
  </div>
</div>