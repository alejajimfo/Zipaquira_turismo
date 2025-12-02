<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/Servicio.php';

$database = new Database();
$db = $database->getConnection();
$servicio = new Servicio($db);

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;

$stmt = $servicio->listar($tipo);
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
            "ubicacion_lat" => $ubicacion_lat,
            "ubicacion_lng" => $ubicacion_lng,
            "telefono" => $telefono,
            "email" => $email,
            "sitio_web" => $sitio_web,
            "horario_apertura" => $horario_apertura,
            "horario_cierre" => $horario_cierre,
            "dias_operacion" => $dias_operacion,
            "precio_desde" => $precio_desde,
            "precio_hasta" => $precio_hasta,
            "capacidad" => $capacidad,
            "propietario" => $propietario,
            "foto_principal" => $foto_principal,
            "verificado" => $verificado
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
        "message" => "No se encontraron servicios"
    ));
}
?>
