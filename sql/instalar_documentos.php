<?php
include_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h2>Instalando Campos de Documentos...</h2><hr>";

try {
    // Verificar si ya existen los campos
    $check = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'documento_identidad'");
    
    if ($check->rowCount() > 0) {
        echo "<p style='color: orange;'>⚠️ Los campos ya existen. No es necesario instalar.</p>";
    } else {
        $sql = file_get_contents('agregar_documentos.sql');
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (empty($statement) || strpos($statement, '--') === 0) continue;
            $conn->exec($statement);
        }
        
        echo "<p style='color: green;'>✓ Campos de documentos agregados exitosamente</p>";
    }
    
    echo "<hr><h3 style='color: green;'>✅ Instalación completada</h3>";
    echo "<p><a href='../index.html'>← Volver a la aplicación</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}
?>
