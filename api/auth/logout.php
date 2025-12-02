<?php
// ============================================
// ARCHIVO: api\auth\logout.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\api\auth\logout.php
// ============================================

require_once '../../config/cors.php';

session_start();
session_destroy();

// Limpiar cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

http_response_code(200);
echo json_encode(array(
    "success" => true,
    "message" => "Sesión cerrada exitosamente"
));
?>