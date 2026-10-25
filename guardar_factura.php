<?php
require_once __DIR__ . '/classes/Factura.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

$idCliente   = (int) ($_POST['id_cliente'] ?? 0);
$detalleJson = $_POST['detalle'] ?? '[]';
$detalle     = json_decode($detalleJson, true);

if ($idCliente <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'Debe seleccionar un cliente válido']);
    exit;
}

if (!is_array($detalle) || empty($detalle)) {
    echo json_encode(['ok' => false, 'mensaje' => 'La factura no tiene productos agregados']);
    exit;
}

// Subtotal, IVA y total se calculan en el servidor con los precios de la base de datos.
$factura = new Factura();
$numeroFactura = $factura->guardar($idCliente, $detalle);

if ($numeroFactura !== false) {
    echo json_encode([
        'ok'             => true,
        'numero_factura' => $numeroFactura,
        'siguiente'      => $numeroFactura + 1,
    ]);
} else {
    echo json_encode(['ok' => false, 'mensaje' => $factura->getError() ?: 'No se pudo guardar la factura']);
}
