<?php
require_once __DIR__ . '/classProducto.php';

header('Content-Type: text/plain; charset=utf-8');

// Almacenar en $cod_pro el valor de la variable dato recibida por el método GET
$cod_pro = intval($_GET['dato'] ?? 0);
$vnom_pro = "";

// Almacenar en $productoSelect el registro que corresponda con el valor de $cod_pro
$productoSelect = datosProductos::consultarProductoCod($cod_pro);

foreach ($productoSelect as $producto1) {
    // Almacenar en la variable $vnom_pro el nombre del producto
    $vnom_pro = $producto1->nom_producto;
}

if ($vnom_pro === "") {
    $vnom_pro = "Producto no encontrado";
}

// Devolver el valor a la función de llamada
$respuesta = $vnom_pro;
echo $respuesta;
