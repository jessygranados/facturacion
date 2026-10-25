<?php
// Menú compartido. Cada página define $paginaActiva antes de incluirlo:
// 'principal' | 'inventario' | 'facturacion'
$paginaActiva = $paginaActiva ?? '';
?>
<!-- ===== Navbar ===== -->
<div class="navbar">
    <h1>Ediciones Fares</h1>
</div>
<div class="submenu">
    <ul class="menu">
        <li class="<?= $paginaActiva === 'principal' ? 'activo' : '' ?>"><a href="frmcliente.php">Principal</a></li>
        <li>Libros <span class="flecha">&#9662;</span></li>
        <li class="desplegable <?= $paginaActiva === 'inventario' ? 'activo' : '' ?>">
            Inventario <span class="flecha">&#9662;</span>
            <div class="desplegable-contenido">
                <a href="frmProducto.php">Crear / modificar producto</a>
                <a href="frmMovimiento.php">Agregar inventario</a>
            </div>
        </li>
        <li class="<?= $paginaActiva === 'facturacion' ? 'activo' : '' ?>"><a href="frmFactura.php">Facturación</a></li>
        <li>Contacto</li>
    </ul>
</div>
