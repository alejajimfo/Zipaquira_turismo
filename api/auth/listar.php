<?php
// ============================================
// listar.php - API para Listar Usuarios
// Guardar en: C:\xampp\htdocs\zipaquira-turismo\api\usuarios\listar.php
// OPCIONAL: Para administración y pruebas
// ============================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Incluir configuración de base de datos
require_once '../config/database.php';

// Obtener parámetros opcionales
$tipo_usuario = isset($_GET['tipo_usuario']) ? $_GET['tipo_usuario'] : null;
$activo = isset($_GET['activo']) ? $_GET['activo'] : null;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

// Construir query
$sql = "SELECT 
    u.id,
    u.tipo_usuario,
    u.email,
    u.nombre_completo,
    u.telefono,
    u.ciudad,
    u.pais,
    u.activo,
    u.verificado,
    u.fecha_registro,
    u.ultima_conexion
FROM usuarios u
WHERE 1=1";

$params = [];
$types = "";

// Filtrar por tipo de usuario
if ($tipo_usuario && in_array($tipo_usuario, ['turista', 'agencia', 'operador', 'restaurante', 'hotel', 'gobierno'])) {
    $sql .= " AND u.tipo_usuario = ?";
    $params[] = $tipo_usuario;
    $types .= "s";
}

// Filtrar por estado activo
if ($activo !== null) {
    $sql .= " AND u.activo = ?";
    $params[] = $activo === '1' ? 1 : 0;
    $types .= "i";
}

// Ordenar y limitar
$sql .= " ORDER BY u.fecha_registro DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

// Preparar statement
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$usuarios = [];
while ($row = $result->fetch_assoc()) {
    // No incluir información sensible
    unset($row['password_hash']);
    $usuarios[] = $row;
}

// Obtener total de registros
$sql_count = "SELECT COUNT(*) as total FROM usuarios WHERE 1=1";
if ($tipo_usuario) {
    $sql_count .= " AND tipo_usuario = '$tipo_usuario'";
}
if ($activo !== null) {
    $activo_val = $activo === '1' ? 1 : 0;
    $sql_count .= " AND activo = $activo_val";
}

$result_count = $conn->query($sql_count);
$total = $result_count->fetch_assoc()['total'];

echo json_encode([
    'success' => true,
    'data' => $usuarios,
    'total' => $total,
    'limit' => $limit,
    'offset' => $offset
]);

$stmt->close();
$conn->close();
?>