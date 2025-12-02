<?php
// ============================================
// ARCHIVO: api\reservas\cancelar.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\api\reservas\cancelar.php
// ============================================

require_once '../../config/database.php';
require_once '../../config/cors.php';
require_once '../../models/Reserva.php';

$database = new Database();
$db = $database->getConnection();
$reserva = new Reserva($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->turista_id)) {
    
    if($reserva->cancelar($data->id, $data->turista_id)) {
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "message" => "Reserva cancelada exitosamente"
        ));
    } else {
        http_response_code(500);
        echo json_encode(array(
            "success" => false,
            "message" => "Error al cancelar reserva"
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Datos incompletos"
    ));
}
?>