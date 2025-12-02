<?php
include_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h2>Verificación de Tabla 'servicios'</h2>";

try {
    // Verificar si la tabla existe
    $stmt = $conn->query("SHOW TABLES LIKE 'servicios'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ La tabla 'servicios' existe</p>";
        
        // Mostrar estructura
        echo "<h3>Estructura de la tabla:</h3>";
        $stmt = $conn->query("DESCRIBE servicios");
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "<td>{$row['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Contar registros
        $stmt = $conn->query("SELECT COUNT(*) as total FROM servicios");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Total de registros: <strong>{$row['total']}</strong></p>";
        
    } else {
        echo "<p style='color: red;'>✗ La tabla 'servicios' NO existe</p>";
        echo "<p>Ejecuta: <a href='sql/ejecutar_servicios.php'>sql/ejecutar_servicios.php</a></p>";
    }
    
    // Verificar tabla usuarios
    echo "<hr><h3>Verificación de tabla 'usuarios':</h3>";
    $stmt = $conn->query("SHOW TABLES LIKE 'usuarios'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ La tabla 'usuarios' existe</p>";
        $stmt = $conn->query("SELECT COUNT(*) as total FROM usuarios");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Total de usuarios: <strong>{$row['total']}</strong></p>";
        
        if ($row['total'] > 0) {
            $stmt = $conn->query("SELECT id, nombre_completo, tipo_usuario FROM usuarios LIMIT 5");
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Tipo</th></tr>";
            while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$user['id']}</td>";
                echo "<td>{$user['nombre_completo']}</td>";
                echo "<td>{$user['tipo_usuario']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>✗ La tabla 'usuarios' NO existe</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
