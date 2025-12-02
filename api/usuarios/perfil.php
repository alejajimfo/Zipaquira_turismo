<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$usuario_id = isset($_GET['usuario_id']) ? intval($_GET['usuario_id']) : 0;

if ($method === 'GET') {
    // Obtener perfil
    if ($usuario_id <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID de usuario inválido"]);
        exit();
    }

    try {
        $query = "SELECT u.*, 
                  pt.foto_perfil, pt.edad, pt.ocupacion, pt.intereses, pt.pais_origen, pt.idiomas
                  FROM usuarios u
                  LEFT JOIN perfil_turista pt ON u.id = pt.usuario_id
                  WHERE u.id = :usuario_id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();
        
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($perfil) {
            // No enviar password_hash
            unset($perfil['password_hash']);
            
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "data" => $perfil
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Usuario no encontrado"
            ]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Error: " . $e->getMessage()
        ]);
    }
    
} elseif ($method === 'POST' || $method === 'PUT') {
    // Actualizar perfil
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->usuario_id)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID de usuario requerido"]);
        exit();
    }
    
    try {
        $db->beginTransaction();
        
        // Actualizar tabla usuarios
        $query = "UPDATE usuarios SET 
                  nombre_completo = :nombre_completo,
                  telefono = :telefono,
                  direccion = :direccion,
                  ciudad = :ciudad,
                  pais = :pais,
                  documento_identidad = :documento_identidad,
                  rut = :rut,
                  camara_comercio = :camara_comercio,
                  certificado_rnt = :certificado_rnt
                  WHERE id = :usuario_id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":nombre_completo", $data->nombre_completo);
        $stmt->bindParam(":telefono", $data->telefono);
        $stmt->bindParam(":direccion", $data->direccion);
        $stmt->bindParam(":ciudad", $data->ciudad);
        $stmt->bindParam(":pais", $data->pais);
        $stmt->bindParam(":documento_identidad", $data->documento_identidad);
        $stmt->bindParam(":rut", $data->rut);
        $stmt->bindParam(":camara_comercio", $data->camara_comercio);
        $stmt->bindParam(":certificado_rnt", $data->certificado_rnt);
        $stmt->bindParam(":usuario_id", $data->usuario_id);
        $stmt->execute();
        
        // Si es turista, actualizar perfil_turista
        if (isset($data->tipo_usuario) && $data->tipo_usuario === 'turista') {
            // Verificar si existe perfil
            $check = $db->prepare("SELECT id FROM perfil_turista WHERE usuario_id = :usuario_id");
            $check->bindParam(":usuario_id", $data->usuario_id);
            $check->execute();
            
            if ($check->rowCount() > 0) {
                // Actualizar
                $query = "UPDATE perfil_turista SET
                          foto_perfil = :foto_perfil,
                          edad = :edad,
                          ocupacion = :ocupacion,
                          intereses = :intereses,
                          pais_origen = :pais_origen,
                          idiomas = :idiomas
                          WHERE usuario_id = :usuario_id";
            } else {
                // Insertar
                $query = "INSERT INTO perfil_turista 
                          (usuario_id, foto_perfil, edad, ocupacion, intereses, pais_origen, idiomas)
                          VALUES (:usuario_id, :foto_perfil, :edad, :ocupacion, :intereses, :pais_origen, :idiomas)";
            }
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(":usuario_id", $data->usuario_id);
            $stmt->bindParam(":foto_perfil", $data->foto_perfil);
            $stmt->bindParam(":edad", $data->edad);
            $stmt->bindParam(":ocupacion", $data->ocupacion);
            $stmt->bindParam(":intereses", $data->intereses);
            $stmt->bindParam(":pais_origen", $data->pais_origen);
            $stmt->bindParam(":idiomas", $data->idiomas);
            $stmt->execute();
        }
        
        $db->commit();
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Perfil actualizado correctamente"
        ]);
        
    } catch (PDOException $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Error: " . $e->getMessage()
        ]);
    }
}
?>
