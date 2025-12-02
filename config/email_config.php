<?php
/**
 * ============================================
 * ARCHIVO: config\email_config.php
 * Ruta: C:\xampp\htdocs\zipaquira-turismo\config\email_config.php
 * ============================================
 * 
 * Configuración de Email para la Plataforma Turística Zipaquirá
 * 
 * Este archivo contiene la configuración para el envío de correos electrónicos.
 * Puedes usar la función mail() de PHP o configurar SMTP para servicios como Gmail, Outlook, etc.
 */

return array(
    
    // ============================================
    // CONFIGURACIÓN GENERAL
    // ============================================
    
    /**
     * Email remitente (FROM)
     * Este es el email que aparecerá como remitente
     */
    'from_email' => 'noreply@zipaquiraturistica.com',
    
    /**
     * Nombre del remitente
     * Nombre que aparecerá junto al email remitente
     */
    'from_name' => 'Zipaquirá Turística',
    
    /**
     * Email de respuesta (REPLY-TO)
     * Email donde los destinatarios pueden responder
     */
    'reply_to' => 'info@zipaquiraturistica.com',
    
    /**
     * Charset del email
     * Por defecto UTF-8 para soportar caracteres especiales
     */
    'charset' => 'UTF-8',
    
    
    // ============================================
    // CONFIGURACIÓN SMTP
    // ============================================
    
    /**
     * Usar SMTP
     * true = Usar servidor SMTP (Gmail, Outlook, etc.)
     * false = Usar función mail() de PHP (requiere servidor configurado)
     */
    'use_smtp' => false,
    
    /**
     * Host SMTP
     * Servidor SMTP a utilizar
     * 
     * Ejemplos comunes:
     * - Gmail: smtp.gmail.com
     * - Outlook: smtp.office365.com
     * - Yahoo: smtp.mail.yahoo.com
     * - SendGrid: smtp.sendgrid.net
     * - Mailgun: smtp.mailgun.org
     */
    'smtp_host' => 'smtp.gmail.com',
    
    /**
     * Puerto SMTP
     * Puerto del servidor SMTP
     * 
     * Puertos comunes:
     * - 25: Sin encriptación (no recomendado)
     * - 465: SSL/TLS
     * - 587: STARTTLS (recomendado)
     * - 2525: Alternativo para STARTTLS
     */
    'smtp_port' => 587,
    
    /**
     * Encriptación SMTP
     * Tipo de encriptación a usar
     * 
     * Opciones:
     * - 'tls': STARTTLS (recomendado para puerto 587)
     * - 'ssl': SSL (para puerto 465)
     * - null: Sin encriptación (no recomendado)
     */
    'smtp_encryption' => 'tls',
    
    /**
     * Usuario SMTP
     * Email completo de la cuenta SMTP
     */
    'smtp_username' => 'tu-email@gmail.com',
    
    /**
     * Contraseña SMTP
     * Contraseña de la cuenta SMTP
     * 
     * NOTA IMPORTANTE para Gmail:
     * - Si usas Gmail, debes generar una "Contraseña de Aplicación"
     * - Ve a: https://myaccount.google.com/apppasswords
     * - Genera una contraseña específica para esta aplicación
     * - NO uses tu contraseña normal de Gmail
     */
    'smtp_password' => 'tu-contraseña-aqui',
    
    /**
     * Autenticación SMTP
     * Si el servidor requiere autenticación
     */
    'smtp_auth' => true,
    
    /**
     * Verificar certificado SSL
     * Si debe verificar el certificado SSL del servidor
     * Establecer en false solo para desarrollo/pruebas
     */
    'smtp_ssl_verify' => true,
    
    
    // ============================================
    // CONFIGURACIÓN DE ENVÍO
    // ============================================
    
    /**
     * Modo debug
     * Nivel de información de debug a mostrar
     * 
     * Niveles:
     * - 0: Sin debug (producción)
     * - 1: Errores y mensajes del cliente
     * - 2: Errores, mensajes del cliente y servidor
     * - 3: Nivel 2 + información de conexión
     * - 4: Nivel 3 + datos de bajo nivel
     */
    'debug_level' => 0,
    
    /**
     * Tiempo de espera (timeout)
     * Segundos de espera para conexión SMTP
     */
    'timeout' => 30,
    
    /**
     * Límite de envíos por hora
     * Previene spam y sobrecarga del servidor
     * null = sin límite
     */
    'send_limit_per_hour' => 100,
    
    
    // ============================================
    // PLANTILLAS DE EMAIL
    // ============================================
    
    /**
     * Usar plantillas HTML
     * true = Enviar emails con formato HTML
     * false = Enviar emails en texto plano
     */
    'use_html_templates' => true,
    
    /**
     * Logo de la empresa (URL completa)
     * URL del logo para incluir en emails HTML
     */
    'logo_url' => 'http://localhost/zipaquira-turismo/assets/images/logo.png',
    
    /**
     * Color principal de la marca
     * Usado en plantillas HTML
     */
    'brand_color' => '#2563eb',
    
    /**
     * Footer de emails
     * Texto que aparece en el pie de todos los emails
     */
    'footer_text' => 'Zipaquirá Turística © 2025 - Ciudad de la Sal, Colombia',
    
    
    // ============================================
    // CONFIGURACIONES AVANZADAS
    // ============================================
    
    /**
     * BCC (Copia Oculta)
     * Email para recibir copia oculta de todos los correos
     * null = desactivado
     */
    'bcc_all_emails' => null,
    
    /**
     * Notificaciones de administrador
     * Email donde se envían notificaciones importantes
     */
    'admin_email' => 'admin@zipaquiraturistica.com',
    
    /**
     * Reintentos de envío
     * Número de intentos si falla el envío
     */
    'max_retries' => 3,
    
    /**
     * Log de emails
     * Registrar todos los emails enviados
     */
    'log_emails' => true,
    
    /**
     * Ruta del archivo de log
     */
    'log_file' => __DIR__ . '/../logs/email.log',
    
    
    // ============================================
    // CONFIGURACIONES POR TIPO DE EMAIL
    // ============================================
    
    /**
     * Activar emails de bienvenida
     */
    'enable_welcome_email' => true,
    
    /**
     * Activar emails de confirmación de reserva
     */
    'enable_booking_confirmation' => true,
    
    /**
     * Activar emails de recordatorio
     */
    'enable_reminder_email' => true,
    
    /**
     * Días antes para recordatorio
     * Días de anticipación para enviar recordatorio de reserva
     */
    'reminder_days_before' => 2,
    
    /**
     * Activar emails de cancelación
     */
    'enable_cancellation_email' => true,
    
    /**
     * Activar emails de recuperación de contraseña
     */
    'enable_password_reset' => true,
    
    /**
     * Tiempo de expiración del código de recuperación (minutos)
     */
    'password_reset_expiry' => 60,
    
    
    // ============================================
    // PROVEEDORES SMTP PRECONFIGURADOS
    // ============================================
    // Descomenta el proveedor que vas a usar y completa las credenciales
    
    /*
    // GMAIL
    'use_smtp' => true,
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_encryption' => 'tls',
    'smtp_username' => 'tu-email@gmail.com',
    'smtp_password' => 'tu-contraseña-de-aplicacion',
    'smtp_auth' => true,
    */
    
    /*
    // OUTLOOK / OFFICE 365
    'use_smtp' => true,
    'smtp_host' => 'smtp.office365.com',
    'smtp_port' => 587,
    'smtp_encryption' => 'tls',
    'smtp_username' => 'tu-email@outlook.com',
    'smtp_password' => 'tu-contraseña',
    'smtp_auth' => true,
    */
    
    /*
    // YAHOO MAIL
    'use_smtp' => true,
    'smtp_host' => 'smtp.mail.yahoo.com',
    'smtp_port' => 587,
    'smtp_encryption' => 'tls',
    'smtp_username' => 'tu-email@yahoo.com',
    'smtp_password' => 'tu-contraseña',
    'smtp_auth' => true,
    */
    
    /*
    // SENDGRID
    'use_smtp' => true,
    'smtp_host' => 'smtp.sendgrid.net',
    'smtp_port' => 587,
    'smtp_encryption' => 'tls',
    'smtp_username' => 'apikey',
    'smtp_password' => 'tu-api-key-de-sendgrid',
    'smtp_auth' => true,
    */
    
    /*
    // MAILGUN
    'use_smtp' => true,
    'smtp_host' => 'smtp.mailgun.org',
    'smtp_port' => 587,
    'smtp_encryption' => 'tls',
    'smtp_username' => 'postmaster@tu-dominio.mailgun.org',
    'smtp_password' => 'tu-contraseña-de-mailgun',
    'smtp_auth' => true,
    */
    
);

