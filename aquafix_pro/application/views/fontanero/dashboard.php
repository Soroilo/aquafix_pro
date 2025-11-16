<main class="container">
    <section class="section-spacing">
        <h1 class="section-title">👋 Bienvenido, <?php echo $this->session->userdata('user_name'); ?></h1>
        <p class="section-subtitle">Panel de Control del Fontanero</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">⭐</div>
                <div style="font-size: 2rem; font-weight: bold; margin-bottom: 0.3rem;"><?php echo number_format($fontanero->rating_promedio, 1); ?></div>
                <div>Calificación Promedio</div>
            </div>

            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">✅</div>
                <div style="font-size: 2rem; font-weight: bold; margin-bottom: 0.3rem;"><?php echo $fontanero->servicios_completados; ?></div>
                <div>Servicios Completados</div>
            </div>

            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">📋</div>
                <div style="font-size: 2rem; font-weight: bold; margin-bottom: 0.3rem;"><?php echo isset($estadisticas->asignadas) ? $estadisticas->asignadas : 0; ?></div>
                <div>Solicitudes Asignadas</div>
            </div>

            <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">⚙️</div>
                <div style="font-size: 2rem; font-weight: bold; margin-bottom: 0.3rem;"><?php echo isset($estadisticas->en_proceso) ? $estadisticas->en_proceso : 0; ?></div>
                <div>En Proceso</div>
            </div>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">🎯 Acciones Rápidas</h2>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="<?php echo base_url('fontanero/solicitudes'); ?>" class="btn btn-primary">
                    📝 Ver Solicitudes
                </a>
                <a href="<?php echo base_url('fontanero/perfil'); ?>" class="btn btn-secondary">
                    👤 Mi Perfil
                </a>
                <button class="btn btn-success" onclick="cambiarDisponibilidad()">
                    <?php echo $fontanero->disponible ? '🟢 Disponible' : '🔴 No Disponible'; ?>
                </button>
            </div>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">📋 Solicitudes Pendientes</h2>
            
            <?php if (empty($solicitudes_pendientes)): ?>
                <p style="text-align: center; padding: 2rem; color: #666;">
                    No tienes solicitudes pendientes en este momento.
                </p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #eee;">
                                <th style="padding: 1rem; text-align: left;">Servicio</th>
                                <th style="padding: 1rem; text-align: left;">Cliente</th>
                                <th style="padding: 1rem; text-align: left;">Fecha Programada</th>
                                <th style="padding: 1rem; text-align: left;">Estado</th>
                                <th style="padding: 1rem; text-align: left;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes_pendientes as $solicitud): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 1rem;"><?php echo $solicitud->nombre_servicio; ?></td>
                                    <td style="padding: 1rem;"><?php echo $solicitud->nombre_cliente; ?></td>
                                    <td style="padding: 1rem;"><?php echo $solicitud->fecha_programada ? date('d/m/Y H:i', strtotime($solicitud->fecha_programada)) : 'Sin programar'; ?></td>
                                    <td style="padding: 1rem;">
                                        <span style="padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem; font-weight: 600; background-color: #d1ecf1;">
                                            <?php echo ucfirst($solicitud->estado); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <button class="btn btn-success" style="padding: 0.5rem 1rem; font-size: 0.9rem;" onclick="iniciarServicio(<?php echo $solicitud->id_solicitud; ?>)">
                                            ▶️ Iniciar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">⭐ Calificaciones Recientes</h2>
            
            <?php if (empty($calificaciones_recientes)): ?>
                <p style="text-align: center; padding: 2rem; color: #666;">
                    Aún no tienes calificaciones.
                </p>
            <?php else: ?>
                <?php foreach (array_slice($calificaciones_recientes, 0, 5) as $cal): ?>
                    <div style="border-bottom: 1px solid #eee; padding: 1rem 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong><?php echo $cal->nombre_cliente; ?></strong>
                                <span style="color: #666; font-size: 0.9rem; margin-left: 0.5rem;">
                                    <?php echo date('d/m/Y', strtotime($cal->fecha_calificacion)); ?>
                                </span>
                            </div>
                            <div style="color: #f39c12; font-size: 1.2rem;">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <?php echo $i <= $cal->puntuacion ? '⭐' : '☆'; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <?php if ($cal->comentario): ?>
                            <p style="color: #666; margin-top: 0.5rem; font-size: 0.9rem;"><?php echo $cal->comentario; ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
function cambiarDisponibilidad() {
    if (confirm('¿Deseas cambiar tu disponibilidad?')) {
        $.ajax({
            url: '<?php echo base_url('fontanero/cambiar_disponibilidad'); ?>',
            type: 'POST',
            data: {
                disponible: <?php echo $fontanero->disponible ? '0' : '1'; ?>,
                <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    }
}

function iniciarServicio(id) {
    if (confirm('¿Deseas iniciar este servicio?')) {
        $.ajax({
            url: '<?php echo base_url('fontanero/iniciar_servicio/'); ?>' + id,
            type: 'POST',
            data: {
                <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
            },
            success: function(response) {
                if (response.success) {
                    alert('Servicio iniciado');
                    location.reload();
                }
            }
        });
    }
}
</script>