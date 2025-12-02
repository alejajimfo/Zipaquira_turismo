<?php
// ============================================
// ARCHIVO: utils\Email.php
// Ruta: C:\xampp\htdocs\zipaquira-turismo\utils\Email.php
// ============================================

class Email {
    private $from_email = "noreply@zipaquiraturistica.com";
    private $from_name = "Zipaquirá Turística";
    private $charset = "UTF-8";

    // Configuración SMTP (opcional - para Gmail, Outlook, etc.)
    private $use_smtp = false;
    private $smtp_host = "smtp.gmail.com";
    private $smtp_port = 587;
    private $smtp_user = "";
    private $smtp_pass = "";

    public function __construct() {
        // Cargar configuración desde archivo si existe
        if(file_exists(__DIR__ . '/../config/email_config.php')) {
            $config = include(__DIR__ . '/../config/email_config.php');
            if(isset($config['from_email'])) $this->from_email = $config['from_email'];
            if(isset($config['from_name'])) $this->from_name = $config['from_name'];
            if(isset($config['use_smtp'])) $this->use_smtp = $config['use_smtp'];
        }
    }

    /**
     * Enviar email de bienvenida
     */
    public function enviarBienvenida($email, $nombre) {
        $subject = "¡Bienvenido a Zipaquirá Turística!";
        $message = $this->getTemplateBienvenida($nombre);
        
        return $this->enviar($email, $subject, $message);
    }

    /**
     * Enviar confirmación de reserva
     */
    public function enviarConfirmacionReserva($email, $nombre, $codigo_confirmacion, $detalles) {
        $subject = "Confirmación de Reserva - {$codigo_confirmacion}";
        $message = $this->getTemplateReserva($nombre, $codigo_confirmacion, $detalles);
        
        return $this->enviar($email, $subject, $message);
    }

    /**
     * Enviar notificación de cancelación
     */
    public function enviarCancelacionReserva($email, $nombre, $codigo_confirmacion) {
        $subject = "Reserva Cancelada - {$codigo_confirmacion}";
        $message = $this->getTemplateCancelacion($nombre, $codigo_confirmacion);
        
        return $this->enviar($email, $subject, $message);
    }

    /**
     * Enviar código de recuperación de contraseña
     */
    public function enviarRecuperacionPassword($email, $nombre, $codigo) {
        $subject = "Recuperación de Contraseña - Zipaquirá Turística";
        $message = $this->getTemplateRecuperacion($nombre, $codigo);
        
        return $this->enviar($email, $subject, $message);
    }

    /**
     * Función principal de envío
     */
    private function enviar($to, $subject, $message) {
        if($this->use_smtp) {
            return $this->enviarSMTP($to, $subject, $message);
        } else {
            return $this->enviarPHPMail($to, $subject, $message);
        }
    }

