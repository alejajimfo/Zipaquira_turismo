<?php
$host = "localhost";
$usuario = "root";
$contrasena = ""; // si tu root tiene contraseña, ponla aquí
$base_de_datos = "zipaquira_turismo";

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]));
}
?>
