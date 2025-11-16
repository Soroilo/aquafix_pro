<section class="hero" id="inicio">
    <div class="hero-content">
        <h1>🚀 AquaFix Pro - Mashups Integrados</h1>
        <p class="hero-description">
            Geolocalización en tiempo real • Información climática • Notificaciones automáticas
        </p>
        <div class="cta-buttons">
            <a href="#fontaneros" class="btn btn-primary">Ver Fontaneros</a>
            <?php if($this->session->userdata('logged_in')): ?>
                <a href="<?php echo base_url('cliente/solicitar'); ?>" class="btn btn-secondary">Solicitar Servicio</a>
            <?php else: ?>
                <a href="<?php echo base_url('register'); ?>" class="btn btn-secondary">Regístrate Ahora</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<main class="container">
    <section id="clima" class="section-spacing">
        <h2 class="section-title">☁️ Información Climática en Tiempo Real</h2>
        <p class="section-subtitle">Mashup con OpenWeatherMap API</p>
        
        <article class="weather-widget" id="weatherWidget">
            <div class="weather-info">
                <div class="weather-icon" id="weatherIcon" aria-label="Icono del clima">⏳</div>
                <div class="weather-main">
                    <div class="weather-temp" id="weatherTemp">--°C</div>
                    <div class="weather-description" id="weatherDescription">Cargando...</div>
                    <div class="weather-location">
                        📍 <span id="weatherCity">Bogotá, CO</span>
                    </div>
                </div>
            </div>
            <div class="weather-details">
                <div class="weather-detail-item">
                    <span class="detail-icon">💧</span>
                    <span class="detail-label">Humedad:</span>
                    <strong id="weatherHumidity">--%</strong>
                </div>
                <div class="weather-detail-item">
                    <span class="detail-icon">💨</span>
                    <span class="detail-label">Viento:</span>
                    <strong id="weatherWind">-- km/h</strong>
                </div>
                <div class="weather-detail-item">
                    <span class="detail-icon">🌡️</span>
                    <span class="detail-label">Sensación:</span>
                    <strong id="weatherFeels">--°C</strong>
                </div>
            </div>
        </article>
    </section>

    <section id="mapa" class="map-section section-spacing">
        <h2 class="section-title">🗺️ Localización de Fontaneros</h2>
        <p class="section-subtitle">Mashup con Google Maps JavaScript API</p>
        
        <div class="map-controls">
            <button class="btn btn-primary" onclick="centrarMapa()" aria-label="Centrar mapa en mi ubicación">
                📍 Mi Ubicación
            </button>
            <button class="btn btn-secondary" onclick="mostrarFontaneros()" aria-label="Mostrar fontaneros cercanos">
                👷 Ver Fontaneros Cercanos
            </button>
            <button class="btn btn-success" onclick="calcularRuta()" aria-label="Calcular ruta al fontanero">
                🚗 Calcular Ruta
            </button>
        </div>
        
        <div id="map" role="application" aria-label="Mapa de ubicación de fontaneros"></div>
    </section>

    <section id="fontaneros" class="section-spacing">
        <h2 class="section-title">👷 Fontaneros Disponibles</h2>
        <p class="section-subtitle">Con distancia calculada en tiempo real usando geolocalización</p>
        
        <div class="plumbers-grid" id="plumbersGrid"></div>
    </section>

    <section id="notificaciones" class="notification-section section-spacing">
        <h2 class="section-title">📧 Sistema de Notificaciones</h2>
        <p class="section-subtitle">Mashup con EmailJS API para envío de notificaciones automáticas</p>
        
        <div class="alert alert-success" id="successAlert" role="alert">
            ✅ Notificación enviada exitosamente!
        </div>
        <div class="alert alert-error" id="errorAlert" role="alert">
            ❌ Error al enviar notificación. Inténtalo nuevamente.
        </div>
        
        <?php if($this->session->userdata('logged_in')): ?>
            <form id="notificationForm" class="notification-form">
                <div class="form-group">
                    <label for="serviceType">Tipo de Servicio: *</label>
                    <select id="serviceType" name="serviceType" required>
                        <option value="">-- Selecciona un servicio --</option>
                        <?php foreach($servicios as $servicio): ?>
                            <option value="<?php echo $servicio->id_servicio; ?>">
                                <?php echo $servicio->nombre; ?> - $<?php echo number_format($servicio->precio_base, 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="direccion_servicio">Dirección del Servicio: *</label>
                    <input type="text" id="direccion_servicio" name="direccion_servicio" placeholder="Calle 123 #45-67, Bogotá" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Descripción del Problema: *</label>
                    <textarea id="message" name="message" rows="5" placeholder="Describe detalladamente el problema..." required></textarea>
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
                    📧 Enviar Solicitud de Servicio
                </button>
            </form>
        <?php else: ?>
            <p class="text-center">
                <a href="<?php echo base_url('login'); ?>" class="btn btn-primary">
                    Inicia sesión para solicitar un servicio
                </a>
            </p>
        <?php endif; ?>
    </section>
</main>
