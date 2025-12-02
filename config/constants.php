// ============================================
// ARCHIVO: config\constants.php  
// Ruta: C:\xampp\htdocs\zipaquira-turismo\config\constants.php
// ============================================

<?php
// Constantes de la aplicación
define('APP_NAME', 'Zipaquirá Turística');
define('APP_VERSION', '1.0.0');
define('UPLOAD_DIR', __DIR__ . '/../api/uploads/');
define('MAX_UPLOAD_SIZE', 10485760); // 10MB
define('ALLOWED_EXTENSIONS', array('jpg', 'jpeg', 'png', 'gif', 'pdf'));

// Configuración de sesión
define('SESSION_LIFETIME', 3600); // 1 hora

// Zona horaria
date_default_timezone_set('America/Bogota');
?>