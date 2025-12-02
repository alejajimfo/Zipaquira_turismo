<?php
// ============================================
// ARCHIVO: api\estadisticas\grafico.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\api\estadisticas\grafico.php
// ============================================

require_once '../../config/database.php';
require_once '../../config/cors.php';
require_once '../../models/Estadistica.php';

$database = new Database();
$db = $database->getConnection();
$estadistica = new Estadistica($db);

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'visitantes';
$periodo_inicio = isset($_GET['inicio']) ? $_GET['inicio'] : date('Y-m-01');
$periodo_fin = isset($_GET['fin']) ? $_GET['fin'] : date('Y-m-d');

try {
    $datos = $estadistica->obtenerDatosGrafico($tipo, $periodo_inicio, $periodo_fin);

    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => $datos,
        "periodo" => array(
            "inicio" => $periodo_inicio,
            "fin" => $periodo_fin
        )
    ));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Error al obtener datos: " . $e->getMessage()
    ));
}
?>