# Sistema de Gestion Turistica Zipaquira

## Descripcion General

Plataforma web completa para la gestion turistica de Zipaquira que permite a diferentes tipos de usuarios (turistas, proveedores de servicios y gobierno) interactuar en un ecosistema turistico integrado.

---

## Inicio Rapido

### Instalacion (Primera vez)
```
http://localhost/zipaquira-turismo/sql/crear_servicios_directo.php
```

### Acceso a la Aplicacion
```
http://localhost/zipaquira-turismo/index.html
```

### Herramientas de Mantenimiento

#### Crear solo tabla de reservas
```
http://localhost/zipaquira-turismo/sql/crear_tabla_reservas.php
```

#### Verificar y crear reservas
```
http://localhost/zipaquira-turismo/verificar_y_crear_reservas.php
```

---

## Tipos de Usuario

### 1. Turista o Viajero
- Visualiza servicios turisticos disponibles
- Realiza y gestiona reservas
- Edita reservas pendientes
- Cancela reservas pendientes
- Visualiza historial completo de reservas

### 2. Proveedores de Servicios
Tipos: Hospedaje (Hoteles), Restaurantes, Operadores Turisticos, Agencias de Turismo

Funciones:
- Registrar servicios turisticos con RNT obligatorio
- Editar informacion de servicios
- Gestionar fotografias de servicios
- Recibir y gestionar reservas
- Confirmar reservas pendientes
- Cancelar reservas pendientes
- Marcar reservas como completadas

### 3. Institucion Gubernamental
- Publicar programas y eventos
- Gestionar iniciativas turisticas

---

## Funcionalidades Principales

### Sistema de Autenticacion
- Registro de usuarios con 6 tipos diferentes
- Login automatico sin seleccion de rol
- Validacion de credenciales
- Almacenamiento de sesion en localStorage
- Contraseñas hasheadas con password_hash de PHP

### Pagina de Inicio (Publica)
- Visualizacion de todos los servicios turisticos sin necesidad de login
- Dashboard de Power BI descargable
- Listado de programas gubernamentales
- Informacion de cada servicio: nombre, tipo, descripcion, precio, fotografia
- Boton de reserva visible solo para turistas autenticados
- Seccion "Unete a nuestra comunidad" solo visible sin sesion iniciada

### Dashboard de Turista
- Boton "Mis Reservas" en lugar de "Panel"
- Vista simplificada mostrando unicamente reservas
- Historial completo de reservas realizadas
- Detalles de cada reserva: servicio, fecha, hora, numero de personas, precio, estado
- Botones de accion en reservas pendientes:
  - Editar: Modificar fecha, hora, personas, notas
  - Cancelar: Cambiar estado a cancelada
- Estados de reserva con colores:
  - Pendiente (amarillo): Se puede editar y cancelar
  - Confirmada (verde): Solo visualizacion
  - Cancelada (rojo): Solo visualizacion
  - Completada (azul): Solo visualizacion

### Dashboard de Proveedor
- Formulario para registrar nuevos servicios
- Lista de "Mis Servicios Registrados" con detalles completos
- Boton de edicion para cada servicio
- Vista detallada de reservas recibidas con:
  - Informacion del cliente
  - Fecha, hora y numero de personas
  - Estado de la reserva con colores
  - Notas del turista
- Gestion de reservas:
  - Confirmar reservas pendientes
  - Cancelar reservas pendientes
  - Marcar como completadas las reservas confirmadas
- Campos obligatorios: Nombre del servicio, RNT

### Sistema de Reservas

#### Crear Reserva
- Modal interactivo para crear reservas
- Seleccion de fecha (con validacion de fecha minima)
- Seleccion de hora (opcional)
- Numero de personas
- Campo para notas o solicitudes especiales
- Calculo automatico del precio total
- Confirmacion y guardado en base de datos

#### Editar Reserva
- Solo disponible para reservas en estado "pendiente"
- Solo el turista propietario puede editar
- Modal reutilizado con titulo "Editar Reserva"
- Permite cambiar: fecha, hora, numero de personas, notas
- Recalcula precio automaticamente
- Boton cambia a "Actualizar Reserva"

#### Cancelar Reserva
- Solo disponible para reservas en estado "pendiente"
- Solo el turista propietario puede cancelar
- Confirmacion obligatoria antes de cancelar
- Cambia el estado a "cancelada"

### Gestion de Servicios
- Registro de servicios con informacion completa:
  - Nombre del servicio
  - RNT (obligatorio)
  - Descripcion
  - Direccion
  - Telefono y email
  - Horarios de apertura y cierre
  - Rango de precios (desde - hasta)

### Sistema de Edicion de Servicios
- Modal con dos tabs: Informacion y Fotografias
- Edicion de todos los campos del servicio (excepto RNT)
- Actualizacion en tiempo real
- Validacion de campos obligatorios

