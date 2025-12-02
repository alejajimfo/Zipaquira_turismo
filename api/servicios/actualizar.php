<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../config/database.php';
include_once '../../models/Servicio.php';

$database = new Database();
$db = $database->getConnection();
$servicio = new Servicio($db);

$data = json_decode(file_get_contents("php://input"));

if(empty($data->id) || empty($data->usuario_id)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Se requiere ID del servicio y usuario_id"
    ]);
    exit();
}

try {
    $servicio->id = $data->id;
    $servicio->usuario_id = $data->usuario_id;
    $servicio->nombre_servicio = $data->nombre_servicio;
    $servicio->descripcion = $data->descripcion ?? null;
    $servicio->direccion = $data->direccion ?? null;
    $servicio->telefono = $data->telefono ?? null;
    $servicio->email = $data->email ?? null;
    $servicio->horario_apertura = $data->horario_apertura ?? null;
    $servicio->horario_cierre = $data->horario_cierre ?? null;
    $servicio->precio_desde = $data->precio_desde ?? null;
    $servicio->precio_hasta = $data->precio_hasta ?? null;

    if($servicio->actualizar()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Servicio actualizado exitosamente"
        ]);
    } else {
        http_response_code(503);
        echo json_encode([
            "success" => false,
            "message" => "No se pudo actualizar el servicio"
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>
