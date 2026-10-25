<?php
require_once __DIR__ . '/classProducto.php';
require_once __DIR__ . '/mensajes.php';

// ---- Página actual (paginación) ----
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
if ($pagina < 1) {
    $pagina = 1;
}

$listaProductos = datosProductos::listarProductos($pagina);
$totalPaginas   = datosProductos::totalPaginas();

// ---- Si viene "editar", se precarga el formulario ----
$editar = null;
if (isset($_GET['editar'])) {
    $res = datosProductos::consultarProductoCod((int) $_GET['editar']);
    $editar = $res[0] ?? null;
}

$paginaActiva = 'inventario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ediciones Fares - Productos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <?php require __DIR__ . '/menu.php'; ?>

    <div class="contenedor">

        <!-- ===== Panel: Formulario ===== -->
        <div class="panel-formulario">
            <div class="titulo"><?= $editar ? 'Modificar producto' : 'Crear producto' ?></div>
            <form action="guardarProducto.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="pagina" value="<?= $pagina ?>">

                <div class="campo">
                    <label>Código</label>
                    <input type="text" name="codigo" value="<?= $editar->codigo ?? '' ?>"
                           placeholder="Se genera automáticamente" readonly>
                </div>

                <div class="campo">
                    <label>Nombre del producto</label>
                    <input type="text" name="nombre" placeholder="Nombre del producto"
                           value="<?= htmlspecialchars($editar->nom_producto ?? '') ?>" required>
                </div>

                <div class="fila-doble">
                    <div class="campo">
                        <label>Costo</label>
                        <input type="number" step="0.01" min="0" name="costo" id="costo" placeholder="0.00"
                               value="<?= $editar->costo ?? '' ?>" required>
                    </div>
                    <div class="campo">
                        <label>% de venta</label>
                        <input type="number" step="0.01" min="0" name="porc_venta" id="porc_venta" placeholder="0"
                               value="<?= $editar->porc_venta ?? '' ?>" required>
                    </div>
                </div>

                <div class="campo">
                    <label>Precio de venta (calculado)</label>
                    <input type="text" id="precio_venta" value="<?= isset($editar->precio_venta) ? number_format((float) $editar->precio_venta, 2, '.', '') : '' ?>" readonly>
                </div>

                <div class="campo">
                    <label>Fecha</label>
                    <input type="date" name="fecha" value="<?= $editar->Fecha ?? date('Y-m-d') ?>" required>
                </div>

                <div class="campo">
                    <label>Imagen <?= $editar ? '(deje vacío para conservar la actual)' : '' ?></label>
                    <input type="file" name="imagen" accept="image/jpeg,image/png,image/gif,image/webp">
                    <?php if (!empty($editar->Imagen)): ?>
                        <img class="miniatura-form" src="imagenes/<?= htmlspecialchars($editar->Imagen) ?>" alt="Imagen actual">
                    <?php endif; ?>
                </div>

                <?php if ($editar): ?>
                    <p class="nota">Stock actual: <strong><?= (int) $editar->stock ?></strong>.
                       Para cambiarlo use <a href="frmMovimiento.php">Agregar inventario</a>.</p>
                <?php endif; ?>

                <button type="submit" class="btn-guardar">Guardar</button>
                <?php if ($editar): ?>
                    <a class="btn-cancelar" href="frmProducto.php?pagina=<?= $pagina ?>">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- ===== Panel: Tabla ===== -->
        <div class="panel-tabla">
            <?php mostrarMensaje(); ?>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Costo</th>
                        <th>% venta</th>
                        <th>Precio venta</th>
                        <th>Imagen</th>
                        <th>Stock</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($listaProductos) > 0): ?>
                    <?php foreach ($listaProductos as $p): ?>
                        <tr>
                            <td><?= $p['codigo'] ?></td>
                            <td><?= htmlspecialchars($p['nom_producto']) ?></td>
                            <td><?= number_format((float) $p['costo'], 2) ?></td>
                            <td><?= rtrim(rtrim(number_format((float) $p['porc_venta'], 2, '.', ''), '0'), '.') ?></td>
                            <td><?= number_format((float) $p['precio_venta'], 2) ?></td>
                            <td>
                                <?php if (!empty($p['Imagen'])): ?>
                                    <img class="miniatura" src="imagenes/<?= htmlspecialchars($p['Imagen']) ?>" alt="">
                                <?php else: ?>
                                    <span class="sin-imagen">&mdash;</span>
                                <?php endif; ?>
                            </td>
                            <td><?= (int) $p['stock'] ?></td>
                            <td><?= $p['Fecha'] ?></td>
                            <td>
                                <div class="acciones">
                                    <a class="btn-accion btn-editar"
                                       href="frmProducto.php?editar=<?= $p['codigo'] ?>&pagina=<?= $pagina ?>"
                                       title="Editar">&#9998;</a>
                                    <a class="btn-accion btn-eliminar"
                                       href="eliminarProducto.php?id=<?= $p['codigo'] ?>&pagina=<?= $pagina ?>"
                                       title="Eliminar"
                                       onclick="return confirm('¿Desea eliminar este producto?');">&#128465;</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="sin-registros">No hay productos registrados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <!-- ===== Paginación ===== -->
            <?php if ($totalPaginas > 1): ?>
                <div class="paginacion">
                    <?php if ($pagina > 1): ?>
                        <a href="frmProducto.php?pagina=<?= $pagina - 1 ?>">&laquo;</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <?php if ($i == $pagina): ?>
                            <span class="activa"><?= $i ?></span>
                        <?php else: ?>
                            <a href="frmProducto.php?pagina=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($pagina < $totalPaginas): ?>
                        <a href="frmProducto.php?pagina=<?= $pagina + 1 ?>">&raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <?php require __DIR__ . '/pie_pagina.php'; ?>

    <script>
        // Vista previa del precio de venta = costo + % de ganancia
        const costo = document.getElementById('costo');
        const porc  = document.getElementById('porc_venta');
        const precio = document.getElementById('precio_venta');
        function calcularPrecio() {
            const c = parseFloat(costo.value) || 0;
            const p = parseFloat(porc.value) || 0;
            precio.value = (c > 0 || p > 0) ? (c * (1 + p / 100)).toFixed(2) : '';
        }
        costo.addEventListener('input', calcularPrecio);
        porc.addEventListener('input', calcularPrecio);
    </script>
</body>
</html>
