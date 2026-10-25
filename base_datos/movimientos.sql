-- ============================================
-- Tabla movimientos (libro, pág. 202)
-- Base de datos: ediciones_fares
-- Entrada = cantidad positiva / Salida = cantidad negativa
-- ============================================
USE ediciones_fares;

CREATE TABLE IF NOT EXISTS movimientos (
    idmovimiento  INT AUTO_INCREMENT PRIMARY KEY,
    id_producto   INT NOT NULL,
    fecha         DATE NOT NULL,
    cantidad_movi INT NOT NULL,
    CONSTRAINT fk_mov_producto FOREIGN KEY (id_producto) REFERENCES inventario(codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
