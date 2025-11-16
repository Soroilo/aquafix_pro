<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Constantes de la aplicación
define('SITE_NAME', 'AquaFix Pro');
define('SITE_VERSION', '1.0.0');
define('SITE_EMAIL', 'info@aquafixpro.com');
define('SITE_PHONE', '+57 (1) 555-0100');

// Estados de solicitud
define('ESTADO_PENDIENTE', 'pendiente');
define('ESTADO_ASIGNADA', 'asignada');
define('ESTADO_EN_PROCESO', 'en_proceso');
define('ESTADO_COMPLETADA', 'completada');
define('ESTADO_CANCELADA', 'cancelada');

// Prioridades
define('PRIORIDAD_BAJA', 'baja');
define('PRIORIDAD_MEDIA', 'media');
define('PRIORIDAD_ALTA', 'alta');
define('PRIORIDAD_URGENTE', 'urgente');

// Tipos de servicio
define('TIPO_REPARACION', 'reparacion');
define('TIPO_INSTALACION', 'instalacion');
define('TIPO_MANTENIMIENTO', 'mantenimiento');
define('TIPO_EMERGENCIA', 'emergencia');

// Roles de usuario
define('ROL_CLIENTE', 'cliente');
define('ROL_FONTANERO', 'fontanero');
define('ROL_ADMIN', 'admin');

// Configuración de APIs
define('OPENWEATHER_API_KEY', 'bd5e378503939ddaee76f12ad7a97608');
define('GOOGLE_MAPS_API_KEY', 'YOUR_GOOGLE_MAPS_API_KEY');
define('EMAILJS_SERVICE_ID', 'YOUR_EMAILJS_SERVICE_ID');
define('EMAILJS_TEMPLATE_ID', 'YOUR_EMAILJS_TEMPLATE_ID');
define('EMAILJS_PUBLIC_KEY', 'YOUR_EMAILJS_PUBLIC_KEY');

// Límites y paginación
define('ITEMS_POR_PAGINA', 10);
define('MAX_FILE_SIZE', 5242880); // 5MB
define('SESSION_TIMEOUT', 7200); // 2 horas
