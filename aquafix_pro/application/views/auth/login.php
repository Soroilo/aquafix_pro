<main class="container">
    <section class="notification-section section-spacing" style="max-width: 500px; margin: 4rem auto;">
        <h2 class="section-title">🔐 Iniciar Sesión</h2>
        
        <div class="alert alert-success" id="successAlert" role="alert" style="display: none;"></div>
        <div class="alert alert-error" id="errorAlert" role="alert" style="display: none;"></div>
        
        <form id="loginForm" class="notification-form">
            <div class="form-group">
                <label for="email">Email: *</label>
                <input type="email" id="email" name="email" placeholder="tu@email.com" required>
                <span class="error-message" id="error-email"></span>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña: *</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <span class="error-message" id="error-password"></span>
            </div>
            
            <div class="form-group">
                <label for="tipo_usuario">Tipo de Usuario: *</label>
                <select id="tipo_usuario" name="tipo_usuario" required>
                    <option value="">-- Selecciona --</option>
                    <option value="cliente">Cliente</option>
                    <option value="fontanero">Fontanero</option>
                    <option value="admin">Administrador</option>
                </select>
                <span class="error-message" id="error-tipo_usuario"></span>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full" id="btnLogin">
                🔓 Iniciar Sesión
            </button>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <p>¿No tienes cuenta? <a href="<?php echo base_url('register'); ?>" style="color: var(--primary-color); font-weight: 600;">Regístrate aquí</a></p>
            </div>
        </form>
    </section>
</main>

<script>
$(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        $('.error-message').text('');
        $('#btnLogin').prop('disabled', true).text('Iniciando sesión...');
        
        const formData = {
            email: $('#email').val(),
            password: $('#password').val(),
            tipo_usuario: $('#tipo_usuario').val(),
            <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
        };

        $.ajax({
            url: '<?php echo base_url('auth/login'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#successAlert').text(response.message).show();
                    $('#errorAlert').hide();
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1000);
                } else {
                    $('#errorAlert').text(response.message).show();
                    $('#successAlert').hide();
                    
                    if (response.errors) {
                        $.each(response.errors, function(field, message) {
                            $('#error-' + field).text(message);
                        });
                    }
                    
                    $('#btnLogin').prop('disabled', false).text('🔓 Iniciar Sesión');
                }
            },
            error: function() {
                $('#errorAlert').text('Error al procesar la solicitud').show();
                $('#btnLogin').prop('disabled', false).text('🔓 Iniciar Sesión');
            }
        });
    });
});
</script>
