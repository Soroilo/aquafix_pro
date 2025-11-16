# DOCUMENTACIÓN TÉCNICA - AQUAFIX PRO

## 1. ARQUITECTURA DEL SISTEMA

### 1.1 Patrón MVC (Model-View-Controller)
- **Models**: Gestión de datos y lógica de negocio
- **Views**: Presentación de información al usuario
- **Controllers**: Intermediarios entre Models y Views

### 1.2 Estructura de Directorios
```
aquafix_pro/
├── application/
│   ├── config/          # Configuraciones del sistema
│   ├── controllers/     # Controladores (lógica de aplicación)
│   ├── models/          # Modelos (acceso a datos)
│   ├── views/           # Vistas (HTML/PHP)
│   ├── helpers/         # Funciones auxiliares
│   └── libraries/       # Librerías personalizadas
├── assets/
│   ├── css/            # Estilos CSS
│   └── js/             # JavaScript y AJAX
└── system/             # Framework CodeIgniter
```

## 2. MODELOS (Models)

### 2.1 Cliente_model.php
**Funciones principales:**
- `crear($datos)`: Crea un nuevo cliente
- `obtener_por_id($id)`: Obtiene cliente por ID
- `obtener_por_email($email)`: Busca cliente por email
- `verificar_credenciales($email, $password)`: Valida login
- `actualizar($id, $datos)`: Actualiza información del cliente

**Validaciones:**
- Password hasheado con bcrypt
- Email único en la base de datos
- Teléfono con formato válido

### 2.2 Fontanero_model.php
**Funciones principales:**
- `listar_disponibles()`: Obtiene fontaneros disponibles
- `actualizar_rating($id)`: Actualiza calificación promedio
- `incrementar_servicios($id)`: Incrementa contador de servicios
- `obtener_estadisticas($id)`: Obtiene métricas del fontanero

### 2.3 Solicitud_model.php
**Funciones principales:**
- `crear($datos)`: Crea nueva solicitud
- `asignar_fontanero($id, $id_fontanero, $fecha)`: Asigna fontanero
- `actualizar_estado($id, $estado)`: Cambia estado de solicitud
- `listar_por_cliente($id_cliente)`: Lista solicitudes del cliente

## 3. CONTROLADORES (Controllers)

### 3.1 Auth.php - Autenticación
**Métodos:**
- `login()`: Procesa inicio de sesión
- `register()`: Registra nuevo usuario
- `logout()`: Cierra sesión

**Validaciones con AJAX:**
```javascript
// Login
$.ajax({
    url: base_url + 'auth/login',
    type: 'POST',
    data: formData,
    success: function(response) {
        if (response.success) {
            window.location.href = response.redirect;
        }
    }
});
```

### 3.2 Api.php - Endpoints AJAX
**Métodos:**
- `obtener_fontaneros_disponibles()`: Lista fontaneros
- `crear_solicitud()`: Crea nueva solicitud
- `validar_email()`: Valida email en tiempo real
- `enviar_calificacion()`: Envía calificación de servicio

**Ejemplo de validación en tiempo real:**
```javascript
$('#email').on('input', function() {
    $.ajax({
        url: base_url + 'api/validar_email',
        type: 'POST',
        data: { email: $(this).val() },
        success: function(response) {
            if (response.available) {
                $('#success-email').show();
            }
        }
    });
});
```

## 4. VALIDACIONES CON AJAX

### 4.1 Validación de Formulario de Login
```javascript
$('#loginForm').on('submit', function(e) {
    e.preventDefault();
    
    // Limpiar errores previos
    $('.error-message').text('');
    
    // Deshabilitar botón
    $('#btnLogin').prop('disabled', true);
    
    // Preparar datos
    const formData = {
        email: $('#email').val(),
        password: $('#password').val(),
        tipo_usuario: $('#tipo_usuario').val()
    };
    
    // Enviar con AJAX
    $.ajax({
        url: $('#base_url').val() + 'auth/login',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                window.location.href = response.redirect;
            } else {
                // Mostrar errores
                if (response.errors) {
                    $.each(response.errors, function(field, message) {
                        $('#error-' + field).text(message);
                    });
                }
            }
        }
    });
});
```

### 4.2 Validación de Registro
**Características:**
- Validación en tiempo real del email
- Medidor de fortaleza de contraseña
- Confirmación de contraseña
- Validación de todos los campos

```javascript
// Validación de email en tiempo real
let emailTimeout;
$('#email').on('input', function() {
    clearTimeout(emailTimeout);
    emailTimeout = setTimeout(function() {
        $.ajax({
            url: base_url + 'api/validar_email',
            type: 'POST',
            data: { email: $('#email').val() },
            success: function(response) {
                if (response.available) {
                    $('#success-email').show();
                } else {
                    $('#error-email').text('Email ya registrado');
                }
            }
        });
    }, 500);
});

// Medidor de fortaleza de contraseña
$('#password').on('input', function() {
    const password = $(this).val();
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    // Mostrar indicador visual
});
```

