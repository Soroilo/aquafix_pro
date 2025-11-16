<main class="container">
    <section class="section-spacing">
        <h1 class="section-title">📝 Solicitar Servicio</h1>
        
        <div class="alert alert-success" id="successAlert" role="alert" style="display: none;"></div>
        <div class="alert alert-error" id="errorAlert" role="alert" style="display: none;"></div>
        
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
            <form id="solicitudForm">
                <div class="form-group">
                    <label for="id_servicio">Tipo de Servicio: *</label>
                    <select id="id_servicio" name="id_servicio" required>
                        <option value="">-- Selecciona un servicio --</option>
                        <?php foreach($servicios as $servicio): ?>
                            <option value="<?php echo $servicio->id_servicio; ?>" data-precio="<?php echo $servicio->precio_base; ?>">
                                <?php echo $servicio->nombre; ?> - $<?php echo number_format($servicio->precio_base, 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="direccion_servicio">Dirección del Servicio: *</label>
                    <input type="text" id="direccion_servicio" name="direccion_servicio" placeholder="Calle 123 #45-67" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion_problema">Descripción del Problema: *</label>
                    <textarea id="descripcion_problema" name="descripcion_problema" rows="5" placeholder="Describe detalladamente el problema..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="prioridad">Prioridad: *</label>
                    <select id="prioridad" name="prioridad" required>
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">🚨 Urgente</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    📧 Enviar Solicitud
                </button>
            </form>
        </div>
    </section>
</main>

<script>
$(document).ready(function() {
    $('#solicitudForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            id_servicio: $('#id_servicio').val(),
            direccion_servicio: $('#direccion_servicio').val(),
            descripcion_problema: $('#descripcion_problema').val(),
            prioridad: $('#prioridad').val(),
            <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
        };

        $.ajax({
    url: '<?php echo base_url('cliente/crear_solicitud'); ?>',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
        if (response.success) {
            $('#successAlert').text(response.message).show();
            $('#errorAlert').hide();
            $('#solicitudForm')[0].reset();
            setTimeout(function() {
                window.location.href = '<?php echo base_url('cliente/mis_solicitudes'); ?>';
            }, 2000);
        } else {
            $('#errorAlert').text(response.message || 'Error al crear la solicitud').show();
            $('#successAlert').hide();
        }
    },
    error: function(xhr, status, error) {
        console.error('Error:', xhr.responseText);
        $('#errorAlert').text('Error al procesar la solicitud. Por favor intenta nuevamente.').show();
        $('#successAlert').hide();
    }
});
    });
});
</script>