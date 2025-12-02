<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/Servicio.php';

$database = new Database();
$db = $database->getConnection();
$servicio = new Servicio($db);

// Obtener usuario_id del parámetro GET
$usuario_id = isset($_GET['usuario_id']) ? intval($_GET['usuario_id']) : 0;

if($usuario_id <= 0) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "ID de usuario inválido"
    ));
    exit();
}

$stmt = $servicio->obtenerPorUsuario($usuario_id);
$num = $stmt->rowCount();

if($num > 0) {
    $servicios_arr = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        $servicio_item = array(
            "id" => $id,
            "tipo_servicio" => $tipo_servicio,
            "nombre_servicio" => $nombre_servicio,
            "rnt" => $rnt,
            "descripcion" => $descripcion,
            "direccion" => $direccion,
            "telefono" => $telefono,
            "email" => $email,
            "horario_apertura" => $horario_apertura,
            "horario_cierre" => $horario_cierre,
            "precio_desde" => $precio_desde,
            "precio_hasta" => $precio_hasta,
            "activo" => $activo,
            "verificado" => $verificado,
            "fecha_creacion" => $fecha_creacion
        );
        
        array_push($servicios_arr, $servicio_item);
    }
    
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => $servicios_arr,
        "total" => $num
    ));
} else {
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => array(),
        "total" => 0,
        "message" => "No tienes servicios registrados aún"
    ));
}
?>
