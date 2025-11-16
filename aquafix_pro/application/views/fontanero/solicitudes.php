<main class="container">
    <section class="section-spacing">
        <h1 class="section-title">=Ë Mis Solicitudes de Servicio</h1>

        <div class="alert alert-success" id="successAlert" style="display: none;"></div>
        <div class="alert alert-error" id="errorAlert" style="display: none;"></div>

        <!-- Filtros -->
        <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <form method="GET" action="<?php echo base_url('fontanero/solicitudes'); ?>">
                <div style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
                    <div class="form-group" style="margin-bottom: 0; min-width: 200px;">
                        <label for="estado">Filtrar por Estado:</label>
                        <select id="estado" name="estado" onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="asignada" <?php echo ($this->input->get('estado') == 'asignada') ? 'selected' : ''; ?>>Asignadas</option>
                            <option value="en_proceso" <?php echo ($this->input->get('estado') == 'en_proceso') ? 'selected' : ''; ?>>En Proceso</option>
                            <option value="completada" <?php echo ($this->input->get('estado') == 'completada') ? 'selected' : ''; ?>>Completadas</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary" style="padding: 0.6rem 1.5rem;">= Filtrar</button>
                    <a href="<?php echo base_url('fontanero/solicitudes'); ?>" class="btn btn-secondary" style="padding: 0.6rem 1.5rem;">= Limpiar</a>
                </div>
            </form>
        </div>

        <!-- Listado de Solicitudes Asignadas -->
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">=' Mis Solicitudes</h2>

            <?php if (empty($solicitudes)): ?>
                <p style="text-align: center; padding: 3rem; color: #666;">
                    No tienes solicitudes con el filtro seleccionado.
                </p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #eee; background: #f8f9fa;">
                                <th style="padding: 1rem; text-align: left;">ID</th>
                                <th style="padding: 1rem; text-align: left;">Cliente</th>
                                <th style="padding: 1rem; text-align: left;">Servicio</th>
                                <th style="padding: 1rem; text-align: left;">Dirección</th>
                                <th style="padding: 1rem; text-align: left;">Fecha Prog.</th>
                                <th style="padding: 1rem; text-align: left;">Estado</th>
                                <th style="padding: 1rem; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes as $solicitud): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 1rem; font-weight: 600;">#<?php echo $solicitud->id_solicitud; ?></td>
                                    <td style="padding: 1rem;">
                                        <div style="font-weight: 600;"><?php echo $solicitud->nombre_cliente; ?></div>
                                        <div style="font-size: 0.85rem; color: #666;">=Þ <?php echo $solicitud->telefono_cliente; ?></div>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <div style="font-weight: 600;"><?php echo $solicitud->nombre_servicio; ?></div>
                                        <div style="font-size: 0.85rem; color: #666;">=° $<?php echo number_format($solicitud->precio_base, 2); ?></div>
                                    </td>
                                    <td style="padding: 1rem; max-width: 200px;">
                                        <div style="font-size: 0.9rem;"><?php echo $solicitud->direccion_servicio; ?></div>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <?php if ($solicitud->fecha_programada): ?>
                                            <div style="font-weight: 600;"><?php echo date('d/m/Y', strtotime($solicitud->fecha_programada)); ?></div>
                                            <div style="font-size: 0.85rem; color: #666;"><?php echo date('H:i', strtotime($solicitud->fecha_programada)); ?></div>
                                        <?php else: ?>
                                            <span style="color: #999;">Sin programar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <?php
                                        $estado_colores = array(
                                            'asignada' => '#d1ecf1',
                                            'en_proceso' => '#cfe2ff',
                                            'completada' => '#d4edda',
                                            'cancelada' => '#f8d7da'
                                        );
                                        $color = isset($estado_colores[$solicitud->estado]) ? $estado_colores[$solicitud->estado] : '#f8f9fa';
                                        ?>
                                        <span style="padding: 0.4rem 0.8rem; border-radius: 15px; font-size: 0.85rem; font-weight: 600; background-color: <?php echo $color; ?>; display: inline-block;">
                                            <?php
                                            $estados = array(
                                                'asignada' => '=Ë Asignada',
                                                'en_proceso' => '™ En Proceso',
                                                'completada' => ' Completada',
                                                'cancelada' => 'L Cancelada'
                                            );
                                            echo isset($estados[$solicitud->estado]) ? $estados[$solicitud->estado] : ucfirst($solicitud->estado);
                                            ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                                            <?php if ($solicitud->estado === 'asignada'): ?>
                                                <button onclick="iniciarServicio(<?php echo $solicitud->id_solicitud; ?>)" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                                    ¶ Iniciar
                                                </button>
                                            <?php endif; ?>

                                            <?php if ($solicitud->estado === 'en_proceso'): ?>
                                                <button onclick="completarServicio(<?php echo $solicitud->id_solicitud; ?>)" class="btn btn-success" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                                     Completar
                                                </button>
                                            <?php endif; ?>

                                            <button onclick="verDetalle(<?php echo $solicitud->id_solicitud; ?>)" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                                =A Ver
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Solicitudes Disponibles para Tomar -->
        <?php if (!empty($solicitudes_disponibles)): ?>
            <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);"><• Solicitudes Disponibles</h2>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #eee; background: #f8f9fa;">
                                <th style="padding: 1rem; text-align: left;">Cliente</th>
                                <th style="padding: 1rem; text-align: left;">Servicio</th>
                                <th style="padding: 1rem; text-align: left;">Dirección</th>
                                <th style="padding: 1rem; text-align: left;">Prioridad</th>
                                <th style="padding: 1rem; text-align: center;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes_disponibles as $solicitud): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 1rem;">
                                        <div style="font-weight: 600;"><?php echo $solicitud->nombre_cliente; ?></div>
                                        <div style="font-size: 0.85rem; color: #666;">=Þ <?php echo $solicitud->telefono_cliente; ?></div>
                                    </td>
                                    <td style="padding: 1rem;"><?php echo $solicitud->nombre_servicio; ?></td>
                                    <td style="padding: 1rem;"><?php echo $solicitud->direccion_servicio; ?></td>
                                    <td style="padding: 1rem;">
                                        <?php
                                        $prioridad_colores = array(
                                            'urgente' => '#dc3545',
                                            'alta' => '#fd7e14',
                                            'media' => '#ffc107',
                                            'baja' => '#28a745'
                                        );
                                        $color = isset($prioridad_colores[$solicitud->prioridad]) ? $prioridad_colores[$solicitud->prioridad] : '#6c757d';
                                        ?>
                                        <span style="padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem; font-weight: 600; background-color: <?php echo $color; ?>; color: white;">
                                            <?php echo ucfirst($solicitud->prioridad); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <button onclick="aceptarSolicitud(<?php echo $solicitud->id_solicitud; ?>)" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                             Aceptar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>

