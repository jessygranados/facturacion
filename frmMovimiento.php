<?php
require_once __DIR__ . '/classMovimiento.php';
require_once __DIR__ . '/mensajes.php';

$ultimos = datos_Movimiento::ultimos(8);
$paginaActiva = 'inventario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ediciones Fares - Cargar inventario</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="js/acciones_script.js"></script>
</head>
<body>

    <?php require __DIR__ . '/menu.php'; ?>

    <div class="contenedor">

        <!-- ===== Panel: Formulario ===== -->
        <div class="panel-formulario">
            <div class="titulo titulo-verde">Cargar inventario</div>
            <form action="insertarMovimiento.php" method="post">

                <div class="campo">
                    <label for="codpro"><b>Id del producto:</b></label>
                    <input type="text" placeholder="Id producto" id="codpro" name="codpro"
                           onkeyup="mostrarProducto(this.value)" autocomplete="off" required>
                </div>

                <div class="campo">
                    <label for="nompro"><b>Producto:</b></label>
                    <input type="text" id="nompro" name="nompro" placeholder="Nombre del producto" readonly>
                </div>

                <div class="campo">
                    <label for="fechmovi"><b>Fecha:</b></label>
                    <input type="date" id="fechmovi" name="fechmovi" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="campo">
                    <label for="cantpro"><b>Cantidad:</b></label>
                    <input type="number" step="1" id="cantpro" name="cantpro" placeholder="Cantidad producto" required>
                    <small class="nota">Positiva = entrada. Negativa = salida.</small>
                </div>

                <button type="submit" class="btn-guardar" name="cguardar">Guardar</button>
            </form>
        </div>

        <!-- ===== Panel: últimos movimientos ===== -->
        <div class="panel-tabla">
            <?php mostrarMensaje(); ?>
            <table>
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Id producto</th>
                        <th>Producto</th>
                        <th>Fecha</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($ultimos) > 0): ?>
                    <?php foreach ($ultimos as $m): ?>
                        <tr>
                            <td><?= $m['idmovimiento'] ?></td>
                            <td><?= $m['id_producto'] ?></td>
                            <td><?= htmlspecialchars($m['nom_producto']) ?></td>
                            <td><?= $m['fecha'] ?></td>
                            <td class="<?= $m['cantidad_movi'] < 0 ? 'cant-salida' : 'cant-entrada' ?>">
                                <?= $m['cantidad_movi'] > 0 ? '+' : '' ?><?= $m['cantidad_movi'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="sin-registros">Aún no hay movimientos registrados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <?php require __DIR__ . '/pie_pagina.php'; ?>
</body>
</html>
