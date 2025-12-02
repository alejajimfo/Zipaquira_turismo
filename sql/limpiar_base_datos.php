<?php
/**
 * Script para limpiar la base de datos
 * ADVERTENCIA: Esto eliminará TODOS los datos de prueba
 * Ejecutar desde: http://localhost/zipaquira-turismo/sql/limpiar_base_datos.php
 */

include_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h2>🧹 Limpieza de Base de Datos</h2>";
echo "<p style='color: orange;'><strong>ADVERTENCIA:</strong> Esto eliminará todos los datos de prueba pero mantendrá la estructura de las tablas.</p>";
echo "<hr>";

// Confirmar acción
if (!isset($_GET['confirmar'])) {
    echo "<h3>¿Estás seguro?</h3>";
    echo "<p>Esta acción eliminará:</p>";
    echo "<ul>";
    echo "<li>Todos los servicios de prueba</li>";
    echo "<li>Todas las reservas</li>";
    echo "<li>Todos los programas gubernamentales</li>";
    echo "<li>Todas las fotos</li>";
    echo "<li>Todas las promociones</li>";
    echo "<li><strong>NO eliminará usuarios</strong></li>";
    echo "</ul>";
    echo "<p><a href='?confirmar=si' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0;'>✓ Sí, limpiar base de datos</a></p>";
    echo "<p><a href='../index.html'>← Cancelar y volver</a></p>";
    exit();
}

echo "<h3>Limpiando base de datos...</h3>";

try {
    // Desactivar foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Limpiar tablas (mantener estructura)
    $tablas = [
        'favoritos',
        'programa_fotos',
        'programas_gobierno',
        'disponibilidad',
        'promociones',
        'servicio_fotos',
        'reservas',
        'servicios',
        'perfil_turista'
    ];
    
    foreach ($tablas as $tabla) {
        try {
            $conn->exec("TRUNCATE TABLE $tabla");
            echo "<p style='color: green;'>✓ Tabla '$tabla' limpiada</p>";
        } catch (PDOException $e) {
            echo "<p style='color: orange;'>⚠ Tabla '$tabla': " . $e->getMessage() . "</p>";
        }
    }
    
    // Reactivar foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "<hr>";
    echo "<h3 style='color: green;'>✅ Base de datos limpiada exitosamente</h3>";
    echo "<p>Las tablas están vacías pero la estructura se mantiene intacta.</p>";
    echo "<p><strong>Los usuarios NO fueron eliminados.</strong></p>";
    
    // Mostrar resumen
    echo "<hr><h3>Resumen:</h3>";
    foreach ($tablas as $tabla) {
        try {
            $stmt = $conn->query("SELECT COUNT(*) as total FROM $tabla");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>📊 $tabla: <strong>{$row['total']}</strong> registros</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Error en $tabla: " . $e->getMessage() . "</p>";
        }
    }
    
    // Contar usuarios
    try {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM usuarios");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<hr><p>👥 Usuarios mantenidos: <strong>{$row['total']}</strong></p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='../index.html' style='background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0;'>← Volver a la aplicación</a></p>";
echo "<p><a href='../test_sistema.html'>Ir a Test del Sistema</a></p>";
?>
