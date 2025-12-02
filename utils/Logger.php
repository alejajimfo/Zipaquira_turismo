<?php
// ============================================
// ARCHIVO: utils\Logger.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\utils\Logger.php
// ============================================

class Logger {
    private $log_file;
    private $log_dir;

    public function __construct() {
        $this->log_dir = __DIR__ . '/../logs/';
        $this->log_file = $this->log_dir . 'app_' . date('Y-m-d') . '.log';
        
        // Crear directorio si no existe
        if(!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0755, true);
        }
    }

    /**
     * Registrar información
     */
    public function info($mensaje, $contexto = array()) {
        $this->log('INFO', $mensaje, $contexto);
    }

    /**
     * Registrar error
     */
    public function error($mensaje, $contexto = array()) {
        $this->log('ERROR', $mensaje, $contexto);
    }

    /**
     * Registrar advertencia
     */
    public function warning($mensaje, $contexto = array()) {
        $this->log('WARNING', $mensaje, $contexto);
    }

    /**
     * Registrar depuración
     */
    public function debug($mensaje, $contexto = array()) {
        $this->log('DEBUG', $mensaje, $contexto);
    }

    /**
     * Función principal de logging
     */
    private function log($nivel, $mensaje, $contexto) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $contexto_str = !empty($contexto) ? json_encode($contexto) : '';
        
        $linea = "[{$timestamp}] [{$nivel}] [{$ip}] {$mensaje} {$contexto_str}\n";
        
        file_put_contents($this->log_file, $linea, FILE_APPEND);
    }

    /**
     * Limpiar logs antiguos (más de 30 días)
     */
    public function limpiarLogsAntiguos() {
        $archivos = glob($this->log_dir . '*.log');
        $ahora = time();
        
        foreach($archivos as $archivo) {
            if(is_file($archivo)) {
                if($ahora - filemtime($archivo) >= 30 * 24 * 60 * 60) {
                    unlink($archivo);
                }
            }
        }
    }
}
?>