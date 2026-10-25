<?php
require_once __DIR__ . '/classes/Cliente.php';

header('Content-Type: application/json; charset=utf-8');

$codigo = trim($_GET['codigo'] ?? '');

if ($codigo === '' || !ctype_digit($codigo)) {
    echo json_encode(['ok' => false, 'mensaje' => 'Código no válido']);
    exit;
}

$cliente = new Cliente();
$datos = $cliente->obtenerPorId((int) $codigo);

if ($datos) {
    echo json_encode([
        'ok'     => true,
        'codigo' => $datos['id_cliente'],
        'nombre' => $datos['nombre'],
    ]);
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Cliente no encontrado']);
}
