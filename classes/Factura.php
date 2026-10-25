<?php
require_once __DIR__ . '/../config/Conexion.php';

/**
 * Clase Factura
 * Maneja el consecutivo de facturas y el guardado del encabezado (facturas),
 * sus líneas (detalle_factura) y la salida de inventario (movimientos + stock).
 */
class Factura
{
    const IVA = 0.15; // 15 %

    private PDO $conexion;
    private string $ultimoError = '';

    public function __construct()
    {
        $this->conexion = Conexion::obtenerConexion();
    }

    public function getError(): string
    {
        return $this->ultimoError;
    }

    /**
     * Devuelve el número que le corresponde a la próxima factura.
     */
    public function siguienteNumero(): int
    {
        $sql = "SELECT IFNULL(MAX(numero_factura), 0) + 1 AS siguiente FROM facturas";
        $stmt = $this->conexion->query($sql);
        $fila = $stmt->fetch();
        return (int) $fila['siguiente'];
    }

    /**
     * Guarda la factura en una sola transacción:
     *  - valida que haya stock suficiente de cada producto,
     *  - toma precio y descripción desde la base de datos (no del navegador),
     *  - calcula subtotal, IVA y total,
     *  - guarda facturas + detalle_factura,
     *  - descuenta el stock y registra cada salida en movimientos (cantidad negativa).
     *
     * $detalle: arreglo de arreglos con las llaves "codigo" y "cantidad".
     * Devuelve el número de factura generado o false si algo falla (ver getError()).
     */
    public function guardar(int $idCliente, array $detalle): int|false
    {
        $this->ultimoError = '';

        if (empty($detalle)) {
            $this->ultimoError = 'La factura no tiene productos agregados';
            return false;
        }

        try {
            $this->conexion->beginTransaction();

            $stmtCliente = $this->conexion->prepare("SELECT 1 FROM clientes WHERE id_cliente = :id");
            $stmtCliente->execute([':id' => $idCliente]);
            if (!$stmtCliente->fetch()) {
                throw new RuntimeException('El cliente seleccionado no existe');
            }

            $stmtProd = $this->conexion->prepare(
                "SELECT codigo, nom_producto, precio_venta, stock FROM inventario WHERE codigo = :codigo FOR UPDATE"
            );
            $stmtStock = $this->conexion->prepare(
                "UPDATE inventario SET stock = stock - :cant WHERE codigo = :codigo"
            );

            $lineas   = [];
            $subtotal = 0.0;

            foreach ($detalle as $linea) {
                $codigo   = (int) ($linea['codigo'] ?? 0);
                $cantidad = (int) ($linea['cantidad'] ?? 0);

                if ($codigo <= 0 || $cantidad <= 0) {
                    throw new RuntimeException('Hay una línea de factura con datos no válidos');
                }

                $stmtProd->execute([':codigo' => $codigo]);
                $prod = $stmtProd->fetch();

                if (!$prod) {
                    throw new RuntimeException("El producto {$codigo} no existe");
                }
                if ((int) $prod['stock'] < $cantidad) {
                    throw new RuntimeException(
                        "Stock insuficiente de \"{$prod['nom_producto']}\" (disponible: {$prod['stock']})"
                    );
                }

                $precio     = (float) $prod['precio_venta'];
                $totalLinea = round($precio * $cantidad, 2);
                $subtotal  += $totalLinea;

                // Descontar ya, para que dos líneas del mismo producto no excedan el stock
                $stmtStock->execute([':cant' => $cantidad, ':codigo' => $codigo]);

                $lineas[] = [
                    'codigo'      => $codigo,
                    'descripcion' => $prod['nom_producto'],
                    'cantidad'    => $cantidad,
                    'precio'      => $precio,
                    'total'       => $totalLinea,
                ];
            }

            $subtotal = round($subtotal, 2);
            $iva      = round($subtotal * self::IVA, 2);
            $total    = round($subtotal + $iva, 2);

            $numeroFactura = $this->siguienteNumero();

            $stmtFactura = $this->conexion->prepare(
                "INSERT INTO facturas (numero_factura, id_cliente, subtotal, iva, total)
                 VALUES (:numero, :id_cliente, :subtotal, :iva, :total)"
            );
            $stmtFactura->execute([
                ':numero'     => $numeroFactura,
                ':id_cliente' => $idCliente,
                ':subtotal'   => $subtotal,
                ':iva'        => $iva,
                ':total'      => $total,
            ]);
            $idFactura = (int) $this->conexion->lastInsertId();

            $stmtDetalle = $this->conexion->prepare(
                "INSERT INTO detalle_factura (id_factura, codigo_producto, descripcion, cantidad, precio, total)
                 VALUES (:id_factura, :codigo, :descripcion, :cantidad, :precio, :total)"
            );
            $stmtMov = $this->conexion->prepare(
                "INSERT INTO movimientos (id_producto, fecha, cantidad_movi)
                 VALUES (:codigo, CURDATE(), :cantidad)"
            );

            foreach ($lineas as $l) {
                $stmtDetalle->execute([
                    ':id_factura'  => $idFactura,
                    ':codigo'      => $l['codigo'],
                    ':descripcion' => $l['descripcion'],
                    ':cantidad'    => $l['cantidad'],
                    ':precio'      => $l['precio'],
                    ':total'       => $l['total'],
                ]);
                // Salida de inventario = cantidad negativa
                $stmtMov->execute([':codigo' => $l['codigo'], ':cantidad' => -$l['cantidad']]);
            }

            $this->conexion->commit();
            return $numeroFactura;

        } catch (RuntimeException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            $this->ultimoError = $e->getMessage();
            return false;
        } catch (Exception $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            $this->ultimoError = 'No se pudo guardar la factura';
            return false;
        }
    }
}
