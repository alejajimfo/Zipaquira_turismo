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
include_once '../../models/Reserva.php';

$database = new Database();
$db = $database->getConnection();
$reserva = new Reserva($db);

$data = json_decode(file_get_contents("php://input"));

if(empty($data->reserva_id) || empty($data->estado)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos. Se requiere: reserva_id, estado"
    ]);
    exit();
}

// Validar estado
$estados_validos = ['pendiente', 'confirmada', 'cancelada', 'completada'];
if(!in_array($data->estado, $estados_validos)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Estado inválido. Debe ser: " . implode(', ', $estados_validos)
    ]);
    exit();
}

try {
    $notas = $data->notas_proveedor ?? null;
    
    if($reserva->actualizarEstado($data->reserva_id, $data->estado, $notas)) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Estado actualizado correctamente"
        ]);
    } else {
        http_response_code(503);
        echo json_encode([
            "success" => false,
            "message" => "No se pudo actualizar el estado"
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
