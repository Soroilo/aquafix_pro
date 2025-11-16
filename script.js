/* ============================================================
   AQUAFIX PRO - JAVASCRIPT
   Implementación de Mashups con APIs externas
   ============================================================ */

// ==================== BASE DE DATOS DE FONTANEROS ====================
const fontaneros = [
    {
        id: 1,
        nombre: "Pedro Martínez",
        especialidad: "Reparaciones Generales",
        rating: 4.8,
        servicios: 156,
        lat: 4.7110,
        lng: -74.0721,
        disponible: true
    },
    {
        id: 2,
        nombre: "Ana López",
        especialidad: "Instalaciones",
        rating: 4.9,
        servicios: 203,
        lat: 4.6850,
        lng: -74.0550,
        disponible: true
    },
    {
        id: 3,
        nombre: "Luis Hernández",
        especialidad: "Emergencias 24/7",
        rating: 4.7,
        servicios: 178,
        lat: 4.7300,
        lng: -74.0850,
        disponible: false
    },
    {
        id: 4,
        nombre: "Carmen Torres",
        especialidad: "Mantenimiento Preventivo",
        rating: 4.6,
        servicios: 134,
        lat: 4.6950,
        lng: -74.0450,
        disponible: true
    }
];

// Variables globales para Google Maps
let map;
let userMarker;
let plumberMarkers = [];
let userLocation = { lat: 4.7110, lng: -74.0721 }; // Bogotá por defecto

// ==================== MASHUP 1: OPENWEATHERMAP API ====================

/**
 * Obtiene información climática en tiempo real de Bogotá
 * API: OpenWeatherMap
 */