### 4.3 Validación de Solicitudes
```javascript
function validarYEnviarSolicitud() {
    // Validación del formulario
    const validator = new FormValidator('#notificationForm');
    
    validator.addRule('serviceType', { required: true });
    validator.addRule('direccion_servicio', { 
        required: true, 
        minLength: 10, 
        maxLength: 200 
    });
    validator.addRule('message', { 
        required: true, 
        minLength: 20, 
        maxLength: 500 
    });
    
    if (validator.validate()) {
        // Enviar con AJAX
        $.ajax({
            url: base_url + 'api/solicitud/crear',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                }
            }
        });
    }
}
```

## 5. SEGURIDAD

### 5.1 Protección CSRF
```php
// En config.php
$config['csrf_protection'] = TRUE;
$config['csrf_token_name'] = 'csrf_token';
$config['csrf_cookie_name'] = 'csrf_cookie';
```

```javascript
// En AJAX
$.ajaxSetup({
    beforeSend: function(xhr, settings) {
        if (settings.type !== 'GET') {
            const csrf_name = $('#csrf_token').attr('name');
            const csrf_value = $('#csrf_token').val();
            settings.data[csrf_name] = csrf_value;
        }
    }
});
```

### 5.2 XSS Filtering
```php
// Automático en CodeIgniter
$config['global_xss_filtering'] = TRUE;

// Manual en inputs
$email = $this->input->post('email', TRUE);
```

### 5.3 Passwords
```php
// Hasheo con bcrypt
$datos['password'] = password_hash($password, PASSWORD_BCRYPT);

// Verificación
password_verify($password, $hash);
```

## 6. AJAX HANDLER CLASS

### 6.1 Clase FormValidator
```javascript
class FormValidator {
    constructor(formId) {
        this.form = $(formId);
        this.rules = {};
    }
    
    addRule(field, rules) {
        this.rules[field] = rules;
    }
    
    validate() {
        // Validación de campos
    }
}
```

### 6.2 Clase AjaxHandler
```javascript
class AjaxHandler {
    constructor() {
        this.baseUrl = $('#base_url').val();
    }
    
    get(url, successCallback) {
        $.ajax({
            url: this.baseUrl + url,
            type: 'GET',
            success: successCallback
        });
    }
    
    post(url, data, successCallback) {
        $.ajax({
            url: this.baseUrl + url,
            type: 'POST',
            data: data,
            success: successCallback
        });
    }
}
```

## 7. MASHUPS IMPLEMENTADOS

### 7.1 OpenWeatherMap API
**Función:** Obtener información climática en tiempo real
```javascript
const obtenerClima = async () => {
    const apiKey = 'bd5e378503939ddaee76f12ad7a97608';
    const url = `https://api.openweathermap.org/data/2.5/weather?q=Bogota&appid=${apiKey}`;
    
    const response = await fetch(url);
    const data = await response.json();
    // Actualizar UI
};
```

### 7.2 Google Maps API
**Función:** Geolocalización y mapas interactivos
```javascript
const initMap = () => {
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 4.7110, lng: -74.0721 },
        zoom: 13
    });
};
```

### 7.3 EmailJS API
**Función:** Envío de notificaciones por email
```javascript
emailjs.send(serviceID, templateID, templateParams)
    .then(response => {
        console.log('Email enviado!');
    });
```

## 8. BASE DE DATOS

### 8.1 Tablas Principales
- **clientes**: Información de clientes
- **fontaneros**: Información de fontaneros
- **servicios**: Catálogo de servicios
- **solicitudes**: Solicitudes de servicio
- **calificaciones**: Ratings de fontaneros

### 8.2 Relaciones
```sql
solicitudes.id_cliente -> clientes.id_cliente
solicitudes.id_fontanero -> fontaneros.id_fontanero
solicitudes.id_servicio -> servicios.id_servicio
```

### 8.3 Triggers
```sql
-- Actualizar rating después de calificación
CREATE TRIGGER after_calificacion_insert
AFTER INSERT ON calificaciones
FOR EACH ROW
BEGIN
    CALL actualizar_rating_fontanero(NEW.id_fontanero);
END;
```

## 9. FLUJO DE DATOS

### 9.1 Creación de Solicitud
1. Usuario completa formulario
2. JavaScript valida campos en tiempo real
3. AJAX envía datos a `Api::crear_solicitud()`
4. Controlador valida con `form_validation`
5. Modelo inserta en base de datos
6. Respuesta JSON al cliente
7. Actualización de UI

### 9.2 Login
1. Usuario ingresa credenciales
2. AJAX envía a `Auth::login()`
3. Modelo verifica con `password_verify()`
4. Sesión se crea si es válido
5. Redirección según tipo de usuario

## 10. BUENAS PRÁCTICAS

### 10.1 Código
- Nombres descriptivos de variables
- Comentarios en funciones complejas
- Separación de responsabilidades
- Reutilización de código

### 10.2 Seguridad
- Nunca confiar en datos del cliente
- Validar en servidor y cliente
- Escapar salidas HTML
- Usar prepared statements

### 10.3 Performance
- Índices en tablas
- Caché de consultas frecuentes
- Minimizar consultas N+1
- Compresión de assets

## 11. TESTING

### 11.1 Datos de Prueba
Los datos se insertan automáticamente con `database.sql`:
- 3 clientes de prueba
- 4 fontaneros de prueba
- 8 servicios disponibles

### 11.2 Credenciales
Ver README.md para credenciales de prueba.
