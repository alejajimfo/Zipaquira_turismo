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

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(empty($data->reserva_id)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Se requiere reserva_id"
    ]);
    exit();
}

try {
    $query = "UPDATE reservas 
              SET fecha_reserva = :fecha_reserva,
                  hora_reserva = :hora_reserva,
                  numero_personas = :numero_personas,
                  precio_total = :precio_total,
                  notas_turista = :notas_turista
              WHERE id = :reserva_id AND estado = 'pendiente'";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":reserva_id", $data->reserva_id);
    $stmt->bindParam(":fecha_reserva", $data->fecha_reserva);
    $stmt->bindParam(":hora_reserva", $data->hora_reserva);
    $stmt->bindParam(":numero_personas", $data->numero_personas);
    $stmt->bindParam(":precio_total", $data->precio_total);
    $stmt->bindParam(":notas_turista", $data->notas_turista);
    
    if($stmt->execute()) {
        if($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Reserva actualizada exitosamente"
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "No se pudo actualizar. La reserva no existe o ya no está en estado pendiente."
            ]);
        }
    } else {
        http_response_code(503);
        echo json_encode([
            "success" => false,
            "message" => "No se pudo actualizar la reserva"
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
