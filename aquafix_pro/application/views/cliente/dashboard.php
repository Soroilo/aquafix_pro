<main class="container">
    <section class="section-spacing">
        <h1 class="section-title">👋 Bienvenido, <?php echo $this->session->userdata('user_name'); ?></h1>
        <p class="section-subtitle">Panel de Control del Cliente</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">📋</div>
                <div style="font-size: 2rem; font-weight: bold; margin-bottom: 0.3rem;"><?php echo $estadisticas['total_solicitudes']; ?></div>
                <div>Solicitudes Totales</div>
            </div>

            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">⏳</div>
                <div style="font-size: 2rem; font-weight: bold; margin-bottom: 0.3rem;"><?php echo $estadisticas['pendientes']; ?></div>
                <div>Pendientes</div>
            </div>

            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">⚙️</div>
                <div style="font-size: 2rem; font-weight: bold; margin-bottom: 0.3rem;"><?php echo $estadisticas['en_proceso']; ?></div>
                <div>En Proceso</div>
            </div>

            <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">✅</div>
                <div style="font-size: 2rem; font-weight: bold; margin-bottom: 0.3rem;"><?php echo $estadisticas['completadas']; ?></div>
                <div>Completadas</div>
            </div>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">🎯 Acciones Rápidas</h2>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="<?php echo base_url('cliente/solicitar'); ?>" class="btn btn-primary">
                    ➕ Nueva Solicitud
                </a>
                <a href="<?php echo base_url('cliente/mis-solicitudes'); ?>" class="btn btn-secondary">
                    📝 Mis Solicitudes
                </a>
                <a href="<?php echo base_url('cliente/perfil'); ?>" class="btn btn-success">
                    👤 Mi Perfil
                </a>
            </div>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">📋 Solicitudes Recientes</h2>
            
            <?php if (empty($solicitudes_recientes)): ?>
                <p style="text-align: center; padding: 2rem; color: #666;">
                    No tienes solicitudes recientes. <a href="<?php echo base_url('cliente/solicitar'); ?>" style="color: var(--primary-color); font-weight: 600;">Solicita un servicio ahora</a>
                </p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #eee;">
                                <th style="padding: 1rem; text-align: left;">Servicio</th>
                                <th style="padding: 1rem; text-align: left;">Fontanero</th>
                                <th style="padding: 1rem; text-align: left;">Fecha</th>
                                <th style="padding: 1rem; text-align: left;">Estado</th>
                                <th style="padding: 1rem; text-align: left;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes_recientes as $solicitud): ?>
                                <tr style="border-bottom: 1px solid #eee;">
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
                                    <td style="padding: 1rem;">
                                        <a href="<?php echo base_url('cliente/detalle_solicitud/'.$solicitud->id_solicitud); ?>" 
                                           style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Ver Detalles</a>
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
