<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper personalizado de AquaFix Pro
 */

if (!function_exists('verificar_sesion_activa')) {
    function verificar_sesion_activa() {
        $CI =& get_instance();
        return $CI->session->userdata('logged_in') === TRUE;
    }
}

if (!function_exists('obtener_tipo_usuario')) {
    function obtener_tipo_usuario() {
        $CI =& get_instance();
        return $CI->session->userdata('user_type');
    }
}

if (!function_exists('es_cliente')) {
    function es_cliente() {
        return obtener_tipo_usuario() === ROL_CLIENTE;
    }
}

if (!function_exists('es_fontanero')) {
    function es_fontanero() {
        return obtener_tipo_usuario() === ROL_FONTANERO;
    }
}

if (!function_exists('es_admin')) {
    function es_admin() {
        return obtener_tipo_usuario() === ROL_ADMIN;
    }
}

if (!function_exists('formatear_fecha')) {
    function formatear_fecha($fecha, $formato = 'd/m/Y H:i') {
        if (!$fecha) return '-';
        return date($formato, strtotime($fecha));
    }
}

if (!function_exists('formatear_precio')) {
    function formatear_precio($precio) {
        return '$' . number_format($precio, 2, ',', '.');
    }
}

if (!function_exists('generar_codigo_unico')) {
    function generar_codigo_unico($prefijo = '') {
        return $prefijo . strtoupper(uniqid());
    }
}

if (!function_exists('calcular_distancia')) {
    function calcular_distancia($lat1, $lon1, $lat2, $lon2) {
        $radio_tierra = 6371;
        $dlat = deg2rad($lat2 - $lat1);
        $dlon = deg2rad($lon2 - $lon1);
        
        $a = sin($dlat/2) * sin($dlat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dlon/2) * sin($dlon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distancia = $radio_tierra * $c;
        
        return round($distancia, 2);
    }
}

if (!function_exists('obtener_badge_estado')) {
    function obtener_badge_estado($estado) {
        $badges = array(
            'pendiente' => '<span class="badge badge-warning">Pendiente</span>',
            'asignada' => '<span class="badge badge-info">Asignada</span>',
            'en_proceso' => '<span class="badge badge-primary">En Proceso</span>',
            'completada' => '<span class="badge badge-success">Completada</span>',
            'cancelada' => '<span class="badge badge-danger">Cancelada</span>'
        );
        
        return isset($badges[$estado]) ? $badges[$estado] : $estado;
    }
}

if (!function_exists('obtener_icono_servicio')) {
    function obtener_icono_servicio($tipo) {
        $iconos = array(
            'reparacion' => '🔧',
            'instalacion' => '🚰',
            'mantenimiento' => '🛠️',
            'emergencia' => '🚨'
        );
        
        return isset($iconos[$tipo]) ? $iconos[$tipo] : '🔨';
    }
}

if (!function_exists('validar_telefono')) {
    function validar_telefono($telefono) {
        return preg_match('/^[\d\s\-\+\(\)]+$/', $telefono);
    }
}

if (!function_exists('sanitizar_texto')) {
    function sanitizar_texto($texto) {
        $CI =& get_instance();
        return $CI->security->xss_clean(trim($texto));
    }
}

if (!function_exists('obtener_nombre_completo')) {
    function obtener_nombre_completo($nombre, $apellido) {
        return trim($nombre . ' ' . $apellido);
    }
}

if (!function_exists('tiempo_transcurrido')) {
    function tiempo_transcurrido($fecha) {
        $tiempo = time() - strtotime($fecha);
        
        if ($tiempo < 60) {
            return 'Hace ' . $tiempo . ' segundos';
        } elseif ($tiempo < 3600) {
            return 'Hace ' . floor($tiempo / 60) . ' minutos';
        } elseif ($tiempo < 86400) {
            return 'Hace ' . floor($tiempo / 3600) . ' horas';
        } elseif ($tiempo < 604800) {
            return 'Hace ' . floor($tiempo / 86400) . ' días';
        } else {
            return formatear_fecha($fecha);
        }
    }
}

if (!function_exists('generar_estrellas_rating')) {
    function generar_estrellas_rating($rating) {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $html .= '⭐';
            } else {
                $html .= '☆';
            }
        }
        return $html . ' (' . number_format($rating, 1) . ')';
    }
}

if (!function_exists('obtener_color_prioridad')) {
    function obtener_color_prioridad($prioridad) {
        $colores = array(
            'baja' => '#28a745',
            'media' => '#ffc107',
            'alta' => '#fd7e14',
            'urgente' => '#dc3545'
        );
        
        return isset($colores[$prioridad]) ? $colores[$prioridad] : '#6c757d';
    }
}

if (!function_exists('enviar_email_notificacion')) {
    function enviar_email_notificacion($para, $asunto, $mensaje) {
        $CI =& get_instance();
        $CI->load->library('email');
        
        $CI->email->from(SITE_EMAIL, SITE_NAME);
        $CI->email->to($para);
        $CI->email->subject($asunto);
        $CI->email->message($mensaje);
        
        return $CI->email->send();
    }
}

if (!function_exists('log_actividad')) {
    function log_actividad($accion, $detalles = '') {
        $CI =& get_instance();
        $datos = array(
            'usuario_id' => $CI->session->userdata('user_id'),
            'tipo_usuario' => $CI->session->userdata('user_type'),
            'accion' => $accion,
            'detalles' => $detalles,
            'ip' => $CI->input->ip_address(),
            'fecha' => date('Y-m-d H:i:s')
        );
        
        log_message('info', json_encode($datos));
    }
}
