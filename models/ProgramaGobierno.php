<?php
class ProgramaGobierno {
    private $conn;
    private $table_name = "programas_gobierno";

    public $id;
    public $usuario_id;
    public $titulo;
    public $descripcion;
    public $actividades;
    public $valor;
    public $ubicacion;
    public $ubicacion_lat;
    public $ubicacion_lng;
    public $fecha_inicio;
    public $fecha_fin;
    public $cupos_disponibles;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear programa
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . "
                SET usuario_id=:usuario_id,
                    titulo=:titulo,
                    descripcion=:descripcion,
                    actividades=:actividades,
                    valor=:valor,
                    ubicacion=:ubicacion,
                    ubicacion_lat=:ubicacion_lat,
                    ubicacion_lng=:ubicacion_lng,
                    fecha_inicio=:fecha_inicio,
                    fecha_fin=:fecha_fin,
                    cupos_disponibles=:cupos_disponibles";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":actividades", $this->actividades);
        $stmt->bindParam(":valor", $this->valor);
        $stmt->bindParam(":ubicacion", $this->ubicacion);
        $stmt->bindParam(":ubicacion_lat", $this->ubicacion_lat);
        $stmt->bindParam(":ubicacion_lng", $this->ubicacion_lng);
        $stmt->bindParam(":fecha_inicio", $this->fecha_inicio);
        $stmt->bindParam(":fecha_fin", $this->fecha_fin);
        $stmt->bindParam(":cupos_disponibles", $this->cupos_disponibles);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Listar programas activos
    public function listar() {
        $query = "SELECT p.*, u.nombre_completo as institucion
                  FROM " . $this->table_name . " p
                  LEFT JOIN usuarios u ON p.usuario_id = u.id
                  WHERE p.activo = 1
                  ORDER BY p.fecha_inicio DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener por usuario
    public function obtenerPorUsuario($usuario_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE usuario_id = :usuario_id
                  ORDER BY fecha_creacion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();
        return $stmt;
    }
}
?>
