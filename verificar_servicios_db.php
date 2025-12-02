<?php
header("Content-Type: text/html; charset=UTF-8");

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

echo "<html><head><meta charset='UTF-8'>";
echo "<style>
    body { font-family: Arial; padding: 20px; background: #f5f5f5; }
    .container { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
    h2 { color: #0066cc; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
    th { background: #0066cc; color: white; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { background: #e7f3ff; padding: 10px; border-left: 4px solid #0066cc; margin: 10px 0; }
</style></head><body>";

echo "<h1>Verificación de Servicios en Base de Datos</h1>";

// 1. Verificar que la tabla existe
echo "<div class='container'>";
echo "<h2>1. Verificar Tabla 'servicios'</h2>";
try {
    $query = "SHOW TABLES LIKE 'servicios'";
    $stmt = $db->query($query);
    $exists = $stmt->rowCount() > 0;
    
    if ($exists) {
        echo "<p class='success'>✓ La tabla 'servicios' existe</p>";
    } else {
        echo "<p class='error'>✗ La tabla 'servicios' NO existe</p>";
        echo "<p>Ejecuta: <a href='sql/crear_servicios_directo.php'>crear_servicios_directo.php</a></p>";
        exit;
    }
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    exit;
}
echo "</div>";

// 2. Ver estructura de la tabla
echo "<div class='container'>";
echo "<h2>2. Estructura de la Tabla</h2>";
try {
    $query = "DESCRIBE servicios";
    $stmt = $db->query($query);
    echo "<table><tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 3. Contar servicios totales
echo "<div class='container'>";
echo "<h2>3. Total de Servicios</h2>";
try {
    $query = "SELECT COUNT(*) as total FROM servicios";
    $stmt = $db->query($query);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $row['total'];
    
    echo "<p class='info'>Total de servicios en la base de datos: <strong>{$total}</strong></p>";
    
    if ($total == 0) {
        echo "<p class='error'>No hay servicios registrados. Necesitas crear servicios primero.</p>";
    }
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 4. Ver todos los servicios
echo "<div class='container'>";
echo "<h2>4. Todos los Servicios</h2>";
try {
    $query = "SELECT s.id, s.usuario_id, s.tipo_servicio, s.nombre_servicio, s.rnt, s.activo, 
              u.nombre_completo, u.email, u.tipo_usuario
              FROM servicios s
              LEFT JOIN usuarios u ON s.usuario_id = u.id
              ORDER BY s.id DESC";
    $stmt = $db->query($query);
    
    if ($stmt->rowCount() > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Usuario ID</th><th>Tipo Usuario</th><th>Tipo Servicio</th><th>Nombre</th><th>RNT</th><th>Propietario</th><th>Activo</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $activo = $row['activo'] ? 'Sí' : 'No';
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['usuario_id']}</td>";
            echo "<td>{$row['tipo_usuario']}</td>";
            echo "<td><strong>{$row['tipo_servicio']}</strong></td>";
            echo "<td>{$row['nombre_servicio']}</td>";
            echo "<td>{$row['rnt']}</td>";
            echo "<td>{$row['nombre_completo']}<br><small>{$row['email']}</small></td>";
            echo "<td>{$activo}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay servicios registrados.</p>";
    }
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 5. Servicios por tipo
echo "<div class='container'>";
echo "<h2>5. Servicios por Tipo</h2>";
try {
    $query = "SELECT tipo_servicio, COUNT(*) as total 
              FROM servicios 
              GROUP BY tipo_servicio 
              ORDER BY total DESC";
    $stmt = $db->query($query);
    
    if ($stmt->rowCount() > 0) {
        echo "<table>";
        echo "<tr><th>Tipo de Servicio</th><th>Cantidad</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td><strong>{$row['tipo_servicio']}</strong></td>";
            echo "<td>{$row['total']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay servicios registrados.</p>";
    }
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 6. Servicios por usuario
echo "<div class='container'>";
echo "<h2>6. Servicios por Usuario</h2>";
try {
    $query = "SELECT u.id, u.nombre_completo, u.email, u.tipo_usuario, COUNT(s.id) as total_servicios
              FROM usuarios u
              LEFT JOIN servicios s ON u.id = s.usuario_id
              WHERE u.tipo_usuario IN ('hotel', 'restaurante', 'operador', 'agencia')
              GROUP BY u.id
              ORDER BY total_servicios DESC";
    $stmt = $db->query($query);
    
    if ($stmt->rowCount() > 0) {
        echo "<table>";
        echo "<tr><th>Usuario ID</th><th>Nombre</th><th>Email</th><th>Tipo</th><th>Servicios</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $color = $row['total_servicios'] > 0 ? 'green' : 'red';
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nombre_completo']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td><strong>{$row['tipo_usuario']}</strong></td>";
            echo "<td style='color: {$color}; font-weight: bold;'>{$row['total_servicios']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay usuarios proveedores registrados.</p>";
    }
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 7. Verificar usuarios sin servicios
echo "<div class='container'>";
echo "<h2>7. Proveedores SIN Servicios</h2>";
try {
    $query = "SELECT u.id, u.nombre_completo, u.email, u.tipo_usuario
              FROM usuarios u
              LEFT JOIN servicios s ON u.id = s.usuario_id
              WHERE u.tipo_usuario IN ('hotel', 'restaurante', 'operador', 'agencia')
              AND s.id IS NULL";
    $stmt = $db->query($query);
    
    if ($stmt->rowCount() > 0) {
        echo "<p class='info'>Estos usuarios proveedores NO tienen servicios registrados:</p>";
        echo "<table>";
        echo "<tr><th>Usuario ID</th><th>Nombre</th><th>Email</th><th>Tipo</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nombre_completo']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td><strong>{$row['tipo_usuario']}</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p class='info'>Estos usuarios necesitan crear servicios desde su Panel.</p>";
    } else {
        echo "<p class='success'>✓ Todos los proveedores tienen al menos un servicio registrado.</p>";
    }
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

echo "</body></html>";
?>
