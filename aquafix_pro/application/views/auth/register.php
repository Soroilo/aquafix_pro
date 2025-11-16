<main class="container">
    <section class="notification-section section-spacing" style="max-width: 600px; margin: 4rem auto;">
        <h2 class="section-title">📝 Crear Cuenta</h2>
        
        <div class="alert alert-success" id="successAlert" role="alert" style="display: none;"></div>
        <div class="alert alert-error" id="errorAlert" role="alert" style="display: none;"></div>
        
        <form id="registerForm" class="notification-form">
            <div class="form-group">
                <label for="tipo_usuario">Tipo de Usuario: *</label>
                <select id="tipo_usuario" name="tipo_usuario" required>
                    <option value="">-- Selecciona --</option>
                    <option value="cliente">Cliente</option>
                    <option value="fontanero">Fontanero</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="nombre">Nombre: *</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Juan" required>
                </div>
                
                <div class="form-group">
                    <label for="apellido">Apellido: *</label>
                    <input type="text" id="apellido" name="apellido" placeholder="Pérez" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email: *</label>
                <input type="email" id="email" name="email" placeholder="tu@email.com" required>
            </div>
            
            <div class="form-group">
                <label for="telefono">Teléfono: *</label>
                <input type="tel" id="telefono" name="telefono" placeholder="555-0123" required>
            </div>
            
            <div class="form-group" id="campo_direccion">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" placeholder="Calle 123 #45-67">
            </div>

            <div class="form-group" id="campo_especialidad" style="display: none;">
                <label for="especialidad">Especialidad:</label>
                <input type="text" id="especialidad" name="especialidad" placeholder="Reparaciones, Instalaciones, etc.">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña: *</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmar Contraseña: *</label>
                <input type="password" id="password_confirm" name="password_confirm" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full" id="btnRegister">
                ✅ Registrarse
            </button>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <p>¿Ya tienes cuenta? <a href="<?php echo base_url('login'); ?>" style="color: var(--primary-color); font-weight: 600;">Inicia sesión aquí</a></p>
            </div>
        </form>
    </section>
</main>

<script>
$(document).ready(function() {
    // Mostrar/ocultar campos según tipo de usuario
    $('#tipo_usuario').on('change', function() {
        const tipo = $(this).val();
        if (tipo === 'cliente') {
            $('#campo_direccion').show();
            $('#campo_especialidad').hide();
            $('#direccion').prop('required', true);
            $('#especialidad').prop('required', false);
        } else if (tipo === 'fontanero') {
            $('#campo_direccion').hide();
            $('#campo_especialidad').show();
            $('#direccion').prop('required', false);
            $('#especialidad').prop('required', true);
        }
    });

    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validar contraseñas
        const password = $('#password').val();
        const passwordConfirm = $('#password_confirm').val();
        
        if (password !== passwordConfirm) {
            $('#errorAlert').text('Las contraseñas no coinciden').show();
            $('#successAlert').hide();
            return;
        }

        if (password.length < 6) {
            $('#errorAlert').text('La contraseña debe tener al menos 6 caracteres').show();
            $('#successAlert').hide();
            return;
        }
        
        $('#btnRegister').prop('disabled', true).text('Registrando...');
        
        const formData = {
            nombre: $('#nombre').val(),
            apellido: $('#apellido').val(),
            email: $('#email').val(),
            telefono: $('#telefono').val(),
            direccion: $('#direccion').val(),
            especialidad: $('#especialidad').val(),
            password: password,
            tipo_usuario: $('#tipo_usuario').val(),
            <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
        };

        $.ajax({
            url: '<?php echo base_url('auth/register'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#successAlert').text(response.message).show();
                    $('#errorAlert').hide();
                    $('#registerForm')[0].reset();
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 2000);
                } else {
                    $('#errorAlert').text(response.message).show();
                    $('#successAlert').hide();
                    $('#btnRegister').prop('disabled', false).text('✅ Registrarse');
                }
            },
            error: function() {
                $('#errorAlert').text('Error al procesar el registro').show();
                $('#successAlert').hide();
                $('#btnRegister').prop('disabled', false).text('✅ Registrarse');
            }
        });
    });
});
</script>