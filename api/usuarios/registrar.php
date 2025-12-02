<?php
// ============================================
// registrar.php - API de Registro de Usuarios
// Guardar en: C:\xampp\htdocs\zipaquira-turismo\api\usuarios\registrar.php
// ============================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir configuración de base de datos
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$conn = $database->connect(); // mysqli connection

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

// Verificar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validar datos requeridos
$required_fields = ['tipo_usuario', 'email', 'password', 'nombre_completo'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        echo json_encode([
            'success' => false, 
            'message' => "El campo $field es requerido"
        ]);
        exit;
    }
}

// Validar tipo de usuario
$tipos_validos = ['turista', 'agencia', 'operador', 'restaurante', 'hotel', 'gobierno'];
if (!in_array($data['tipo_usuario'], $tipos_validos)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Tipo de usuario inválido'
    ]);
    exit;
}

// Validar email
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Email inválido'
    ]);
    exit;
}

// Verificar si el email ya existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $data['email']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'El email ya está registrado'
    ]);
    exit;
}
$stmt->close();

// Hash de la contraseña
$password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

// Obtener IP del usuario
$ip_registro = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// Fecha actual
$fecha_actual = date('Y-m-d H:i:s');

// Preparar INSERT
$sql = "INSERT INTO usuarios (
    tipo_usuario, 
    email, 
    password_hash, 
    nombre_completo, 
    telefono, 
    direccion, 
    ciudad, 
    pais, 
    aceptacion_terminos, 
    aceptacion_habeas_data, 
    fecha_aceptacion_politicas,
    ip_registro,
    fecha_registro
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

// Valores de cada campo
$tipo_usuario = $data['tipo_usuario'];
$email = $data['email'];
$nombre_completo = $data['nombre_completo'];
$telefono = $data['telefono'] ?? null;
$direccion = $data['direccion'] ?? null;
$ciudad = $data['ciudad'] ?? 'Zipaquirá';
$pais = $data['pais'] ?? 'Colombia';
$aceptacion_terminos = isset($data['aceptacion_terminos']) && $data['aceptacion_terminos'] ? 1 : 0;
$aceptacion_habeas_data = isset($data['aceptacion_habeas_data']) && $data['aceptacion_habeas_data'] ? 1 : 0;

// Bind de parámetros según tipo: s=string, i=integer
$stmt->bind_param(
    "ssssssssiisss",
    $tipo_usuario,
    $email,
    $password_hash,
    $nombre_completo,
    $telefono,
    $direccion,
    $ciudad,
    $pais,
    $aceptacion_terminos,
    $aceptacion_habeas_data,
    $fecha_actual,
    $ip_registro,
    $fecha_actual
);

if ($stmt->execute()) {
    $usuario_id = $stmt->insert_id;
    
    // Crear registro en tabla específica según tipo de usuario
    $tabla_creada = crearRegistroEspecifico($conn, $data['tipo_usuario'], $usuario_id, $data);
    
    // Registrar en auditoría
    registrarAuditoria($conn, $usuario_id, 'REGISTRO_USUARIO', 'usuarios', $usuario_id, $ip_registro);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Usuario registrado exitosamente',
        'usuario_id' => $usuario_id
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Error al registrar usuario: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();

// ============================================
// FUNCIONES AUXILIARES
// ============================================

/**
 * Crear registro específico según tipo de usuario
 */
function crearRegistroEspecifico($conn, $tipo_usuario, $usuario_id, $data) {
    try {
        switch($tipo_usuario) {
            case 'turista':
                $sql = "INSERT INTO turistas (usuario_id, tipo_documento, numero_documento) 
                        VALUES (?, 'CC', 'PENDIENTE')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $usuario_id);
                return $stmt->execute();
                
            case 'agencia':
                $sql = "INSERT INTO agencias_turismo (usuario_id, razon_social, nit) 
                        VALUES (?, ?, 'PENDIENTE')";
                $stmt = $conn->prepare($sql);
                $nombre = $data['nombre_completo'];
                $stmt->bind_param("is", $usuario_id, $nombre);
                return $stmt->execute();
                
            case 'operador':
                $sql = "INSERT INTO operadores_turisticos (usuario_id, nombre_empresa, nit) 
                        VALUES (?, ?, 'PENDIENTE')";
                $stmt = $conn->prepare($sql);
                $nombre = $data['nombre_completo'];
                $stmt->bind_param("is", $usuario_id, $nombre);
                return $stmt->execute();
                
            case 'restaurante':
                $sql = "INSERT INTO restaurantes (usuario_id, nombre_establecimiento, nit) 
                        VALUES (?, ?, 'PENDIENTE')";
                $stmt = $conn->prepare($sql);
                $nombre = $data['nombre_completo'];
                $stmt->bind_param("is", $usuario_id, $nombre);
                return $stmt->execute();
                
            case 'hotel':
                $sql = "INSERT INTO hospedajes (usuario_id, nombre_establecimiento, nit, tipo_hospedaje) 
                        VALUES (?, ?, 'PENDIENTE', 'Hotel')";
                $stmt = $conn->prepare($sql);
                $nombre = $data['nombre_completo'];
                $stmt->bind_param("is", $usuario_id, $nombre);
                return $stmt->execute();
                
            case 'gobierno':
                $sql = "INSERT INTO instituciones_gobierno (usuario_id, nombre_institucion, nit, tipo_entidad) 
                        VALUES (?, ?, 'PENDIENTE', 'Municipal')";
                $stmt = $conn->prepare($sql);
                $nombre = $data['nombre_completo'];
                $stmt->bind_param("is", $usuario_id, $nombre);
                return $stmt->execute();
        }
        return true;
    } catch (Exception $e) {
        error_log("Error creando registro específico: " . $e->getMessage());
        return false;
    }
}

/**
 * Registrar en auditoría
 */
function registrarAuditoria($conn, $usuario_id, $accion, $tabla, $registro_id, $ip) {
    try {
        $sql = "INSERT INTO auditoria (usuario_id, accion, tabla_afectada, registro_id, ip_address) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issis", $usuario_id, $accion, $tabla, $registro_id, $ip);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        error_log("Error en auditoría: " . $e->getMessage());
    }
}
?>