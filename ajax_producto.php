<?php
require_once __DIR__ . '/classes/Producto.php';

header('Content-Type: application/json; charset=utf-8');

$codigo = trim($_GET['codigo'] ?? '');

if ($codigo === '') {
    echo json_encode(['ok' => false, 'mensaje' => 'Código no enviado']);
    exit;
}

$producto = new Producto();
$datos = $producto->buscarPorCodigo($codigo);

if ($datos) {
    echo json_encode([
        'ok'          => true,
        'codigo'      => $datos['codigo'],
        'descripcion' => $datos['nom_producto'],
        'precio'      => (float) $datos['precio_venta'],
        'stock'       => (int) $datos['stock'],
    ]);
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Producto no encontrado']);
}
