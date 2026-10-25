-- Solo si ya cargaste los datos de ejemplo antiguos:
-- recalcula precio_venta = costo + porcentaje para todos los productos.
USE ediciones_fares;
UPDATE inventario SET precio_venta = ROUND(costo * (1 + porc_venta / 100), 2);
