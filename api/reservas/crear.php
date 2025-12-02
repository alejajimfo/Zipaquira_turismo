<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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

if(empty($data->servicio_id) || empty($data->turista_id) || empty($data->fecha_reserva)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos. Se requiere: servicio_id, turista_id, fecha_reserva"
    ]);
    exit();
}

try {
    $reserva->servicio_id = $data->servicio_id;
    $reserva->turista_id = $data->turista_id;
    $reserva->fecha_reserva = $data->fecha_reserva;
    $reserva->hora_reserva = $data->hora_reserva ?? null;
    $reserva->numero_personas = $data->numero_personas ?? 1;
    $reserva->precio_total = $data->precio_total ?? null;
    $reserva->estado = 'pendiente';
    $reserva->notas_turista = $data->notas_turista ?? null;

    if($reserva->crear()) {
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Reserva creada exitosamente",
            "reserva_id" => $reserva->id
        ]);
    } else {
        http_response_code(503);
        echo json_encode([
            "success" => false,
            "message" => "No se pudo crear la reserva"
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
