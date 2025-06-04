(function($) {
    'use strict';

    // Variable para controlar si el mapa ya se inicializó
    let mapInitialized = false;

    // Función principal de inicialización
    function initializeMap() {
        if (mapInitialized) return;
        
        const mapContainer = document.querySelector('.bv-venue-map');
        const addressElement = document.querySelector('.bv-venue-address');

        if (!mapContainer || !addressElement) return;

        const location = addressElement.textContent.trim();
        if (!location) return;

        // Esperar a que Google Maps esté completamente cargado
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            setTimeout(initializeMap, 500);
            return;
        }

        mapInitialized = true;

        const geocoder = new google.maps.Geocoder();
        
        geocoder.geocode({ address: location }, function(results, status) {
            if (status === 'OK') {
                const mapOptions = {
						center: results[0].geometry.location,
						zoom: 15,
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						// Deshabilitar todos los controles
						zoomControl: false,
						mapTypeControl: false,
						scaleControl: false,
						streetViewControl: false,
						rotateControl: false,
						fullscreenControl: false,
						// Deshabilitar interacciones
						draggable: false,
						scrollwheel: false,
						disableDoubleClickZoom: true,
						keyboardShortcuts: false,
						gestureHandling: 'none'
					};

                const map = new google.maps.Map(mapContainer, mapOptions);
                
               const marker = new google.maps.Marker({
						position: results[0].geometry.location,
						map: map,
						title: location
					});
            } else {
                console.error('Geocoding failed:', status);
                mapContainer.innerHTML = '<div class="bv-map-error">No se pudo cargar la ubicación en el mapa.</div>';
            }
        });
    }

    // Iniciar cuando el documento esté listo
    $(document).ready(function() {
        // Intentar inicializar después de un breve retraso
        setTimeout(initializeMap, 1000);
    });

    // Backup: intentar cuando la ventana esté completamente cargada
    $(window).on('load', initializeMap);

})(jQuery);