const obtenerClima = async () => {
    try {
        console.log("⏳ Consultando clima desde Open-Meteo...");

        // Coordenadas aproximadas de Bogotá
        const lat = 4.7110;
        const lon = -74.0721;

        const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`;

        const response = await fetch(url);
        if (!response.ok) throw new Error("Error en petición de Open-Meteo");

        const data = await response.json();
        const clima = data.current_weather;

        // Mapeo sencillo de iconos según código del tiempo
        const weatherCodeMap = {
            0: "☀️ Despejado",
            1: "🌤️ Mayormente despejado",
            2: "⛅ Parcialmente nublado",
            3: "☁️ Nublado",
            45: "🌫️ Niebla",
            48: "🌫️ Niebla",
            51: "🌦️ Llovizna",
            53: "🌦️ Llovizna moderada",
            55: "🌧️ Llovizna fuerte",
            61: "🌧️ Lluvia ligera",
            63: "🌧️ Lluvia moderada",
            65: "🌧️ Lluvia fuerte",
            80: "🌦️ Lluvias dispersas",
            81: "🌧️ Lluvias moderadas",
            82: "⛈️ Lluvia intensa"
        };

        const descripcion = weatherCodeMap[clima.weathercode] || "Clima no disponible";

        // Actualizar UI
        document.getElementById("weatherTemp").textContent = `${clima.temperature}°C`;
        document.getElementById("weatherDescription").textContent = descripcion;
        document.getElementById("weatherCity").textContent = "Bogotá, CO";
        document.getElementById("weatherHumidity").textContent = "N/A";
        document.getElementById("weatherWind").textContent = `${clima.windspeed} km/h`;
        document.getElementById("weatherFeels").textContent = `${clima.temperature}°C`;

        document.getElementById("weatherIcon").textContent =
            descripcion.includes("Lluvia") ? "🌧️" :
            descripcion.includes("Nublado") ? "☁️" :
            descripcion.includes("Niebla") ? "🌫️" :
            descripcion.includes("Despejado") ? "☀️" : "⛅";

        console.log("✔ Clima actualizado desde Open-Meteo");
    } catch (error) {
        console.error("❌ Error obteniendo clima:", error);
        document.getElementById("weatherDescription").textContent = "Error al cargar clima";
    }
};


/**
 * Actualiza el widget de clima con los datos recibidos
 * Uso de programación funcional para mapear iconos
 */
const actualizarWidgetClima = (data) => {
    // Mapeo de iconos usando objeto (más eficiente que if-else)
    const iconMap = {
        'Clear': '☀️',
        'Clouds': '☁️',
        'Rain': '🌧️',
        'Drizzle': '🌦️',
        'Thunderstorm': '⛈️',
        'Snow': '❄️',
        'Mist': '🌫️',
        'Fog': '🌫️',
        'Haze': '🌫️'
    };
    
    const weatherMain = data.weather[0].main;
    const icon = iconMap[weatherMain] || '🌤️';
    
    // Actualizar DOM
    document.getElementById('weatherIcon').textContent = icon;
    document.getElementById('weatherTemp').textContent = `${Math.round(data.main.temp)}°C`;
    document.getElementById('weatherDescription').textContent = data.weather[0].description;
    document.getElementById('weatherCity').textContent = `${data.name}, ${data.sys.country}`;
    document.getElementById('weatherHumidity').textContent = `${data.main.humidity}%`;
    document.getElementById('weatherWind').textContent = `${Math.round(data.wind.speed * 3.6)} km/h`;
    document.getElementById('weatherFeels').textContent = `${Math.round(data.main.feels_like)}°C`;
};

const mostrarErrorClima = () => {
    document.getElementById('weatherIcon').textContent = '❌';
    document.getElementById('weatherTemp').textContent = '--°C';
    document.getElementById('weatherDescription').textContent = 'No disponible';
};

// ==================== MASHUP 2: GOOGLE MAPS API ====================

/**
 * Inicializa el mapa de Google Maps
 * API: Google Maps JavaScript API
 */
const initMap = () => {
    // Verificar si Google Maps está disponible
    if (typeof google === 'undefined' || !google.maps) {
        console.log('⚠️ Google Maps no está disponible. Mostrando mapa simulado.');
        mostrarMapaSimulado();
        return;
    }


    
    // Inicializar mapa centrado en Bogotá
    map = new google.maps.Map(document.getElementById('map'), {
        center: userLocation,
        zoom: 13,
        styles: [
            {
                featureType: "poi",
                elementType: "labels",
                stylers: [{ visibility: "off" }]
            }
        ]
    });
    
    // Marcador del usuario
    userMarker = new google.maps.Marker({
        position: userLocation,
        map: map,
        title: "Tu ubicación",
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 10,
            fillColor: "#4285F4",
            fillOpacity: 1,
            strokeColor: "white",
            strokeWeight: 3
        }
    });
    
    console.log('🗺️ Mapa de Google Maps inicializado');
};
    window.initMap = initMap;

/**
 * Muestra un mapa simulado cuando Google Maps API no está disponible
 */
const mostrarMapaSimulado = () => {
    const mapDiv = document.getElementById('map');
    mapDiv.innerHTML = `
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center; padding: 2rem; border-radius: 10px;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🗺️</div>
            <h3 style="margin-bottom: 1rem; font-size: 1.5rem;">Mapa de Google Maps</h3>
            <p style="max-width: 500px; margin-bottom: 1rem; line-height: 1.6;">
                Para ver el mapa real, necesitas agregar tu Google Maps API Key en el código HTML.
            </p>
            <p style="font-size: 0.9rem; opacity: 0.8; margin-bottom: 2rem;">
                Obtén tu API Key gratuita en: 
                <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" 
                   target="_blank" 
                   style="color: #ffeb3b; text-decoration: underline;">
                    Google Cloud Console
                </a>
            </p>
            <div style="padding: 1.5rem; background: rgba(255,255,255,0.2); border-radius: 12px; width: 100%; max-width: 400px;">
                <p style="margin-bottom: 1rem;"><strong>📍 Fontaneros Cercanos:</strong></p>
                <div style="text-align: left;">
                    <p style="margin: 0.5rem 0;">👷 Pedro Martínez - 2.3 km</p>
                    <p style="margin: 0.5rem 0;">👷 Ana López - 3.8 km</p>
                    <p style="margin: 0.5rem 0;">👷 Carmen Torres - 4.5 km</p>
                </div>
            </div>
        </div>
    `;
    
    // Actualizar grid de fontaneros de todos modos
    actualizarGridFontaneros();
};

/**
 * Muestra fontaneros en el mapa con marcadores personalizados
 */
const mostrarFontaneros = () => {
    if (!map) {
        console.log('ℹ️ Mapa no disponible, mostrando solo grid de fontaneros');
        actualizarGridFontaneros();
        return;
    }
    
    // Limpiar marcadores anteriores usando programación funcional
    plumberMarkers.forEach(marker => marker.setMap(null));
    plumberMarkers = [];
    
    // Crear marcadores para cada fontanero usando MAP
    fontaneros.forEach((fontanero) => {
        const marker = new google.maps.Marker({
            position: { lat: fontanero.lat, lng: fontanero.lng },
            map: map,
            title: fontanero.nombre,
            animation: google.maps.Animation.DROP,
            icon: {
                url: `data:image/svg+xml,${encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"><text x="20" y="30" font-size="30" text-anchor="middle">👷</text></svg>')}`,
                scaledSize: new google.maps.Size(40, 40)
            }
        });
        
        // InfoWindow con información del fontanero
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 10px; font-family: Arial, sans-serif;">
                    <h3 style="margin: 0 0 8px 0; color: #0066cc;">${fontanero.nombre}</h3>
                    <p style="margin: 0; color: #666; font-size: 14px;">${fontanero.especialidad}</p>
                    <div style="margin-top: 8px;">
                        <span style="color: #ff6b35;">⭐ ${fontanero.rating}</span> | 
                        <span style="color: #00b894;">✅ ${fontanero.servicios} servicios</span>
                    </div>
                    <p style="margin: 8px 0 0 0; font-size: 13px;">
                        Estado: <strong style="color: ${fontanero.disponible ? '#00b894' : '#fdcb6e'}">
                            ${fontanero.disponible ? 'Disponible' : 'Ocupado'}
                        </strong>
                    </p>
                </div>
            `
        });
        
        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });
        
        plumberMarkers.push(marker);
    });
    
    console.log('✅ Fontaneros mostrados en el mapa');
    actualizarGridFontaneros();
};

/**
 * Centra el mapa en la ubicación actual del usuario
 * Usa Geolocation API del navegador
 */
const centrarMapa = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                if (map) {
                    map.setCenter(userLocation);
                    userMarker.setPosition(userLocation);
                }
                
                console.log('📍 Ubicación actualizada:', userLocation);
                
                // Actualizar distancias
                actualizarGridFontaneros();
                
                alert('✅ Ubicación actualizada correctamente');
            },
            (error) => {
                console.error('❌ Error de geolocalización:', error);
                alert('⚠️ No se pudo obtener tu ubicación. Usando ubicación por defecto (Bogotá).');
            }
        );
    } else {
        alert('❌ Tu navegador no soporta geolocalización');
    }
};

/**
 * Calcula la distancia entre dos puntos geográficos
 * Fórmula de Haversine
 */
const calcularDistancia = (lat1, lon1, lat2, lon2) => {
    const R = 6371; // Radio de la Tierra en km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon/2) * Math.sin(dLon/2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c;
    
    return distance.toFixed(1);
};

/**
 * Actualiza el grid de fontaneros con distancias calculadas
 * Uso de MAP, FILTER y SORT (Programación Funcional)
 */
const actualizarGridFontaneros = () => {
    const grid = document.getElementById('plumbersGrid');
    
    // Calcular distancias usando MAP y ordenar con SORT
    const fontanerosConDistancia = fontaneros
        .map(f => ({
            ...f,  // Spread operator
            distancia: calcularDistancia(
                userLocation.lat, 
                userLocation.lng, 
                f.lat, 
                f.lng
            )
        }))
        .sort((a, b) => parseFloat(a.distancia) - parseFloat(b.distancia));
    
    // Renderizar tarjetas usando MAP y template strings
    grid.innerHTML = fontanerosConDistancia
        .map(f => `
            <article class="plumber-card" data-id="${f.id}">
                <div class="plumber-header">
                    <div class="plumber-avatar" aria-label="Avatar de ${f.nombre}">👷</div>
                    <div class="plumber-info">
                        <h3>${f.nombre}</h3>
                        <div class="plumber-specialty">${f.especialidad}</div>
                    </div>
                </div>
                
                <div class="plumber-stats">
                    <div class="stat">
                        <span class="stat-value">⭐ ${f.rating}</span>
                        <span class="stat-label">Rating</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value">${f.servicios}</span>
                        <span class="stat-label">Servicios</span>
                    </div>
                </div>
                
                <div class="plumber-distance">
                    📍 A ${f.distancia} km de distancia
                </div>
                
                <span class="status-badge ${f.disponible ? 'status-available' : 'status-busy'}">
                    ${f.disponible ? '✅ Disponible' : '⏰ Ocupado'}
                </span>
                
                <button 
                    class="btn btn-primary" 
                    style="width: 100%; margin-top: 1rem;" 
                    onclick="seleccionarFontanero(${f.id})"
                    ${!f.disponible ? 'disabled' : ''}
                    aria-label="Solicitar servicio a ${f.nombre}">
                    ${f.disponible ? 'Solicitar Servicio' : 'No Disponible'}
                </button>
            </article>
        `)
        .join('');
    
    console.log('✅ Grid de fontaneros actualizado');
};

/**
 * Calcula ruta hacia el fontanero más cercano
 * Abre Google Maps en nueva pestaña
 */
const calcularRuta = () => {
    // Filtrar solo fontaneros disponibles y obtener el más cercano
    const disponibles = fontaneros.filter(f => f.disponible);
    
    if (disponibles.length === 0) {
        alert('⚠️ No hay fontaneros disponibles en este momento');
        return;
    }
    
    // Calcular distancias y obtener el más cercano usando REDUCE
    const masCercano = disponibles.reduce((prev, current) => {
        const distPrev = calcularDistancia(userLocation.lat, userLocation.lng, prev.lat, prev.lng);
        const distCurrent = calcularDistancia(userLocation.lat, userLocation.lng, current.lat, current.lng);
        return parseFloat(distCurrent) < parseFloat(distPrev) ? current : prev;
    });
    
    const url = `https://www.google.com/maps/dir/?api=1&origin=${userLocation.lat},${userLocation.lng}&destination=${masCercano.lat},${masCercano.lng}&travelmode=driving`;
    
    console.log('🚗 Calculando ruta hacia:', masCercano.nombre);
    window.open(url, '_blank');
};

