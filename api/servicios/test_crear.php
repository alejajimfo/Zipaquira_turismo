<?php
// Test de creación de servicio con debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

echo json_encode([
    "debug" => true,
    "method" => $_SERVER['REQUEST_METHOD'],
    "headers" => getallheaders(),
    "raw_input" => file_get_contents("php://input"),
    "timestamp" => date('Y-m-d H:i:s')
]);
?>
