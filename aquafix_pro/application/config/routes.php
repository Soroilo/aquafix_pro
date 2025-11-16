<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Rutas públicas
$route['inicio'] = 'home/index';
$route['login'] = 'auth/login';
$route['register'] = 'auth/register';
$route['logout'] = 'auth/logout';

// Rutas de clientes
$route['cliente/dashboard'] = 'cliente/dashboard';
$route['cliente/solicitar'] = 'cliente/solicitar_servicio';
$route['cliente/mis-solicitudes'] = 'cliente/mis_solicitudes';
$route['cliente/perfil'] = 'cliente/perfil';
$route['cliente/crear_solicitud'] = 'cliente/crear_solicitud';

// Rutas de fontaneros
$route['fontanero/dashboard'] = 'fontanero/dashboard';
$route['fontanero/solicitudes'] = 'fontanero/solicitudes';
$route['fontanero/completar/(:num)'] = 'fontanero/completar/$1';
$route['fontanero/perfil'] = 'fontanero/perfil';

// Rutas de administrador
$route['admin/dashboard'] = 'admin/dashboard';
$route['admin/clientes'] = 'admin/clientes';
$route['admin/fontaneros'] = 'admin/fontaneros';
$route['admin/servicios'] = 'admin/servicios';
$route['admin/solicitudes'] = 'admin/solicitudes';
$route['admin/reportes'] = 'admin/reportes';

// API AJAX Routes
$route['api/fontaneros/disponibles'] = 'api/obtener_fontaneros_disponibles';
$route['api/fontaneros/cercanos'] = 'api/obtener_fontaneros_cercanos';
$route['api/servicios/listar'] = 'api/listar_servicios';
$route['api/solicitud/crear'] = 'api/crear_solicitud';
$route['api/solicitud/actualizar'] = 'api/actualizar_solicitud';
$route['api/calificacion/enviar'] = 'api/enviar_calificacion';
