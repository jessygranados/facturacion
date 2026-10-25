<?php
require_once __DIR__ . '/config/Conexion.php';

/**
 * Clase datosProductos
 * Acceso a datos de la tabla "inventario" (alta, consulta, modificación,
 * eliminación y paginación de productos).
 */
class datosProductos
{
    const TABLA = 'inventario';
    const REGISTROS_POR_PAGINA = 6;

    public function __construct(
        private $codproducto = null,
        private $nom_producto = "",
        private $costoproducto = 0.00,
        private $porc_ventapro = 0,
        private $precio_ventapro = 0.00,
        private $imagenpro = "",
        private $stockpro = 0,
        private $fechapro = null
    ) {}

    // ================= GETTERS =================
    public function get_codproducto()      { return $this->codproducto; }
    public function get_nom_producto()     { return $this->nom_producto; }
    public function get_costoproducto()    { return $this->costoproducto; }
    public function get_porc_ventapro()    { return $this->porc_ventapro; }
    public function get_precio_ventapro()  { return $this->precio_ventapro; }
    public function get_imagenpro()        { return $this->imagenpro; }
    public function get_stockpro()         { return $this->stockpro; }
    public function get_fechapro()         { return $this->fechapro; }

    // ================= SETTERS =================
    public function set_codproducto($v)      { $this->codproducto = $v; }
    public function set_nom_producto($v)     { $this->nom_producto = $v; }
    public function set_costoproducto($v)    { $this->costoproducto = $v; }
    public function set_porc_ventapro($v)    { $this->porc_ventapro = $v; }
    public function set_precio_ventapro($v)  { $this->precio_ventapro = $v; }
    public function set_imagenpro($v)        { $this->imagenpro = $v; }
    public function set_stockpro($v)         { $this->stockpro = $v; }
    public function set_fechapro($v)         { $this->fechapro = $v; }

    /** Precio de venta = costo + porcentaje de ganancia. */
    public static function calcularPrecio(float $costo, float $porcentaje): float
    {
        return round($costo * (1 + $porcentaje / 100), 2);
    }

    // ================= GUARDAR PRODUCTO =================
    // Devuelve el código (id) del producto creado.
    public function guardarProducto(): int
    {
        $conexion = Conexion::obtenerConexion();
        $consulta = $conexion->prepare('INSERT INTO ' . self::TABLA . '
            (nom_producto, costo, porc_venta, precio_venta, Imagen, Fecha)
            VALUES (:producto, :pcosto, :pporc_venta, :precio_venta, :pImagen, :pFecha)');

        $consulta->execute([
            ':producto'     => $this->nom_producto,
            ':pcosto'       => $this->costoproducto,
            ':pporc_venta'  => $this->porc_ventapro,
            ':precio_venta' => $this->precio_ventapro,
            ':pImagen'      => $this->imagenpro !== '' ? $this->imagenpro : null,
            ':pFecha'       => $this->fechapro,
        ]);

        return (int) $conexion->lastInsertId();
    }

    // ================= ACTUALIZAR PRODUCTO =================
    public function actualizarProducto(): bool
    {
        $conexion = Conexion::obtenerConexion();
        $consulta = $conexion->prepare('UPDATE ' . self::TABLA . ' SET
            nom_producto = :producto,
            costo        = :pcosto,
            porc_venta   = :pporc_venta,
            precio_venta = :precio_venta,
            Imagen       = :pImagen,
            Fecha        = :pFecha
            WHERE codigo = :codpro');

        return $consulta->execute([
            ':producto'     => $this->nom_producto,
            ':pcosto'       => $this->costoproducto,
            ':pporc_venta'  => $this->porc_ventapro,
            ':precio_venta' => $this->precio_ventapro,
            ':pImagen'      => $this->imagenpro !== '' ? $this->imagenpro : null,
            ':pFecha'       => $this->fechapro,
            ':codpro'       => $this->codproducto,
        ]);
    }

    // ================= ACTUALIZAR SOLO LA IMAGEN =================
    public static function actualizarImagen(int $codigo, ?string $archivo): bool
    {
        $conexion = Conexion::obtenerConexion();
        $consulta = $conexion->prepare('UPDATE ' . self::TABLA . ' SET Imagen = :img WHERE codigo = :codpro');
        return $consulta->execute([':img' => $archivo, ':codpro' => $codigo]);
    }

    // ================= ACTUALIZAR STOCK (valor absoluto) =================
    public static function actualizarStock(int $codigo, int $nuevoStock): bool
    {
        $conexion = Conexion::obtenerConexion();
        $consulta = $conexion->prepare('UPDATE ' . self::TABLA . ' SET stock = :p_stock WHERE codigo = :codpro');
        return $consulta->execute([':p_stock' => $nuevoStock, ':codpro' => $codigo]);
    }

    // ================= CONTAR TODOS LOS PRODUCTOS =================
    public static function todosProductos(): int
    {
        $conexion = Conexion::obtenerConexion();
        return (int) $conexion->query('SELECT COUNT(*) FROM ' . self::TABLA)->fetchColumn();
    }

    public static function totalPaginas(): int
    {
        return (int) ceil(self::todosProductos() / self::REGISTROS_POR_PAGINA);
    }

    // ================= LISTAR (paginado) =================
    public static function listarProductos(int $pagina = 1): array
    {
        if ($pagina < 1) {
            $pagina = 1;
        }
        $inicio = ($pagina - 1) * self::REGISTROS_POR_PAGINA;

        $conexion = Conexion::obtenerConexion();
        $consulta = $conexion->prepare('SELECT * FROM ' . self::TABLA . ' ORDER BY codigo ASC LIMIT :inicio, :registros');
        $consulta->bindValue(':inicio', $inicio, PDO::PARAM_INT);
        $consulta->bindValue(':registros', self::REGISTROS_POR_PAGINA, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================= CONSULTAR PRODUCTO POR CÓDIGO =================
    // Devuelve un arreglo de objetos (como en el libro): $p->nom_producto, $p->stock ...
    public static function consultarProductoCod($codproducto): array
    {
        $conexion = Conexion::obtenerConexion();
        $consulta = $conexion->prepare('SELECT * FROM ' . self::TABLA . ' WHERE codigo = :codpro');
        $consulta->execute([':codpro' => $codproducto]);
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    // ================= ELIMINAR PRODUCTO =================
    // Lanza PDOException si el producto ya tiene facturas o movimientos.
    public function eliminarProducto(): bool
    {
        $conexion = Conexion::obtenerConexion();
        $consulta = $conexion->prepare('DELETE FROM ' . self::TABLA . ' WHERE codigo = :codpro');
        return $consulta->execute([':codpro' => $this->codproducto]);
    }
}
