-- Tabla de reservas
CREATE TABLE IF NOT EXISTS reservas (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
