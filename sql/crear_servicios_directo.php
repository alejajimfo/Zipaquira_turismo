<?php
/**
 * Script para crear tablas de servicios directamente
 */

include_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h2>Creando tablas de servicios turísticos...</h2><hr>";

// Desactivar foreign key checks
$conn->exec("SET FOREIGN_KEY_CHECKS = 0");

// Eliminar tablas si existen
$tablas = ['favoritos', 'perfil_turista', 'programa_fotos', 'programas_gobierno', 'disponibilidad', 'promociones', 'servicio_fotos', 'servicios'];
foreach ($tablas as $tabla) {
    $conn->exec("DROP TABLE IF EXISTS $tabla");
}

echo "<p style='color: green;'>✓ Tablas antiguas eliminadas</p>";

// Crear tabla servicios
$sql_servicios = "CREATE TABLE servicios (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $conn->exec($sql_servicios);
    echo "<p style='color: green;'>✓ Tabla 'servicios' creada</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error creando 'servicios': " . $e->getMessage() . "</p>";
    exit;
}

// Crear tabla servicio_fotos
$sql_fotos = "CREATE TABLE servicio_fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servicio_id INT NOT NULL,
    url_foto VARCHAR(500) NOT NULL,
    descripcion VARCHAR(255),
    es_principal BOOLEAN DEFAULT FALSE,
    orden INT DEFAULT 0,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE,
    INDEX idx_servicio (servicio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $conn->exec($sql_fotos);
    echo "<p style='color: green;'>✓ Tabla 'servicio_fotos' creada</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Crear tabla promociones
$sql_promociones = "CREATE TABLE promociones (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $conn->exec($sql_promociones);
    echo "<p style='color: green;'>✓ Tabla 'promociones' creada</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Crear tabla disponibilidad
$sql_disponibilidad = "CREATE TABLE disponibilidad (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $conn->exec($sql_disponibilidad);
    echo "<p style='color: green;'>✓ Tabla 'disponibilidad' creada</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Crear tabla programas_gobierno
$sql_programas = "CREATE TABLE programas_gobierno (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $conn->exec($sql_programas);
    echo "<p style='color: green;'>✓ Tabla 'programas_gobierno' creada</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Crear tabla programa_fotos
$sql_programa_fotos = "CREATE TABLE programa_fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    programa_id INT NOT NULL,
    url_foto VARCHAR(500) NOT NULL,
    descripcion VARCHAR(255),
    orden INT DEFAULT 0,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (programa_id) REFERENCES programas_gobierno(id) ON DELETE CASCADE,
    INDEX idx_programa (programa_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $conn->exec($sql_programa_fotos);
    echo "<p style='color: green;'>✓ Tabla 'programa_fotos' creada</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Crear tabla perfil_turista
$sql_perfil = "CREATE TABLE perfil_turista (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $conn->exec($sql_perfil);
    echo "<p style='color: green;'>✓ Tabla 'perfil_turista' creada</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Crear tabla favoritos
$sql_favoritos = "CREATE TABLE favoritos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    servicio_id INT NOT NULL,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorito (usuario_id, servicio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $conn->exec($sql_favoritos);
    echo "<p style='color: green;'>✓ Tabla 'favoritos' creada</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Reactivar foreign key checks
$conn->exec("SET FOREIGN_KEY_CHECKS = 1");

echo "<hr><h3 style='color: green;'>✅ ¡Todas las tablas creadas exitosamente!</h3>";

// Crear tabla reservas
$sql_reservas = "CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servicio_id INT NOT NULL,
    turista_id INT NOT NULL,
    fecha_reserva DATE NOT NULL,
    hora_reserva TIME,
    numero_personas INT NOT NULL DEFAULT 1,
    precio_total DECIMAL(10, 2),
    estado ENUM('pendiente', 'confirmada', 'cancelada', 'completada') DEFAULT 'pendiente',
    notas_turista TEXT,
    notas_proveedor TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE,
    FOREIGN KEY (turista_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_servicio (servicio_id),
    INDEX idx_turista (turista_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_reserva)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $conn->exec($sql_reservas);
    echo "<p style='color: green;'>✓ Tabla 'reservas' creada</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Insertar datos de ejemplo
echo "<hr><h3>Insertando datos de ejemplo...</h3>";

try {
    $stmt = $conn->query("SELECT id FROM usuarios LIMIT 1");
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        $usuario_id = $usuario['id'];
        
        $conn->exec("INSERT INTO servicios (usuario_id, tipo_servicio, nombre_servicio, rnt, descripcion, direccion, telefono, email, horario_apertura, horario_cierre, precio_desde, precio_hasta, activo) 
                     VALUES ($usuario_id, 'hospedaje', 'Hotel Cacique Real', 'RNT-12345', 'Hotel colonial en el centro histórico de Zipaquirá', 'Calle 4 # 3-25', '3001234567', 'info@hotelcaciquereal.com', '00:00:00', '23:59:59', 150000, 350000, 1)");
        
        $conn->exec("INSERT INTO servicios (usuario_id, tipo_servicio, nombre_servicio, rnt, descripcion, direccion, telefono, email, horario_apertura, horario_cierre, precio_desde, precio_hasta, activo) 
                     VALUES ($usuario_id, 'restaurante', 'Restaurante La Sal', 'RNT-12346', 'Gastronomía típica colombiana', 'Carrera 7 # 5-12', '3009876543', 'info@lasal.com', '08:00:00', '22:00:00', 25000, 80000, 1)");
        
        $conn->exec("INSERT INTO servicios (usuario_id, tipo_servicio, nombre_servicio, rnt, descripcion, direccion, telefono, email, horario_apertura, horario_cierre, precio_desde, precio_hasta, activo) 
                     VALUES ($usuario_id, 'operador', 'Tours Zipaquirá', 'RNT-12347', 'Tours guiados por la ciudad y la Catedral de Sal', 'Calle 3 # 6-45', '3101234567', 'info@tourszipa.com', '07:00:00', '18:00:00', 50000, 150000, 1)");
        
        echo "<p style='color: green;'>✓ 3 servicios de ejemplo creados</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>✅ ¡Instalación completada!</h3>";
echo "<p><a href='../diagnostico_servicio.html' style='background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0;'>Probar Diagnóstico</a></p>";
echo "<p><a href='../verificar_tabla_servicios.php'>Ver estructura de tablas</a></p>";
echo "<p><a href='../index.html'>← Volver a la aplicación</a></p>";
?>
