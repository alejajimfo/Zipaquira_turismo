<?php
/**
 * Script para crear SOLO la tabla reservas
 * Ejecutar si ya tienes servicios y usuarios creados
 */

include_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h2>Creando tabla de reservas...</h2><hr>";

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
    echo "<p style='color: green;'>✓ Tabla 'reservas' creada exitosamente</p>";
    
    // Verificar estructura
    $stmt = $conn->query("DESCRIBE reservas");
    echo "<h3>Estructura de la tabla:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr><h3 style='color: green;'>✅ Tabla de reservas lista para usar</h3>";
    echo "<p>Ahora puedes crear reservas desde la aplicación.</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error creando tabla: " . $e->getMessage() . "</p>";
    echo "<p>Posibles causas:</p>";
    echo "<ul>";
    echo "<li>La tabla 'servicios' no existe (ejecuta crear_servicios_directo.php primero)</li>";
    echo "<li>La tabla 'usuarios' no existe</li>";
    echo "<li>Error de permisos en la base de datos</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='../index.html' style='background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>← Volver a la aplicación</a></p>";
?>