/**
 * Selecciona un fontanero específico
 */
const seleccionarFontanero = (id) => {
    const fontanero = fontaneros.find(f => f.id === id);
    
    if (!fontanero) {
        alert('❌ Fontanero no encontrado');
        return;
    }
    
    const distancia = calcularDistancia(
        userLocation.lat, userLocation.lng,
        fontanero.lat, fontanero.lng
    );
    
    const tiempoEstimado = Math.ceil(parseFloat(distancia) * 3);
    
    const mensaje = `✅ Fontanero seleccionado\n\n` +
                   `👷 Nombre: ${fontanero.nombre}\n` +
                   `🔧 Especialidad: ${fontanero.especialidad}\n` +
                   `⭐ Rating: ${fontanero.rating} / 5.0\n` +
                   `📍 Distancia: ${distancia} km\n` +
                   `⏱️ Tiempo estimado: ${tiempoEstimado} minutos\n\n` +
                   `Se enviará una notificación con los detalles del servicio.`;
    
    alert(mensaje);
    
    // Scroll al formulario de notificaciones
    document.getElementById('notificaciones').scrollIntoView({ 
        behavior: 'smooth' 
    });
};

// ==================== MASHUP 3: EMAILJS API ====================

/**
 * Envía notificación por email
 * API: EmailJS (simulado en esta versión)
 */
