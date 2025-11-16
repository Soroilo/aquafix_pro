# AquaFix Pro - Sistema de Gestión de Servicios de Fontanería

## Descripción
Plataforma web profesional para gestión de servicios de fontanería con integración de mashups (APIs externas):
- OpenWeatherMap API (información climática)
- Google Maps API (geolocalización)
- EmailJS API (notificaciones)

## Tecnologías Utilizadas
- PHP 7.4+
- CodeIgniter 3.x
- MySQL 5.7+
- JavaScript ES6+
- jQuery 3.6+
- AJAX
- HTML5 & CSS3

## Instalación

### 1. Requisitos Previos
- XAMPP / WAMP / LAMP
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor Apache con mod_rewrite habilitado

### 2. Configuración de Base de Datos
```sql
-- Ejecutar el archivo database.sql en MySQL
mysql -u root -p < database.sql
```

### 3. Configuración de CodeIgniter
```php
// Editar application/config/database.php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'aquafix_pro',
);

// Editar application/config/config.php
$config['base_url'] = 'http://localhost/aquafix_pro/';
```

### 4. Configurar Permisos
```bash
chmod -R 755 application/cache
chmod -R 755 application/logs
```

### 5. Acceso
```
URL: http://localhost/aquafix_pro/
```

## Credenciales de Prueba

### Clientes
- Email: juan.perez@email.com
- Password: password (cambiar en producción)

### Fontaneros
- Email: pedro.martinez@aquafix.com
- Password: password (cambiar en producción)

### Admin
- Email: admin@aquafix.com
- Password: password (cambiar en producción)

## Estructura del Proyecto
```
aquafix_pro/
├── application/
│   ├── config/
│   │   ├── autoload.php
│   │   ├── config.php
│   │   ├── database.php
│   │   └── routes.php
│   ├── controllers/
│   │   ├── Admin.php
│   │   ├── Api.php
│   │   ├── Auth.php
│   │   ├── Cliente.php
│   │   ├── Fontanero.php
│   │   └── Home.php
│   ├── models/
│   │   ├── Calificacion_model.php
│   │   ├── Cliente_model.php
│   │   ├── Fontanero_model.php
│   │   ├── Servicio_model.php
│   │   └── Solicitud_model.php
│   └── views/
│       ├── admin/
│       ├── auth/
│       ├── cliente/
│       ├── fontanero/
│       ├── home/
│       └── templates/
├── assets/
│   ├── css/
│   │   └── styles.css
│   └── js/
│       ├── script.js
│       └── ajax-handler.js
├── system/ (CodeIgniter framework)
├── .htaccess
├── index.php
└── database.sql
```

## Funcionalidades

### Clientes
- Registro e inicio de sesión
- Solicitar servicios de fontanería
- Ver historial de solicitudes
- Calificar fontaneros
- Gestión de perfil

### Fontaneros
- Registro e inicio de sesión
- Ver solicitudes disponibles
- Aceptar solicitudes
- Gestionar disponibilidad
- Ver historial de trabajos

### Administrador
- Dashboard con estadísticas
- Gestión de clientes
- Gestión de fontaneros
- Gestión de servicios
- Asignación de trabajos
- Reportes

## APIs Configuradas

### OpenWeatherMap API
```javascript
// Configurar en assets/js/script.js
const apiKey = 'YOUR_API_KEY';
```

### Google Maps API
```html
<!-- Agregar en views/templates/header.php -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
```

### EmailJS API
```javascript
// Configurar en assets/js/script.js
emailjs.init('YOUR_PUBLIC_KEY');
```

## Seguridad
- Validación de datos con AJAX
- Passwords hasheados con bcrypt
- Sesiones seguras
- XSS filtering

## Autor
Proyecto desarrollado para el curso de Aplicaciones Web