### Sistema de Fotografias
- Agregar multiples fotografias por servicio mediante URLs
- Marcar una fotografia como principal
- Agregar descripcion a cada fotografia
- Visualizacion en grid responsive
- Eliminar fotografias con confirmacion
- Preview automatico de imagenes
- Manejo de errores de carga

Nota: El sistema utiliza URLs de imagenes. Se recomienda usar Imgur.com para alojar las imagenes:
1. Subir imagen a Imgur.com (gratis, sin registro)
2. Copiar el "Direct Link"
3. Pegar en el campo URL del formulario

---

## Estructura de Archivos

### Frontend
- index.html - Pagina principal
- app.js - Aplicacion React completa
- styles.css - Estilos CSS

### Backend - APIs

#### Usuarios
- api/usuarios/registrar.php - Registro de usuarios
- api/usuarios/login.php - Autenticacion
- api/usuarios/perfil.php - Gestion de perfil

#### Servicios
- api/servicios/listar.php - Listar todos los servicios
- api/servicios/mis_servicios.php - Servicios del usuario
- api/servicios/crear.php - Crear servicio
- api/servicios/actualizar.php - Actualizar servicio
- api/servicios/fotos.php - Listar fotografias
- api/servicios/agregar_foto.php - Agregar fotografia
- api/servicios/eliminar_foto.php - Eliminar fotografia

#### Reservas
- api/reservas/crear.php - Crear reserva
- api/reservas/mis_reservas.php - Reservas del turista
- api/reservas/reservas_proveedor.php - Reservas recibidas por proveedor
- api/reservas/actualizar_estado.php - Actualizar estado de reserva
- api/reservas/actualizar.php - Actualizar datos de reserva (editar)

#### Programas
- api/programas/listar.php - Listar programas gubernamentales

### Modelos
- models/Usuario.php - Modelo de usuarios
- models/Servicio.php - Modelo de servicios
- models/Reserva.php - Modelo de reservas
- models/ProgramaGobierno.php - Modelo de programas

### Configuracion
- config/database.php - Configuracion de base de datos
- config/cors.php - Configuracion CORS
- config/constants.php - Constantes del sistema
- config/email_config.php - Configuracion de email

### Base de Datos
- sql/schema.sql - Esquema completo de la base de datos
- sql/crear_servicios_directo.php - Instalador de tablas
- sql/crear_tabla_reservas.php - Crear solo tabla reservas

### Recursos
- assets/docs/Dashboard Turismo Zipaquira.pbix - Dashboard Power BI
- assets/images/ - Imagenes del sistema
- assets/videos/ - Videos del sistema

---

## Base de Datos

### Tablas Principales

#### usuarios
- id
- tipo_usuario (turista, hotel, restaurante, operador, agencia, gobierno)
- email
- password
- nombre_completo
- telefono
- direccion
- ciudad
- pais
- foto_perfil
- documentos (para proveedores)
- fecha_registro

#### servicios
- id
- usuario_id
- tipo_servicio
- nombre_servicio
- rnt
- descripcion
- direccion
- telefono
- email
- horario_apertura
- horario_cierre
- precio_desde
- precio_hasta
- activo
- verificado
- fecha_creacion

#### servicio_fotos
- id
- servicio_id
- url_foto
- descripcion
- es_principal
- orden
- fecha_creacion

#### reservas
- id
- servicio_id
- turista_id
- fecha_reserva
- hora_reserva
- numero_personas
- precio_total
- estado (pendiente, confirmada, cancelada, completada)
- notas_turista
- notas_proveedor
- fecha_creacion
- fecha_actualizacion

#### programas_gobierno
- id
- usuario_id
- titulo
- descripcion
- actividades
- valor
- ubicacion
- fecha_inicio
- fecha_fin
- cupos_disponibles
- fecha_creacion

---

## Flujos de Usuario

### Flujo de Turista

1. Accede a la pagina de inicio
2. Visualiza servicios disponibles sin necesidad de login
3. Se registra como turista
4. Inicia sesion
5. Hace clic en "Reservar" en un servicio
6. Completa el modal de reserva con fecha, hora, numero de personas y notas
7. Confirma la reserva
8. Visualiza sus reservas en "Mis Reservas"
9. Puede editar reservas pendientes
10. Puede cancelar reservas pendientes

### Flujo de Proveedor

1. Accede a la pagina de inicio
2. Se registra seleccionando su tipo (Hotel, Restaurante, Operador, Agencia)
3. Inicia sesion
4. Accede a su dashboard
5. Hace clic en "Registrar Servicio"
6. Completa el formulario con informacion del servicio y RNT
7. El servicio aparece en "Mis Servicios"
8. Hace clic en "Editar" en el servicio
9. Agrega informacion adicional y fotografias
10. El servicio se muestra publicamente en la pagina de inicio
11. Recibe reservas de turistas en su dashboard

