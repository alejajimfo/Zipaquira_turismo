<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $tipo_usuario;
    public $email;
    public $password;
    public $nombre_completo;
    public $telefono;
    public $direccion;
    public $ciudad;
    public $pais;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear nuevo usuario
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . "
                SET tipo_usuario=:tipo_usuario,
                    email=:email,
                    password_hash=:password_hash,
                    nombre_completo=:nombre_completo,
                    telefono=:telefono,
                    direccion=:direccion,
                    ciudad=:ciudad,
                    pais=:pais,
                    aceptacion_terminos=:aceptacion_terminos,
                    aceptacion_habeas_data=:aceptacion_habeas_data,
                    fecha_aceptacion_politicas=NOW(),
                    ip_registro=:ip_registro";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $this->tipo_usuario = htmlspecialchars(strip_tags($this->tipo_usuario));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->nombre_completo = htmlspecialchars(strip_tags($this->nombre_completo));
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        // Bind
        $stmt->bindParam(":tipo_usuario", $this->tipo_usuario);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password_hash", $password_hash);
        $stmt->bindParam(":nombre_completo", $this->nombre_completo);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":direccion", $this->direccion);
        $stmt->bindParam(":ciudad", $this->ciudad);
        $stmt->bindParam(":pais", $this->pais);
        $stmt->bindParam(":aceptacion_terminos", $this->aceptacion_terminos);
        $stmt->bindParam(":aceptacion_habeas_data", $this->aceptacion_habeas_data);
        $stmt->bindParam(":ip_registro", $_SERVER['REMOTE_ADDR']);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Login
public function login() {
    $query = "SELECT id, tipo_usuario, email, password_hash, nombre_completo, activo, verificado
            FROM " . $this->table_name . "
            WHERE email = :email
            LIMIT 1";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":email", $this->email);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verificar si la cuenta está activa
        if($row['activo'] == 0) {
            return array(
                "success" => false,
                "message" => "Cuenta inactiva"
            );
        }

        // Verificar contraseña
        if(password_verify($this->password, $row['password_hash'])) {
            // Actualizar última conexión
            $update_query = "UPDATE " . $this->table_name . " 
                           SET ultima_conexion = NOW() 
                           WHERE id = :id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(":id", $row['id']);
            $update_stmt->execute();

            // Retornar datos del usuario
            return array(
                "success" => true,
                "id" => $row['id'],
                "tipo_usuario" => $row['tipo_usuario'],
                "email" => $row['email'],
                "nombre_completo" => $row['nombre_completo'],
                "verificado" => $row['verificado']
            );
        } else {
            return array(
                "success" => false,
                "message" => "Contraseña incorrecta"
            );
        }
    }
    
    return array(
        "success" => false,
        "message" => "Usuario no encontrado"
    );
}
    // Verificar si email existe
    public function emailExiste() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Obtener usuario por ID
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>