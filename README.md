# Sistema de Gestion Turistica Zipaquira

Plataforma web completa para la gestion turistica de Zipaquira.

## Inicio Rapido

### Instalacion (Primera vez)
```
http://localhost/zipaquira-turismo/sql/crear_servicios_directo.php
```

### Acceder a la Aplicacion
```
http://localhost/zipaquira-turismo/index.html
```

## Tipos de Usuario

- **Turista**: Reserva servicios, gestiona reservas (editar/cancelar pendientes)
- **Proveedores** (Hotel, Restaurante, Operador, Agencia): Registran servicios, gestionan reservas recibidas
- **Gobierno**: Publica programas turisticos

## Funcionalidades Principales

### Para Turistas
- Ver servicios disponibles sin login
- Crear, editar y cancelar reservas
- Descargar Dashboard Power BI

### Para Proveedores
- Registrar servicios con RNT
- Gestionar fotografias
- Confirmar/cancelar/completar reservas recibidas

### Para Gobierno
- Publicar programas y eventos

## Herramientas de Diagnostico

- `verificar_servicios_db.php` - Verificar servicios en BD
- `debug_panel.php` - Debug completo del sistema
- `test_mis_servicios_roles.html` - Probar APIs por rol

## Documentacion Completa

Ver **DOCUMENTACION_COMPLETA.md** para:
- Guia detallada de instalacion
- Estructura de base de datos
- APIs disponibles
- Solucion de problemas
- Consultas SQL utiles

## Requisitos

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- Navegador moderno

## Soporte

Para problemas tecnicos, consultar DOCUMENTACION_COMPLETA.md seccion "Solucion de Problemas".

---

Version: 2.0
Fecha: Diciembre 2025
