<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/Reserva.php';

$database = new Database();
$db = $database->getConnection();
$reserva = new Reserva($db);

$usuario_id = isset($_GET['usuario_id']) ? intval($_GET['usuario_id']) : 0;

if($usuario_id <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "ID de usuario inválido"
    ]);
    exit();
}

$stmt = $reserva->obtenerPorProveedor($usuario_id);
$num = $stmt->rowCount();

$reservas_arr = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $reserva_item = array(
        "id" => $row['id'],
        "servicio_id" => $row['servicio_id'],
        "nombre_servicio" => $row['nombre_servicio'],
        "tipo_servicio" => $row['tipo_servicio'],
        "turista_nombre" => $row['turista_nombre'],
        "turista_telefono" => $row['turista_telefono'],
        "turista_email" => $row['turista_email'],
        "fecha_reserva" => $row['fecha_reserva'],
        "hora_reserva" => $row['hora_reserva'],
        "numero_personas" => $row['numero_personas'],
        "precio_total" => $row['precio_total'],
        "estado" => $row['estado'],
        "notas_turista" => $row['notas_turista'],
        "notas_proveedor" => $row['notas_proveedor'],
        "fecha_creacion" => $row['fecha_creacion']
    );
    
    array_push($reservas_arr, $reserva_item);
}

http_response_code(200);
echo json_encode([
    "success" => true,
    "data" => $reservas_arr,
    "total" => $num
]);
?>
