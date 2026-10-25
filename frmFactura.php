<?php
require_once __DIR__ . '/classes/Factura.php';

$factura = new Factura();
$numeroFactura = $factura->siguienteNumero();
$numeroFacturaFormateado = str_pad((string) $numeroFactura, 4, '0', STR_PAD_LEFT);
$paginaActiva = 'facturacion';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ediciones Fares - Sistema de Facturación</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <?php require __DIR__ . '/menu.php'; ?>

    <div class="contenedor">
        <div class="panel-factura">

            <!-- ===== Encabezado: Factura No. ===== -->
            <div class="factura-header">
                <div class="titulo">Sistema de Facturación</div>
                <div class="numero-factura">
                    Factura No. <span id="numeroFactura"><?= $numeroFacturaFormateado ?></span>
                </div>
            </div>

            <!-- ===== Botones Nueva factura / Guardar factura ===== -->
            <div class="factura-botones">
                <button type="button" id="btnNuevaFactura" class="btn-factura">Nueva factura</button>
                <button type="button" id="btnGuardarFactura" class="btn-factura btn-guardar-factura">Guardar factura</button>
            </div>

            <!-- ===== Cuerpo: tabla de la factura + totales ===== -->
            <div class="factura-cuerpo">
                <div class="factura-tabla-wrap">
                    <table id="tablaFactura">
                        <thead>
                            <tr>
                                <th>Id-Producto</th>
                                <th>Cantidad</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Las líneas de la factura se agregan aquí con JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div class="factura-totales">
                    <div class="campo-total">
                        <label>Subtotal:</label>
                        <input type="text" id="subtotal" readonly>
                    </div>
                    <div class="campo-total">
                        <label>IVA:</label>
                        <input type="text" id="iva" readonly>
                    </div>
                    <div class="campo-total">
                        <label>Total a pagar:</label>
                        <input type="text" id="totalapagar" readonly>
                    </div>
                </div>
            </div>

            <!-- ===== Seleccionar producto / Seleccionar cliente ===== -->
            <div class="factura-secciones">

                <div class="seccion-caja">
                    <div class="subtitulo">Seleccionar producto:</div>
                    <div class="seccion-fila">
                        <div class="campo">
                            <label>Código:</label>
                            <input type="text" id="codpro" autocomplete="off">
                        </div>
                        <div class="campo">
                            <label>Cantidad:</label>
                            <input type="number" id="cantidadpro" min="1" step="1">
                        </div>
                        <div class="campo descripcion">
                            <label>Descripción:</label>
                            <input type="text" id="descripcionpro" readonly>
                        </div>
                        <div class="campo">
                            <label>Precio:</label>
                            <input type="text" id="preciopro" readonly>
                        </div>
                        <div class="campo">
                            <label>Total:</label>
                            <input type="text" id="totalpro" readonly>
                        </div>
                    </div>
                    <br>
                    <button type="button" id="btnAgregarProducto" class="btn-factura btn-guardar-factura">Agregar producto</button>
                </div>

                <div class="seccion-caja">
                    <div class="subtitulo">Seleccionar cliente:</div>
                    <div class="seccion-fila">
                        <div class="campo">
                            <label>Código:</label>
                            <input type="text" id="codcli" autocomplete="off">
                        </div>
                        <div class="campo descripcion">
                            <label>Nombre del cliente:</label>
                            <input type="text" id="nombrecliente" readonly>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <?php require __DIR__ . '/pie_pagina.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/factura.js"></script>
</body>
</html>
