<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AquaFix Pro - Plataforma profesional de servicios de fontanería con geolocalización en tiempo real">
    <title><?php echo isset($title) ? $title : 'AquaFix Pro'; ?></title>
    
    <link rel="stylesheet" href="<?php echo base_url('assets/css/styles.css'); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Función global para obtener el token CSRF de la cookie
        function getCsrfToken() {
            var name = '<?php echo $this->config->item('csrf_cookie_name'); ?>=';
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return '';
        }
    </script>
</head>
<body>
    <header>
        <nav>
            <div class="logo">AquaFix Pro</div>
            <button class="menu-toggle" aria-label="Menú de navegación">☰</button>
            <ul class="nav-menu">
                <li><a href="<?php echo base_url(); ?>">Inicio</a></li>
                <?php if($this->session->userdata('logged_in')): ?>
                    <?php if($this->session->userdata('user_type') == 'cliente'): ?>
                        <li><a href="<?php echo base_url('cliente/dashboard'); ?>">Dashboard</a></li>
                        <li><a href="<?php echo base_url('cliente/solicitar'); ?>">Solicitar Servicio</a></li>
                        <li><a href="<?php echo base_url('cliente/mis-solicitudes'); ?>">Mis Solicitudes</a></li>
                    <?php elseif($this->session->userdata('user_type') == 'fontanero'): ?>
                        <li><a href="<?php echo base_url('fontanero/dashboard'); ?>">Dashboard</a></li>
                        <li><a href="<?php echo base_url('fontanero/solicitudes'); ?>">Solicitudes</a></li>
                        <li><a href="<?php echo base_url('fontanero/perfil'); ?>">Perfil</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo base_url('logout'); ?>">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="<?php echo base_url('login'); ?>">Iniciar Sesión</a></li>
                    <li><a href="<?php echo base_url('register'); ?>">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
    <input type="hidden" id="csrf_token" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
