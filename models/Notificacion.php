<?php
// ============================================
// ARCHIVO: models\Notificacion.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\models\Notificacion.php
// ============================================

class Notificacion {
    private $conn;
    private $table_name = "notificaciones";

    public $id;
    public $usuario_id;
    public $tipo_notificacion;
    public $titulo;
    public $mensaje;
    public $leida;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear notificación
    public function crear($usuario_id, $tipo, $titulo, $mensaje, $url = null) {
        $query = "INSERT INTO " . $this->table_name . "
                SET usuario_id=:usuario_id,
                    tipo_notificacion=:tipo,
                    titulo=:titulo,
                    mensaje=:mensaje,
                    url_accion=:url";

        $stmt = $this->conn->prepare($query);

        // Sanitizar
        $titulo = htmlspecialchars(strip_tags($titulo));
        $mensaje = htmlspecialchars(strip_tags($mensaje));

        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":tipo", $tipo);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":mensaje", $mensaje);
        $stmt->bindParam(":url", $url);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Obtener notificaciones de usuario
    public function obtenerPorUsuario($usuario_id, $solo_no_leidas = false) {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE usuario_id = :usuario_id";
        
        if($solo_no_leidas) {
            $query .= " AND leida = 0";
        }
        
        $query .= " ORDER BY fecha_creacion DESC LIMIT 50";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Marcar como leída
    public function marcarLeida($id, $usuario_id) {
        $query = "UPDATE " . $this->table_name . "
                SET leida=1
                WHERE id=:id AND usuario_id=:usuario_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":usuario_id", $usuario_id);

        return $stmt->execute();
    }

    // Marcar todas como leídas
    public function marcarTodasLeidas($usuario_id) {
        $query = "UPDATE " . $this->table_name . "
                SET leida=1
                WHERE usuario_id=:usuario_id AND leida=0";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);

        return $stmt->execute();
    }

    // Contar no leídas
    public function contarNoLeidas($usuario_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . "
                WHERE usuario_id=:usuario_id AND leida=0";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Eliminar notificación
    public function eliminar($id, $usuario_id) {
        $query = "DELETE FROM " . $this->table_name . "
                WHERE id=:id AND usuario_id=:usuario_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":usuario_id", $usuario_id);

        return $stmt->execute();
    }

    // Eliminar notificaciones antiguas (más de 30 días)
    public function limpiarAntiguas() {
        $query = "DELETE FROM " . $this->table_name . "
                WHERE fecha_creacion < DATE_SUB(NOW(), INTERVAL 30 DAY)
                AND leida = 1";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
}
?>