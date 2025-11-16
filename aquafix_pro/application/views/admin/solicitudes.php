<main class="container">
    <section class="section-spacing">
        <h1 class="section-title">📋 Gestión de Solicitudes</h1>
        
        <div class="alert alert-success" id="successAlert" role="alert" style="display: none;"></div>
        <div class="alert alert-error" id="errorAlert" role="alert" style="display: none;"></div>
        
        <!-- Filtros -->
        <div style="background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;">
                <div class="form-group" style="margin: 0;">
                    <label>Estado:</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="pendiente" <?php echo ($this->input->get('estado') == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="asignada" <?php echo ($this->input->get('estado') == 'asignada') ? 'selected' : ''; ?>>Asignada</option>
                        <option value="en_proceso" <?php echo ($this->input->get('estado') == 'en_proceso') ? 'selected' : ''; ?>>En Proceso</option>
                        <option value="completada" <?php echo ($this->input->get('estado') == 'completada') ? 'selected' : ''; ?>>Completada</option>
                        <option value="cancelada" <?php echo ($this->input->get('estado') == 'cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
            </form>
        </div>

        <!-- Tabla de solicitudes -->
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 1rem; text-align: left;">ID</th>
                        <th style="padding: 1rem; text-align: left;">Cliente</th>
                        <th style="padding: 1rem; text-align: left;">Servicio</th>
                        <th style="padding: 1rem; text-align: left;">Fontanero</th>
                        <th style="padding: 1rem; text-align: left;">Estado</th>
                        <th style="padding: 1rem; text-align: left;">Prioridad</th>
                        <th style="padding: 1rem; text-align: left;">Fecha</th>
                        <th style="padding: 1rem; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($solicitudes)): ?>
                        <tr>
                            <td colspan="8" style="padding: 2rem; text-align: center; color: #999;">
                                No hay solicitudes para mostrar
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($solicitudes as $sol): ?>
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 1rem;">#<?php echo $sol->id_solicitud; ?></td>
                                <td style="padding: 1rem;"><?php echo $sol->nombre_cliente; ?></td>
                                <td style="padding: 1rem;"><?php echo $sol->nombre_servicio; ?></td>
                                <td style="padding: 1rem;"><?php echo $sol->nombre_fontanero ?: '❌ Sin asignar'; ?></td>
                                <td style="padding: 1rem;">
                                    <?php
                                    $badges = [
                                        'pendiente' => '<span style="background: #ffc107; color: #000; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem;">⏳ Pendiente</span>',
                                        'asignada' => '<span style="background: #17a2b8; color: #fff; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem;">📌 Asignada</span>',
                                        'en_proceso' => '<span style="background: #007bff; color: #fff; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem;">🔄 En Proceso</span>',
                                        'completada' => '<span style="background: #28a745; color: #fff; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem;">✅ Completada</span>',
                                        'cancelada' => '<span style="background: #dc3545; color: #fff; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem;">❌ Cancelada</span>'
                                    ];
                                    echo $badges[$sol->estado];
                                    ?>
                                </td>
                                <td style="padding: 1rem;"><?php echo ucfirst($sol->prioridad); ?></td>
                                <td style="padding: 1rem;"><?php echo date('d/m/Y', strtotime($sol->fecha_solicitud)); ?></td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                                        <?php if ($sol->estado == 'pendiente'): ?>
                                            <button onclick="abrirModalAsignar(<?php echo $sol->id_solicitud; ?>)" class="btn btn-primary btn-sm">
                                                👷 Asignar
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($sol->estado == 'asignada'): ?>
                                            <button onclick="cambiarEstado(<?php echo $sol->id_solicitud; ?>, 'en_proceso')" class="btn btn-primary btn-sm">
                                                🔄 Iniciar
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($sol->estado == 'en_proceso'): ?>
                                            <button onclick="cambiarEstado(<?php echo $sol->id_solicitud; ?>, 'completada')" class="btn btn-success btn-sm">
                                                ✅ Completar
                                            </button>
                                        <?php endif; ?>

                                        <?php if (in_array($sol->estado, ['pendiente', 'asignada', 'en_proceso'])): ?>
                                            <button onclick="cambiarEstado(<?php echo $sol->id_solicitud; ?>, 'cancelada')" class="btn btn-secondary btn-sm" style="background: #dc3545;">
                                                ❌ Cancelar
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<!-- Modal para asignar fontanero -->
<div id="modalAsignar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 2rem; border-radius: 15px; max-width: 500px; width: 90%;">
        <h3>👷 Asignar Fontanero</h3>
        <form id="formAsignar">
            <input type="hidden" id="id_solicitud_asignar">
            
            <div class="form-group">
                <label for="id_fontanero">Fontanero: *</label>
                <select id="id_fontanero" name="id_fontanero" required>
                    <option value="">-- Seleccionar --</option>
                    <?php
                    $this->load->model('Fontanero_model');
                    $fontaneros = $this->Fontanero_model->listar_disponibles();
                    foreach ($fontaneros as $f):
                    ?>
                        <option value="<?php echo $f->id_fontanero; ?>">
                            <?php echo $f->nombre . ' ' . $f->apellido; ?> - <?php echo $f->especialidad; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="fecha_programada">Fecha Programada: *</label>
                <input type="datetime-local" id="fecha_programada" name="fecha_programada" required>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">✅ Asignar</button>
                <button type="button" onclick="cerrarModalAsignar()" class="btn btn-secondary" style="flex: 1;">❌ Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalAsignar(idSolicitud) {
    document.getElementById('id_solicitud_asignar').value = idSolicitud;
    document.getElementById('modalAsignar').style.display = 'flex';
}

function cerrarModalAsignar() {
    document.getElementById('modalAsignar').style.display = 'none';
    document.getElementById('formAsignar').reset();
}

function cambiarEstado(idSolicitud, nuevoEstado) {
    const mensajes = {
        'en_proceso': '¿Iniciar el servicio?',
        'completada': '¿Marcar como completada?',
        'cancelada': '¿Cancelar esta solicitud?'
    };

    if (!confirm(mensajes[nuevoEstado] || '¿Cambiar estado de la solicitud?')) {
        return;
    }

    $.ajax({
        url: '<?php echo base_url('admin/cambiar_estado_solicitud'); ?>',
        type: 'POST',
        data: {
            id_solicitud: idSolicitud,
            estado: nuevoEstado,
            <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#successAlert').text(response.message).show();
                $('#errorAlert').hide();
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                $('#errorAlert').text(response.message).show();
                $('#successAlert').hide();
            }
        },
        error: function() {
            $('#errorAlert').text('Error al cambiar el estado').show();
            $('#successAlert').hide();
        }
    });
}

$(document).ready(function() {
    $('#formAsignar').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            id_solicitud: $('#id_solicitud_asignar').val(),
            id_fontanero: $('#id_fontanero').val(),
            fecha_programada: $('#fecha_programada').val(),
            <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
        };

        $.ajax({
            url: '<?php echo base_url('admin/asignar_fontanero'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#successAlert').text(response.message).show();
                    $('#errorAlert').hide();
                    cerrarModalAsignar();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $('#errorAlert').text(response.message).show();
                    $('#successAlert').hide();
                }
            },
            error: function() {
                $('#errorAlert').text('Error al asignar fontanero').show();
            }
        });
    });
});
</script>