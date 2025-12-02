<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$servicio_id = isset($_GET['servicio_id']) ? intval($_GET['servicio_id']) : 0;

if($servicio_id <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "ID de servicio inválido"
    ]);
    exit();
}

try {
    $query = "SELECT * FROM servicio_fotos 
              WHERE servicio_id = :servicio_id 
              ORDER BY es_principal DESC, orden ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":servicio_id", $servicio_id);
    $stmt->execute();
    
    $fotos = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($fotos, $row);
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $fotos,
        "total" => count($fotos)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>
