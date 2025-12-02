<?php
// ============================================
// ARCHIVO: api\estadisticas\mensuales.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\api\estadisticas\mensuales.php
// ============================================

require_once '../../config/database.php';
require_once '../../config/cors.php';
require_once '../../models/Estadistica.php';

$database = new Database();
$db = $database->getConnection();
$estadistica = new Estadistica($db);

$ano = isset($_GET['ano']) ? intval($_GET['ano']) : date('Y');
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('n');

// Validar mes
if($mes < 1 || $mes > 12) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Mes inválido. Debe ser entre 1 y 12"
    ));
    exit;
}

try {
    $datos = $estadistica->obtenerMensuales($ano, $mes);

    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => $datos,
        "periodo" => array(
            "ano" => $ano,
            "mes" => $mes,
            "nombre_mes" => date('F', mktime(0, 0, 0, $mes, 1))
        )
    ));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Error al obtener estadísticas: " . $e->getMessage()
    ));
}
?>