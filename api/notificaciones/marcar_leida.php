<?php
// ============================================
// ARCHIVO: api\notificaciones\marcar_leida.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\api\notificaciones\marcar_leida.php
// ============================================

require_once '../../config/database.php';
require_once '../../config/cors.php';
require_once '../../models/Notificacion.php';

$database = new Database();
$db = $database->getConnection();
$notificacion = new Notificacion($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->usuario_id)) {
    
    if($notificacion->marcarLeida($data->id, $data->usuario_id)) {
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "message" => "Notificación marcada como leída"
        ));
    } else {
        http_response_code(500);
        echo json_encode(array(
            "success" => false,
            "message" => "Error al marcar notificación"
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