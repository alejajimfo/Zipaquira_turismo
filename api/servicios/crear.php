<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    include_once '../../config/database.php';
    include_once '../../models/Servicio.php';

    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Error de conexión a la base de datos");
    }
    
    $servicio = new Servicio($db);

    $raw_input = file_get_contents("php://input");
    $data = json_decode($raw_input);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar JSON: " . json_last_error_msg());
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Error del servidor: " . $e->getMessage()
    ));
    exit();
}

// Validar datos recibidos
if(empty($data)) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "No se recibieron datos"
    ));
    exit();
}

if(empty($data->usuario_id) || empty($data->tipo_servicio) || empty($data->nombre_servicio) || empty($data->rnt)) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Datos incompletos. Se requiere: usuario_id, tipo_servicio, nombre_servicio, rnt",
        "received" => [
            "usuario_id" => isset($data->usuario_id) ? "✓" : "✗",
            "tipo_servicio" => isset($data->tipo_servicio) ? "✓" : "✗",
            "nombre_servicio" => isset($data->nombre_servicio) ? "✓" : "✗",
            "rnt" => isset($data->rnt) ? "✓" : "✗"
        ]
    ));
    exit();
}

try {
    $servicio->usuario_id = $data->usuario_id;
    $servicio->tipo_servicio = $data->tipo_servicio;
    $servicio->nombre_servicio = $data->nombre_servicio;
    $servicio->rnt = $data->rnt;
    $servicio->descripcion = isset($data->descripcion) ? $data->descripcion : null;
    $servicio->direccion = isset($data->direccion) ? $data->direccion : null;
    $servicio->ubicacion_lat = isset($data->ubicacion_lat) ? $data->ubicacion_lat : null;
    $servicio->ubicacion_lng = isset($data->ubicacion_lng) ? $data->ubicacion_lng : null;
    $servicio->telefono = isset($data->telefono) ? $data->telefono : null;
    $servicio->email = isset($data->email) ? $data->email : null;
    $servicio->sitio_web = isset($data->sitio_web) ? $data->sitio_web : null;
    $servicio->horario_apertura = isset($data->horario_apertura) ? $data->horario_apertura : null;
    $servicio->horario_cierre = isset($data->horario_cierre) ? $data->horario_cierre : null;
    $servicio->dias_operacion = isset($data->dias_operacion) ? $data->dias_operacion : null;
    $servicio->precio_desde = isset($data->precio_desde) ? $data->precio_desde : null;
    $servicio->precio_hasta = isset($data->precio_hasta) ? $data->precio_hasta : null;
    $servicio->capacidad = isset($data->capacidad) ? $data->capacidad : null;

    if($servicio->crear()) {
        http_response_code(201);
        echo json_encode(array(
            "success" => true,
            "message" => "Servicio creado exitosamente",
            "servicio_id" => $servicio->id
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "No se pudo crear el servicio en la base de datos"
        ));
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Error al procesar: " . $e->getMessage()
    ));
}
?>
