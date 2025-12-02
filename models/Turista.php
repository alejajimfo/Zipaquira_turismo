<?php
// ============================================
// ARCHIVO: models\Turista.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\models\Turista.php
// ============================================

class Turista {
    private $conn;
    private $table_name = "turistas";

    public $id;
    public $usuario_id;
    public $tipo_documento;
    public $numero_documento;
    public $fecha_nacimiento;
    public $genero;
    public $nacionalidad;
    public $idioma_preferido;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear perfil de turista
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . "
                SET usuario_id=:usuario_id,
                    tipo_documento=:tipo_documento,
                    numero_documento=:numero_documento,
                    fecha_nacimiento=:fecha_nacimiento,
                    genero=:genero,
                    nacionalidad=:nacionalidad,
                    idioma_preferido=:idioma_preferido";

        $stmt = $this->conn->prepare($query);

        // Sanitizar
        $this->tipo_documento = htmlspecialchars(strip_tags($this->tipo_documento));
        $this->numero_documento = htmlspecialchars(strip_tags($this->numero_documento));
        $this->nacionalidad = htmlspecialchars(strip_tags($this->nacionalidad));
        $this->idioma_preferido = htmlspecialchars(strip_tags($this->idioma_preferido));

        // Bind
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":tipo_documento", $this->tipo_documento);
        $stmt->bindParam(":numero_documento", $this->numero_documento);
        $stmt->bindParam(":fecha_nacimiento", $this->fecha_nacimiento);
        $stmt->bindParam(":genero", $this->genero);
        $stmt->bindParam(":nacionalidad", $this->nacionalidad);
        $stmt->bindParam(":idioma_preferido", $this->idioma_preferido);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Obtener perfil completo
    public function obtenerPorUsuarioId($usuario_id) {
        $query = "SELECT t.*, u.nombre_completo, u.email, u.telefono
                FROM " . $this->table_name . " t
                INNER JOIN usuarios u ON t.usuario_id = u.id
                WHERE t.usuario_id = :usuario_id
                LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar preferencias
    public function actualizarPreferencias($usuario_id, $preferencias, $alergias = null, $necesidades = null) {
        $query = "UPDATE " . $this->table_name . "
                SET preferencias_turisticas=:preferencias,
                    alergias_alimentarias=:alergias,
                    necesidades_especiales=:necesidades
                WHERE usuario_id=:usuario_id";

        $stmt = $this->conn->prepare($query);

        $preferencias_json = is_array($preferencias) ? json_encode($preferencias) : $preferencias;
        
        $stmt->bindParam(":preferencias", $preferencias_json);
        $stmt->bindParam(":alergias", $alergias);
        $stmt->bindParam(":necesidades", $necesidades);
        $stmt->bindParam(":usuario_id", $usuario_id);

        return $stmt->execute();
    }

    // Actualizar puntos de fidelización
    public function agregarPuntos($usuario_id, $puntos) {
        $query = "UPDATE " . $this->table_name . "
                SET puntos_acumulados = puntos_acumulados + :puntos
                WHERE usuario_id=:usuario_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":puntos", $puntos);
        $stmt->bindParam(":usuario_id", $usuario_id);

        return $stmt->execute();
    }

    // Obtener puntos actuales
    public function obtenerPuntos($usuario_id) {
        $query = "SELECT puntos_acumulados FROM " . $this->table_name . " 
                WHERE usuario_id = :usuario_id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['puntos_acumulados'] : 0;
    }

    // Verificar si documento ya existe
    public function documentoExiste() {
        $query = "SELECT id FROM " . $this->table_name . " 
                WHERE tipo_documento = :tipo_documento 
                AND numero_documento = :numero_documento 
                LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tipo_documento", $this->tipo_documento);
        $stmt->bindParam(":numero_documento", $this->numero_documento);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}
?>