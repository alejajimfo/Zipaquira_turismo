<?php
include_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h2>Instalando Sistema de Reservas...</h2><hr>";

try {
    $sql = file_get_contents('crear_reservas.sql');
    $conn->exec($sql);
    echo "<p style='color: green;'>✓ Tabla 'reservas' creada exitosamente</p>";
    
    // Verificar
    $stmt = $conn->query("DESCRIBE reservas");
    echo "<h3>Estructura de la tabla:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr><h3 style='color: green;'>✅ Sistema de reservas instalado correctamente</h3>";
    echo "<p><a href='../index.html'>← Volver a la aplicación</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}
?>
