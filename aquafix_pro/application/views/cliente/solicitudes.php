<main class="container">
    <section class="section-spacing">
        <h1 class="section-title">📋 Mis Solicitudes</h1>
        
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="margin-bottom: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="?estado=" class="btn btn-secondary">Todas</a>
                <a href="?estado=pendiente" class="btn btn-secondary">Pendientes</a>
                <a href="?estado=asignada" class="btn btn-secondary">Asignadas</a>
                <a href="?estado=en_proceso" class="btn btn-secondary">En Proceso</a>
                <a href="?estado=completada" class="btn btn-success">Completadas</a>
            </div>
            
            <?php if (empty($solicitudes)): ?>
                <p style="text-align: center; padding: 3rem; color: #666;">
                    No tienes solicitudes. <a href="<?php echo base_url('cliente/solicitar'); ?>" style="color: var(--primary-color); font-weight: 600;">Solicita un servicio ahora</a>
                </p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #eee;">
                                <th style="padding: 1rem; text-align: left;">ID</th>
                                <th style="padding: 1rem; text-align: left;">Servicio</th>
                                <th style="padding: 1rem; text-align: left;">Fontanero</th>
                                <th style="padding: 1rem; text-align: left;">Fecha</th>
                                <th style="padding: 1rem; text-align: left;">Estado</th>
                                <th style="padding: 1rem; text-align: left;">Precio</th>
                                <th style="padding: 1rem; text-align: left;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes as $solicitud): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 1rem;">#<?php echo $solicitud->id_solicitud; ?></td>
                                    <td style="padding: 1rem;"><?php echo $solicitud->nombre_servicio; ?></td>
                                    <td style="padding: 1rem;"><?php echo $solicitud->nombre_fontanero ?? 'Sin asignar'; ?></td>
                                    <td style="padding: 1rem;"><?php echo date('d/m/Y', strtotime($solicitud->fecha_solicitud)); ?></td>
                                    <td style="padding: 1rem;">
                                        <span style="padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem; font-weight: 600;
                                            background-color: <?php
                                                switch($solicitud->estado) {
                                                    case 'pendiente': echo '#fff3cd'; break;
                                                    case 'asignada': echo '#d1ecf1'; break;
                                                    case 'en_proceso': echo '#cfe2ff'; break;
                                                    case 'completada': echo '#d4edda'; break;
                                                    default: echo '#f8d7da';
                                                }
                                            ?>;">
                                            <?php echo ucfirst($solicitud->estado); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem;">$<?php echo number_format($solicitud->precio_base, 2); ?></td>
                                    <td style="padding: 1rem;">
                                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                            <a href="<?php echo base_url('cliente/detalle_solicitud/' . $solicitud->id_solicitud); ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                                👁️ Ver Detalles
                                            </a>
                                            <?php if ($solicitud->estado == 'completada'): ?>
                                                <button class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;" onclick="calificar(<?php echo $solicitud->id_solicitud; ?>, <?php echo $solicitud->id_fontanero ?? 'null'; ?>)">
                                                    ⭐ Calificar
                                                </button>
                                            <?php elseif ($solicitud->estado == 'pendiente'): ?>
                                                <button class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem; background: #dc3545;" onclick="cancelar(<?php echo $solicitud->id_solicitud; ?>)">
                                                    ❌ Cancelar
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
function cancelar(id) {
    if (confirm('¿Estás seguro de cancelar esta solicitud?')) {
        $.ajax({
            url: '<?php echo base_url('cliente/cancelar_solicitud/'); ?>' + id,
            type: 'POST',
            data: {
                <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('✅ Solicitud cancelada');
                    location.reload();
                } else {
                    alert('❌ Error: ' + response.message);
                }
            },
            error: function() {
                alert('❌ Error al procesar la solicitud');
            }
        });
    }
}

function calificar(id, idFontanero) {
    if (!idFontanero) {
        alert('No se puede calificar: el servicio no tiene un fontanero asignado.');
        return;
    }

    const rating = prompt('Califica el servicio del 1 al 5:');
    if (rating && rating >= 1 && rating <= 5) {
        const comentario = prompt('Deja un comentario (opcional):');

        $.ajax({
            url: '<?php echo base_url('cliente/calificar'); ?>',
            type: 'POST',
            data: {
                id_solicitud: id,
                id_fontanero: idFontanero,
                puntuacion: rating,
                comentario: comentario || '',
                <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('✅ ¡Gracias por tu calificación!');
                    location.reload();
                } else {
                    alert('❌ Error: ' + (response.message || 'No se pudo enviar la calificación'));
                }
            },
            error: function(xhr, status, error) {
                alert('❌ Error al enviar la calificación. Por favor intenta de nuevo.');
                console.error('Error:', error);
            }
        });
    } else if (rating !== null) {
        alert('Por favor ingresa una calificación válida entre 1 y 5');
    }
}
</script>