<?php
/**
 * Script para RESET COMPLETO de la base de datos
 * ADVERTENCIA: Esto eliminará TODO incluyendo usuarios
 * Ejecutar desde: http://localhost/zipaquira-turismo/sql/reset_completo.php
 */

include_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h2>🔄 Reset Completo de Base de Datos</h2>";
echo "<p style='color: red;'><strong>⚠️ ADVERTENCIA MÁXIMA:</strong> Esto eliminará TODOS los datos incluyendo usuarios.</p>";
echo "<hr>";

// Confirmar acción
if (!isset($_GET['confirmar'])) {
    echo "<h3 style='color: red;'>¿Estás COMPLETAMENTE seguro?</h3>";
    echo "<p>Esta acción eliminará:</p>";
    echo "<ul style='color: red;'>";
    echo "<li><strong>TODOS los usuarios</strong></li>";
    echo "<li>Todos los servicios</li>";
    echo "<li>Todas las reservas</li>";
    echo "<li>Todos los programas</li>";
    echo "<li>Todas las fotos</li>";
    echo "<li>Todo el contenido</li>";
    echo "</ul>";
    echo "<p style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;'>";
    echo "<strong>⚠️ Nota:</strong> Después de esto tendrás que crear nuevos usuarios desde cero.";
    echo "</p>";
    echo "<p><a href='?confirmar=si' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0;'>⚠️ Sí, ELIMINAR TODO</a></p>";
    echo "<p><a href='../index.html' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>← Cancelar (Recomendado)</a></p>";
    exit();
}

echo "<h3>Eliminando TODOS los datos...</h3>";

try {
    // Desactivar foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Limpiar TODAS las tablas
    $tablas = [
        'favoritos',
        'perfil_turista',
        'programa_fotos',
        'programas_gobierno',
        'disponibilidad',
        'promociones',
        'servicio_fotos',
        'reservas',
        'servicios',
        'notificaciones',
        'resenas',
        'estadisticas',
        'usuarios'  // ⚠️ Incluye usuarios
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
    echo "<h3 style='color: green;'>✅ Base de datos completamente limpia</h3>";
    echo "<p>Todas las tablas están vacías. El sistema está como recién instalado.</p>";
    
    echo "<hr><h3>Próximos pasos:</h3>";
    echo "<ol>";
    echo "<li>Ir a la aplicación</li>";
    echo "<li>Crear tu primer usuario</li>";
    echo "<li>Comenzar a usar el sistema</li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='../index.html' style='background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0;'>← Ir a la aplicación</a></p>";
?>
