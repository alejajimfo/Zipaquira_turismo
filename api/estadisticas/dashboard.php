<?php
// ============================================
// ARCHIVO: api\estadisticas\dashboard.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\api\estadisticas\dashboard.php
// ============================================

require_once '../../config/database.php';
require_once '../../config/cors.php';
require_once '../../models/Estadistica.php';

$database = new Database();
$db = $database->getConnection();
$estadistica = new Estadistica($db);

try {
    $datos = $estadistica->obtenerDashboardGobierno();

    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => $datos
    ));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Error al obtener estadísticas: " . $e->getMessage()
    ));
}
?>