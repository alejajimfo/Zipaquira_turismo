-- =====================================================
-- BASE DE DATOS: PLATAFORMA TURÍSTICA ZIPAQUIRÁ
-- Versión: 1.0
-- Cumplimiento: Ley 1581 de 2012 (Habeas Data)
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS zipaquira_turismo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE zipaquira_turismo;

-- =====================================================
-- TABLA: usuarios
-- Almacena información general de todos los usuarios
-- =====================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_usuario ENUM('turista', 'agencia', 'operador', 'restaurante', 'hotel', 'gobierno') NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    ciudad VARCHAR(100),
    pais VARCHAR(100) DEFAULT 'Colombia',
    foto_perfil VARCHAR(500),
    activo BOOLEAN DEFAULT TRUE,
    verificado BOOLEAN DEFAULT FALSE,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultima_conexion DATETIME,
    aceptacion_terminos BOOLEAN DEFAULT FALSE,
    aceptacion_habeas_data BOOLEAN DEFAULT FALSE,
    fecha_aceptacion_politicas DATETIME,
    ip_registro VARCHAR(45),
    INDEX idx_email (email),
    INDEX idx_tipo_usuario (tipo_usuario),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: turistas
-- Información específica de turistas/viajeros
-- =====================================================
CREATE TABLE turistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_documento ENUM('CC', 'CE', 'Pasaporte', 'TI') NOT NULL,
    numero_documento VARCHAR(50) NOT NULL,
    fecha_nacimiento DATE,
    genero ENUM('M', 'F', 'Otro', 'Prefiero no decir'),
    nacionalidad VARCHAR(100),
    idioma_preferido VARCHAR(50) DEFAULT 'Español',
    preferencias_turisticas TEXT, -- JSON con intereses
    alergias_alimentarias TEXT,
    necesidades_especiales TEXT,
    programa_fidelizacion BOOLEAN DEFAULT FALSE,
    puntos_acumulados INT DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_documento (tipo_documento, numero_documento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: agencias_turismo
-- Información de agencias de turismo
-- =====================================================
CREATE TABLE agencias_turismo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    razon_social VARCHAR(255) NOT NULL,
    nit VARCHAR(50) UNIQUE NOT NULL,
    rnt VARCHAR(50), -- Registro Nacional de Turismo
    representante_legal VARCHAR(255),
    sitio_web VARCHAR(255),
    descripcion_servicios TEXT,
    horario_atencion VARCHAR(255),
    numero_licencia VARCHAR(100),
    fecha_licencia DATE,
    certificaciones TEXT, -- JSON con certificaciones
    calificacion_promedio DECIMAL(3,2) DEFAULT 0.00,
    numero_resenas INT DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: operadores_turisticos
-- Información de operadores turísticos
-- =====================================================
CREATE TABLE operadores_turisticos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre_empresa VARCHAR(255) NOT NULL,
    nit VARCHAR(50) UNIQUE NOT NULL,
    rnt VARCHAR(50),
    tipo_operador ENUM('Local', 'Regional', 'Nacional', 'Internacional'),
    especialidad VARCHAR(255), -- Tours culturales, aventura, etc.
    capacidad_maxima_grupo INT,
    idiomas_disponibles TEXT, -- JSON
    seguro_responsabilidad VARCHAR(255),
    poliza_numero VARCHAR(100),
    vehiculos_propios BOOLEAN DEFAULT FALSE,
    numero_guias INT DEFAULT 0,
    calificacion_promedio DECIMAL(3,2) DEFAULT 0.00,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: restaurantes
-- Información de restaurantes
-- =====================================================
CREATE TABLE restaurantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre_establecimiento VARCHAR(255) NOT NULL,
    nit VARCHAR(50) UNIQUE NOT NULL,
    tipo_cocina VARCHAR(255), -- Colombiana, Internacional, Fusion, etc.
    rango_precios ENUM('$', '$$', '$$$', '$$$$'),
    capacidad_comensales INT,
    horario_apertura TIME,
    horario_cierre TIME,
    dias_atencion VARCHAR(100), -- Lun-Dom
    reservas_online BOOLEAN DEFAULT FALSE,
    delivery BOOLEAN DEFAULT FALSE,
    menu_digital_url VARCHAR(500),
    certificado_manipulacion BOOLEAN DEFAULT FALSE,
    fecha_certificado DATE,
    calificacion_promedio DECIMAL(3,2) DEFAULT 0.00,
    especialidades TEXT, -- JSON con platos destacados
    opciones_vegetarianas BOOLEAN DEFAULT FALSE,
    opciones_veganas BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: hospedajes
-- Información de hoteles y alojamientos
-- =====================================================
CREATE TABLE hospedajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre_establecimiento VARCHAR(255) NOT NULL,
    nit VARCHAR(50) UNIQUE NOT NULL,
    rnt VARCHAR(50),
    tipo_hospedaje ENUM('Hotel', 'Hostal', 'Casa Rural', 'Apartamento', 'Glamping'),
    categoria_estrellas INT, -- 1-5
    numero_habitaciones INT,
    capacidad_total_personas INT,
    hora_checkin TIME DEFAULT '15:00:00',
    hora_checkout TIME DEFAULT '12:00:00',
    servicios_incluidos TEXT, -- JSON: wifi, desayuno, parqueadero, etc.
    precio_minimo_noche DECIMAL(10,2),
    precio_maximo_noche DECIMAL(10,2),
    politica_cancelacion TEXT,
    acepta_mascotas BOOLEAN DEFAULT FALSE,
    accesibilidad_discapacidad BOOLEAN DEFAULT FALSE,
    calificacion_promedio DECIMAL(3,2) DEFAULT 0.00,
    fotos_galeria TEXT, -- JSON con URLs de fotos
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: instituciones_gobierno
-- Información de entidades gubernamentales
-- =====================================================
CREATE TABLE instituciones_gobierno (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre_institucion VARCHAR(255) NOT NULL,
    nit VARCHAR(50) UNIQUE NOT NULL,
    tipo_entidad ENUM('Municipal', 'Departamental', 'Nacional'),
    area_responsabilidad VARCHAR(255), -- Turismo, Cultura, etc.
    funcionario_responsable VARCHAR(255),
    cargo_funcionario VARCHAR(255),
    nivel_acceso ENUM('Lectura', 'Editor', 'Administrador') DEFAULT 'Lectura',
    permisos_especiales TEXT, -- JSON con permisos detallados
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: tours
-- Catálogo de tours disponibles
-- =====================================================
CREATE TABLE tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operador_id INT NOT NULL,
    nombre_tour VARCHAR(255) NOT NULL,
    descripcion TEXT,
    duracion_horas DECIMAL(4,2),
    precio_adulto DECIMAL(10,2),
    precio_nino DECIMAL(10,2),
    precio_tercera_edad DECIMAL(10,2),
    incluye TEXT, -- Qué incluye el tour
    no_incluye TEXT, -- Qué no incluye
    punto_encuentro VARCHAR(500),
    idiomas_disponibles VARCHAR(255),
    dificultad ENUM('Fácil', 'Moderada', 'Difícil'),
    edad_minima INT,
    cupo_minimo INT,
    cupo_maximo INT,
    activo BOOLEAN DEFAULT TRUE,
    destacado BOOLEAN DEFAULT FALSE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operador_id) REFERENCES operadores_turisticos(id) ON DELETE CASCADE,
    INDEX idx_activo (activo),
    INDEX idx_destacado (destacado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: reservas
-- Reservas realizadas por turistas
-- =====================================================
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    turista_id INT NOT NULL,
    tipo_reserva ENUM('tour', 'restaurante', 'hospedaje') NOT NULL,
    referencia_id INT NOT NULL, -- ID del tour, restaurante u hospedaje
    fecha_reserva DATE NOT NULL,
    hora_reserva TIME,
    numero_personas INT NOT NULL,
    precio_total DECIMAL(10,2),
    estado ENUM('Pendiente', 'Confirmada', 'Cancelada', 'Completada') DEFAULT 'Pendiente',
    metodo_pago ENUM('Efectivo', 'Tarjeta', 'Transferencia', 'PayPal', 'Otro'),
    comprobante_pago VARCHAR(500),
    notas_especiales TEXT,
    codigo_confirmacion VARCHAR(50) UNIQUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (turista_id) REFERENCES turistas(id) ON DELETE CASCADE,
    INDEX idx_fecha_reserva (fecha_reserva),
    INDEX idx_estado (estado),
    INDEX idx_tipo_reserva (tipo_reserva)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: resenas
-- Reseñas y calificaciones
-- =====================================================
CREATE TABLE resenas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_entidad ENUM('tour', 'restaurante', 'hospedaje', 'agencia', 'operador', 'general') NOT NULL,
    entidad_id INT, -- ID de la entidad reseñada (puede ser NULL para reseñas generales)
    calificacion INT NOT NULL CHECK (calificacion >= 1 AND calificacion <= 5),
    titulo VARCHAR(255),
    comentario TEXT NOT NULL,
    respuesta TEXT, -- Respuesta del prestador del servicio
    fecha_respuesta DATETIME,
    verificada BOOLEAN DEFAULT FALSE, -- Si la visita fue verificada
    reportada BOOLEAN DEFAULT FALSE,
    visible BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_tipo_entidad (tipo_entidad, entidad_id),
    INDEX idx_calificacion (calificacion),
    INDEX idx_visible (visible)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: galeria_multimedia
-- Imágenes y videos de la plataforma
-- =====================================================
CREATE TABLE galeria_multimedia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_contenido ENUM('imagen', 'video') NOT NULL,
    url VARCHAR(500) NOT NULL,
    titulo VARCHAR(255),
    descripcion TEXT,
    tipo_entidad ENUM('catedral', 'ciudad', 'tour', 'restaurante', 'hotel', 'evento', 'general'),
    entidad_id INT, -- ID relacionado si aplica
    subido_por INT, -- usuario_id
    orden_visualizacion INT DEFAULT 0,
    destacado BOOLEAN DEFAULT FALSE,
    activo BOOLEAN DEFAULT TRUE,
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subido_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_tipo_entidad (tipo_entidad),
    INDEX idx_destacado (destacado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: eventos
-- Eventos turísticos y culturales
-- =====================================================
CREATE TABLE eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_evento VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME,
    ubicacion VARCHAR(500),
    organizador_id INT,
    tipo_evento ENUM('Cultural', 'Deportivo', 'Gastronómico', 'Musical', 'Religioso', 'Otro'),
    precio_entrada DECIMAL(10,2),
    entrada_gratis BOOLEAN DEFAULT FALSE,
    cupo_limitado BOOLEAN DEFAULT FALSE,
    cupo_disponible INT,
    imagen_evento VARCHAR(500),
    contacto_informacion VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (organizador_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha_inicio (fecha_inicio),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: estadisticas_visitantes
-- Métricas y análisis de visitantes
-- =====================================================
CREATE TABLE estadisticas_visitantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    numero_visitantes INT DEFAULT 0,
    visitantes_nacionales INT DEFAULT 0,
    visitantes_extranjeros INT DEFAULT 0,
    origen_principal VARCHAR(100),
    edad_promedio DECIMAL(5,2),
    gasto_promedio DECIMAL(10,2),
    satisfaccion_promedio DECIMAL(3,2),
    lugares_mas_visitados TEXT, -- JSON
    temporada ENUM('Alta', 'Media', 'Baja'),
    fuente_datos VARCHAR(100),
    observaciones TEXT,
    UNIQUE KEY unique_fecha (fecha),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: notificaciones
-- Sistema de notificaciones para usuarios
-- =====================================================
CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_notificacion ENUM('reserva', 'mensaje', 'resena', 'promocion', 'sistema', 'alerta'),
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    leida BOOLEAN DEFAULT FALSE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    url_accion VARCHAR(500), -- URL para acción relacionada
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_leida (usuario_id, leida),
    INDEX idx_fecha (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: mensajes
-- Sistema de mensajería entre usuarios
-- =====================================================
CREATE TABLE mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    remitente_id INT NOT NULL,
    destinatario_id INT NOT NULL,
    asunto VARCHAR(255),
    mensaje TEXT NOT NULL,
    leido BOOLEAN DEFAULT FALSE,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_lectura DATETIME,
    respuesta_a INT, -- ID del mensaje al que responde
    FOREIGN KEY (remitente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (destinatario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (respuesta_a) REFERENCES mensajes(id) ON DELETE SET NULL,
    INDEX idx_destinatario_leido (destinatario_id, leido),
    INDEX idx_fecha (fecha_envio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: configuracion_sistema
-- Configuraciones globales del sistema
-- =====================================================
CREATE TABLE configuracion_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descripcion TEXT,
    tipo_dato ENUM('texto', 'numero', 'booleano', 'json') DEFAULT 'texto',
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: auditoria
-- Registro de acciones importantes (Cumplimiento Habeas Data)
-- =====================================================
CREATE TABLE auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(255) NOT NULL,
    tabla_afectada VARCHAR(100),
    registro_id INT,
    datos_anteriores TEXT, -- JSON con datos antes del cambio
    datos_nuevos TEXT, -- JSON con datos después del cambio
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    fecha_accion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha_accion),
    INDEX idx_tabla (tabla_afectada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERTAR DATOS DE CONFIGURACIÓN INICIAL
-- =====================================================
INSERT INTO configuracion_sistema (clave, valor, descripcion, tipo_dato) VALUES
('nombre_plataforma', 'Zipaquirá Turística', 'Nombre de la plataforma', 'texto'),
('email_contacto', 'info@zipaquiraturistica.com', 'Email de contacto principal', 'texto'),
('telefono_contacto', '+57 1 8512000', 'Teléfono de contacto', 'texto'),
('habeas_data_version', '1.0', 'Versión actual de políticas de Habeas Data', 'texto'),
('terminos_version', '1.0', 'Versión actual de términos y condiciones', 'texto'),
('calificacion_minima', '1', 'Calificación mínima permitida', 'numero'),
('calificacion_maxima', '5', 'Calificación máxima permitida', 'numero'),
('permitir_registro_publico', 'true', 'Permitir registro público de usuarios', 'booleano'),
('modo_mantenimiento', 'false', 'Activar modo mantenimiento', 'booleano');

-- =====================================================
-- VISTAS ÚTILES PARA REPORTES
-- =====================================================

-- Vista: Resumen de agencias con sus métricas
CREATE VIEW v_agencias_resumen AS
SELECT 
    a.id,
    a.razon_social,
    a.nit,
    a.rnt,
    u.email,
    u.telefono,
    a.calificacion_promedio,
    a.numero_resenas,
    u.activo,
    u.fecha_registro
FROM agencias_turismo a
INNER JOIN usuarios u ON a.usuario_id = u.id;

-- Vista: Resumen de restaurantes con métricas
CREATE VIEW v_restaurantes_resumen AS
SELECT 
    r.id,
    r.nombre_establecimiento,
    r.tipo_cocina,
    r.rango_precios,
    r.calificacion_promedio,
    u.email,
    u.telefono,
    u.direccion,
    u.activo
FROM restaurantes r
INNER JOIN usuarios u ON r.usuario_id = u.id;

-- Vista: Estadísticas generales de turismo
CREATE VIEW v_estadisticas_generales AS
SELECT 
    DATE_FORMAT(fecha, '%Y-%m') as mes,
    SUM(numero_visitantes) as total_visitantes,
    AVG(satisfaccion_promedio) as satisfaccion_promedio,
    SUM(visitantes_nacionales) as total_nacionales,
    SUM(visitantes_extranjeros) as total_extranjeros
FROM estadisticas_visitantes
GROUP BY DATE_FORMAT(fecha, '%Y-%m')
ORDER BY mes DESC;

-- Vista: Reseñas recientes con información del usuario
CREATE VIEW v_resenas_recientes AS
SELECT 
    r.id,
    r.calificacion,
    r.titulo,
    r.comentario,
    r.tipo_entidad,
    r.fecha_creacion,
    u.nombre_completo,
    u.tipo_usuario
FROM resenas r
INNER JOIN usuarios u ON r.usuario_id = u.id
WHERE r.visible = TRUE
ORDER BY r.fecha_creacion DESC;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS ÚTILES
-- =====================================================

DELIMITER //

-- Procedimiento: Actualizar calificación promedio de una entidad
CREATE PROCEDURE sp_actualizar_calificacion_promedio(
    IN p_tipo_entidad VARCHAR(50),
    IN p_entidad_id INT
)
BEGIN
    DECLARE v_calificacion_promedio DECIMAL(3,2);
    DECLARE v_numero_resenas INT;
    
    -- Calcular promedio y número de reseñas
    SELECT 
        AVG(calificacion),
        COUNT(*)
    INTO v_calificacion_promedio, v_numero_resenas
    FROM resenas
    WHERE tipo_entidad = p_tipo_entidad 
        AND entidad_id = p_entidad_id
        AND visible = TRUE;
    
    -- Actualizar según el tipo de entidad
    CASE p_tipo_entidad
        WHEN 'agencia' THEN
            UPDATE agencias_turismo 
            SET calificacion_promedio = IFNULL(v_calificacion_promedio, 0),
                numero_resenas = v_numero_resenas
            WHERE id = p_entidad_id;
            
        WHEN 'operador' THEN
            UPDATE operadores_turisticos 
            SET calificacion_promedio = IFNULL(v_calificacion_promedio, 0)
            WHERE id = p_entidad_id;
            
        WHEN 'restaurante' THEN
            UPDATE restaurantes 
            SET calificacion_promedio = IFNULL(v_calificacion_promedio, 0)
            WHERE id = p_entidad_id;
            
        WHEN 'hospedaje' THEN
            UPDATE hospedajes 
            SET calificacion_promedio = IFNULL(v_calificacion_promedio, 0)
            WHERE id = p_entidad_id;
    END CASE;
END//

-- Procedimiento: Registrar acción de auditoría
CREATE PROCEDURE sp_registrar_auditoria(
    IN p_usuario_id INT,
    IN p_accion VARCHAR(255),
    IN p_tabla VARCHAR(100),
    IN p_registro_id INT,
    IN p_datos_anteriores TEXT,
    IN p_datos_nuevos TEXT,
    IN p_ip VARCHAR(45)
)
BEGIN
    INSERT INTO auditoria (
        usuario_id, accion, tabla_afectada, registro_id,
        datos_anteriores, datos_nuevos, ip_address
    ) VALUES (
        p_usuario_id, p_accion, p_tabla, p_registro_id,
        p_datos_anteriores, p_datos_nuevos, p_ip
    );
END//

DELIMITER ;

-- =====================================================
-- TRIGGERS PARA AUDITORÍA AUTOMÁTICA
-- =====================================================

DELIMITER //

-- Trigger: Auditar eliminación de usuarios
CREATE TRIGGER trg_usuarios_before_delete
BEFORE DELETE ON usuarios
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (usuario_id, accion, tabla_afectada, registro_id, datos_anteriores)
    VALUES (OLD.id, 'DELETE', 'usuarios', OLD.id, 
            JSON_OBJECT('email', OLD.email, 'nombre', OLD.nombre_completo, 'tipo', OLD.tipo_usuario));
END//

-- Trigger: Auditar cambios en reseñas
CREATE TRIGGER trg_resenas_after_update
AFTER UPDATE ON resenas
FOR EACH ROW
BEGIN
    IF OLD.visible != NEW.visible THEN
        INSERT INTO auditoria (usuario_id, accion, tabla_afectada, registro_id, datos_anteriores, datos_nuevos)
        VALUES (NEW.usuario_id, 'UPDATE_VISIBILITY', 'resenas', NEW.id,
                JSON_OBJECT('visible', OLD.visible), 
                JSON_OBJECT('visible', NEW.visible));
    END IF;
END//

DELIMITER ;

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================

CREATE INDEX idx_usuarios_fecha_registro ON usuarios(fecha_registro);
CREATE INDEX idx_reservas_fecha_estado ON reservas(fecha_reserva, estado);
CREATE INDEX idx_resenas_fecha_visible ON resenas(fecha_creacion, visible);
CREATE INDEX idx_tours_precio ON tours(precio_adulto);

-- =====================================================
-- FIN DEL SCRIPT DE BASE DE DATOS
-- =====================================================