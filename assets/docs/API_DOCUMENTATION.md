# Documentación de API
## Plataforma Turística Zipaquirá

### URL Base
http://localhost/zipaquira-turismo/api

---

### Autenticación

#### POST /auth/login.php
Iniciar sesión

**Request:**
```json
{
    "email": "usuario@ejemplo.com",
    "password": "contraseña123"
}
```

**Response (200):**
```json
{
    "success": true,
    "id": 1,
    "tipo_usuario": "turista",
    "email": "usuario@ejemplo.com",
    "nombre_completo": "Juan Pérez"
}
```

#### POST /auth/register.php
Registrar nuevo usuario

**Request:**
```json
{
    "email": "nuevo@ejemplo.com",
    "password": "contraseña123",
    "nombre_completo": "María García",
    "tipo_usuario": "turista",
    "telefono": "3001234567",
    "ciudad": "Bogotá"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Usuario registrado exitosamente",
    "user_id": 5
}
```

#### GET /auth/logout.php
Cerrar sesión

**Response (200):**
```json
{
    "success": true,
    "message": "Sesión cerrada exitosamente"
}
```

---

### Reseñas

#### POST /resenas/crear.php
Crear nueva reseña

**Request:**
```json
{
    "usuario_id": 1,
    "tipo_entidad": "general",
    "calificacion": 5,
    "titulo": "Excelente experiencia",
    "comentario": "La Catedral de Sal es impresionante..."
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Reseña creada exitosamente",
    "resena_id": 10
}
```

#### GET /resenas/listar.php
Listar reseñas

**Parámetros:**
- `limit` (opcional): Número de resultados (default: 50)

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "usuario_id": 1,
            "nombre_completo": "María González",
            "calificacion": 5,
            "comentario": "Excelente...",
            "fecha_creacion": "2025-11-15 10:30:00"
        }
    ],
    "total": 10
}
```

---

### Reservas

#### POST /reservas/crear.php
Crear reserva

**Request:**
```json
{
    "turista_id": 1,
    "tipo_reserva": "tour",
    "referencia_id": 5,
    "fecha_reserva": "2025-12-15",
    "hora_reserva": "10:00",
    "numero_personas": 4,
    "precio_total": 150000,
    "notas_especiales": "Vegetarianos"
}
```

**Response (201):**
```json
{
    "success": true,
    "data": {
        "id": 25,
        "codigo_confirmacion": "ZIP-A1B2C3D4E5"
    }
}
```

#### GET /reservas/listar.php
Listar reservas

**Parámetros:**
- `turista_id`: ID del turista
- `estado` (opcional): Filtrar por estado

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 25,
            "codigo_confirmacion": "ZIP-A1B2C3D4E5",
            "fecha_reserva": "2025-12-15",
            "estado": "Confirmada"
        }
    ]
}
```

---

### Estadísticas

#### GET /estadisticas/dashboard.php
Obtener dashboard gubernamental

**Response (200):**
```json
{
    "success": true,
    "data": {
        "visitantes_mes": 15432,
        "crecimiento_porcentaje": 12.5,
        "operadores_activos": 156,
        "satisfaccion_promedio": 4.7
    }
}
```

#### GET /estadisticas/mensuales.php
Estadísticas mensuales

**Parámetros:**
- `ano`: Año (YYYY)
- `mes`: Mes (1-12)

**Response (200):**
```json
{
    "success": true,
    "data": {
        "total_visitantes": 15432,
        "satisfaccion_promedio": 4.7,
        "total_nacionales": 12000,
        "total_extranjeros": 3432
    }
}
```

---

### Códigos de Estado HTTP

- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `500` - Internal Server Error

---

### Errores Comunes

**Error 400:**
```json
{
    "success": false,
    "message": "Datos incompletos"
}
```

**Error 401:**
```json
{
    "success": false,
    "message": "Credenciales inválidas"
}
```

**Error 500:**
```json
{
    "success": false,
    "message": "Error del servidor"
}
```

