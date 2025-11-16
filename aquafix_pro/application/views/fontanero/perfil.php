<main class="container">
    <section class="section-spacing">
        <h1 class="section-title">👤 Mi Perfil de Fontanero</h1>
        
        <div class="alert alert-success" id="successAlert" style="display: none;"></div>
        <div class="alert alert-error" id="errorAlert" style="display: none;"></div>
        
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
            
            <div style="text-align: center; margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; color: white;">
                <div style="font-size: 4rem;">👷</div>
                <h2><?php echo $fontanero->nombre . ' ' . $fontanero->apellido; ?></h2>
                <div style="font-size: 2rem; margin: 1rem 0;">⭐ <?php echo number_format($fontanero->rating_promedio, 1); ?></div>
                <div style="font-size: 1.2rem;">✅ <?php echo $fontanero->servicios_completados; ?> servicios completados</div>
            </div>
            
            <form id="perfilForm">
                <div class="form-group">
                    <label for="nombre">Nombre: *</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo $fontanero->nombre; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="apellido">Apellido: *</label>
                    <input type="text" id="apellido" name="apellido" value="<?php echo $fontanero->apellido; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $fontanero->email; ?>" readonly style="background: #f0f0f0;">
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono: *</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo $fontanero->telefono; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="especialidad">Especialidad: *</label>
                    <input type="text" id="especialidad" name="especialidad" value="<?php echo $fontanero->especialidad; ?>" required>
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
            
            <hr style="margin: 2rem 0;">
            
            <h3 style="margin-bottom: 1rem;">Mis Calificaciones</h3>
            
            <?php if (empty($calificaciones)): ?>
                <p style="text-align: center; color: #666; padding: 1rem;">Aún no tienes calificaciones.</p>
            <?php else: ?>
                <?php foreach ($calificaciones as $cal): ?>
                    <div style="border: 1px solid #eee; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <strong><?php echo $cal->nombre_cliente; ?></strong>
                            <div style="color: #f39c12;">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <?php echo $i <= $cal->puntuacion ? '⭐' : '☆'; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.5rem;">
                            <?php echo $cal->nombre_servicio; ?> - <?php echo date('d/m/Y', strtotime($cal->fecha_solicitud)); ?>
                        </div>
                        <?php if ($cal->comentario): ?>
                            <p style="color: #333; font-size: 0.95rem; font-style: italic;">
                                "<?php echo $cal->comentario; ?>"
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
            especialidad: $('#especialidad').val(),
            password: password,
            <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
        };

        $.ajax({
            url: '<?php echo base_url('fontanero/perfil'); ?>',
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