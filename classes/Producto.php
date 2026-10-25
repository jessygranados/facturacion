<?php
require_once __DIR__ . '/../config/Conexion.php';

/**
 * Clase Producto
 * Acceso de solo lectura a la tabla "inventario" para el
 * Sistema de Facturación (búsqueda de producto por código).
 */
class Producto
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::obtenerConexion();
    }

    /**
     * Busca un producto por su código.
     * Devuelve un arreglo asociativo o false si no existe.
     */
    public function buscarPorCodigo($codigo): array|false
    {
        $sql = "SELECT codigo, nom_producto, precio_venta, stock
                FROM inventario
                WHERE codigo = :codigo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':codigo' => $codigo]);
        return $stmt->fetch();
    }
}
