<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/ProgramaGobierno.php';

$database = new Database();
$db = $database->getConnection();
$programa = new ProgramaGobierno($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->usuario_id) && !empty($data->titulo) && !empty($data->descripcion)) {
    
    $programa->usuario_id = $data->usuario_id;
    $programa->titulo = $data->titulo;
    $programa->descripcion = $data->descripcion;
    $programa->actividades = $data->actividades ?? null;
    $programa->valor = $data->valor ?? null;
    $programa->ubicacion = $data->ubicacion ?? null;
    $programa->ubicacion_lat = $data->ubicacion_lat ?? null;
    $programa->ubicacion_lng = $data->ubicacion_lng ?? null;
    $programa->fecha_inicio = $data->fecha_inicio ?? null;
    $programa->fecha_fin = $data->fecha_fin ?? null;
    $programa->cupos_disponibles = $data->cupos_disponibles ?? null;

    if($programa->crear()) {
        http_response_code(201);
        echo json_encode(array(
            "success" => true,
            "message" => "Programa creado exitosamente",
            "programa_id" => $programa->id
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "No se pudo crear el programa"
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