const enviarNotificacion = async (e) => {
    e.preventDefault();
    
    // Obtener datos del formulario
    const formData = {
        user_email: document.getElementById('userEmail').value,
        user_name: document.getElementById('userName').value,
        user_phone: document.getElementById('userPhone').value,
        service_type: document.getElementById('serviceType').value,
        message: document.getElementById('message').value,
        fecha: new Date().toLocaleString('es-CO', {
            dateStyle: 'full',
            timeStyle: 'short'
        }),
        ciudad: 'Bogotá, Colombia'
    };
    
    console.log('📧 Enviando notificación...', formData);
    
    try {
        // Simular envío de email (en producción usar EmailJS real)
        await simulateEmailSend(formData);
        
        // Mostrar alerta de éxito
        const mensaje = `✅ Notificación enviada exitosamente!\n\n` +
                       `📧 Email: ${formData.user_email}\n` +
                       `🔧 Servicio: ${formData.service_type}\n\n` +
                       `Te contactaremos en los próximos 15 minutos.`;
        
        mostrarAlerta('success', mensaje);
        
        // Limpiar formulario
        document.getElementById('notificationForm').reset();
        
        console.log('✅ Notificación enviada correctamente');
        
    } catch (error) {
        mostrarAlerta('error', '❌ Error al enviar la notificación. Por favor intenta nuevamente.');
        console.error('❌ Error al enviar:', error);
    }
};

