/**
 * Redirigir filtros de página principal al Cotizador de Eventos
 * En lugar de filtrar con AJAX, redirige a /cotizador-de-eventos/ con parámetros
 */

(function() {
    'use strict';
    
    // Solo ejecutar en página Airbnb
    if (!document.body.classList.contains('page-template-airbnb')) {
        return;
    }
    
    console.log('✅ Sistema de redirección al Cotizador activado');
    
    // Esperar a que el DOM esté listo
    function initRedirectSystem() {
        // Obtener elementos
        const searchInput = document.getElementById('mrb-service-search');
        const locationSelect = document.getElementById('mrb-location-search');
        const dateInput = document.getElementById('mrb-date-search');
        const searchButton = document.querySelector('.mrb-search-button');
        const categoryItems = document.querySelectorAll('.mrb-category-item');
        
        // Variable para guardar la categoría seleccionada
        let selectedCategory = '';
        
        // Manejar click en categorías
        categoryItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Obtener categoría
                const category = this.dataset.category || '';
                
                // Si es "all" o la misma, limpiar selección
                if (category === 'all' || category === selectedCategory) {
                    selectedCategory = '';
                } else {
                    selectedCategory = category;
                }
                
                // Actualizar visual
                categoryItems.forEach(cat => cat.classList.remove('active'));
                if (selectedCategory) {
                    this.classList.add('active');
                } else {
                    // Activar "Todos"
                    document.querySelector('[data-category="all"]')?.classList.add('active');
                }
                
                console.log('Categoría seleccionada:', selectedCategory || 'todas');
            });
        });
        
        // Función para construir URL del Cotizador
        function buildCotizadorURL() {
            const params = new URLSearchParams();
            
            // Añadir parámetros si tienen valor
            const searchTerm = searchInput?.value.trim();
            const location = locationSelect?.value;
            const date = dateInput?.value;
            
            // Búsqueda por nombre
            if (searchTerm) {
                params.set('nombre', searchTerm);
            }
            
            // Categoría (desde iconos)
            if (selectedCategory && selectedCategory !== 'all') {
                params.set('categoria', selectedCategory);
            }
            
            // Ciudad - obtener el texto del option seleccionado, no el value (slug)
            if (location && locationSelect) {
                const selectedOption = locationSelect.options[locationSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    // Obtener el texto limpio sin el contador de listings
                    const cityText = selectedOption.textContent.replace(/\s*\(\d+\s+listings?\)/, '').trim();
                    params.set('ciudad', cityText);
                }
            }
            
            // Fecha
            if (date) {
                params.set('fecha', date);
            }
            
            // Construir URL completa
            const baseURL = '/cotizador-de-eventos/';
            const queryString = params.toString();
            
            return queryString ? `${baseURL}?${queryString}` : baseURL;
        }
        
        // Manejar click en botón de búsqueda
        if (searchButton) {
            searchButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const url = buildCotizadorURL();
                console.log('🚀 Redirigiendo a:', url);
                
                // Redirigir
                window.location.href = url;
            });
        }
        
        // Manejar Enter en campo de búsqueda
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const url = buildCotizadorURL();
                    console.log('🚀 Redirigiendo a:', url);
                    window.location.href = url;
                }
            });
        }
        
        console.log('✅ Event listeners configurados para redirección');
    }
    
    // Inicializar cuando DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRedirectSystem);
    } else {
        setTimeout(initRedirectSystem, 100);
    }
    
})();