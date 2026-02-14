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
        
        // Función para guardar filtros en localStorage y redirigir
        function saveFiltersAndRedirect() {
            // Obtener valores actuales
            const searchTerm = searchInput?.value.trim();
            const location = locationSelect?.value;
            const date = dateInput?.value;
            
            // Limpiar localStorage anterior de Airbnb (evitar datos viejos)
            localStorage.removeItem('airbnb_search_service');
            localStorage.removeItem('airbnb_search_category');
            localStorage.removeItem('airbnb_search_location');
            localStorage.removeItem('airbnb_search_date');
            
            // Guardar solo los valores que tienen contenido
            if (searchTerm) {
                localStorage.setItem('airbnb_search_service', searchTerm);
                console.log('💾 Guardado servicio:', searchTerm);
            }
            
            if (selectedCategory && selectedCategory !== 'all') {
                localStorage.setItem('airbnb_search_category', selectedCategory);
                console.log('💾 Guardado categoría:', selectedCategory);
            }
            
            if (location && locationSelect) {
                const selectedOption = locationSelect.options[locationSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    // Obtener el texto limpio sin el contador de listings
                    const cityText = selectedOption.textContent.replace(/\s*\(\d+\s+listings?\)/, '').trim();
                    localStorage.setItem('airbnb_search_location', cityText);
                    console.log('💾 Guardado ciudad:', cityText);
                }
            }
            
            if (date) {
                localStorage.setItem('airbnb_search_date', date);
                console.log('💾 Guardado fecha:', date);
            }
            
            // Añadir timestamp para saber que viene de Airbnb
            localStorage.setItem('airbnb_search_timestamp', Date.now().toString());
            
            // Redirigir al cotizador (URL limpia)
            return '/cotizador-de-eventos/';
        }
        
        // Manejar click en botón de búsqueda
        if (searchButton) {
            searchButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const url = saveFiltersAndRedirect();
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
                    const url = saveFiltersAndRedirect();
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