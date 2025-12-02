<?php
// ============================================
// ARCHIVO: models\Estadistica.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\models\Estadistica.php
// ============================================

class Estadistica {
    private $conn;
    private $table_name = "estadisticas_visitantes";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Registrar estadística diaria
    public function registrarDiaria($fecha, $datos) {
        $query = "INSERT INTO " . $this->table_name . "
                SET fecha=:fecha,
                    numero_visitantes=:numero_visitantes,
                    visitantes_nacionales=:visitantes_nacionales,
                    visitantes_extranjeros=:visitantes_extranjeros,
                    satisfaccion_promedio=:satisfaccion_promedio
                ON DUPLICATE KEY UPDATE
                    numero_visitantes=:numero_visitantes,
                    visitantes_nacionales=:visitantes_nacionales,
                    visitantes_extranjeros=:visitantes_extranjeros,
                    satisfaccion_promedio=:satisfaccion_promedio";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":fecha", $fecha);
        $stmt->bindParam(":numero_visitantes", $datos['numero_visitantes']);
        $stmt->bindParam(":visitantes_nacionales", $datos['visitantes_nacionales']);
        $stmt->bindParam(":visitantes_extranjeros", $datos['visitantes_extranjeros']);
        $stmt->bindParam(":satisfaccion_promedio", $datos['satisfaccion_promedio']);

        return $stmt->execute();
    }

    // Obtener estadísticas mensuales
    public function obtenerMensuales($ano, $mes) {
        $query = "SELECT 
                    SUM(numero_visitantes) as total_visitantes,
                    AVG(satisfaccion_promedio) as satisfaccion_promedio,
                    SUM(visitantes_nacionales) as total_nacionales,
                    SUM(visitantes_extranjeros) as total_extranjeros
                FROM " . $this->table_name . " 
                WHERE YEAR(fecha) = :ano AND MONTH(fecha) = :mes";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ano", $ano, PDO::PARAM_INT);
        $stmt->bindParam(":mes", $mes, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Dashboard gubernamental
    public function obtenerDashboardGobierno() {
        $mes_actual = date('Y-m');
        $mes_anterior = date('Y-m', strtotime('-1 month'));

        // Visitantes mes actual
        $query1 = "SELECT SUM(numero_visitantes) as total 
                  FROM " . $this->table_name . " 
                  WHERE DATE_FORMAT(fecha, '%Y-%m') = :mes_actual";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bindParam(":mes_actual", $mes_actual);
        $stmt1->execute();
        $visitantes_actual = $stmt1->fetch(PDO::FETCH_ASSOC);

        // Visitantes mes anterior
        $query2 = "SELECT SUM(numero_visitantes) as total 
                  FROM " . $this->table_name . " 
                  WHERE DATE_FORMAT(fecha, '%Y-%m') = :mes_anterior";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(":mes_anterior", $mes_anterior);
        $stmt2->execute();
        $visitantes_anterior = $stmt2->fetch(PDO::FETCH_ASSOC);

        // Calcular porcentaje de crecimiento
        $crecimiento = 0;
        if($visitantes_anterior['total'] > 0) {
            $crecimiento = (($visitantes_actual['total'] - $visitantes_anterior['total']) / $visitantes_anterior['total']) * 100;
        }

        // Operadores activos
        $query3 = "SELECT COUNT(*) as total FROM usuarios 
                  WHERE tipo_usuario IN ('agencia', 'operador', 'restaurante', 'hotel') 
                  AND activo = 1";
        $stmt3 = $this->conn->prepare($query3);
        $stmt3->execute();
        $operadores = $stmt3->fetch(PDO::FETCH_ASSOC);

        // Satisfacción promedio
        $query4 = "SELECT AVG(calificacion) as promedio FROM resenas 
                  WHERE visible = 1 
                  AND fecha_creacion >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $stmt4 = $this->conn->prepare($query4);
        $stmt4->execute();
        $satisfaccion = $stmt4->fetch(PDO::FETCH_ASSOC);

        return array(
            'visitantes_mes' => $visitantes_actual['total'] ?? 0,
            'crecimiento_porcentaje' => round($crecimiento, 2),
            'operadores_activos' => $operadores['total'] ?? 0,
            'satisfaccion_promedio' => round($satisfaccion['promedio'] ?? 0, 1)
        );
    }

    // Obtener datos para gráficos
    public function obtenerDatosGrafico($tipo, $periodo_inicio, $periodo_fin) {
        $query = "SELECT 
                    fecha,
                    numero_visitantes,
                    visitantes_nacionales,
                    visitantes_extranjeros,
                    satisfaccion_promedio
                FROM " . $this->table_name . "
                WHERE fecha BETWEEN :inicio AND :fin
                ORDER BY fecha ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":inicio", $periodo_inicio);
        $stmt->bindParam(":fin", $periodo_fin);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener totales anuales
    public function obtenerTotalesAnuales($ano) {
        $query = "SELECT 
                    SUM(numero_visitantes) as total_visitantes,
                    AVG(satisfaccion_promedio) as satisfaccion_promedio,
                    MAX(numero_visitantes) as max_visitantes_dia,
                    MIN(numero_visitantes) as min_visitantes_dia
                FROM " . $this->table_name . " 
                WHERE YEAR(fecha) = :ano";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ano", $ano, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>