/**
 * Simulación de envío de email
 * En producción, usar EmailJS real
 */
const simulateEmailSend = (data) => {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            // Simular respuesta exitosa
            const emailContent = `
═══════════════════════════════════════════════════
📧 NOTIFICACIÓN DE SERVICIO - AQUAFIX PRO
═══════════════════════════════════════════════════

Estimado/a ${data.user_name},

Hemos recibido tu solicitud de servicio:

🔧 Servicio solicitado: ${data.service_type}
📅 Fecha y hora: ${data.fecha}
📍 Ubicación: ${data.ciudad}
📞 Teléfono: ${data.user_phone}

💬 Tu mensaje:
"${data.message}"

✅ Un fontanero certificado de AquaFix Pro se pondrá 
en contacto contigo en los próximos 15 minutos.

Gracias por confiar en nosotros.

═══════════════════════════════════════════════════
AquaFix Pro | Servicio Profesional 24/7
📞 +57 (1) 555-0100 | 🌐 www.aquafixpro.com
═══════════════════════════════════════════════════
            `;
            
            console.log(emailContent);
            resolve({ status: 200, text: 'OK' });
        }, 1500);
    });
};

/**
 * Muestra alertas de éxito o error
 */
const mostrarAlerta = (tipo, mensaje) => {
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');
    
    // Ocultar ambas alertas
    successAlert.classList.remove('show');
    errorAlert.classList.remove('show');
    
    // Mostrar la alerta correspondiente
    if (tipo === 'success') {
        successAlert.textContent = mensaje;
        successAlert.classList.add('show');
        
        // Ocultar después de 8 segundos
        setTimeout(() => {
            successAlert.classList.remove('show');
        }, 8000);
    } else {
        errorAlert.textContent = mensaje;
        errorAlert.classList.add('show');
        
        setTimeout(() => {
            errorAlert.classList.remove('show');
        }, 8000);
    }
};

// ==================== PROGRAMACIÓN FUNCIONAL: ESTADÍSTICAS ====================

/**
 * Obtiene fontaneros disponibles usando FILTER
 */
const obtenerFontanerosDisponibles = () => {
    return fontaneros.filter(f => f.disponible);
};

/**
 * Obtiene mejores fontaneros por rating usando FILTER y SORT
 */
const obtenerMejoresFontaneros = (minRating = 4.5) => {
    return fontaneros
        .filter(f => f.rating >= minRating && f.disponible)
        .sort((a, b) => b.rating - a.rating);
};

/**
 * Calcula estadísticas usando REDUCE
 */
const calcularEstadisticasFontaneros = () => {
    const stats = fontaneros.reduce((acc, f) => {
        acc.totalServicios += f.servicios;
        acc.sumaRatings += f.rating;
        acc.disponibles += f.disponible ? 1 : 0;
        return acc;
    }, { 
        totalServicios: 0, 
        sumaRatings: 0, 
        disponibles: 0 
    });
    
    stats.ratingPromedio = (stats.sumaRatings / fontaneros.length).toFixed(2);
    stats.total = fontaneros.length;
    
    return stats;
};

