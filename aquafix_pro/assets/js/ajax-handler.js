/**
 * AQUAFIX PRO - AJAX HANDLER
 * Manejador de peticiones AJAX con validaciones
 */

// Configuración global de AJAX
$.ajaxSetup({
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    },
    beforeSend: function(xhr, settings) {
        // Agregar token CSRF a todas las peticiones
        if (settings.type !== 'GET') {
            const csrf_name = $('#csrf_token').attr('name');
            const csrf_value = $('#csrf_token').val();
            if (settings.data && typeof settings.data === 'object') {
                settings.data[csrf_name] = csrf_value;
            }
        }
    },
    complete: function(xhr) {
        // Actualizar token CSRF
        const newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
        if (newToken) {
            $('#csrf_token').val(newToken);
        }
    }
});

/**
 * Clase para manejar validaciones de formularios
 */
class FormValidator {
    constructor(formId) {
        this.form = $(formId);
        this.rules = {};
    }

    addRule(field, rules) {
        this.rules[field] = rules;
    }

    validate() {
        let isValid = true;
        $('.error-message').text('');

        Object.keys(this.rules).forEach(field => {
            const value = $(`#${field}`).val();
            const rules = this.rules[field];

            if (rules.required && !value) {
                this.showError(field, 'Este campo es requerido');
                isValid = false;
            }

            if (rules.minLength && value.length < rules.minLength) {
                this.showError(field, `Mínimo ${rules.minLength} caracteres`);
                isValid = false;
            }

            if (rules.maxLength && value.length > rules.maxLength) {
                this.showError(field, `Máximo ${rules.maxLength} caracteres`);
                isValid = false;
            }

            if (rules.email && !this.isValidEmail(value)) {
                this.showError(field, 'Email inválido');
                isValid = false;
            }

            if (rules.phone && !this.isValidPhone(value)) {
                this.showError(field, 'Teléfono inválido');
                isValid = false;
            }

            if (rules.match && value !== $(`#${rules.match}`).val()) {
                this.showError(field, 'Los campos no coinciden');
                isValid = false;
            }

            if (rules.custom && !rules.custom(value)) {
                this.showError(field, rules.customMessage || 'Valor inválido');
                isValid = false;
            }
        });

        return isValid;
    }

    showError(field, message) {
        $(`#error-${field}`).text(message);
        $(`#${field}`).addClass('input-error');
    }

    clearErrors() {
        $('.error-message').text('');
        $('input, select, textarea').removeClass('input-error');
    }

    isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    isValidPhone(phone) {
        const re = /^[\d\s\-\+\(\)]+$/;
        return re.test(phone);
    }
}

/**
 * Clase para manejar peticiones AJAX
 */
class AjaxHandler {
    constructor() {
        this.baseUrl = $('#base_url').val();
    }

    get(url, successCallback, errorCallback) {
        $.ajax({
            url: this.baseUrl + url,
            type: 'GET',
            dataType: 'json',
            success: successCallback,
            error: errorCallback || this.defaultErrorHandler
        });
    }

    post(url, data, successCallback, errorCallback) {
        $.ajax({
            url: this.baseUrl + url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: successCallback,
            error: errorCallback || this.defaultErrorHandler
        });
    }

    put(url, data, successCallback, errorCallback) {
        data._method = 'PUT';
        this.post(url, data, successCallback, errorCallback);
    }

    delete(url, successCallback, errorCallback) {
        $.ajax({
            url: this.baseUrl + url,
            type: 'DELETE',
            dataType: 'json',
            success: successCallback,
            error: errorCallback || this.defaultErrorHandler
        });
    }

    defaultErrorHandler(xhr, status, error) {
        console.error('Error AJAX:', error);
        showAlert('error', 'Error al procesar la solicitud. Por favor, intente nuevamente.');
    }
}

/**
 * Función para mostrar alertas
 */
function showAlert(type, message, duration = 5000) {
    const alertId = type === 'success' ? 'successAlert' : 'errorAlert';
    const $alert = $(`#${alertId}`);
    
    $('.alert').hide();
    $alert.text(message).show();
    
    setTimeout(() => {
        $alert.hide();
    }, duration);
}

