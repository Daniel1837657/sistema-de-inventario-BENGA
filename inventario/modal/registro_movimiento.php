<?php if (isset($con)) : ?>
<!-- Modal -->
<div class="modal fade" id="nuevoMovimiento" tabindex="-1" aria-labelledby="nuevoMovimientoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="nuevoMovimientoLabel">
                    <i class="bi bi-arrow-left-right me-2"></i>Registrar Movimiento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form method="post" id="guardar_movimiento" name="guardar_movimiento" novalidate>
                <div class="modal-body">
                    <div id="resultados_ajax_movimiento" role="alert" aria-live="polite"></div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="producto_mov" class="form-label">Producto <span class="text-danger">*</span></label>
                            <select class="form-select" id="producto_mov" name="producto_mov" required>
                                <option value="">Selecciona un producto</option>
                                <?php
                                $query_productos = mysqli_query($con, "SELECT id_producto, codigo_producto, nombre_producto, stock FROM productos ORDER BY nombre_producto");
                                while ($row = mysqli_fetch_array($query_productos)) {
                                    echo '<option value="' . $row['id_producto'] . '" data-stock="' . $row['stock'] . '">' . 
                                         htmlspecialchars($row['codigo_producto'] . ' - ' . $row['nombre_producto'] . ' (Stock: ' . $row['stock'] . ')') . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="tipo_mov" class="form-label">Tipo de Movimiento <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipo_mov" name="tipo_mov" required>
                                <option value="">Selecciona tipo</option>
                                <option value="entrada">Entrada (+)</option>
                                <option value="salida">Salida (-)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="cantidad_mov" class="form-label">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="cantidad_mov" name="cantidad_mov" min="1" required>
                            <div class="form-text" id="stock-info"></div>
                        </div>

                        <div class="col-12">
                            <label for="motivo_mov" class="form-label">Motivo <span class="text-danger">*</span></label>
                            <select class="form-select" id="motivo_mov" name="motivo_mov" required>
                                <option value="">Selecciona motivo</option>
                                <optgroup label="Entradas">
                                    <option value="Compra">Compra</option>
                                    <option value="Devolución cliente">Devolución de cliente</option>
                                    <option value="Ajuste inventario">Ajuste de inventario</option>
                                    <option value="Reabastecimiento">Reabastecimiento</option>
                                </optgroup>
                                <optgroup label="Salidas">
                                    <option value="Venta">Venta</option>
                                    <option value="Devolución proveedor">Devolución a proveedor</option>
                                    <option value="Producto dañado">Producto dañado</option>
                                    <option value="Producto vencido">Producto vencido</option>
                                    <option value="Ajuste inventario">Ajuste de inventario</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="observaciones_mov" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones_mov" name="observaciones_mov" rows="2" placeholder="Observaciones adicionales (opcional)"></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-info" id="guardar_movimiento_btn">
                        <i class="bi bi-check-circle me-1"></i>Registrar Movimiento
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Incluye jQuery antes de usar $ -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Actualizar información de stock cuando se selecciona producto
        $('#producto_mov').change(function() {
            var stock = $(this).find(':selected').data('stock');
            if (stock !== undefined) {
                $('#stock-info').text('Stock actual: ' + stock + ' unidades');
            } else {
                $('#stock-info').text('');
            }
        });

        // Filtrar motivos según tipo de movimiento
        $('#tipo_mov').change(function() {
            var tipo = $(this).val();
            var motivos = $('#motivo_mov optgroup');
            
            motivos.hide();
            $('#motivo_mov').val('');
            
            if (tipo === 'entrada') {
                $('#motivo_mov optgroup[label="Entradas"]').show();
            } else if (tipo === 'salida') {
                $('#motivo_mov optgroup[label="Salidas"]').show();
            }
        });

        // Manejar envío del formulario
        $("#guardar_movimiento").submit(function (event) {
            event.preventDefault();
            $('#guardar_movimiento_btn').attr("disabled", true);
            var parametros = $(this).serialize();
            $.ajax({
                type: "POST",
                url: "ajax/nuevo_movimiento.php",
                data: parametros,
                beforeSend: function () {
                    $("#resultados_ajax_movimiento").html("Mensaje: Cargando...");
                },
                success: function (datos) {
                    $("#resultados_ajax_movimiento").html(datos);
                    $('#guardar_movimiento_btn').attr("disabled", false);
                    if (datos.indexOf('alert-success') > -1) {
                        // Solo cerrar el modal si Bootstrap no lo hace automáticamente
                        var modalElement = document.getElementById('nuevoMovimiento');
                        var modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal && $(modalElement).hasClass('show')) {
                            modal.hide();
                        }
                        $('#guardar_movimiento')[0].reset();
                        $('#stock-info').text('');
                        loadMovimientos();
                    }
                },
                error: function() {
                    $("#resultados_ajax_movimiento").html('<div class="alert alert-danger">Error al procesar la solicitud</div>');
                    $('#guardar_movimiento_btn').attr("disabled", false);
                }
            });
        });
    });
</script>
<?php endif; ?>
