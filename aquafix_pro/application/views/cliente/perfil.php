<main class="container">
    <section class="section-spacing">
        <h1 class="section-title">👤 Mi Perfil</h1>
        
        <div class="alert alert-success" id="successAlert" style="display: none;"></div>
        <div class="alert alert-error" id="errorAlert" style="display: none;"></div>
        
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
            <form id="perfilForm">
                <div class="form-group">
                    <label for="nombre">Nombre: *</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo $cliente->nombre; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="apellido">Apellido: *</label>
                    <input type="text" id="apellido" name="apellido" value="<?php echo $cliente->apellido; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $cliente->email; ?>" readonly style="background: #f0f0f0;">
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono: *</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo $cliente->telefono; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="direccion">Dirección: *</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo $cliente->direccion; ?>" required>
                </div>
                
                <hr style="margin: 2rem 0;">
                
                <h3 style="margin-bottom: 1rem;">Cambiar Contraseña (opcional)</h3>
                
                <div class="form-group">
                    <label for="password">Nueva Contraseña:</label>
                    <input type="password" id="password" name="password" placeholder="Dejar en blanco para no cambiar">
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Confirmar Contraseña:</label>
                    <input type="password" id="password_confirm" name="password_confirm">
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    💾 Guardar Cambios
                </button>
            </form>
        </div>
    </section>
</main>

<script>
$(document).ready(function() {
    $('#perfilForm').on('submit', function(e) {
        e.preventDefault();

        const password = $('#password').val();
        const password_confirm = $('#password_confirm').val();

        if (password && password !== password_confirm) {
            $('#errorAlert').text('Las contraseñas no coinciden').show();
            $('#successAlert').hide();
            return;
        }

        const formData = {
            nombre: $('#nombre').val(),
            apellido: $('#apellido').val(),
            telefono: $('#telefono').val(),
            direccion: $('#direccion').val(),
            password: password,
            <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
        };

        $.ajax({
            url: '<?php echo base_url('cliente/perfil'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#successAlert').text(response.message).show();
                    $('#errorAlert').hide();
                    $('#password').val('');
                    $('#password_confirm').val('');
                } else {
                    $('#errorAlert').text(response.message || 'Error al actualizar el perfil').show();
                    $('#successAlert').hide();
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                $('#errorAlert').text('Error al procesar la solicitud').show();
                $('#successAlert').hide();
            }
        });
    });
});
</script>