<?php
class Reserva {
    private $conn;
    private $table_name = "reservas";

    public $id;
    public $servicio_id;
    public $turista_id;
    public $fecha_reserva;
    public $hora_reserva;
    public $numero_personas;
    public $precio_total;
    public $estado;
    public $notas_turista;
    public $notas_proveedor;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear reserva
    public function crear() {
        try {
            $query = "INSERT INTO " . $this->table_name . "
                    SET servicio_id=:servicio_id,
                        turista_id=:turista_id,
                        fecha_reserva=:fecha_reserva,
                        hora_reserva=:hora_reserva,
                        numero_personas=:numero_personas,
                        precio_total=:precio_total,
                        estado=:estado,
                        notas_turista=:notas_turista";

            $stmt = $this->conn->prepare($query);

            // Sanitizar
            $this->servicio_id = htmlspecialchars(strip_tags($this->servicio_id));
            $this->turista_id = htmlspecialchars(strip_tags($this->turista_id));
            $this->estado = $this->estado ?? 'pendiente';

            // Bind
            $stmt->bindParam(":servicio_id", $this->servicio_id);
            $stmt->bindParam(":turista_id", $this->turista_id);
            $stmt->bindParam(":fecha_reserva", $this->fecha_reserva);
            $stmt->bindParam(":hora_reserva", $this->hora_reserva);
            $stmt->bindParam(":numero_personas", $this->numero_personas);
            $stmt->bindParam(":precio_total", $this->precio_total);
            $stmt->bindParam(":estado", $this->estado);
            $stmt->bindParam(":notas_turista", $this->notas_turista);

            if($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error en Reserva::crear(): " . $e->getMessage());
            throw $e;
        }
    }

    // Obtener reservas del turista
    public function obtenerPorTurista($turista_id) {
        $query = "SELECT r.*, s.nombre_servicio, s.tipo_servicio, s.direccion,
                  u.nombre_completo as proveedor_nombre
                  FROM " . $this->table_name . " r
                  LEFT JOIN servicios s ON r.servicio_id = s.id
                  LEFT JOIN usuarios u ON s.usuario_id = u.id
                  WHERE r.turista_id = :turista_id
                  ORDER BY r.fecha_reserva DESC, r.fecha_creacion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":turista_id", $turista_id);
        $stmt->execute();
        return $stmt;
    }

    // Obtener reservas del proveedor (por sus servicios)
    public function obtenerPorProveedor($usuario_id) {
        $query = "SELECT r.*, s.nombre_servicio, s.tipo_servicio,
                  t.nombre_completo as turista_nombre, t.telefono as turista_telefono, t.email as turista_email
                  FROM " . $this->table_name . " r
                  LEFT JOIN servicios s ON r.servicio_id = s.id
                  LEFT JOIN usuarios t ON r.turista_id = t.id
                  WHERE s.usuario_id = :usuario_id
                  ORDER BY r.fecha_reserva DESC, r.fecha_creacion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();
        return $stmt;
    }

    // Actualizar estado de reserva
    public function actualizarEstado($reserva_id, $nuevo_estado, $notas_proveedor = null) {
        $query = "UPDATE " . $this->table_name . "
                  SET estado = :estado,
                      notas_proveedor = :notas_proveedor
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $nuevo_estado);
        $stmt->bindParam(":notas_proveedor", $notas_proveedor);
        $stmt->bindParam(":id", $reserva_id);

        return $stmt->execute();
    }

    // Obtener reserva por ID
    public function obtenerPorId($id) {
        $query = "SELECT r.*, s.nombre_servicio, s.tipo_servicio, s.direccion,
                  u.nombre_completo as proveedor_nombre, u.telefono as proveedor_telefono,
                  t.nombre_completo as turista_nombre, t.telefono as turista_telefono
                  FROM " . $this->table_name . " r
                  LEFT JOIN servicios s ON r.servicio_id = s.id
                  LEFT JOIN usuarios u ON s.usuario_id = u.id
                  LEFT JOIN usuarios t ON r.turista_id = t.id
                  WHERE r.id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
