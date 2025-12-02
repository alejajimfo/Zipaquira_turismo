<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

echo json_encode([
    "test" => "Conexión OK",
    "timestamp" => date('Y-m-d H:i:s'),
    "php_version" => phpversion(),
    "server" => $_SERVER['SERVER_SOFTWARE']
]);
?>
