<?php
require_once __DIR__ . '/config/Conexion.php';

/**
 * Clase datos_Movimiento (libro, págs. 211-212)
 * Registra una entrada/salida en la tabla "movimientos" y actualiza
 * el stock del producto en "inventario" dentro de una sola transacción.
 */
class datos_Movimiento
{
    const TABLA = 'movimientos';

    public function __construct(
        private $idmovimiento = null,
        private $idproducto = null,
        private $fechamovimiento = null,
        private $cantidadmov = 0
    ) {}

    // ================= GETTERS =================
    public function get_idmovimiento()    { return $this->idmovimiento; }
    public function get_idproducto()      { return $this->idproducto; }
    public function get_fechamovimiento() { return $this->fechamovimiento; }
    public function get_cantidadmov()     { return $this->cantidadmov; }

    // ================= SETTERS =================
    public function set_idmovimiento($v)    { $this->idmovimiento = $v; }
    public function set_idproducto($v)      { $this->idproducto = $v; }
    public function set_fechamovimiento($v) { $this->fechamovimiento = $v; }
    public function set_cantidadmov($v)     { $this->cantidadmov = $v; }

    /**
     * Guarda el movimiento y actualiza el stock (transacción).
     * Devuelve ['ok' => bool, 'codigo' => string] donde "codigo" identifica el mensaje.
     */
    public function guardarmov(): array
    {
        $cantidad = (int) $this->cantidadmov;
        if ($cantidad === 0) {
            return ['ok' => false, 'codigo' => 'cantidad_invalida'];
        }

        $conexion = Conexion::obtenerConexion();

        try {
            $conexion->beginTransaction();

            // Se bloquea la fila del producto para calcular el stock actual sin carreras.
            $sel = $conexion->prepare('SELECT stock FROM inventario WHERE codigo = :codigo FOR UPDATE');
            $sel->execute([':codigo' => $this->idproducto]);
            $fila = $sel->fetch();

            if (!$fila) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'producto_no_existe'];
            }

            $nuevoStock = (int) $fila['stock'] + $cantidad;   // Calcular el nuevo stock
            if ($nuevoStock < 0) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'stock_insuficiente'];
            }

            $ins = $conexion->prepare('INSERT INTO ' . self::TABLA . ' (id_producto, fecha, cantidad_movi)
                                       VALUES (:producto, :fecha, :cantidad)');
            $ins->execute([
                ':producto' => $this->idproducto,
                ':fecha'    => $this->fechamovimiento,
                ':cantidad' => $cantidad,
            ]);

            $upd = $conexion->prepare('UPDATE inventario SET stock = :stock WHERE codigo = :codigo');
            $upd->execute([':stock' => $nuevoStock, ':codigo' => $this->idproducto]);

            $conexion->commit();
            return ['ok' => true, 'codigo' => 'movimiento_guardado'];

        } catch (Throwable $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();   // Rehacer los cambios
            }
            return ['ok' => false, 'codigo' => 'error_guardar'];
        }
    }

    /** Últimos movimientos registrados (para mostrar debajo del formulario). */
    public static function ultimos(int $limite = 8): array
    {
        $conexion = Conexion::obtenerConexion();
        $sql = 'SELECT m.idmovimiento, m.id_producto, i.nom_producto, m.fecha, m.cantidad_movi
                FROM ' . self::TABLA . ' m
                INNER JOIN inventario i ON i.codigo = m.id_producto
                ORDER BY m.idmovimiento DESC
                LIMIT :limite';
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
