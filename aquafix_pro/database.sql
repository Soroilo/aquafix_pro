-- =====================================================
-- BASE DE DATOS AQUAFIX PRO
-- Sistema de Gestión de Servicios de Fontanería
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS aquafix_pro;
USE aquafix_pro;

-- =====================================================
-- TABLA: CLIENTES
-- =====================================================
CREATE TABLE IF NOT EXISTS clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    direccion VARCHAR(200),
    password VARCHAR(255) NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email),
    INDEX idx_telefono (telefono)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: FONTANEROS
-- =====================================================
CREATE TABLE IF NOT EXISTS fontaneros (
    id_fontanero INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    especialidad VARCHAR(100),
    certificacion VARCHAR(100),
    rating_promedio DECIMAL(3,2) DEFAULT 0.00,
    servicios_completados INT DEFAULT 0,
    password VARCHAR(255) NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_disponible (disponible),
    INDEX idx_rating (rating_promedio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: SERVICIOS
-- =====================================================
CREATE TABLE IF NOT EXISTS servicios (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio_base DECIMAL(10,2) NOT NULL,
    duracion_estimada INT NOT NULL COMMENT 'Duración en minutos',
    tipo ENUM('reparacion', 'instalacion', 'mantenimiento', 'emergencia') NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    imagen_url VARCHAR(255),
    INDEX idx_tipo (tipo),
    INDEX idx_disponible (disponible)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: SOLICITUDES
-- =====================================================
CREATE TABLE IF NOT EXISTS solicitudes (
    id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_fontanero INT,
    id_servicio INT NOT NULL,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_programada DATETIME,
    estado ENUM('pendiente', 'asignada', 'en_proceso', 'completada', 'cancelada') DEFAULT 'pendiente',
    direccion_servicio VARCHAR(200) NOT NULL,
    descripcion_problema TEXT,
    latitud DECIMAL(10,8),
    longitud DECIMAL(11,8),
    prioridad ENUM('baja', 'media', 'alta', 'urgente') DEFAULT 'media',
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_fontanero) REFERENCES fontaneros(id_fontanero) ON DELETE SET NULL,
    FOREIGN KEY (id_servicio) REFERENCES servicios(id_servicio) ON DELETE CASCADE,
    INDEX idx_cliente (id_cliente),
    INDEX idx_fontanero (id_fontanero),
    INDEX idx_estado (estado),
    INDEX idx_fecha_programada (fecha_programada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: COTIZACIONES
-- =====================================================
CREATE TABLE IF NOT EXISTS cotizaciones (
    id_cotizacion INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud INT UNIQUE NOT NULL,
    precio_inicial DECIMAL(10,2) NOT NULL,
    precio_final DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(5,2) DEFAULT 0.00,
    detalles TEXT,
    fecha_cotizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'aceptada', 'rechazada') DEFAULT 'pendiente',
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes(id_solicitud) ON DELETE CASCADE,
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: CALIFICACIONES
-- =====================================================
CREATE TABLE IF NOT EXISTS calificaciones (
    id_calificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_fontanero INT NOT NULL,
    id_solicitud INT UNIQUE NOT NULL,
    puntuacion INT NOT NULL CHECK (puntuacion BETWEEN 1 AND 5),
    comentario TEXT,
    fecha_calificacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_fontanero) REFERENCES fontaneros(id_fontanero) ON DELETE CASCADE,
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes(id_solicitud) ON DELETE CASCADE,
    INDEX idx_fontanero (id_fontanero),
    INDEX idx_puntuacion (puntuacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: DISPONIBILIDAD
-- =====================================================
CREATE TABLE IF NOT EXISTS disponibilidad (
    id_disponibilidad INT AUTO_INCREMENT PRIMARY KEY,
    id_fontanero INT NOT NULL,
    dia_semana ENUM('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_fontanero) REFERENCES fontaneros(id_fontanero) ON DELETE CASCADE,
    INDEX idx_fontanero (id_fontanero),
    INDEX idx_dia (dia_semana)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: ADMINISTRADORES
-- =====================================================
CREATE TABLE IF NOT EXISTS administradores (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('super_admin', 'admin', 'operador') DEFAULT 'operador',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: REPORTES
-- =====================================================
CREATE TABLE IF NOT EXISTS reportes (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    id_admin INT NOT NULL,
    tipo_reporte VARCHAR(50) NOT NULL,
    fecha_generacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    datos_json JSON,
    periodo VARCHAR(50),
    FOREIGN KEY (id_admin) REFERENCES administradores(id_admin) ON DELETE CASCADE,
    INDEX idx_tipo (tipo_reporte),
    INDEX idx_fecha (fecha_generacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: SEGUIMIENTO
-- =====================================================
CREATE TABLE IF NOT EXISTS seguimiento (
    id_seguimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud INT NOT NULL,
    estado VARCHAR(50) NOT NULL,
    descripcion TEXT,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    actualizado_por VARCHAR(100),
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes(id_solicitud) ON DELETE CASCADE,
    INDEX idx_solicitud (id_solicitud),
    INDEX idx_fecha (fecha_actualizacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATOS DE PRUEBA
-- =====================================================

-- Insertar Servicios
INSERT INTO servicios (nombre, descripcion, precio_base, duracion_estimada, tipo, disponible, imagen_url) VALUES
('Reparación de Fuga', 'Reparación de fugas en tuberías, llaves y conexiones', 50.00, 60, 'reparacion', TRUE, 'img/servicios/fuga.jpg'),
('Instalación de Lavabo', 'Instalación completa de lavabo con grifería', 80.00, 120, 'instalacion', TRUE, 'img/servicios/lavabo.jpg'),
('Mantenimiento Preventivo', 'Revisión completa del sistema de fontanería', 40.00, 90, 'mantenimiento', TRUE, 'img/servicios/mantenimiento.jpg'),
('Reparación de Tubería', 'Reparación de tuberías rotas o dañadas', 70.00, 90, 'reparacion', TRUE, 'img/servicios/tuberia.jpg'),
('Emergencia 24/7', 'Servicio de emergencia disponible las 24 horas', 100.00, 60, 'emergencia', TRUE, 'img/servicios/emergencia.jpg'),
('Instalación de Inodoro', 'Instalación completa de inodoro', 90.00, 120, 'instalacion', TRUE, 'img/servicios/inodoro.jpg'),
('Destape de Drenaje', 'Destape de drenajes y desagües obstruidos', 60.00, 75, 'reparacion', TRUE, 'img/servicios/drenaje.jpg'),
('Renovación de Baño', 'Renovación completa de instalaciones de baño', 500.00, 480, 'instalacion', TRUE, 'img/servicios/renovacion.jpg');

-- Insertar Clientes de prueba
INSERT INTO clientes (nombre, apellido, email, telefono, direccion, password) VALUES
('Juan', 'Pérez', 'juan.perez@email.com', '555-0101', 'Calle 123 #45-67, Bogotá', '$2y$10$hashedpassword1'),
('María', 'García', 'maria.garcia@email.com', '555-0102', 'Carrera 45 #12-34, Bogotá', '$2y$10$hashedpassword2'),
('Carlos', 'Rodríguez', 'carlos.rodriguez@email.com', '555-0103', 'Avenida 68 #23-45, Bogotá', '$2y$10$hashedpassword3');

-- Insertar Fontaneros de prueba
INSERT INTO fontaneros (nombre, apellido, email, telefono, especialidad, certificacion, rating_promedio, servicios_completados, password, disponible) VALUES
('Pedro', 'Martínez', 'pedro.martinez@aquafix.com', '555-0201', 'Reparaciones Generales', 'Cert-2023-001', 4.8, 156, '$2y$10$hashedpassword4', TRUE),
('Ana', 'López', 'ana.lopez@aquafix.com', '555-0202', 'Instalaciones', 'Cert-2023-002', 4.9, 203, '$2y$10$hashedpassword5', TRUE),
('Luis', 'Hernández', 'luis.hernandez@aquafix.com', '555-0203', 'Emergencias', 'Cert-2023-003', 4.7, 178, '$2y$10$hashedpassword6', TRUE),
('Carmen', 'Torres', 'carmen.torres@aquafix.com', '555-0204', 'Mantenimiento', 'Cert-2023-004', 4.6, 134, '$2y$10$hashedpassword7', TRUE);

-- Insertar Administrador
INSERT INTO administradores (nombre, apellido, email, password, rol) VALUES
('Admin', 'Principal', 'admin@aquafix.com', '$2y$10$hashedpassword8', 'super_admin'),
('Operador', 'Uno', 'operador@aquafix.com', '$2y$10$hashedpassword9', 'operador');

-- Insertar Disponibilidad de fontaneros
INSERT INTO disponibilidad (id_fontanero, dia_semana, hora_inicio, hora_fin, activo) VALUES
(1, 'lunes', '08:00:00', '17:00:00', TRUE),
(1, 'martes', '08:00:00', '17:00:00', TRUE),
(1, 'miercoles', '08:00:00', '17:00:00', TRUE),
(2, 'lunes', '09:00:00', '18:00:00', TRUE),
(2, 'martes', '09:00:00', '18:00:00', TRUE),
(3, 'lunes', '00:00:00', '23:59:59', TRUE),
(3, 'martes', '00:00:00', '23:59:59', TRUE);

-- =====================================================
-- VISTAS ÚTILES
-- =====================================================

-- Vista de solicitudes con información completa
CREATE OR REPLACE VIEW vista_solicitudes_completas AS
SELECT 
    s.id_solicitud,
    CONCAT(c.nombre, ' ', c.apellido) AS cliente,
    c.email AS email_cliente,
    c.telefono AS telefono_cliente,
    CONCAT(f.nombre, ' ', f.apellido) AS fontanero,
    f.telefono AS telefono_fontanero,
    srv.nombre AS servicio,
    srv.precio_base,
    s.fecha_solicitud,
    s.fecha_programada,
    s.estado,
    s.direccion_servicio,
    s.prioridad,
    cot.precio_final
FROM solicitudes s
INNER JOIN clientes c ON s.id_cliente = c.id_cliente
LEFT JOIN fontaneros f ON s.id_fontanero = f.id_fontanero
INNER JOIN servicios srv ON s.id_servicio = srv.id_servicio
LEFT JOIN cotizaciones cot ON s.id_solicitud = cot.id_solicitud;

-- Vista de estadísticas de fontaneros
CREATE OR REPLACE VIEW vista_estadisticas_fontaneros AS
SELECT 
    f.id_fontanero,
    CONCAT(f.nombre, ' ', f.apellido) AS fontanero,
    f.especialidad,
    f.rating_promedio,
    f.servicios_completados,
    COUNT(DISTINCT s.id_solicitud) AS solicitudes_actuales,
    f.disponible
FROM fontaneros f
LEFT JOIN solicitudes s ON f.id_fontanero = s.id_fontanero 
    AND s.estado IN ('asignada', 'en_proceso')
GROUP BY f.id_fontanero;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================

-- Procedimiento para actualizar rating de fontanero
DELIMITER //
CREATE PROCEDURE actualizar_rating_fontanero(IN p_id_fontanero INT)
BEGIN
    UPDATE fontaneros
    SET rating_promedio = (
        SELECT COALESCE(AVG(puntuacion), 0)
        FROM calificaciones
        WHERE id_fontanero = p_id_fontanero
    )
    WHERE id_fontanero = p_id_fontanero;
END //
DELIMITER ;

-- Procedimiento para completar servicio
DELIMITER //
CREATE PROCEDURE completar_servicio(IN p_id_solicitud INT)
BEGIN
    DECLARE v_id_fontanero INT;
    
    SELECT id_fontanero INTO v_id_fontanero
    FROM solicitudes
    WHERE id_solicitud = p_id_solicitud;
    
    UPDATE solicitudes
    SET estado = 'completada'
    WHERE id_solicitud = p_id_solicitud;
    
    UPDATE fontaneros
    SET servicios_completados = servicios_completados + 1
    WHERE id_fontanero = v_id_fontanero;
    
    INSERT INTO seguimiento (id_solicitud, estado, descripcion, actualizado_por)
    VALUES (p_id_solicitud, 'completada', 'Servicio completado exitosamente', 'SISTEMA');
END //
DELIMITER ;

-- =====================================================
-- TRIGGERS
-- =====================================================

-- Trigger para actualizar rating después de insertar calificación
DELIMITER //
CREATE TRIGGER after_calificacion_insert
AFTER INSERT ON calificaciones
FOR EACH ROW
BEGIN
    CALL actualizar_rating_fontanero(NEW.id_fontanero);
END //
DELIMITER ;

-- Trigger para registrar cambios de estado en seguimiento
DELIMITER //
CREATE TRIGGER after_solicitud_update
AFTER UPDATE ON solicitudes
FOR EACH ROW
BEGIN
    IF NEW.estado != OLD.estado THEN
        INSERT INTO seguimiento (id_solicitud, estado, descripcion, actualizado_por)
        VALUES (NEW.id_solicitud, NEW.estado, 
                CONCAT('Estado cambiado de ', OLD.estado, ' a ', NEW.estado),
                'SISTEMA');
    END IF;
END //
DELIMITER ;

DELIMITER ;

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================

CREATE INDEX idx_solicitudes_fecha_estado ON solicitudes(fecha_programada, estado);
CREATE INDEX idx_calificaciones_fecha ON calificaciones(fecha_calificacion);
CREATE INDEX idx_seguimiento_solicitud_fecha ON seguimiento(id_solicitud, fecha_actualizacion);

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================