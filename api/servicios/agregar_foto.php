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

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(empty($data->servicio_id) || empty($data->url_foto)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Se requiere servicio_id y url_foto"
    ]);
    exit();
}

try {
    $query = "INSERT INTO servicio_fotos 
              (servicio_id, url_foto, descripcion, es_principal, orden)
              VALUES (:servicio_id, :url_foto, :descripcion, :es_principal, :orden)";
    
    $stmt = $db->prepare($query);
    
    $es_principal = isset($data->es_principal) ? $data->es_principal : 0;
    $orden = isset($data->orden) ? $data->orden : 0;
    $descripcion = isset($data->descripcion) ? $data->descripcion : null;
    
    $stmt->bindParam(":servicio_id", $data->servicio_id);
    $stmt->bindParam(":url_foto", $data->url_foto);
    $stmt->bindParam(":descripcion", $descripcion);
    $stmt->bindParam(":es_principal", $es_principal);
    $stmt->bindParam(":orden", $orden);
    
    if($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Foto agregada exitosamente",
            "foto_id" => $db->lastInsertId()
        ]);
    } else {
        http_response_code(503);
        echo json_encode([
            "success" => false,
            "message" => "No se pudo agregar la foto"
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
