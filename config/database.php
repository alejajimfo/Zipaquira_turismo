<?php
/**
 * CONFIGURACIÓN DE LA BASE DE DATOS
 */
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", ""); // vacío en XAMPP
define("DB_NAME", "zipaquira_turismo");
define("DB_PORT", 3306);

// ----------------------------
//  CLASE PRINCIPAL DATABASE
// ----------------------------
class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $port = DB_PORT;
    private $conn;

    /**
     * Obtener conexión PDO
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }

    /**
     * Método alternativo para mysqli (compatible con verificar_instalacion.php)
     */
    public function connect() {
        $conn = new mysqli($this->host, $this->username, $this->password, $this->db_name, $this->port);

        if ($conn->connect_errno) {
            return false;
        }

        return $conn;
    }

    /**
     * Cerrar conexión
     */
    public function close() {
        $this->conn = null;
    }
}
?>