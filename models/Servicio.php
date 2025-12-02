<?php
class Servicio {
    private $conn;
    private $table_name = "servicios";

    public $id;
    public $usuario_id;
    public $tipo_servicio;
    public $nombre_servicio;
    public $rnt;
    public $descripcion;
    public $direccion;
    public $ubicacion_lat;
    public $ubicacion_lng;
    public $telefono;
    public $email;
    public $sitio_web;
    public $horario_apertura;
    public $horario_cierre;
    public $dias_operacion;
    public $precio_desde;
    public $precio_hasta;
    public $capacidad;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear servicio
    public function crear() {
        try {
            $query = "INSERT INTO " . $this->table_name . "
                    SET usuario_id=:usuario_id,
                        tipo_servicio=:tipo_servicio,
                        nombre_servicio=:nombre_servicio,
                        rnt=:rnt,
                        descripcion=:descripcion,
                        direccion=:direccion,
                        ubicacion_lat=:ubicacion_lat,
                        ubicacion_lng=:ubicacion_lng,
                        telefono=:telefono,
                        email=:email,
                        sitio_web=:sitio_web,
                        horario_apertura=:horario_apertura,
                        horario_cierre=:horario_cierre,
                        dias_operacion=:dias_operacion,
                        precio_desde=:precio_desde,
                        precio_hasta=:precio_hasta,
                        capacidad=:capacidad";

            $stmt = $this->conn->prepare($query);

            // Sanitizar datos
            $this->nombre_servicio = htmlspecialchars(strip_tags($this->nombre_servicio));
            $this->rnt = htmlspecialchars(strip_tags($this->rnt));
            $this->tipo_servicio = htmlspecialchars(strip_tags($this->tipo_servicio));

            // Bind
            $stmt->bindParam(":usuario_id", $this->usuario_id);
            $stmt->bindParam(":tipo_servicio", $this->tipo_servicio);
            $stmt->bindParam(":nombre_servicio", $this->nombre_servicio);
            $stmt->bindParam(":rnt", $this->rnt);
            $stmt->bindParam(":descripcion", $this->descripcion);
            $stmt->bindParam(":direccion", $this->direccion);
            $stmt->bindParam(":ubicacion_lat", $this->ubicacion_lat);
            $stmt->bindParam(":ubicacion_lng", $this->ubicacion_lng);
            $stmt->bindParam(":telefono", $this->telefono);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":sitio_web", $this->sitio_web);
            $stmt->bindParam(":horario_apertura", $this->horario_apertura);
            $stmt->bindParam(":horario_cierre", $this->horario_cierre);
            $stmt->bindParam(":dias_operacion", $this->dias_operacion);
            $stmt->bindParam(":precio_desde", $this->precio_desde);
            $stmt->bindParam(":precio_hasta", $this->precio_hasta);
            $stmt->bindParam(":capacidad", $this->capacidad);

            if($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            
            // Si falla, mostrar error
            error_log("Error SQL: " . print_r($stmt->errorInfo(), true));
            return false;
            
        } catch (PDOException $e) {
            error_log("Error en Servicio::crear(): " . $e->getMessage());
            throw $e;
        }
    }

    // Listar todos los servicios activos
    public function listar($tipo = null) {
        $query = "SELECT s.*, u.nombre_completo as propietario,
                  (SELECT url_foto FROM servicio_fotos WHERE servicio_id = s.id AND es_principal = 1 LIMIT 1) as foto_principal
                  FROM " . $this->table_name . " s
                  LEFT JOIN usuarios u ON s.usuario_id = u.id
                  WHERE s.activo = 1";
        
        if($tipo) {
            $query .= " AND s.tipo_servicio = :tipo";
        }
        
        $query .= " ORDER BY s.fecha_creacion DESC";

        $stmt = $this->conn->prepare($query);
        
        if($tipo) {
            $stmt->bindParam(":tipo", $tipo);
        }
        
        $stmt->execute();
        return $stmt;
    }

    // Obtener servicio por ID
    public function obtenerPorId($id) {
        $query = "SELECT s.*, u.nombre_completo as propietario, u.email as email_propietario
                  FROM " . $this->table_name . " s
                  LEFT JOIN usuarios u ON s.usuario_id = u.id
                  WHERE s.id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener servicios por usuario
    public function obtenerPorUsuario($usuario_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE usuario_id = :usuario_id
                  ORDER BY fecha_creacion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();

        return $stmt;
    }

    // Actualizar servicio
    public function actualizar() {
        $query = "UPDATE " . $this->table_name . "
                SET nombre_servicio=:nombre_servicio,
                    descripcion=:descripcion,
                    direccion=:direccion,
                    telefono=:telefono,
                    email=:email,
                    horario_apertura=:horario_apertura,
                    horario_cierre=:horario_cierre,
                    precio_desde=:precio_desde,
                    precio_hasta=:precio_hasta
                WHERE id=:id AND usuario_id=:usuario_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nombre_servicio", $this->nombre_servicio);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":direccion", $this->direccion);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":horario_apertura", $this->horario_apertura);
        $stmt->bindParam(":horario_cierre", $this->horario_cierre);
        $stmt->bindParam(":precio_desde", $this->precio_desde);
        $stmt->bindParam(":precio_hasta", $this->precio_hasta);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":usuario_id", $this->usuario_id);

        return $stmt->execute();
    }
}
?>