### Flujo de Gobierno

1. Se registra como institucion gubernamental
2. Inicia sesion
3. Accede al dashboard gubernamental
4. Publica programas y eventos
5. Los programas se muestran publicamente en la pagina de inicio

---

## Validaciones

### Registro de Usuario
- Nombre completo obligatorio
- Email valido y unico
- Contraseña minimo 6 caracteres
- Confirmacion de contraseña
- Aceptacion de terminos y condiciones
- Aceptacion de politica de Habeas Data

### Registro de Servicio
- Nombre del servicio obligatorio
- RNT obligatorio
- Validacion de formato de email
- Validacion de formato de telefono
- Precios numericos positivos

### Creacion de Reserva
- Fecha obligatoria
- Fecha no puede ser anterior a hoy
- Numero de personas minimo 1
- Usuario debe ser turista autenticado

### Edicion de Reserva
- Solo el dueño de la reserva
- Solo reservas "pendientes"
- Fecha no puede ser anterior a hoy
- Numero de personas minimo 1

### Cancelacion de Reserva
- Solo el dueño de la reserva
- Solo reservas "pendientes"
- Confirmacion obligatoria

### Gestion de Fotografias
- URL de fotografia obligatoria
- Solo el propietario puede agregar/eliminar fotografias
- Confirmacion antes de eliminar

---

## Estados de Reserva

- **pendiente**: Reserva creada, esperando confirmacion. Se puede editar y cancelar.
- **confirmada**: Reserva confirmada por el proveedor. Solo visualizacion.
- **cancelada**: Reserva cancelada. Solo visualizacion.
- **completada**: Servicio completado. Solo visualizacion.

---

## Seguridad

### Autenticacion
- Contraseñas hasheadas con password_hash de PHP
- Validacion de sesion en cada peticion
- Tokens de sesion almacenados en localStorage

### Autorizacion
- Validacion de usuario_id en operaciones de modificacion
- Solo el propietario puede editar sus servicios
- Solo turistas pueden crear reservas
- Solo el propietario puede editar/cancelar sus reservas
- Validacion de tipo de usuario en backend

### Validacion de Datos
- Sanitizacion de inputs en PHP
- Validacion de tipos de datos
- Prevencion de SQL injection con prepared statements
- Validacion de CORS en APIs

---

## Solucion de Problemas

### Error: "Column 'servicio_id' not found"

Causa: La tabla reservas no existe o no tiene la estructura correcta.

Solucion:
1. Ejecutar: http://localhost/zipaquira-turismo/verificar_y_crear_reservas.php
2. O ejecutar: http://localhost/zipaquira-turismo/sql/crear_tabla_reservas.php
3. Verificar en phpMyAdmin que la tabla existe

### Los servicios no se muestran en la pagina de inicio

Verificar:
1. Consola del navegador (F12) para errores JavaScript
2. Buscar mensaje "Servicios cargados:" en consola
3. Verificar que XAMPP/servidor este corriendo
4. Verificar URL de API en app.js
5. Acceder directamente a api/servicios/listar.php
6. Verificar que existan servicios en la base de datos con activo=1

### Error al crear reserva

Verificar:
1. Usuario esta autenticado como turista
2. Fecha esta seleccionada
3. Servicio existe y esta activo
4. API de reservas esta funcionando
5. Tabla reservas existe en base de datos

### No se pueden editar reservas

Verificar:
1. Usuario es el propietario de la reserva
2. Reserva esta en estado "pendiente"
3. API actualizar.php existe y funciona
4. Consola del navegador para errores

### Fotografias no se agregan

Verificar:
1. URL de fotografia es valida
2. Usuario es propietario del servicio
3. Servicio existe
4. Tabla servicio_fotos existe
5. API de fotografias esta funcionando

### Error de conexion a base de datos

Verificar:
1. Credenciales en config/database.php
2. Base de datos existe
3. Usuario tiene permisos
4. Servidor MySQL esta corriendo

### Seccion "Unete a nuestra comunidad" sigue apareciendo

Verificar:
1. Usuario esta correctamente autenticado
2. Refrescar la pagina (F5)
3. Revisar consola del navegador para errores
4. Verificar que currentUser existe en el state

---

## Configuracion

### Requisitos del Sistema
- Servidor web (Apache/Nginx)
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Navegador web moderno

### Instalacion

1. Copiar archivos al directorio del servidor web
2. Crear base de datos MySQL
3. Ejecutar sql/crear_servicios_directo.php para crear tablas
4. Configurar conexion en config/database.php
5. Acceder a index.html desde el navegador

### Configuracion de API

