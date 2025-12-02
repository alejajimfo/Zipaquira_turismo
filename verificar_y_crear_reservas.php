<?php
/**
 * Verificar y crear tabla reservas
 * Este script verifica si la tabla existe y la crea si es necesario
 */

include_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<meta charset='UTF-8'>";
echo "<title>Verificar y Crear Tabla Reservas</title>";
echo "<style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
    .success { color: green; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .error { color: red; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .info { color: #004085; background: #cce5ff; padding: 15px; border-radius: 5px; margin: 10px 0; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f0f0f0; }
    .btn { background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
</style>";
echo "</head><body>";

echo "<h1>Verificación de Tabla Reservas</h1>";
echo "<hr>";

// Paso 1: Verificar si la tabla existe
echo "<h2>Paso 1: Verificando si la tabla existe...</h2>";

try {
    $stmt = $conn->query("SHOW TABLES LIKE 'reservas'");
    $existe = $stmt->rowCount() > 0;
    
    if ($existe) {
        echo "<div class='success'>✓ La tabla 'reservas' existe</div>";
        
        // Verificar estructura
        echo "<h3>Estructura actual:</h3>";
        $stmt = $conn->query("DESCRIBE reservas");
        echo "<table>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        
        $columnas = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $columnas[] = $row['Field'];
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
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
            echo "<div class='success'>✓ Todas las columnas necesarias están presentes</div>";
        } else {
            echo "<div class='error'>✗ Faltan columnas: " . implode(', ', $columnas_faltantes) . "</div>";
            echo "<div class='info'>Se recomienda eliminar y recrear la tabla</div>";
        }
        
        // Contar  numero de registros 
        $stmt = $conn->query("SELECT COUNT(*) as total FROM reservas");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Total de reservas en la tabla: <strong>" . $row['total'] . "</strong></p>";
        
    } else {
        echo "<div class='error'>✗ La tabla 'reservas' NO existe</div>";
        echo "<div class='info'>Se procederá a crear la tabla...</div>";
        
        // Paso 2: Crear la tabla
        echo "<h2>Paso 2: Creando tabla reservas...</h2>";
        
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
            echo "<div class='success'>✓ Tabla 'reservas' creada exitosamente</div>";
            
            // Mostrar estructura
            echo "<h3>Estructura de la tabla creada:</h3>";
            $stmt = $conn->query("DESCRIBE reservas");
            echo "<table>";
            echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
        } catch (PDOException $e) {
            echo "<div class='error'>✗ Error al crear la tabla: " . $e->getMessage() . "</div>";
            echo "<h3>Posibles causas:</h3>";
            echo "<ul>";
            echo "<li>La tabla 'servicios' no existe</li>";
            echo "<li>La tabla 'usuarios' no existe</li>";
            echo "<li>Permisos insuficientes en la base de datos</li>";
            echo "</ul>";
            echo "<p>Ejecuta primero: <a href='sql/crear_servicios_directo.php'>crear_servicios_directo.php</a></p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>✗ Error de conexión: " . $e->getMessage() . "</div>";
}

// Paso 3: Verificar tablas relacionadas
echo "<hr>";
echo "<h2>Paso 3: Verificando tablas relacionadas...</h2>";

$tablas_necesarias = ['usuarios', 'servicios'];
foreach ($tablas_necesarias as $tabla) {
    try {
        $stmt = $conn->query("SHOW TABLES LIKE '$tabla'");
        if ($stmt->rowCount() > 0) {
            $stmt = $conn->query("SELECT COUNT(*) as total FROM $tabla");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<div class='success'>✓ Tabla '$tabla' existe con " . $row['total'] . " registros</div>";
        } else {
            echo "<div class='error'>✗ Tabla '$tabla' NO existe</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='error'>✗ Error verificando '$tabla': " . $e->getMessage() . "</div>";
    }
}

echo "<hr>";
echo "<h2>Resumen</h2>";

// Verificación final
try {
    $stmt = $conn->query("SHOW TABLES LIKE 'reservas'");
    if ($stmt->rowCount() > 0) {
        echo "<div class='success'>";
        echo "<h3>✓ Sistema de Reservas Listo</h3>";
        echo "<p>La tabla 'reservas' está correctamente configurada.</p>";
        echo "<p>Ahora puedes:</p>";
        echo "<ul>";
        echo "<li>Iniciar sesión como TURISTA</li>";
        echo "<li>Ir a la página de Inicio</li>";
        echo "<li>Hacer clic en 'Reservar' en cualquier servicio</li>";
        echo "<li>Completar el formulario de reserva</li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>✗ La tabla no se pudo crear</h3>";
        echo "<p>Revisa los errores anteriores y contacta al administrador.</p>";
        echo "</div>";
    }
} catch (PDOException $e) {
    echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
}

echo "<hr>";
echo "<a href='index.html' class='btn'>← Volver a la Aplicación</a>";
echo "<a href='test_mis_servicios_debug.html' class='btn'>Herramientas de Debug</a>";

echo "</body></html>";
?>
