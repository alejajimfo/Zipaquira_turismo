<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/ProgramaGobierno.php';

$database = new Database();
$db = $database->getConnection();
$programa = new ProgramaGobierno($db);

$stmt = $programa->listar();
$num = $stmt->rowCount();

if($num > 0) {
    $programas_arr = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        $programa_item = array(
            "id" => $id,
            "titulo" => $titulo,
            "descripcion" => $descripcion,
            "actividades" => $actividades,
            "valor" => $valor,
            "ubicacion" => $ubicacion,
            "ubicacion_lat" => $ubicacion_lat,
            "ubicacion_lng" => $ubicacion_lng,
            "fecha_inicio" => $fecha_inicio,
            "fecha_fin" => $fecha_fin,
            "cupos_disponibles" => $cupos_disponibles,
            "institucion" => $institucion
        );
        
        array_push($programas_arr, $programa_item);
    }
    
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => $programas_arr,
        "total" => $num
    ));
} else {
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => array(),
        "total" => 0
    ));
}
?>
