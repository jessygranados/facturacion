<?php
require_once __DIR__ . '/classMovimiento.php';
require_once __DIR__ . '/classProducto.php';

// Función para limpiar los datos recibidos
function filtrofares($dat_fares)
{
    $datos = trim($dat_fares);                 // Elimina espacios antes y después
    $datos = stripslashes($datos);             // Elimina backslashes "\"
    $datos = htmlspecialchars($datos);         // Traduce caracteres especiales en entidades HTML
    return $datos;
}

// Declarar variables
$cod_pro = "";
$vfecha = "";
$vcantidad_movi = "";
$msg = 'error_guardar';

// Comprobar si los datos se pasaron a través del método POST
if (isset($_POST["cguardar"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

    if (!empty($_POST["codpro"])) {
        $cod_pro = filtrofares($_POST["codpro"]);
    }
    if (!empty($_POST["fechmovi"])) {
        $vfecha = filtrofares($_POST["fechmovi"]);
    }
    if (isset($_POST["cantpro"]) && $_POST["cantpro"] !== '') {
        $vcantidad_movi = filtrofares($_POST["cantpro"]);
    }

    $fechaOk = DateTime::createFromFormat('Y-m-d', $vfecha);

    if (!ctype_digit($cod_pro) || !$fechaOk || $fechaOk->format('Y-m-d') !== $vfecha) {
        $msg = 'producto_no_existe';
    } elseif (filter_var($vcantidad_movi, FILTER_VALIDATE_INT) === false || (int) $vcantidad_movi === 0) {
        $msg = 'cantidad_invalida';
    } elseif (!datosProductos::consultarProductoCod((int) $cod_pro)) {
        $msg = 'producto_no_existe';
    } else {
        // El stock actual se lee dentro de la transacción de guardarmov()
        $datosmov = new datos_Movimiento(null, (int) $cod_pro, $vfecha, (int) $vcantidad_movi);
        $resultado = $datosmov->guardarmov();
        $msg = $resultado['codigo'];
    }
}

header('Location: frmMovimiento.php?msg=' . $msg); // Llamar a la página frmMovimiento.php
die();
