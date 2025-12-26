-- Tabla para movimientos de stock
CREATE TABLE IF NOT EXISTS `movimientos_stock` (
  `id_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada','salida') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `observaciones` text,
  `fecha_movimiento` datetime NOT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY (`id_movimiento`),
  KEY `idx_producto` (`id_producto`),
  KEY `idx_fecha` (`fecha_movimiento`),
  KEY `idx_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Agregar campos adicionales a la tabla productos si no existen
ALTER TABLE `productos` 
ADD COLUMN IF NOT EXISTS `stock_minimo` int(11) DEFAULT 10,
ADD COLUMN IF NOT EXISTS `fecha_vencimiento` date NULL,
ADD COLUMN IF NOT EXISTS `codigo_barras` varchar(50) NULL,
ADD COLUMN IF NOT EXISTS `proveedor` varchar(255) NULL;

-- Índices adicionales para mejor rendimiento
ALTER TABLE `productos` 
ADD INDEX IF NOT EXISTS `idx_stock` (`stock`),
ADD INDEX IF NOT EXISTS `idx_codigo_barras` (`codigo_barras`),
ADD INDEX IF NOT EXISTS `idx_vencimiento` (`fecha_vencimiento`);

-- Insertar algunos movimientos de ejemplo (opcional)
INSERT IGNORE INTO `movimientos_stock` (`id_producto`, `tipo_movimiento`, `cantidad`, `motivo`, `fecha_movimiento`, `usuario_id`) 
SELECT 
    p.id_producto,
    'entrada',
    p.stock,
    'Stock inicial',
    NOW(),
    1
FROM productos p 
WHERE p.stock > 0;
