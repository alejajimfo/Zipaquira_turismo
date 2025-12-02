// ============================================
// ARCHIVO: api\resenas\crear.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\api\resenas\crear.php
// ============================================

<?php
require_once '../../config/database.php';
require_once '../../config/cors.php';
require_once '../../models/Resena.php';

$database = new Database();
$db = $database->getConnection();
$resena = new Resena($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->usuario_id) && 
   !empty($data->calificacion) &&
   !empty($data->comentario)) {

    $id = $resena->crear(
        $data->usuario_id,
        $data->tipo_entidad ?? 'general',
        $data->entidad_id ?? null,
        $data->calificacion,
        $data->titulo ?? '',
        $data->comentario
    );

    if($id) {
        http_response_code(201);
        echo json_encode(array(
            "success" => true,
            "message" => "Reseña creada exitosamente",
            "resena_id" => $id
        ));
    } else {
        http_response_code(500);
        echo json_encode(array(
            "success" => false,
            "message" => "Error al crear reseña"
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Datos incompletos"
    ));
}
?>