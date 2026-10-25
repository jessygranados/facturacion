<?php
require_once __DIR__ . '/classProducto.php';

const CARPETA_IMAGENES = __DIR__ . '/imagenes';

/**
 * Guarda la imagen subida como imagenes/<codigo>.<ext> (como en el libro: 100.jpg).
 * Devuelve el nombre del archivo, null si no se subió nada, o false si es inválida.
 */
function subirImagen(array $archivo, int $codigo)
{
    if (!isset($archivo['error']) || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($archivo['error'] !== UPLOAD_ERR_OK || $archivo['size'] > 2 * 1024 * 1024) {
        return false;
    }

    $info = @getimagesize($archivo['tmp_name']);
    $permitidos = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_GIF  => 'gif',
        IMAGETYPE_WEBP => 'webp',
    ];
    if ($info === false || !isset($permitidos[$info[2]])) {
        return false;
    }

    if (!is_dir(CARPETA_IMAGENES)) {
        mkdir(CARPETA_IMAGENES, 0755, true);
    }

    // Quitar imágenes anteriores del mismo producto (otra extensión)
    foreach (glob(CARPETA_IMAGENES . '/' . $codigo . '.*') ?: [] as $vieja) {
        @unlink($vieja);
    }

    $nombre = $codigo . '.' . $permitidos[$info[2]];
    if (!move_uploaded_file($archivo['tmp_name'], CARPETA_IMAGENES . '/' . $nombre)) {
        return false;
    }
    return $nombre;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: frmProducto.php');
    exit;
}

$pagina  = max(1, (int) ($_POST['pagina'] ?? 1));
$codigo  = (int) ($_POST['codigo'] ?? 0);
$nombre  = trim($_POST['nombre'] ?? '');
$costo   = (float) ($_POST['costo'] ?? 0);
$porc    = (float) ($_POST['porc_venta'] ?? 0);
$fecha   = $_POST['fecha'] ?? '';

$f = DateTime::createFromFormat('Y-m-d', $fecha);
if (!$f || $f->format('Y-m-d') !== $fecha) {
    $fecha = date('Y-m-d');
}

if ($nombre === '' || $costo < 0 || $porc < 0) {
    header('Location: frmProducto.php?msg=datos_invalidos&pagina=' . $pagina);
    exit;
}

$precio = datosProductos::calcularPrecio($costo, $porc);
$msg = 'producto_guardado';

if ($codigo > 0) {
    // ----- Modo edición -----
    $actual = datosProductos::consultarProductoCod($codigo)[0] ?? null;
    if (!$actual) {
        header('Location: frmProducto.php?msg=datos_invalidos&pagina=' . $pagina);
        exit;
    }

    $imagen = subirImagen($_FILES['imagen'] ?? [], $codigo);
    if ($imagen === false) {
        $msg = 'imagen_invalida';
        $imagen = $actual->Imagen;            // conserva la anterior
    } elseif ($imagen === null) {
        $imagen = $actual->Imagen;
    }

    $producto = new datosProductos($codigo, $nombre, $costo, $porc, $precio, (string) $imagen, 0, $fecha);
    $producto->actualizarProducto();
    if ($msg === 'producto_guardado') {
        $msg = 'producto_actualizado';
    }
} else {
    // ----- Modo creación -----
    $producto = new datosProductos(null, $nombre, $costo, $porc, $precio, '', 0, $fecha);
    $nuevoId = $producto->guardarProducto();

    $imagen = subirImagen($_FILES['imagen'] ?? [], $nuevoId);
    if ($imagen === false) {
        $msg = 'imagen_invalida';
    } elseif ($imagen !== null) {
        datosProductos::actualizarImagen($nuevoId, $imagen);
    }
}

header('Location: frmProducto.php?msg=' . $msg . '&pagina=' . $pagina);
exit;