/**
 * Función para cargar fontaneros disponibles
 */
function cargarFontanerosDisponibles() {
    const ajax = new AjaxHandler();
    
    ajax.get('api/fontaneros/disponibles', function(response) {
        if (response.success && response.data) {
            renderizarFontaneros(response.data);
        }
    });
}

/**
 * Función para renderizar fontaneros en el DOM
 */
function renderizarFontaneros(fontaneros) {
    const grid = $('#plumbersGrid');
    grid.empty();
    
    fontaneros.forEach(fontanero => {
        const card = `
            <div class="plumber-card">
                <div class="plumber-header">
                    <div class="plumber-avatar">👷</div>
                    <div class="plumber-info">
                        <h3>${fontanero.nombre}</h3>
                        <div class="plumber-specialty">${fontanero.especialidad}</div>
                    </div>
                </div>
                <div class="plumber-stats">
                    <div class="stat">
                        <span class="stat-value">⭐ ${fontanero.rating}</span>
                        <span class="stat-label">Rating</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value">${fontanero.servicios}</span>
                        <span class="stat-label">Servicios</span>
                    </div>
                </div>
                <span class="status-badge status-available">Disponible</span>
                <button class="btn btn-primary btn-full" onclick="seleccionarFontanero(${fontanero.id})">
                    Solicitar Servicio
                </button>
            </div>
        `;
        grid.append(card);
    });
}

/**
 * Función para crear una solicitud de servicio
 */
function crearSolicitud(datos) {
    const ajax = new AjaxHandler();
    
    ajax.post('api/solicitud/crear', datos, function(response) {
        if (response.success) {
            showAlert('success', response.message);
            // Redirigir o actualizar UI
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showAlert('error', response.message);
            
            if (response.errors) {
                $.each(response.errors, function(field, message) {
                    $(`#error-${field}`).text(message);
                });
            }
        }
    });
}

/**
 * Validar formulario de solicitud con AJAX
 */
function validarYEnviarSolicitud(formId) {
    const validator = new FormValidator(formId);
    
    validator.addRule('serviceType', { required: true });
    validator.addRule('direccion_servicio', { required: true, minLength: 10, maxLength: 200 });
    validator.addRule('message', { required: true, minLength: 20, maxLength: 500 });
    validator.addRule('prioridad', { required: true });
    
    if (validator.validate()) {
        const formData = {
            id_servicio: $('#serviceType').val(),
            direccion_servicio: $('#direccion_servicio').val(),
            descripcion_problema: $('#message').val(),
            prioridad: $('#prioridad').val()
        };
        
        crearSolicitud(formData);
    }
}

/**
 * Obtener ubicación del usuario
 */
function obtenerUbicacionUsuario(callback) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                callback({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                });
            },
            error => {
                console.error('Error al obtener ubicación:', error);
                callback({
                    lat: 4.7110,
                    lng: -74.0721
                });
            }
        );
    } else {
        callback({
            lat: 4.7110,
            lng: -74.0721
        });
    }
}

/**
 * Inicialización cuando el DOM está listo
 */
$(document).ready(function() {
    // Manejo del formulario de notificaciones
    $('#notificationForm').on('submit', function(e) {
        e.preventDefault();
        validarYEnviarSolicitud('#notificationForm');
    });

    // Cargar fontaneros si existe el grid
    if ($('#plumbersGrid').length) {
        cargarFontanerosDisponibles();
    }

    // Manejo del menú móvil
    $('.menu-toggle').on('click', function() {
        $('.nav-menu').toggleClass('active');
    });

    // Cerrar menú al hacer clic en un enlace
    $('.nav-menu a').on('click', function() {
        $('.nav-menu').removeClass('active');
    });

    // Validación en tiempo real para inputs
    $('input, textarea, select').on('blur', function() {
        const field = $(this).attr('id');
        if (field && $(this).val() === '') {
            $(`#error-${field}`).text('Este campo es requerido');
        } else {
            $(`#error-${field}`).text('');
        }
    });
});

// Exportar clases para uso global
window.FormValidator = FormValidator;
window.AjaxHandler = AjaxHandler;
window.showAlert = showAlert;
window.cargarFontanerosDisponibles = cargarFontanerosDisponibles;
