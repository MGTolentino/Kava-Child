/**
 * Sistema de Filtros AJAX
 * Maneja búsqueda y filtros de categorías/ubicación
 */

(function($) {
    'use strict';
    
    let currentFilters = {
        category: 'all',
        location: '',
        search: '',
        date: '',
        page: 1
    };
    
    let isLoading = false;
    
    // Inicializar cuando DOM esté listo
    $(document).ready(function() {
        initializeFilters();
        setupEventListeners();
        updateCategoryLinks();
    });
    
    /**
     * Inicializar filtros desde URL si existen
     */
    function initializeFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('categoria')) {
            currentFilters.category = urlParams.get('categoria');
        }
        if (urlParams.has('ubicacion')) {
            currentFilters.location = urlParams.get('ubicacion');
        }
        if (urlParams.has('buscar')) {
            currentFilters.search = urlParams.get('buscar');
        }
    }
    
    /**
     * Configurar event listeners
     */
    function setupEventListeners() {
        // Búsqueda principal
        $('#mrb-service-search').on('keyup', debounce(function() {
            currentFilters.search = $(this).val();
            currentFilters.page = 1;
            filterListings();
        }, 500));
        
        // Ubicación
        $('#mrb-location-search').on('change', function() {
            currentFilters.location = $(this).val();
            currentFilters.page = 1;
            filterListings();
        });
        
        // Fecha
        $('#mrb-date-search').on('change', function() {
            currentFilters.date = $(this).val();
            currentFilters.page = 1;
            filterListings();
        });
        
        // Botón buscar
        $(document).on('click', '.mrb-search-button', function(e) {
            e.preventDefault();
            filterListings();
        });
    }
    
    /**
     * Actualizar links de categorías para usar AJAX
     */
    function updateCategoryLinks() {
        $('.mrb-category-item').each(function() {
            const $item = $(this);
            
            // Remover onclick anterior
            $item.removeAttr('onclick');
            
            // Añadir nuevo evento
            $item.on('click', function(e) {
                e.preventDefault();
                
                // Obtener slug de la categoría
                let categorySlug = 'all';
                const onclickAttr = $(this).attr('onclick');
                
                if (onclickAttr) {
                    const match = onclickAttr.match(/filterByCategory\('([^']+)'\)/);
                    if (match) {
                        categorySlug = match[1];
                    }
                }
                
                // Actualizar estado activo
                $('.mrb-category-item').removeClass('active');
                $(this).addClass('active');
                
                // Filtrar
                currentFilters.category = categorySlug;
                currentFilters.page = 1;
                filterListings();
                
                // Actualizar URL sin recargar
                updateURL();
            });
        });
    }
    
    /**
     * Filtrar listings via AJAX
     */
    function filterListings() {
        if (isLoading) return;
        
        isLoading = true;
        
        // Mostrar loading
        const $grid = $('#listings-grid');
        $grid.css('opacity', '0.5');
        
        // Hacer request AJAX
        $.ajax({
            url: mrb_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mrb_filter_listings',
                nonce: mrb_ajax.nonce,
                category: currentFilters.category,
                location: currentFilters.location,
                search: currentFilters.search,
                date: currentFilters.date,
                paged: currentFilters.page
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar grid con nuevos resultados
                    $grid.html(response.data.html);
                    
                    // Fade in suave
                    $grid.css('opacity', '1');
                    
                    // Actualizar contador si existe
                    updateResultsCount(response.data.found_posts);
                    
                    // Reinicializar funciones de cards (favoritos, etc)
                    if (typeof initializeCardFeatures === 'function') {
                        initializeCardFeatures();
                    }
                    
                    // Scroll suave al top de listings
                    $('html, body').animate({
                        scrollTop: $('.mrb-listings').offset().top - 100
                    }, 500);
                }
            },
            error: function() {
                console.error('Error al filtrar listings');
                $grid.css('opacity', '1');
            },
            complete: function() {
                isLoading = false;
            }
        });
    }
    
    /**
     * Actualizar URL sin recargar página
     */
    function updateURL() {
        const params = new URLSearchParams();
        
        if (currentFilters.category && currentFilters.category !== 'all') {
            params.set('categoria', currentFilters.category);
        }
        if (currentFilters.location) {
            params.set('ubicacion', currentFilters.location);
        }
        if (currentFilters.search) {
            params.set('buscar', currentFilters.search);
        }
        if (currentFilters.date) {
            params.set('fecha', currentFilters.date);
        }
        
        const newURL = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({filters: currentFilters}, '', newURL);
    }
    
    /**
     * Actualizar contador de resultados
     */
    function updateResultsCount(count) {
        const $counter = $('.mrb-results-count');
        if ($counter.length) {
            $counter.text(count + ' servicios encontrados');
        }
    }
    
    /**
     * Función debounce para optimizar búsqueda
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    /**
     * Manejar botón atrás del navegador
     */
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.filters) {
            currentFilters = e.state.filters;
            
            // Actualizar UI
            $('#mrb-service-search').val(currentFilters.search);
            $('#mrb-location-search').val(currentFilters.location);
            $('#mrb-date-search').val(currentFilters.date);
            
            // Actualizar categoría activa
            $('.mrb-category-item').removeClass('active');
            if (currentFilters.category === 'all') {
                $('.mrb-category-item:first').addClass('active');
            } else {
                $('.mrb-category-item').each(function() {
                    const onclick = $(this).attr('onclick');
                    if (onclick && onclick.includes(currentFilters.category)) {
                        $(this).addClass('active');
                    }
                });
            }
            
            // Recargar listings
            filterListings();
        }
    });
    
})(jQuery);

// Hacer funciones disponibles globalmente si es necesario
window.filterByCategory = function(slug) {
    // Esta función ahora es manejada por jQuery arriba
    console.log('filterByCategory llamada con:', slug);
};