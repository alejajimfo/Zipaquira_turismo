// ============================================
// ARCHIVO: api\resenas\listar.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\api\resenas\listar.php
// ============================================

<?php
require_once '../../config/database.php';
require_once '../../config/cors.php';
require_once '../../models/Resena.php';

$database = new Database();
$db = $database->getConnection();
$resena = new Resena($db);

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;

$resenas = $resena->obtenerTodas($limit);

http_response_code(200);
echo json_encode(array(
    "success" => true,
    "data" => $resenas,
    "total" => count($resenas)
));
?>