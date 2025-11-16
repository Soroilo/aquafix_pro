# INSTALACIÓN RÁPIDA - AQUAFIX PRO

## Paso 1: Requisitos
- XAMPP/WAMP/LAMP instalado
- PHP 7.4+
- MySQL 5.7+


O usar phpMyAdmin:
1. Abrir http://localhost/phpmyadmin
2. Crear base de datos "aquafix_pro"
3. Importar database.sql

## Paso 4: Configuración
```php
// Editar application/config/database.php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',  // Tu contraseña de MySQL
    'database' => 'aquafix_pro',
);

// Editar application/config/config.php
$config['base_urlco'] = 'http://localhost/aquafix_pro/';
```


## Paso 6: Acceso
Abrir en el navegador:
```
http://localhost/aquafix_pro/
```

## Credenciales de Prueba

### Cliente
- Email: juan.perez@email.com
- Password: password123

### Fontanero
- Email: pedro.martinez@aquafix.com
- Password: password

### Admin
- Email: admin@aquafix.com
- Password: admin123

## Paso 7: Configurar APIs (Opcional)

### OpenWeatherMap API
1. Obtener API Key en: https://openweathermap.org/api
2. Editar assets/js/script.js:
```javascript
const apiKey = 'TU_API_KEY';
```

### Google Maps API
1. Obtener API Key en: https://console.cloud.google.com/
2. Agregar en views/templates/header.php:
```html
<script src="https://maps.googleapis.com/maps/api/js?key=TU_API_KEY"></script>
```

### EmailJS API
1. Crear cuenta en: https://www.emailjs.com/
2. Configurar en assets/js/script.js:
```javascript
emailjs.init('TU_PUBLIC_KEY');
```

## Resolución de Problemas

### Error 404 - Not Found
Verificar que mod_rewrite está habilitado en Apache:
```bash
# En httpd.conf
LoadModule rewrite_module modules/mod_rewrite.so
```

### Error de Conexión a Base de Datos
- Verificar que MySQL está corriendo
- Verificar credenciales en database.php
- Verificar que la base de datos existe

`

## Verificación
Para verificar que todo funciona:
1. Página principal carga correctamente
2. Clima muestra información actual
3. Mapa muestra ubicación
4. Login funciona correctamente
5. Registro crea nuevo usuario



## Estructura de Archivos Clave
```
aquafix_pro/
├── index.php                    # Punto de entrada
├── .htaccess                    # Configuración Apache
├── database.sql                 # Script de base de datos
├── application/
│   ├── config/
│   │   ├── config.php          # Configuración principal
│   │   ├── database.php        # Configuración de BD
│   │   └── routes.php          # Rutas
│   ├── controllers/
│   │   ├── Home.php            # Página principal
│   │   ├── Auth.php            # Autenticación
│   │   ├── Api.php             # Endpoints AJAX
│   │   ├── Cliente.php         # Panel cliente
│   │   └── Fontanero.php       # Panel fontanero
│   ├── models/                 # Modelos de datos
│   └── views/                  # Vistas HTML
└── assets/
    ├── css/styles.css          # Estilos
    └── js/
        ├── script.js           # JavaScript principal
        └── ajax-handler.js     # Manejador AJAX
```

## Siguientes Pasos
3. Personalizar estilos
4. Agregar más servicios
5. Configurar email real

¡Listo! Tu aplicación AquaFix Pro está funcionando.
