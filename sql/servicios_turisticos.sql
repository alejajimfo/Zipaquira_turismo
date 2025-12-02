-- ============================================
-- TABLAS PARA SERVICIOS TURÍSTICOS
-- ============================================

-- Tabla para servicios (hospedaje, restaurante, operador, agencia)
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_servicio ENUM('hospedaje', 'restaurante', 'operador', 'agencia') NOT NULL,
    nombre_servicio VARCHAR(255) NOT NULL,
    rnt VARCHAR(50) NOT NULL COMMENT 'Registro Nacional de Turismo',
    descripcion TEXT,
    direccion VARCHAR(255),
    ubicacion_lat DECIMAL(10, 8),
    ubicacion_lng DECIMAL(11, 8),
    telefono VARCHAR(20),
    email VARCHAR(100),
    sitio_web VARCHAR(255),
    horario_apertura TIME,
    horario_cierre TIME,
    dias_operacion VARCHAR(100) COMMENT 'Lunes-Viernes, etc',
    precio_desde DECIMAL(10, 2),
    precio_hasta DECIMAL(10, 2),
    moneda VARCHAR(10) DEFAULT 'COP',
    capacidad INT,
    activo BOOLEAN DEFAULT TRUE,
    verificado BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_tipo_servicio (tipo_servicio),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para fotografías de servicios
CREATE TABLE IF NOT EXISTS servicio_fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servicio_id INT NOT NULL,
    url_foto VARCHAR(500) NOT NULL,
    descripcion VARCHAR(255),
    es_principal BOOLEAN DEFAULT FALSE,
    orden INT DEFAULT 0,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE,
    INDEX idx_servicio (servicio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para promociones
CREATE TABLE IF NOT EXISTS promociones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servicio_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    descuento_porcentaje DECIMAL(5, 2),
    precio_promocional DECIMAL(10, 2),
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE,
    INDEX idx_fechas (fecha_inicio, fecha_fin),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para disponibilidad
CREATE TABLE IF NOT EXISTS disponibilidad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servicio_id INT NOT NULL,
    fecha DATE NOT NULL,
    cupos_disponibles INT NOT NULL,
    cupos_totales INT NOT NULL,
    precio_dia DECIMAL(10, 2),
    notas TEXT,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_servicio_fecha (servicio_id, fecha),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para programas gubernamentales
CREATE TABLE IF NOT EXISTS programas_gobierno (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    actividades TEXT,
    valor DECIMAL(10, 2),
    ubicacion VARCHAR(255),
    ubicacion_lat DECIMAL(10, 8),
    ubicacion_lng DECIMAL(11, 8),
    fecha_inicio DATE,
    fecha_fin DATE,
    cupos_disponibles INT,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_activo (activo),
    INDEX idx_fechas (fecha_inicio, fecha_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para fotos de programas gubernamentales
CREATE TABLE IF NOT EXISTS programa_fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    programa_id INT NOT NULL,
    url_foto VARCHAR(500) NOT NULL,
    descripcion VARCHAR(255),
    orden INT DEFAULT 0,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (programa_id) REFERENCES programas_gobierno(id) ON DELETE CASCADE,
    INDEX idx_programa (programa_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para perfil de turista extendido
CREATE TABLE IF NOT EXISTS perfil_turista (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    foto_perfil VARCHAR(500),
    edad INT,
    ocupacion VARCHAR(100),
    intereses TEXT,
    pais_origen VARCHAR(100),
    idiomas VARCHAR(255),
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para favoritos de turistas
CREATE TABLE IF NOT EXISTS favoritos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    servicio_id INT NOT NULL,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorito (usuario_id, servicio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