/**
 * ============================================
 * NOTAS IMPORTANTES
 * ============================================
 * 
 * 1. SEGURIDAD:
 *    - NUNCA subas este archivo a repositorios públicos
 *    - Usa variables de entorno en producción
 *    - Mantén las contraseñas seguras
 * 
 * 2. GMAIL:
 *    - Debes habilitar "Acceso de aplicaciones menos seguras"
 *    - O mejor: Usar "Contraseñas de Aplicación"
 *    - Ve a: https://myaccount.google.com/apppasswords
 * 
 * 3. PRUEBAS:
 *    - Usa mailtrap.io o mailhog para desarrollo
 *    - No uses emails reales en desarrollo
 * 
 * 4. PRODUCCIÓN:
 *    - Considera usar servicios profesionales (SendGrid, Mailgun)
 *    - Configura SPF, DKIM y DMARC en tu dominio
 *    - Monitorea la tasa de entrega
 * 
 * 5. LÍMITES:
 *    - Gmail: ~500 emails/día
 *    - Outlook: ~300 emails/día
 *    - Servicios profesionales: Miles de emails/día
 * 
 * 6. WINDOWS/XAMPP:
 *    - La función mail() de PHP puede no funcionar
 *    - Se recomienda usar SMTP
 *    - O instalar sendmail para Windows
 * 
 * 7. TESTING:
 *    - Prueba con emails reales antes de producción
 *    - Verifica que no caigan en spam
 *    - Revisa los logs de error
 * 
 * 
 * Para desarrollo local (sin SMTP)
 * ============================================
 * php'use_smtp' => false,
*```

*### Para Gmail:
*1. Descomenta la sección de Gmail
*2. Ve a https://myaccount.google.com/apppasswords
*3. Genera una contraseña de aplicación
*4. Úsala en el archivo

*### Para Producción:
*Se recomienda usar servicios profesionales como SendGrid o Mailgun

*## 📍 Ubicación del Archivo:
*```
*C:\xampp\htdocs\zipaquira-turismo\config\email_config.php
* 
*/

?>