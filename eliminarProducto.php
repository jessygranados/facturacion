<?php
require_once __DIR__ . '/classProducto.php';

$pagina = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;
$msg = 'datos_invalidos';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $actual = datosProductos::consultarProductoCod($id)[0] ?? null;

    try {
        $producto = new datosProductos($id);
        $producto->eliminarProducto();
        $msg = 'producto_eliminado';

        // Borra también el archivo de imagen
        if ($actual && !empty($actual->Imagen)) {
            @unlink(__DIR__ . '/imagenes/' . basename($actual->Imagen));
        }
    } catch (PDOException $e) {
        // Llave foránea: el producto ya se usó en facturas o movimientos
        $msg = 'producto_en_uso';
    }
}

header('Location: frmProducto.php?msg=' . $msg . '&pagina=' . $pagina);
exit;
