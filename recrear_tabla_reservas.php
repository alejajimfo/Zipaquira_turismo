<?php
/**
 * Recrear tabla reservas con estructura correcta
 * ADVERTENCIA: Esto eliminará todas las reservas existentes
 */

include_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<meta charset='UTF-8'>";
echo "<title>Recrear Tabla Reservas</title>";
echo "<style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
    .success { color: green; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .error { color: red; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .info { color: #004085; background: #cce5ff; padding: 15px; border-radius: 5px; margin: 10px 0; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f0f0f0; }
    .btn { background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
</style>";
echo "</head><body>";

echo "<h1>Recrear Tabla Reservas</h1>";
echo "<hr>";

// Verificar si hay reservas existentes
echo "<h2>Paso 1: Verificando reservas existentes...</h2>";

try {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM reservas");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_reservas = $row['total'];
    
    if ($total_reservas > 0) {
        echo "<div class='warning'>";
        echo "<strong>⚠️ ADVERTENCIA:</strong> Hay $total_reservas reserva(s) en la tabla.<br>";
        echo "Al recrear la tabla, estas reservas se eliminarán.<br>";
        echo "Si necesitas conservarlas, haz un respaldo primero.";
        echo "</div>";
    } else {
        echo "<div class='info'>✓ No hay reservas en la tabla. Es seguro recrearla.</div>";
    }
} catch (PDOException $e) {
    echo "<div class='error'>Error verificando reservas: " . $e->getMessage() . "</div>";
}

// Paso 2: Eliminar tabla existente
echo "<h2>Paso 2: Eliminando tabla antigua...</h2>";

try {
    // Desactivar foreign key checks temporalmente
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    $conn->exec("DROP TABLE IF EXISTS reservas");
    echo "<div class='success'>✓ Tabla antigua eliminada</div>";
    
    // Reactivar foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    
} catch (PDOException $e) {
    echo "<div class='error'>✗ Error eliminando tabla: " . $e->getMessage() . "</div>";
    echo "</body></html>";
    exit;
}

// Paso 3: Crear tabla nueva con estructura correcta
echo "<h2>Paso 3: Creando tabla con estructura correcta...</h2>";

$sql_reservas = "CREATE TABLE reservas (
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
    echo "<div class='success'>✓ Tabla 'reservas' creada exitosamente con estructura correcta</div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>✗ Error creando tabla: " . $e->getMessage() . "</div>";
    echo "<h3>Posibles causas:</h3>";
    echo "<ul>";
    echo "<li>La tabla 'servicios' no existe</li>";
    echo "<li>La tabla 'usuarios' no existe</li>";
    echo "<li>Permisos insuficientes</li>";
    echo "</ul>";
    echo "</body></html>";
    exit;
}

// Paso 4: Verificar estructura
echo "<h2>Paso 4: Verificando estructura de la tabla...</h2>";

try {
    $stmt = $conn->query("DESCRIBE reservas");
    echo "<table>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    $columnas = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columnas[] = $row['Field'];
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar columnas necesarias
    $columnas_necesarias = ['id', 'servicio_id', 'turista_id', 'fecha_reserva', 'hora_reserva', 'numero_personas', 'precio_total', 'estado', 'notas_turista', 'notas_proveedor'];
    $columnas_faltantes = array_diff($columnas_necesarias, $columnas);
    
    if (empty($columnas_faltantes)) {
        echo "<div class='success'>";
        echo "<h3>✓ ¡Tabla creada correctamente!</h3>";
        echo "<p>Todas las columnas necesarias están presentes:</p>";
        echo "<ul>";
        foreach ($columnas_necesarias as $col) {
            echo "<li>✓ $col</li>";
        }
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>✗ Aún faltan columnas:</h3>";
        echo "<ul>";
        foreach ($columnas_faltantes as $col) {
            echo "<li>✗ $col</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>Error verificando estructura: " . $e->getMessage() . "</div>";
}

// Resumen final
echo "<hr>";
echo "<div class='success'>";
echo "<h2>✅ Proceso Completado</h2>";
echo "<p><strong>La tabla 'reservas' ha sido recreada con la estructura correcta.</strong></p>";
echo "<h3>Próximos pasos:</h3>";
echo "<ol>";
echo "<li>Vuelve a la aplicación</li>";
echo "<li>Inicia sesión como <strong>TURISTA</strong></li>";
echo "<li>Ve a la página de Inicio</li>";
echo "<li>Haz clic en 'Reservar' en cualquier servicio</li>";
echo "<li>Completa el formulario de reserva</li>";
echo "<li>Haz clic en 'Confirmar Reserva'</li>";
echo "<li>¡Debería funcionar sin errores!</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<a href='index.html' class='btn'>← Volver a la Aplicación</a>";
echo "<a href='verificar_y_crear_reservas.php' class='btn'>Verificar Tabla</a>";

echo "</body></html>";
?>