    /**
     * Enviar usando función mail() de PHP
     */
    private function enviarPHPMail($to, $subject, $message) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset={$this->charset}" . "\r\n";
        $headers .= "From: {$this->from_name} <{$this->from_email}>" . "\r\n";
        $headers .= "Reply-To: {$this->from_email}" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        try {
            return mail($to, $subject, $message, $headers);
        } catch (Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar usando SMTP (requiere PHPMailer o similar)
     */
    private function enviarSMTP($to, $subject, $message) {
        // Implementar con PHPMailer si se necesita
        // Por ahora retorna false
        error_log("SMTP no configurado");
        return false;
    }

    /**
     * Template de bienvenida
     */
    private function getTemplateBienvenida($nombre) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; }
                .header { background: linear-gradient(135deg, #2563eb, #1e40af); color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; background: #f9fafb; }
                .button { background: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
                .footer { background: #1f2937; color: white; padding: 20px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>¡Bienvenido a Zipaquirá Turística!</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>{$nombre}</strong>,</p>
                    <p>Gracias por registrarte en nuestra plataforma turística.</p>
                    <p>Ahora puedes explorar todo lo que Zipaquirá tiene para ofrecerte:</p>
                    <ul>
                        <li>Visita virtual de la Catedral de Sal</li>
                        <li>Reservas de tours y actividades</li>
                        <li>Recomendaciones de restaurantes y hospedajes</li>
                        <li>Sistema de reseñas y calificaciones</li>
                        <li>Y mucho más...</li>
                    </ul>
                    <center>
                        <a href='http://localhost/zipaquira-turismo' class='button'>Explorar Ahora</a>
                    </center>
                    <p>¡Esperamos que disfrutes tu experiencia en Zipaquirá!</p>
                    <p>Saludos,<br><strong>El equipo de Zipaquirá Turística</strong></p>
                </div>
                <div class='footer'>
                    <p>Zipaquirá Turística © 2025</p>
                    <p>Cumpliendo con la Ley 1581 de 2012 - Protección de Datos Personales</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Template de confirmación de reserva
     */
    private function getTemplateReserva($nombre, $codigo, $detalles) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; }
                .header { background: #10b981; color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; background: #f9fafb; }
                .codigo { background: #2563eb; color: white; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; border-radius: 5px; margin: 20px 0; }
                .detalles { background: white; padding: 20px; border-left: 4px solid #2563eb; margin: 20px 0; }
                .footer { background: #1f2937; color: white; padding: 20px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✓ Reserva Confirmada</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>{$nombre}</strong>,</p>
                    <p>Tu reserva ha sido confirmada exitosamente.</p>
                    <div class='codigo'>
                        Código: {$codigo}
                    </div>
                    <div class='detalles'>
                        <h3>Detalles de tu reserva:</h3>
                        <ul>
                            <li><strong>Fecha:</strong> " . ($detalles['fecha'] ?? 'N/A') . "</li>
                            <li><strong>Hora:</strong> " . ($detalles['hora'] ?? 'N/A') . "</li>
                            <li><strong>Número de personas:</strong> " . ($detalles['personas'] ?? 'N/A') . "</li>
                            <li><strong>Total:</strong> $" . number_format($detalles['total'] ?? 0, 0, ',', '.') . "</li>
                        </ul>
                    </div>
                    <p><strong>Importante:</strong> Por favor guarda este código para presentarlo el día de tu visita.</p>
                    <p>Si necesitas cancelar o modificar tu reserva, contáctanos.</p>
                    <p>¡Te esperamos en Zipaquirá!</p>
                </div>
                <div class='footer'>
                    <p>Zipaquirá Turística © 2025</p>
                    <p>Teléfono: +57 1 8512000 | Email: info@zipaquiraturistica.com</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Template de cancelación
     */
    private function getTemplateCancelacion($nombre, $codigo) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <body style='font-family: Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #ef4444;'>Reserva Cancelada</h2>
                <p>Hola <strong>{$nombre}</strong>,</p>
                <p>Tu reserva con código <strong>{$codigo}</strong> ha sido cancelada.</p>
                <p>Si esto fue un error, por favor contáctanos lo antes posible.</p>
                <p>Esperamos verte pronto en Zipaquirá.</p>
                <hr>
                <p style='font-size: 12px; color: #666;'>Zipaquirá Turística © 2025</p>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Template de recuperación de contraseña
     */
    private function getTemplateRecuperacion($nombre, $codigo) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <body style='font-family: Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #2563eb;'>Recuperación de Contraseña</h2>
                <p>Hola <strong>{$nombre}</strong>,</p>
                <p>Hemos recibido una solicitud para recuperar tu contraseña.</p>
                <p>Tu código de recuperación es:</p>
                <div style='background: #2563eb; color: white; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; margin: 20px 0;'>
                    {$codigo}
                </div>
                <p>Este código expira en 1 hora.</p>
                <p>Si no solicitaste este cambio, ignora este mensaje.</p>
                <hr>
                <p style='font-size: 12px; color: #666;'>Zipaquirá Turística © 2025</p>
            </div>
        </body>
        </html>
        ";
    }
}
?>
