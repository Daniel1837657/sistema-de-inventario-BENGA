<!-- Modal -->
<div id="remove-stock" class="modal fade" tabindex="-1" aria-labelledby="removeStockLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="removeStockLabel">
          <i class="bi bi-dash-circle me-2"></i>Eliminar Stock
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form method="post" name="remove_stock_form" id="remove_stock_form" novalidate>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="quantity_remove" class="form-label">Cantidad <span class="text-danger">*</span></label>
              <input type="number" min="1" name="quantity_remove" class="form-control" id="quantity_remove" placeholder="Cantidad" required aria-required="true" aria-describedby="quantityRemoveHelp" />
              <div id="quantityRemoveHelp" class="form-text">Cantidad a eliminar del stock</div>
            </div>

            <div class="col-md-6">
              <label for="reference_remove" class="form-label">Referencia</label>
              <input type="text" name="reference_remove" class="form-control" id="reference_remove" placeholder="Referencia" aria-describedby="referenceRemoveHelp" />
              <div id="referenceRemoveHelp" class="form-text">Motivo o referencia de la eliminación (opcional)</div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Cerrar modal">
            <i class="bi bi-x-circle me-1"></i>Cerrar
          </button>
          <button type="submit" class="btn btn-danger">
            <i class="bi bi-dash-circle me-1"></i>Eliminar Stock
          </button>
        </div>

      </form>
    </div>
  </div>
</div>