<main class="container">
    <section class="section-spacing">
        <h1 class="section-title">📊 Panel de Administración</h1>
        
        <!-- Tarjetas de estadísticas -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; font-size: 2.5rem;">👥 <?php echo $total_clientes; ?></h3>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Clientes Registrados</p>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; font-size: 2.5rem;">🔧 <?php echo $total_fontaneros; ?></h3>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Fontaneros Activos</p>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; font-size: 2.5rem;">⚙️ <?php echo $total_servicios; ?></h3>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Servicios Disponibles</p>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; font-size: 2.5rem;">📋 <?php echo $estadisticas_solicitudes->total; ?></h3>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Solicitudes Totales</p>
            </div>
        </div>

        <!-- Estado de solicitudes -->
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h2 style="margin-top: 0;">📈 Estado de Solicitudes</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div style="padding: 1rem; background: #fff3cd; border-radius: 10px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: #856404;">⏳ <?php echo $estadisticas_solicitudes->pendientes; ?></div>
                    <div style="color: #856404;">Pendientes</div>
                </div>
                <div style="padding: 1rem; background: #d1ecf1; border-radius: 10px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: #0c5460;">📌 <?php echo $estadisticas_solicitudes->asignadas; ?></div>
                    <div style="color: #0c5460;">Asignadas</div>
                </div>
                <div style="padding: 1rem; background: #cfe2ff; border-radius: 10px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: #084298;">🔄 <?php echo $estadisticas_solicitudes->en_proceso; ?></div>
                    <div style="color: #084298;">En Proceso</div>
                </div>
                <div style="padding: 1rem; background: #d1e7dd; border-radius: 10px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: #0f5132;">✅ <?php echo $estadisticas_solicitudes->completadas; ?></div>
                    <div style="color: #0f5132;">Completadas</div>
                </div>
                <div style="padding: 1rem; background: #f8d7da; border-radius: 10px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: #842029;">❌ <?php echo $estadisticas_solicitudes->canceladas; ?></div>
                    <div style="color: #842029;">Canceladas</div>
                </div>
            </div>
        </div>

        <!-- Accesos rápidos -->
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h2 style="margin-top: 0;">⚡ Accesos Rápidos</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <a href="<?php echo base_url('admin/solicitudes?estado=pendiente'); ?>" class="btn btn-primary" style="text-decoration: none; text-align: center; padding: 1.5rem;">
                    📋 Solicitudes Pendientes
                </a>
                <a href="<?php echo base_url('admin/clientes'); ?>" class="btn btn-secondary" style="text-decoration: none; text-align: center; padding: 1.5rem;">
                    👥 Gestionar Clientes
                </a>
                <a href="<?php echo base_url('admin/fontaneros'); ?>" class="btn btn-secondary" style="text-decoration: none; text-align: center; padding: 1.5rem;">
                    🔧 Gestionar Fontaneros
                </a>
                <a href="<?php echo base_url('admin/servicios'); ?>" class="btn btn-secondary" style="text-decoration: none; text-align: center; padding: 1.5rem;">
                    ⚙️ Gestionar Servicios
                </a>
                <a href="<?php echo base_url('admin/reportes'); ?>" class="btn btn-secondary" style="text-decoration: none; text-align: center; padding: 1.5rem;">
                    📊 Ver Reportes
                </a>
            </div>
        </div>
    </section>
</main>