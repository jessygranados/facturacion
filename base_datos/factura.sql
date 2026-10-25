-- ============================================
-- Tablas para el Sistema de Facturación
-- Base de datos: ediciones_fares
-- ============================================

USE ediciones_fares;

-- ============================================
-- Tabla: inventario (productos)
-- ============================================
CREATE TABLE IF NOT EXISTS inventario (
    codigo        INT AUTO_INCREMENT PRIMARY KEY,
    nom_producto  VARCHAR(150)   NOT NULL,
    costo         DECIMAL(10,2)  NOT NULL DEFAULT 0,
    porc_venta    DECIMAL(5,2)   NOT NULL DEFAULT 0,
    precio_venta  DECIMAL(10,2)  NOT NULL DEFAULT 0,
    Imagen        VARCHAR(255)   DEFAULT NULL,
    stock         INT            NOT NULL DEFAULT 0,
    Fecha         DATE           DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Tabla: facturas (encabezado)
-- ============================================
CREATE TABLE IF NOT EXISTS facturas (
    id_factura     INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura INT NOT NULL,
    id_cliente     INT NOT NULL,
    fecha          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    subtotal       DECIMAL(10,2) NOT NULL DEFAULT 0,
    iva            DECIMAL(10,2) NOT NULL DEFAULT 0,
    total          DECIMAL(10,2) NOT NULL DEFAULT 0,
    CONSTRAINT fk_factura_cliente FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Tabla: detalle_factura (líneas de la factura)
-- ============================================
CREATE TABLE IF NOT EXISTS detalle_factura (
    id_detalle      INT AUTO_INCREMENT PRIMARY KEY,
    id_factura      INT NOT NULL,
    codigo_producto INT NOT NULL,
    descripcion     VARCHAR(150) NOT NULL,
    cantidad        INT NOT NULL,
    precio          DECIMAL(10,2) NOT NULL,
    total           DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_detalle_factura  FOREIGN KEY (id_factura)      REFERENCES facturas(id_factura),
    CONSTRAINT fk_detalle_producto FOREIGN KEY (codigo_producto) REFERENCES inventario(codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Datos de ejemplo (según la imagen de referencia)
-- ============================================
INSERT INTO inventario (codigo, nom_producto, costo, porc_venta, precio_venta, stock, Fecha) VALUES
(100, 'Informática', 400.00, 30, 520.00, 50, CURDATE()),
(101, 'Papelería general', 15.00, 40, 21.00, 200, CURDATE()),
(102, 'Cartuchos de tinta', 90.00, 35, 121.50, 80, CURDATE()),
(103, 'Resma de papel bond', 4.50, 25, 5.63, 300, CURDATE()),
(104, 'Mouse óptico', 30.00, 45, 43.50, 60, CURDATE());
