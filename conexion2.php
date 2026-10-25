<?php
// Archivo conservado por compatibilidad con el libro.
// Toda la aplicación usa la misma conexión (config/Conexion.php, base ediciones_fares).
// Antes aquí se declaraba otra clase "Conexion" hacia la base "sistema_fares",
// lo que causaba un error fatal al cargarse junto con config/Conexion.php.
require_once __DIR__ . '/config/Conexion.php';
