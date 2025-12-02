<?php
// ============================================
// ARCHIVO: api\notificaciones\listar.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\api\notificaciones\listar.php
// ============================================

require_once '../../config/database.php';
require_once '../../config/cors.php';
require_once '../../models/Notificacion.php';

$database = new Database();
$db = $database->getConnection();
$notificacion = new Notificacion($db);

$usuario_id = isset($_GET['usuario_id']) ? intval($_GET['usuario_id']) : null;
$solo_no_leidas = isset($_GET['no_leidas']) ? (bool)$_GET['no_leidas'] : false;

if(!$usuario_id) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Usuario ID requerido"
    ));
    exit;
}

try {
    $notificaciones = $notificacion->obtenerPorUsuario($usuario_id, $solo_no_leidas);
    $contador = $notificacion->contarNoLeidas($usuario_id);

    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => $notificaciones,
        "no_leidas" => $contador
    ));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Error al obtener notificaciones: " . $e->getMessage()
    ));
}
?>