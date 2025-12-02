<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(empty($data->foto_id)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Se requiere foto_id"
    ]);
    exit();
}

try {
    $query = "DELETE FROM servicio_fotos WHERE id = :foto_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":foto_id", $data->foto_id);
    
    if($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Foto eliminada exitosamente"
        ]);
    } else {
        http_response_code(503);
        echo json_encode([
            "success" => false,
            "message" => "No se pudo eliminar la foto"
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
