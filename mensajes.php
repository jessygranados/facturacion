<?php
/**
 * Mensajes de aviso. Las páginas redirigen con ?msg=codigo y aquí se traduce
 * a texto (solo códigos conocidos, nunca texto libre de la URL).
 */
function textoMensaje(string $codigo): array
{
    $mensajes = [
        'producto_guardado'    => ['ok',    'Producto guardado correctamente.'],
        'producto_actualizado' => ['ok',    'Producto actualizado correctamente.'],
        'producto_eliminado'   => ['ok',    'Producto eliminado.'],
        'producto_en_uso'      => ['error', 'No se puede eliminar: el producto tiene facturas o movimientos registrados.'],
        'datos_invalidos'      => ['error', 'Revise los datos: nombre, costo y porcentaje son obligatorios.'],
        'imagen_invalida'      => ['error', 'La imagen debe ser JPG, PNG, GIF o WEBP de máximo 2 MB. El producto se guardó sin imagen.'],
        'movimiento_guardado'  => ['ok',    'Datos guardados con éxito. El stock fue actualizado.'],
        'cantidad_invalida'    => ['error', 'La cantidad debe ser un número entero distinto de cero.'],
        'producto_no_existe'   => ['error', 'El producto indicado no existe.'],
        'stock_insuficiente'   => ['error', 'Imposible guardar: el stock no puede quedar negativo.'],
        'error_guardar'        => ['error', 'Imposible guardar los datos.'],
    ];
    return $mensajes[$codigo] ?? ['', ''];
}

function mostrarMensaje(): void
{
    if (!isset($_GET['msg'])) {
        return;
    }
    [$tipo, $texto] = textoMensaje((string) $_GET['msg']);
    if ($tipo !== '') {
        echo '<div class="aviso aviso-' . $tipo . '">' . htmlspecialchars($texto) . '</div>';
    }
}
