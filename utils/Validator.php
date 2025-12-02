<?php
// ============================================
// ARCHIVO: utils\Validator.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\utils\Validator.php
// ============================================

class Validator {
    
    /**
     * Validar email
     */
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validar teléfono colombiano
     */
    public static function telefonoColombia($telefono) {
        // Formato: +57 xxx xxxxxxx o xxx xxxxxxx o xxxxxxxxxx
        $telefono_limpio = preg_replace('/\s+/', '', $telefono);
        $pattern = '/^(\+57)?[3][0-9]{9}$/';
        return preg_match($pattern, $telefono_limpio);
    }

    /**
     * Validar NIT colombiano
     */
    public static function nit($nit) {
        // Formato básico NIT: xxxxxxxxx-x
        $pattern = '/^\d{9}-\d$/';
        return preg_match($pattern, $nit);
    }

    /**
     * Validar cédula colombiana
     */
    public static function cedula($cedula) {
        // Solo números, entre 6 y 10 dígitos
        $pattern = '/^\d{6,10}$/';
        return preg_match($pattern, $cedula);
    }

    /**
     * Validar fecha
     */
    public static function fecha($fecha, $formato = 'Y-m-d') {
        $d = DateTime::createFromFormat($formato, $fecha);
        return $d && $d->format($formato) === $fecha;
    }

    /**
     * Validar rango de fechas
     */
    public static function rangoFechas($fecha_inicio, $fecha_fin) {
        if(!self::fecha($fecha_inicio) || !self::fecha($fecha_fin)) {
            return false;
        }
        return strtotime($fecha_inicio) <= strtotime($fecha_fin);
    }

    /**
     * Validar calificación (1-5)
     */
    public static function calificacion($valor) {
        return is_numeric($valor) && $valor >= 1 && $valor <= 5;
    }

    /**
     * Validar contraseña segura
     */
    public static function passwordSeguro($password) {
        // Mínimo 8 caracteres, al menos una mayúscula, una minúscula y un número
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
        return preg_match($pattern, $password);
    }

    /**
     * Sanitizar texto
     */
    public static function sanitizar($texto) {
        return htmlspecialchars(strip_tags(trim($texto)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validar longitud de texto
     */
    public static function longitud($texto, $min, $max) {
        $len = strlen($texto);
        return $len >= $min && $len <= $max;
    }

    /**
     * Validar URL
     */
    public static function url($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validar número entero positivo
     */
    public static function enteroPositivo($numero) {
        return is_numeric($numero) && $numero > 0 && $numero == floor($numero);
    }

    /**
     * Validar precio/monto
     */
    public static function precio($precio) {
        return is_numeric($precio) && $precio >= 0;
    }

    /**
     * Validar edad mínima
     */
    public static function edadMinima($fecha_nacimiento, $edad_minima = 18) {
        $fecha = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha)->y;
        return $edad >= $edad_minima;
    }

    /**
     * Validar código postal colombiano
     */
    public static function codigoPostal($codigo) {
        // Formato: 6 dígitos
        $pattern = '/^\d{6}$/';
        return preg_match($pattern, $codigo);
    }

    /**
     * Validar archivo subido
     */
    public static function archivoSubido($archivo, $extensiones_permitidas, $tamano_maximo) {
        if($archivo['error'] !== UPLOAD_ERR_OK) {
            return array('valido' => false, 'mensaje' => 'Error al subir archivo');
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if(!in_array($extension, $extensiones_permitidas)) {
            return array('valido' => false, 'mensaje' => 'Tipo de archivo no permitido');
        }

        if($archivo['size'] > $tamano_maximo) {
            return array('valido' => false, 'mensaje' => 'Archivo muy grande');
        }

        return array('valido' => true, 'mensaje' => 'Archivo válido');
    }

    /**
     * Validar JSON
     */
    public static function json($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Limpiar número de teléfono
     */
    public static function limpiarTelefono($telefono) {
        return preg_replace('/[^0-9+]/', '', $telefono);
    }

    /**
     * Formatear precio colombiano
     */
    public static function formatearPrecio($precio) {
        return '$' . number_format($precio, 0, ',', '.');
    }

    /**
     * Validar tipo de usuario
     */
    public static function tipoUsuario($tipo) {
        $tipos_validos = array('turista', 'agencia', 'operador', 'restaurante', 'hotel', 'gobierno');
        return in_array($tipo, $tipos_validos);
    }

    /**
     * Validar estado de reserva
     */
    public static function estadoReserva($estado) {
        $estados_validos = array('Pendiente', 'Confirmada', 'Cancelada', 'Completada');
        return in_array($estado, $estados_validos);
    }

    /**
     * Generar código aleatorio
     */
    public static function generarCodigo($longitud = 6) {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigo = '';
        for($i = 0; $i < $longitud; $i++) {
            $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        return $codigo;
    }

    /**
     * Validar formato de hora (HH:MM)
     */
    public static function hora($hora) {
        $pattern = '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/';
        return preg_match($pattern, $hora);
    }

    /**
     * Validar IP
     */
    public static function ip($ip) {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
}
?>