El archivo app.js utiliza la siguiente configuracion:
```javascript
const API_URL = window.location.origin + '/zipaquira-turismo/api';
```

Ajustar segun la ruta de instalacion.

---

## Mantenimiento

### Respaldo de Base de Datos
Ejecutar regularmente:
```sql
mysqldump -u usuario -p nombre_bd > backup.sql
```

### Limpieza de Datos
- Eliminar reservas antiguas completadas
- Verificar servicios inactivos
- Limpiar fotografias huerfanas

### Actualizacion de Servicios
- Verificar RNT vigente
- Actualizar precios
- Mantener fotografias actualizadas

---

## Pruebas

### Pruebas de Servicios Publicos
1. Abrir pagina de inicio sin login
2. Verificar que se muestran servicios
3. Verificar fotografias, descripciones y precios
4. Verificar que boton "Reservar" no aparece sin login

### Pruebas de Autenticacion
1. Registrar usuario de cada tipo
2. Iniciar sesion con cada tipo
3. Verificar dashboard correcto segun rol
4. Verificar persistencia de sesion

### Pruebas de Reservas
1. Login como turista
2. Hacer clic en "Reservar" en un servicio
3. Completar modal de reserva
4. Verificar reserva en "Mis Reservas"
5. Verificar calculo de precio total

### Pruebas de Edicion de Reservas
1. Login como turista
2. Ir a "Mis Reservas"
3. Buscar reserva "Pendiente"
4. Hacer clic en "Editar"
5. Cambiar fecha/hora/personas
6. Confirmar cambios
7. Verificar que se actualizo

### Pruebas de Cancelacion de Reservas
1. Login como turista
2. Ir a "Mis Reservas"
3. Buscar reserva "Pendiente"
4. Hacer clic en "Cancelar"
5. Confirmar accion
6. Verificar que cambio a "Cancelada"

### Pruebas de Servicios
1. Login como proveedor
2. Registrar nuevo servicio
3. Verificar en "Mis Servicios"
4. Verificar aparicion en pagina de inicio
5. Editar servicio
6. Agregar fotografias
7. Marcar fotografia principal
8. Eliminar fotografia

---

## Consultas SQL Utiles

### Ver todos los servicios con usuario
```sql
SELECT s.id, s.nombre_servicio, s.usuario_id, s.tipo_servicio, u.nombre_completo, u.email
FROM servicios s
LEFT JOIN usuarios u ON s.usuario_id = u.id
ORDER BY s.id DESC;
```

### Ver reservas con detalles
```sql
SELECT r.id, r.fecha_reserva, r.estado, s.nombre_servicio, t.nombre_completo as turista
FROM reservas r
JOIN servicios s ON r.servicio_id = s.id
JOIN usuarios t ON r.turista_id = t.id
ORDER BY r.id DESC;
```

### Ver servicios de un usuario especifico
```sql
SELECT * FROM servicios WHERE usuario_id = X;
```

### Ver usuarios registrados
```sql
SELECT id, nombre_completo, email, tipo_usuario FROM usuarios;
```

### Verificar estructura de tabla reservas
```sql
DESCRIBE reservas;
```

---

## Herramientas de Diagnostico

### Verificar Servicios en Base de Datos
```
http://localhost/zipaquira-turismo/verificar_servicios_db.php
```
Muestra:
- Estructura de la tabla servicios
- Total de servicios registrados
- Servicios por tipo y usuario
- Proveedores sin servicios

### Debug Panel de Proveedores
```
http://localhost/zipaquira-turismo/debug_panel.php
```
Muestra:
- Usuarios proveedores registrados
- Servicios por cada proveedor
- Reservas recibidas por proveedor
- Programas gubernamentales
- Diagnostico general del sistema

### Test Mis Servicios por Rol
```
http://localhost/zipaquira-turismo/test_mis_servicios_roles.html
```
Permite:
- Ver todos los usuarios y servicios
- Probar API mis_servicios.php con cualquier usuario
- Verificar servicios por tipo de proveedor

---

## Notas Importantes

### Dashboard Power BI
- Archivo ubicado en: assets/docs/Dashboard Turismo Zipaquira.pbix
- Descargable desde la pagina de inicio
- Requiere Power BI Desktop para abrir
- Contiene estadisticas y analisis del turismo en Zipaquira

### RNT (Registro Nacional de Turismo)
- Obligatorio para proveedores
- Debe ser valido
- Se valida en el backend

### Roles
- No puedes cambiar tu rol despues del registro
- Solo puedes iniciar sesion con tu rol registrado
- Cada rol tiene funcionalidades especificas

### Sistema de Fotografias
- Usa URLs en lugar de upload directo
- Recomendado: Imgur.com para alojar imagenes
- Gratuito y sin necesidad de registro

---

Version: 2.0
Fecha: Diciembre 2025
Estado: Produccion
