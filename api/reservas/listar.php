<?php
// ============================================
// ARCHIVO: api\reservas\listar.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\api\reservas\listar.php
// ============================================

require_once '../../config/database.php';
require_once '../../config/cors.php';
require_once '../../models/Reserva.php';

$database = new Database();
$db = $database->getConnection();
$reserva = new Reserva($db);

// Obtener parámetros
$turista_id = isset($_GET['turista_id']) ? intval($_GET['turista_id']) : null;
$estado = isset($_GET['estado']) ? $_GET['estado'] : null;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

try {
    if($turista_id) {
        // Obtener reservas de un turista específico
        $reservas = $reserva->obtenerPorTurista($turista_id, $estado);
    } else {
        // Listar todas (solo admin)
        $reservas = $reserva->listarTodas($limit, $offset);
    }

    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => $reservas,
        "total" => count($reservas)
    ));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Error al obtener reservas: " . $e->getMessage()
    ));
}
?>