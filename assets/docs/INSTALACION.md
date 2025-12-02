# Manual de Instalación
## Plataforma Turística Zipaquirá

### Requisitos del Sistema

**Software Necesario:**
- Windows 7 o superior
- XAMPP 8.0 o superior (incluye Apache, MySQL, PHP)
- Navegador web moderno (Chrome, Firefox, Edge)
- Al menos 2GB de RAM libre
- 5GB de espacio en disco

**Versiones de Software:**
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache 2.4 o superior

---

### Instalación Paso a Paso

#### 1. Instalar XAMPP

1. Descargar XAMPP desde: https://www.apachefriends.org/
2. Ejecutar el instalador
3. Seleccionar componentes:
   - ✅ Apache
   - ✅ MySQL
   - ✅ PHP
   - ✅ phpMyAdmin
4. Instalar en: `C:\xampp`
5. Finalizar instalación

#### 2. Iniciar Servicios

1. Abrir "XAMPP Control Panel"
2. Clic en "Start" junto a Apache
3. Clic en "Start" junto a MySQL
4. Verificar que ambos estén en verde (Running)

#### 3. Descargar Archivos del Proyecto

1. Crear carpeta: `C:\xampp\htdocs\zipaquira-turismo\`
2. Descargar todos los archivos del proyecto
3. Extraer/Copiar en la carpeta creada

#### 4. Ejecutar Instalador Automático

1. Navegar a: `C:\xampp\htdocs\zipaquira-turismo\`
2. Doble clic en: `install.bat`
3. Seguir las instrucciones en pantalla
4. El instalador creará automáticamente:
   - Estructura de carpetas
   - Base de datos
   - Archivos de configuración
   - Permisos necesarios

#### 5. Verificar Instalación

**Verificar Apache:**

***********************************

Abrir navegador: http://localhost
Debe mostrar: Página de XAMPP

**Verificar MySQL:**
Abrir: http://localhost/phpmyadmin
Usuario: root
Contraseña: (dejar vacío)
Verificar que existe la base de datos: zipaquira_turismo

**Verificar Aplicación:**
Abrir: http://localhost/zipaquira-turismo
Debe mostrar: Página principal de Zipaquirá Turística

**Verificar API:**
Abrir: http://localhost/zipaquira-turismo/api/test.php
Debe mostrar JSON con: {"status":"ok"}

---

### Configuración Adicional

#### Configurar Email (Opcional)

Crear archivo: `config\email_config.php`
```php
<?php
return array(
    'from_email' => 'noreply@zipaquiraturistica.com',
    'from_name' => 'Zipaquirá Turística',
    'use_smtp' => false
);
?>
```

#### Configurar Límites PHP

Editar: `C:\xampp\php\php.ini`

Buscar y modificar:
```ini
upload_max_filesize = 10M
post_max_size = 10M
memory_limit = 256M
max_execution_time = 300
display_errors = Off
log_errors = On
```

Reiniciar Apache después de modificar.

---

### Solución de Problemas

**Problema: Apache no inicia**

Solución:
1. Verificar que el puerto 80 no esté ocupado
2. Cerrar Skype, IIS u otros programas
3. O cambiar puerto Apache en httpd.conf

**Problema: MySQL no inicia**

Solución:
1. Verificar que el puerto 3306 no esté ocupado
2. Revisar logs en: C:\xampp\mysql\data\mysql_error.log

**Problema: Página en blanco**

Solución:
1. Activar errores PHP temporalmente
2. Revisar logs: logs\php_errors.log
3. Verificar que todos los archivos estén en su lugar

**Problema: Error de conexión a BD**

Solución:
1. Verificar que MySQL esté corriendo
2. Verificar credenciales en config\database.php
3. Verificar que la base de datos existe

---

### Credenciales por Defecto

**Usuario Administrador:**
Email: admin@zipaquira.gov.co
Password: password

⚠️ **IMPORTANTE:** Cambiar esta contraseña inmediatamente después de la primera instalación.

**Base de Datos:**
Host: localhost
Usuario: root
Contraseña: (vacío)
Base de datos: zipaquira_turismo

---

### Mantenimiento

**Backups Recomendados:**
- Base de datos: Semanal
- Archivos: Mensual
- Logs: Limpiar cada 30 días

**Actualizar Sistema:**
```batch
1. Hacer backup completo
2. Descargar nueva versión
3. Reemplazar archivos (excepto config)
4. Ejecutar scripts de actualización si los hay
5. Probar funcionalidad
```

---

### Soporte

**Contacto:**
- Email: soporte@zipaquiraturistica.com
- Teléfono: +57 1 8512000
- Sitio web: www.zipaquiraturistica.com

**Documentación adicional:**
- Manual de usuario: docs\MANUAL_USUARIO.md
- API: docs\API_DOCUMENTATION.md
- Changelog: docs\CHANGELOG.md

