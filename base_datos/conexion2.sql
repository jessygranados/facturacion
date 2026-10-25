-- ============================================
-- Base de datos: ediciones_fares
-- ============================================

CREATE DATABASE IF NOT EXISTS ediciones_fares
CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;

USE ediciones_fares;

-- ============================================
-- Tabla: clientes
-- ============================================
CREATE TABLE IF NOT EXISTS clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    direccion VARCHAR(200) NOT NULL,
    telefono_residencial VARCHAR(20) NOT NULL,
    celular VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Datos de ejemplo (según la imagen de referencia)
-- ============================================

INSERT INTO clientes (nombre, direccion, telefono_residencial, celular, email) VALUES
('Cliente 15', 'Calle 100 # 10-01, Bogotá', '6010001', '3110000001', 'cliente15@correo.com'),
('Cliente 16', 'Carrera 15 # 20-02, Bogotá', '6010002', '3110000002', 'cliente16@correo.com'),
('Cliente 17', 'Av. Los Alpes 33, Bogotá', '6010003', '3110000003', 'cliente17@correo.com'),
('Cliente 18', 'Calle 140 # 5-09, Bogotá', '6010004', '3110000004', 'cliente18@correo.com'),
('Cliente 19', 'Carrera 22 # 34-10, Bogotá', '6010005', '3110000005', 'cliente19@correo.com'),
('Cliente 20', 'Calle 75 # 12-11, Bogotá', '6010006', '3110000006', 'cliente20@correo.com'),
('Cliente 21', 'Carrera 8 # 17-12, Bogotá', '6010007', '3110000007', 'cliente21@correo.com'),
('Cliente 22', 'Av. 19 # 45-13, Bogotá', '6010008', '3110000008', 'cliente22@correo.com'),
('Cliente 23', 'Calle 90 # 7-14, Bogotá', '6010009', '3110000009', 'cliente23@correo.com'),
('Cliente 24', 'Carrera 18 # 60-15, Bogotá', '6010010', '3110000010', 'cliente24@correo.com'),
('Cliente 25', 'Calle 12 # 24-16, Bogotá', '6010011', '3110000011', 'cliente25@correo.com'),
('Cliente 26', 'Carrera 33 # 8-17, Bogotá', '6010012', '3110000012', 'cliente26@correo.com'),
('Cliente 27', 'Av. La Esperanza 5, Bogotá', '6010013', '3110000013', 'cliente27@correo.com'),
('Cliente 28', 'Calle 55 # 2-18, Bogotá', '6010014', '3110000014', 'cliente28@correo.com'),
('Cliente 29', 'Carrera 45 # 15-19, Bogotá', '6010015', '3110000015', 'cliente29@correo.com'),
('Cliente 30', 'Calle 10 # 3-20, Bogotá', '6010016', '3110000016', 'cliente30@correo.com'),
('Cliente 31', 'Carrera 52 # 19-21, Bogotá', '6010017', '3110000017', 'cliente31@correo.com'),
('Cliente 32', 'Av. 26 # 11-22, Bogotá', '6010018', '3110000018', 'cliente32@correo.com'),
('Cliente 33', 'Calle 30 # 25-23, Bogotá', '6010019', '3110000019', 'cliente33@correo.com'),
('Cliente 34', 'Carrera 17 # 4-24, Bogotá', '6010020', '3110000020', 'cliente34@correo.com'),
('Cliente 35', 'Av. 9 # 28-25, Bogotá', '6010021', '3110000021', 'cliente35@correo.com'),
('Cliente 36', 'Calle 41 # 16-26, Bogotá', '6010022', '3110000022', 'cliente36@correo.com'),
('Cliente 37', 'Carrera 12 # 9-27, Bogotá', '6010023', '3110000023', 'cliente37@correo.com'),
('Cliente 38', 'Av. 1 # 60-28, Bogotá', '6010024', '3110000024', 'cliente38@correo.com'),
('Cliente 39', 'Calle 68 # 14-29, Bogotá', '6010025', '3110000025', 'cliente39@correo.com'),
('Cliente 40', 'Carrera 5 # 33-30, Bogotá', '6010026', '3110000026', 'cliente40@correo.com'),
('Cliente 41', 'Av. 35 # 12-31, Bogotá', '6010027', '3110000027', 'cliente41@correo.com'),
('Cliente 42', 'Calle 24 # 18-32, Bogotá', '6010028', '3110000028', 'cliente42@correo.com'),
('Cliente 43', 'Carrera 64 # 7-33, Bogotá', '6010029', '3110000029', 'cliente43@correo.com'),
('Cliente 44', 'Av. 10 # 22-34, Bogotá', '6010030', '3110000030', 'cliente44@correo.com'),
('Cliente 45', 'Calle 81 # 6-35, Bogotá', '6010031', '3110000031', 'cliente45@correo.com'),
('Cliente 46', 'Carrera 29 # 25-36, Bogotá', '6010032', '3110000032', 'cliente46@correo.com'),
('Cliente 47', 'Av. 50 # 11-37, Bogotá', '6010033', '3110000033', 'cliente47@correo.com'),
('Cliente 48', 'Calle 17 # 3-38, Bogotá', '6010034', '3110000034', 'cliente48@correo.com'),
('Cliente 49', 'Carrera 40 # 14-39, Bogotá', '6010035', '3110000035', 'cliente49@correo.com'),
('Cliente 50', 'Av. 66 # 18-40, Bogotá', '6010036', '3110000036', 'cliente50@correo.com'),
('Cliente 51', 'Calle 58 # 1-41, Bogotá', '6010037', '3110000037', 'cliente51@correo.com'),
('Cliente 52', 'Carrera 38 # 23-42, Bogotá', '6010038', '3110000038', 'cliente52@correo.com'),
('Cliente 53', 'Av. 72 # 5-43, Bogotá', '6010039', '3110000039', 'cliente53@correo.com'),
('Cliente 54', 'Calle 2 # 90-44, Bogotá', '6010040', '3110000040', 'cliente54@correo.com'),
('Cliente 55', 'Carrera 21 # 16-45, Bogotá', '6010041', '3110000041', 'cliente55@correo.com'),
('Cliente 56', 'Av. 32 # 19-46, Bogotá', '6010042', '3110000042', 'cliente56@correo.com'),
('Cliente 57', 'Calle 70 # 8-47, Bogotá', '6010043', '3110000043', 'cliente57@correo.com'),
('Cliente 58', 'Carrera 7 # 44-48, Bogotá', '6010044', '3110000044', 'cliente58@correo.com'),
('Cliente 59', 'Av. 27 # 12-49, Bogotá', '6010045', '3110000045', 'cliente59@correo.com'),
('Cliente 60', 'Calle 65 # 20-50, Bogotá', '6010046', '3110000046', 'cliente60@correo.com'),
('Cliente 61', 'Carrera 14 # 30-51, Bogotá', '6010047', '3110000047', 'cliente61@correo.com'),
('Cliente 62', 'Av. 16 # 17-52, Bogotá', '6010048', '3110000048', 'cliente62@correo.com'),
('Cliente 63', 'Calle 88 # 2-53, Bogotá', '6010049', '3110000049', 'cliente63@correo.com'),
('Cliente 64', 'Carrera 28 # 9-54, Bogotá', '6010050', '3110000050', 'cliente64@correo.com'),
('Cliente 65', 'Av. 4 # 39-55, Bogotá', '6010051', '3110000051', 'cliente65@correo.com'),
('Cliente 66', 'Calle 77 # 24-56, Bogotá', '6010052', '3110000052', 'cliente66@correo.com'),
('Cliente 67', 'Carrera 50 # 11-57, Bogotá', '6010053', '3110000053', 'cliente67@correo.com');