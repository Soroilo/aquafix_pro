<main class="container">
    <section class="section-spacing">
        <div style="margin-bottom: 2rem;">
            <a href="<?php echo base_url('cliente/mis-solicitudes'); ?>" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                ← Volver a Mis Solicitudes
            </a>
        </div>

        <h1 class="section-title">📋 Detalle de Solicitud #<?php echo $solicitud->id_solicitud; ?></h1>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem;">
            <!-- Información del Servicio -->
            <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h2 style="color: var(--primary-color); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    🔧 Información del Servicio
                </h2>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <strong style="color: #666;">Servicio:</strong>
                        <p style="margin: 0.3rem 0 0 0; font-size: 1.1rem;"><?php echo $solicitud->nombre_servicio; ?></p>
                    </div>

                    <div>
                        <strong style="color: #666;">Estado:</strong>
                        <p style="margin: 0.3rem 0 0 0;">
                            <span style="padding: 0.5rem 1rem; border-radius: 15px; font-size: 0.9rem; font-weight: 600; display: inline-block;
                                background-color: <?php
                                    switch($solicitud->estado) {
                                        case 'pendiente': echo '#fff3cd'; break;
                                        case 'asignada': echo '#d1ecf1'; break;
                                        case 'en_proceso': echo '#cfe2ff'; break;
                                        case 'completada': echo '#d4edda'; break;
                                        case 'cancelada': echo '#f8d7da'; break;
                                        default: echo '#e2e3e5';
                                    }
                                ?>;">
                                <?php echo ucfirst($solicitud->estado); ?>
                            </span>
                        </p>
                    </div>

                    <div>
                        <strong style="color: #666;">Precio Base:</strong>
                        <p style="margin: 0.3rem 0 0 0; font-size: 1.2rem; color: var(--primary-color); font-weight: 600;">
                            $<?php echo number_format($solicitud->precio_base, 2); ?>
                        </p>
                    </div>

                    <div>
                        <strong style="color: #666;">Duración Estimada:</strong>
                        <p style="margin: 0.3rem 0 0 0;"><?php echo $solicitud->duracion_estimada; ?> minutos</p>
                    </div>

                    <div>
                        <strong style="color: #666;">Prioridad:</strong>
                        <p style="margin: 0.3rem 0 0 0;">
                            <span style="padding: 0.3rem 0.8rem; border-radius: 10px; font-size: 0.85rem; font-weight: 600;
                                background-color: <?php
                                    switch($solicitud->prioridad) {
                                        case 'urgente': echo '#f8d7da'; break;
                                        case 'alta': echo '#fff3cd'; break;
                                        case 'media': echo '#cfe2ff'; break;
                                        case 'baja': echo '#d1ecf1'; break;
                                        default: echo '#e2e3e5';
                                    }
                                ?>;">
                                <?php echo ucfirst($solicitud->prioridad); ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información del Fontanero -->
            <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h2 style="color: var(--primary-color); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    👷 Información del Fontanero
                </h2>

                <?php if ($solicitud->id_fontanero): ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <strong style="color: #666;">Nombre:</strong>
                            <p style="margin: 0.3rem 0 0 0; font-size: 1.1rem;"><?php echo $solicitud->nombre_fontanero; ?></p>
                        </div>

                        <div>
                            <strong style="color: #666;">Teléfono:</strong>
                            <p style="margin: 0.3rem 0 0 0;">
                                <a href="tel:<?php echo $solicitud->telefono_fontanero; ?>" style="color: var(--primary-color);">
                                    📞 <?php echo $solicitud->telefono_fontanero; ?>
                                </a>
                            </p>
                        </div>

                        <?php if ($solicitud->fecha_programada): ?>
                            <div>
                                <strong style="color: #666;">Fecha Programada:</strong>
                                <p style="margin: 0.3rem 0 0 0;">
                                    📅 <?php echo date('d/m/Y H:i', strtotime($solicitud->fecha_programada)); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 2rem; color: #999;">
                        <p style="font-size: 3rem; margin-bottom: 1rem;">⏳</p>
                        <p>Aún no se ha asignado un fontanero a esta solicitud</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Detalles de la Solicitud -->
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-top: 2rem;">
            <h2 style="color: var(--primary-color); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                📝 Detalles de la Solicitud
            </h2>

            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div>
                    <strong style="color: #666;">Dirección del Servicio:</strong>
                    <p style="margin: 0.5rem 0 0 0; font-size: 1rem; line-height: 1.6;">
                        📍 <?php echo $solicitud->direccion_servicio; ?>
                    </p>
                </div>

                <div>
                    <strong style="color: #666;">Descripción del Problema:</strong>
                    <p style="margin: 0.5rem 0 0 0; font-size: 1rem; line-height: 1.6; background: #f8f9fa; padding: 1rem; border-radius: 10px;">
                        <?php echo nl2br(htmlspecialchars($solicitud->descripcion_problema)); ?>
                    </p>
                </div>

                <div>
                    <strong style="color: #666;">Fecha de Solicitud:</strong>
                    <p style="margin: 0.5rem 0 0 0;">
                        🕒 <?php echo date('d/m/Y H:i', strtotime($solicitud->fecha_solicitud)); ?>
                    </p>
                </div>

                <?php if ($solicitud->fecha_completado): ?>
                    <div>
                        <strong style="color: #666;">Fecha de Finalización:</strong>
                        <p style="margin: 0.5rem 0 0 0;">
                            ✅ <?php echo date('d/m/Y H:i', strtotime($solicitud->fecha_completado)); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Acciones -->
        <?php if ($solicitud->estado == 'completada' && $solicitud->id_fontanero): ?>
            <div style="margin-top: 2rem; text-align: center;">
                <button class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;" onclick="calificar(<?php echo $solicitud->id_solicitud; ?>, <?php echo $solicitud->id_fontanero; ?>)">
                    ⭐ Calificar Servicio
                </button>
            </div>
        <?php elseif ($solicitud->estado == 'pendiente'): ?>
            <div style="margin-top: 2rem; text-align: center;">
                <button class="btn btn-secondary" style="padding: 1rem 2rem; font-size: 1.1rem; background: #dc3545;" onclick="cancelar(<?php echo $solicitud->id_solicitud; ?>)">
                    ❌ Cancelar Solicitud
                </button>
            </div>
        <?php endif; ?>
    </section>
</main>

<script>
function cancelar(id) {
    if (confirm('¿Estás seguro de cancelar esta solicitud?')) {
        $.ajax({
            url: '<?php echo base_url('cliente/cancelar_solicitud/'); ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Solicitud cancelada exitosamente');
                    window.location.href = '<?php echo base_url('cliente/mis-solicitudes'); ?>';
                } else {
                    alert('Error al cancelar la solicitud');
                }
            },
            error: function() {
                alert('Error al procesar la solicitud');
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
            url: '<?php echo base_url('api/calificacion/enviar'); ?>',
            type: 'POST',
            data: {
                id_solicitud: id,
                id_fontanero: idFontanero,
                puntuacion: rating,
                comentario: comentario || ''
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('¡Gracias por tu calificación!');
                    window.location.href = '<?php echo base_url('cliente/mis-solicitudes'); ?>';
                } else {
                    alert('Error: ' + (response.message || 'No se pudo enviar la calificación'));
                }
            },
            error: function(xhr, status, error) {
                alert('Error al enviar la calificación. Por favor intenta de nuevo.');
                console.error('Error:', error);
            }
        });
    } else if (rating !== null) {
        alert('Por favor ingresa una calificación válida entre 1 y 5');
    }
}
</script>