<!-- Modal para aceptar solicitud -->
<div id="modalAceptar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 2rem; border-radius: 15px; max-width: 500px; width: 90%;">
        <h3 style="margin-bottom: 1.5rem;">=Å Programar Servicio</h3>
        <form id="formAceptar">
            <input type="hidden" id="id_solicitud_aceptar" name="id_solicitud">
            <div class="form-group">
                <label for="fecha_programada">Fecha y Hora: *</label>
                <input type="datetime-local" id="fecha_programada" name="fecha_programada" required>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;"> Confirmar</button>
                <button type="button" onclick="cerrarModal()" class="btn btn-secondary" style="flex: 1;">L Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function iniciarServicio(id) {
    if (confirm('¿Deseas iniciar este servicio?')) {
        $.ajax({
            url: '<?php echo base_url('fontanero/iniciar_servicio/'); ?>' + id,
            type: 'POST',
            data: {
                <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#successAlert').text(response.message).show();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $('#errorAlert').text(response.message).show();
                }
            },
            error: function() {
                $('#errorAlert').text('Error al procesar la solicitud').show();
            }
        });
    }
}

function completarServicio(id) {
    if (confirm('¿Confirmas que el servicio ha sido completado?')) {
        $.ajax({
            url: '<?php echo base_url('fontanero/completar/'); ?>' + id,
            type: 'POST',
            data: {
                <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#successAlert').text(' ' + response.message).show();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $('#errorAlert').text(response.message).show();
                }
            },
            error: function() {
                $('#errorAlert').text('Error al procesar la solicitud').show();
            }
        });
    }
}

function aceptarSolicitud(id) {
    $('#id_solicitud_aceptar').val(id);
    $('#modalAceptar').css('display', 'flex');
}

function cerrarModal() {
    $('#modalAceptar').hide();
    $('#formAceptar')[0].reset();
}

function verDetalle(id) {
    window.location.href = '<?php echo base_url('fontanero/solicitud_detalle/'); ?>' + id;
}

$(document).ready(function() {
    $('#formAceptar').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            id_solicitud: $('#id_solicitud_aceptar').val(),
            fecha_programada: $('#fecha_programada').val(),
            <?php echo $this->security->get_csrf_token_name(); ?>: getCsrfToken()
        };

        $.ajax({
            url: '<?php echo base_url('fontanero/aceptar_solicitud'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#successAlert').text(' ' + response.message).show();
                    cerrarModal();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $('#errorAlert').text(response.message).show();
                }
            },
            error: function() {
                $('#errorAlert').text('Error al procesar la solicitud').show();
            }
        });
    });
});
</script>
