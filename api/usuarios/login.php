<?php
// ============================================
// ARCHIVO: api/auth/login.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\api\auth\login.php
// ============================================

// Headers CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../../config/database.php';
require_once '../../models/Usuario.php';

try {
    // Crear conexión
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        http_response_code(500);
        echo json_encode(array(
            "success" => false,
            "message" => "Error de conexión a la base de datos"
        ));
        exit();
    }
    
    $usuario = new Usuario($db);
    
    // Obtener datos del POST
    $data = json_decode(file_get_contents("php://input"));
    
    // Log para debugging (opcional)
    error_log("Login attempt - Email: " . ($data->email ?? 'not provided'));
    
    // Validar datos
    if(empty($data->email) || empty($data->password)) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Email y contraseña son requeridos"
        ));
        exit();
    }
    
    // Asignar datos al modelo
    $usuario->email = $data->email;
    $usuario->password = $data->password;
    
    // Intentar login (sin validar tipo_usuario)
    $resultado = $usuario->login();
    
    // Verificar resultado
    if(isset($resultado['success']) && $resultado['success']) {
        // Login exitoso
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "usuario" => array(
                "id" => $resultado['id'],
                "tipo_usuario" => $resultado['tipo_usuario'],
                "email" => $resultado['email'],
                "nombre_completo" => $resultado['nombre_completo'],
                "verificado" => $resultado['verificado']
            )
        ));
    } else {
        // Login fallido
        http_response_code(401);
        echo json_encode(array(
            "success" => false,
            "message" => $resultado['message'] ?? "Credenciales inválidas"
        ));
    }
    
} catch (Exception $e) {
    // Error del servidor
    error_log("Login error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Error interno del servidor"
    ));
}
?>