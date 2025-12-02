<?php
header("Content-Type: text/html; charset=UTF-8");
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

echo "<html><head><meta charset='UTF-8'>";
echo "<style>
    body { font-family: Arial; padding: 20px; background: #f5f5f5; }
    .box { background: white; padding: 20px; border-radius: 8px; margin: 10px 0; }
    h2 { color: #0066cc; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
    th { background: #0066cc; color: white; }
</style></head><body>";

echo "<h1>Debug: Panel de Proveedores</h1>";

// 1. Usuarios proveedores
echo "<div class='box'>";
echo "<h2>1. Usuarios Proveedores</h2>";
$query = "SELECT id, nombre_completo, email, tipo_usuario FROM usuarios 
          WHERE tipo_usuario IN ('hotel', 'restaurante', 'operador', 'agencia', 'gobierno')
          ORDER BY tipo_usuario, id";
$stmt = $db->query($query);
if ($stmt->rowCount() > 0) {
    echo "<table><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Tipo</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['nombre_completo']}</td><td>{$row['email']}</td><td><strong>{$row['tipo_usuario']}</strong></td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='warning'>No hay usuarios proveedores registrados</p>";
}
echo "</div>";

// 2. Servicios por usuario
echo "<div class='box'>";
echo "<h2>2. Servicios por Usuario Proveedor</h2>";
$query = "SELECT u.id, u.nombre_completo, u.tipo_usuario, COUNT(s.id) as total_servicios
          FROM usuarios u
          LEFT JOIN servicios s ON u.id = s.usuario_id
          WHERE u.tipo_usuario IN ('hotel', 'restaurante', 'operador', 'agencia')
          GROUP BY u.id
          ORDER BY u.tipo_usuario, u.id";
$stmt = $db->query($query);
if ($stmt->rowCount() > 0) {
    echo "<table><tr><th>Usuario ID</th><th>Nombre</th><th>Tipo</th><th>Servicios</th><th>Estado</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $estado = $row['total_servicios'] > 0 ? "<span class='success'>✓ Tiene servicios</span>" : "<span class='error'>✗ Sin servicios</span>";
        echo "<tr><td>{$row['id']}</td><td>{$row['nombre_completo']}</td><td>{$row['tipo_usuario']}</td><td>{$row['total_servicios']}</td><td>{$estado}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='warning'>No hay usuarios proveedores</p>";
}
echo "</div>";

// 3. Todos los servicios
echo "<div class='box'>";
echo "<h2>3. Todos los Servicios</h2>";
$query = "SELECT s.id, s.usuario_id, s.tipo_servicio, s.nombre_servicio, s.rnt, u.nombre_completo, u.tipo_usuario
          FROM servicios s
          LEFT JOIN usuarios u ON s.usuario_id = u.id
          ORDER BY s.id DESC";
$stmt = $db->query($query);
if ($stmt->rowCount() > 0) {
    echo "<p class='success'>Total servicios: " . $stmt->rowCount() . "</p>";
    echo "<table><tr><th>ID</th><th>Usuario ID</th><th>Tipo Usuario</th><th>Tipo Servicio</th><th>Nombre</th><th>RNT</th><th>Propietario</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['usuario_id']}</td>";
        echo "<td>{$row['tipo_usuario']}</td>";
        echo "<td><strong>{$row['tipo_servicio']}</strong></td>";
        echo "<td>{$row['nombre_servicio']}</td>";
        echo "<td>{$row['rnt']}</td>";
        echo "<td>{$row['nombre_completo']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>No hay servicios registrados</p>";
    echo "<p>Los proveedores deben crear servicios desde su Panel.</p>";
}
echo "</div>";

// 4. Reservas recibidas por proveedor
echo "<div class='box'>";
echo "<h2>4. Reservas por Proveedor</h2>";
$query = "SELECT u.id, u.nombre_completo, u.tipo_usuario, COUNT(r.id) as total_reservas
          FROM usuarios u
          LEFT JOIN servicios s ON u.id = s.usuario_id
          LEFT JOIN reservas r ON s.id = r.servicio_id
          WHERE u.tipo_usuario IN ('hotel', 'restaurante', 'operador', 'agencia')
          GROUP BY u.id
          ORDER BY total_reservas DESC";
$stmt = $db->query($query);
if ($stmt->rowCount() > 0) {
    echo "<table><tr><th>Usuario ID</th><th>Nombre</th><th>Tipo</th><th>Reservas Recibidas</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['nombre_completo']}</td><td>{$row['tipo_usuario']}</td><td>{$row['total_reservas']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='warning'>No hay proveedores</p>";
}
echo "</div>";

// 5. Programas gubernamentales
echo "<div class='box'>";
echo "<h2>5. Programas Gubernamentales</h2>";
$query = "SELECT p.id, p.titulo, p.fecha_inicio, u.nombre_completo
          FROM programas_gobierno p
          LEFT JOIN usuarios u ON p.usuario_id = u.id
          ORDER BY p.id DESC";
$stmt = $db->query($query);
if ($stmt->rowCount() > 0) {
    echo "<p class='success'>Total programas: " . $stmt->rowCount() . "</p>";
    echo "<table><tr><th>ID</th><th>Título</th><th>Fecha Inicio</th><th>Publicado por</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['titulo']}</td><td>{$row['fecha_inicio']}</td><td>{$row['nombre_completo']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>No hay programas gubernamentales</p>";
    echo "<p>Los usuarios de gobierno deben crear programas desde su Panel.</p>";
}
echo "</div>";

// 6. Diagnóstico
echo "<div class='box'>";
echo "<h2>6. Diagnóstico</h2>";

$query = "SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario IN ('hotel', 'restaurante', 'operador', 'agencia')";
$stmt = $db->query($query);
$proveedores = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM servicios";
$stmt = $db->query($query);
$servicios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'gobierno'";
$stmt = $db->query($query);
$gobierno = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM programas_gobierno";
$stmt = $db->query($query);
$programas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

echo "<ul>";
echo "<li>Usuarios proveedores: <strong>{$proveedores}</strong></li>";
echo "<li>Servicios registrados: <strong>{$servicios}</strong></li>";
echo "<li>Usuarios gobierno: <strong>{$gobierno}</strong></li>";
echo "<li>Programas publicados: <strong>{$programas}</strong></li>";
echo "</ul>";

if ($proveedores > 0 && $servicios == 0) {
    echo "<p class='error'>⚠️ HAY PROVEEDORES PERO NO HAY SERVICIOS</p>";
    echo "<p>Los proveedores deben:</p>";
    echo "<ol>";
    echo "<li>Iniciar sesión en la aplicación</li>";
    echo "<li>Ir a su Panel</li>";
    echo "<li>Hacer clic en '+ Registrar Servicio'</li>";
    echo "<li>Completar el formulario con RNT</li>";
    echo "<li>Guardar el servicio</li>";
    echo "</ol>";
}

if ($gobierno > 0 && $programas == 0) {
    echo "<p class='error'>⚠️ HAY USUARIOS DE GOBIERNO PERO NO HAY PROGRAMAS</p>";
    echo "<p>Los usuarios de gobierno deben crear programas desde su Panel.</p>";
}

echo "</div>";

echo "</body></html>";
?>
