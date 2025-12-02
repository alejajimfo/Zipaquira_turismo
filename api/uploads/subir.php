<?php
// ============================================
// ARCHIVO: api\uploads\subir.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\api\uploads\subir.php
// ============================================

require_once '../../config/database.php';
require_once '../../config/cors.php';
require_once '../../config/constants.php';

// Verificar que se recibió un archivo
if(!isset($_FILES['archivo'])) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "No se recibió ningún archivo"
    ));
    exit;
}

$archivo = $_FILES['archivo'];

// Verificar errores de subida
if($archivo['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Error al subir archivo: " . $archivo['error']
    ));
    exit;
}

// Verificar tamaño
if($archivo['size'] > MAX_UPLOAD_SIZE) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "El archivo excede el tamaño máximo permitido (10MB)"
    ));
    exit;
}

// Verificar extensión
$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
if(!in_array($extension, ALLOWED_EXTENSIONS)) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Tipo de archivo no permitido. Permitidos: " . implode(", ", ALLOWED_EXTENSIONS)
    ));
    exit;
}

// Generar nombre único
$nombre_archivo = uniqid() . '_' . time() . '.' . $extension;
$ruta_destino = UPLOAD_DIR . $nombre_archivo;

// Crear directorio si no existe
if(!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Mover archivo
if(move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "message" => "Archivo subido exitosamente",
        "data" => array(
            "nombre" => $nombre_archivo,
            "url" => "/zipaquira-turismo/api/uploads/" . $nombre_archivo,
            "tamano" => $archivo['size'],
            "tipo" => $archivo['type']
        )
    ));
} else {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Error al guardar archivo"
    ));
}
?>