/**
 * Obtiene resumen de fontaneros más cercanos
 * Uso de FILTER, MAP, SORT y SLICE (Function Chaining)
 */
const obtenerResumenFontaneros = () => {
    return fontaneros
        .filter(f => f.disponible)
        .map(f => ({
            nombre: f.nombre,
            rating: f.rating,
            especialidad: f.especialidad,
            distancia: calcularDistancia(
                userLocation.lat, 
                userLocation.lng, 
                f.lat, 
                f.lng
            )
        }))
        .sort((a, b) => parseFloat(a.distancia) - parseFloat(b.distancia))
        .slice(0, 3); // Top 3 más cercanos
};

/**
 * Procesa solicitud completa integrando los 3 mashups
 * Combina: Geolocalización + Clima + Notificación
 */
const procesarSolicitudCompleta = async (fontaneroId) => {
    try {
        // 1. Obtener fontanero
        const fontanero = fontaneros.find(f => f.id === fontaneroId);
        if (!fontanero) throw new Error('Fontanero no encontrado');
        
        // 2. Calcular distancia
        const distancia = calcularDistancia(
            userLocation.lat, userLocation.lng,
            fontanero.lat, fontanero.lng
        );
        
        // 3. Obtener clima actual
        const climaResponse = await fetch(
            `https://api.openweathermap.org/data/2.5/weather?lat=${userLocation.lat}&lon=${userLocation.lng}&appid=bd5e378503939ddaee76f12ad7a97608&units=metric&lang=es`
        );
        const climaData = await climaResponse.json();
        
        // 4. Preparar solicitud completa
        const solicitud = {
            fontanero: fontanero.nombre,
            especialidad: fontanero.especialidad,
            distancia: distancia + ' km',
            tiempoEstimado: Math.ceil(parseFloat(distancia) * 3) + ' minutos',
            clima: climaData.weather[0].description,
            temperatura: Math.round(climaData.main.temp) + '°C',
            fecha: new Date().toLocaleString('es-CO')
        };
        
        console.log('═══════════════════════════════════════');
        console.log('📋 SOLICITUD COMPLETA - MASHUPS INTEGRADOS');
        console.log('═══════════════════════════════════════');
        console.log('👷 Fontanero:', solicitud.fontanero);
        console.log('🔧 Especialidad:', solicitud.especialidad);
        console.log('📍 Distancia:', solicitud.distancia);
        console.log('⏱️ Tiempo estimado:', solicitud.tiempoEstimado);
        console.log('☁️ Clima actual:', solicitud.clima);
        console.log('🌡️ Temperatura:', solicitud.temperatura);
        console.log('📅 Fecha:', solicitud.fecha);
        console.log('═══════════════════════════════════════');
        
        return solicitud;
        
    } catch (error) {
        console.error('❌ Error al procesar solicitud:', error);
        throw error;
    }
};

// ==================== INICIALIZACIÓN ====================

/**
 * Inicializa la aplicación al cargar el DOM
 */
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Iniciando AquaFix Pro...');
    
    // 1. Obtener clima inicial
    obtenerClima();
    
    // 2. Inicializar mapa (o mostrar simulado)
    initMap();
    
    // 3. Mostrar fontaneros en el grid
    actualizarGridFontaneros();
    
    // 4. Event listener para formulario de notificaciones
    const form = document.getElementById('notificationForm');
    form.addEventListener('submit', enviarNotificacion);
    
    // 5. Event listener para menú móvil
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    menuToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
    
    // Cerrar menú al hacer click en un enlace
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
        });
    });
    
    // 6. Actualizar clima cada 10 minutos
    setInterval(obtenerClima, 600000);
    
    // 7. Mostrar estadísticas en consola
    console.log('📊 Estadísticas:', calcularEstadisticasFontaneros());
    console.log('⭐ Mejores fontaneros:', obtenerMejoresFontaneros());
    console.log('📍 Top 3 más cercanos:', obtenerResumenFontaneros());
    
    console.log('✅ AquaFix Pro cargado correctamente');
});