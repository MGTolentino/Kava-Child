/**
 * Sistema de Filtros Combinados estilo Airbnb
 * Todos los filtros trabajan juntos para actualizar los listings
 */

(function() {
    // Solo ejecutar en página Airbnb
    if (!document.body.classList.contains('page-template-airbnb')) {
        console.log('❌ No es página Airbnb - filtros no cargados');
        return;
    }

    console.log('✅ Sistema de filtros Airbnb iniciando...');
    'use strict';
    
    // Estado actual de los filtros
    let currentFilters = {
        search: '',      // Búsqueda por nombre o categoría
        category: '',    // Categoría seleccionada
        location: '',    // Ciudad/ubicación
        date: ''        // Fecha del evento
    };
    
    // Cache de elementos DOM
    const elements = {
        searchInput: null,
        locationSelect: null,
        dateInput: null,
        searchButton: null,
        categoryItems: null,
        listingsGrid: null,
        listingsContainer: null
    };
    
    /**
     * Inicializar el sistema de filtros
     */
    function initFilters() {
        // Cachear elementos DOM
        elements.searchInput = document.getElementById('mrb-service-search');
        elements.locationSelect = document.getElementById('mrb-location-select');
        elements.dateInput = document.getElementById('mrb-event-date');
        elements.searchButton = document.querySelector('.mrb-search-button');
        elements.categoryItems = document.querySelectorAll('.mrb-category-item');
        elements.listingsGrid = document.querySelector('.mrb-listings-grid');
        elements.listingsContainer = document.querySelector('.mrb-listings-container');
        
        if (!elements.listingsGrid) {
            console.warn('⚠️ Grid de listings no encontrado');
            return;
        }
        
        // Configurar event listeners
        setupEventListeners();
        
        // Leer parámetros URL iniciales
        loadFiltersFromURL();
        
        console.log('✅ Sistema de filtros inicializado');
    }
    
    /**
     * Configurar todos los event listeners
     */
    function setupEventListeners() {
        // Búsqueda por texto (con debounce)
        if (elements.searchInput) {
            let searchTimeout;
            elements.searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    currentFilters.search = this.value.trim();
                    applyFilters();
                }, 500); // Esperar 500ms después de que el usuario deje de escribir
            });
            
            // Enter key
            elements.searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    currentFilters.search = this.value.trim();
                    applyFilters();
                }
            });
        }
        
        // Selector de ubicación
        if (elements.locationSelect) {
            elements.locationSelect.addEventListener('change', function() {
                currentFilters.location = this.value;
                applyFilters();
            });
        }
        
        // Selector de fecha
        if (elements.dateInput) {
            elements.dateInput.addEventListener('change', function() {
                currentFilters.date = this.value;
                applyFilters();
            });
        }
        
        // Botón de búsqueda
        if (elements.searchButton) {
            elements.searchButton.addEventListener('click', function(e) {
                e.preventDefault();
                applyFilters();
            });
        }
        
        // Categorías
        elements.categoryItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Obtener slug de la categoría
                const categorySlug = this.dataset.category || '';
                
                // Si es la misma categoría, deseleccionar
                if (currentFilters.category === categorySlug) {
                    currentFilters.category = '';
                    this.classList.remove('active');
                } else {
                    // Remover active de todas las categorías
                    elements.categoryItems.forEach(cat => cat.classList.remove('active'));
                    
                    // Activar la nueva categoría
                    currentFilters.category = categorySlug;
                    this.classList.add('active');
                }
                
                // Aplicar filtros
                applyFilters();
            });
        });
        
        // Navegador back/forward
        window.addEventListener('popstate', function(e) {
            loadFiltersFromURL();
            updateUI();
            fetchFilteredListings();
        });
    }
    
    /**
     * Aplicar todos los filtros activos
     */
    function applyFilters() {
        console.log('🔍 Aplicando filtros:', currentFilters);
        
        // Actualizar URL sin recargar
        updateURL();
        
        // Mostrar loading
        showLoading();
        
        // Hacer petición AJAX
        fetchFilteredListings();
    }
    
    /**
     * Obtener listings filtrados vía AJAX
     */
    function fetchFilteredListings() {
        // Preparar datos para enviar
        const formData = new FormData();
        formData.append('action', 'mrb_filter_listings');
        formData.append('nonce', mrb_ajax_obj?.nonce || '');
        
        // Añadir filtros activos
        if (currentFilters.search) {
            formData.append('search', currentFilters.search);
        }
        if (currentFilters.category && currentFilters.category !== 'all') {
            formData.append('category', currentFilters.category);
        }
        if (currentFilters.location) {
            formData.append('location', currentFilters.location);
        }
        if (currentFilters.date) {
            formData.append('date', currentFilters.date);
        }
        
        // URL del admin-ajax
        const ajaxUrl = mrb_ajax_obj?.ajax_url || '/wp-admin/admin-ajax.php';
        
        // Hacer petición
        fetch(ajaxUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            console.log('📦 Respuesta del servidor:', data);
            
            if (data.success && data.data) {
                updateListingsGrid(data.data);
            } else {
                console.error('❌ Error en respuesta:', data);
                showNoResults();
            }
        })
        .catch(error => {
            console.error('❌ Error AJAX:', error);
            hideLoading();
        });
    }
    
    /**
     * Actualizar el grid de listings con los resultados
     */
    function updateListingsGrid(html) {
        if (!elements.listingsGrid) return;
        
        // Fade out actual
        elements.listingsGrid.style.opacity = '0.5';
        
        setTimeout(() => {
            // Actualizar contenido
            elements.listingsGrid.innerHTML = html;
            
            // Fade in nuevo contenido
            elements.listingsGrid.style.opacity = '1';
            
            // Re-aplicar event listeners a las nuevas cards
            reinitializeCards();
            
            hideLoading();
        }, 200);
    }
    
    /**
     * Re-inicializar event listeners en nuevas cards
     */
    function reinitializeCards() {
        const newCards = document.querySelectorAll('.mrb-listing-card');
        
        newCards.forEach(card => {
            card.addEventListener('click', function(e) {
                // No abrir si es favorito
                if (e.target.closest('.mrb-card-favorite')) {
                    return;
                }
                
                // Obtener URL
                const url = this.dataset.url || '/listing/' + this.dataset.listingId + '/';
                
                if (url) {
                    window.open(url, '_blank');
                }
            });
        });
        
        console.log('✅ Re-inicializadas', newCards.length, 'cards');
    }
    
    /**
     * Actualizar URL sin recargar página
     */
    function updateURL() {
        const params = new URLSearchParams();
        
        // Solo añadir parámetros con valor
        if (currentFilters.search) {
            params.set('buscar', currentFilters.search);
        }
        if (currentFilters.category && currentFilters.category !== 'all') {
            params.set('categoria', currentFilters.category);
        }
        if (currentFilters.location) {
            params.set('ciudad', currentFilters.location);
        }
        if (currentFilters.date) {
            params.set('fecha', currentFilters.date);
        }
        
        // Construir nueva URL
        const newURL = params.toString() 
            ? window.location.pathname + '?' + params.toString()
            : window.location.pathname;
        
        // Actualizar sin recargar
        window.history.pushState({filters: currentFilters}, '', newURL);
    }
    
    /**
     * Cargar filtros desde URL
     */
    function loadFiltersFromURL() {
        const params = new URLSearchParams(window.location.search);
        
        currentFilters.search = params.get('buscar') || '';
        currentFilters.category = params.get('categoria') || '';
        currentFilters.location = params.get('ciudad') || '';
        currentFilters.date = params.get('fecha') || '';
        
        console.log('📍 Filtros desde URL:', currentFilters);
    }
    
    /**
     * Actualizar UI con filtros actuales
     */
    function updateUI() {
        // Actualizar campos
        if (elements.searchInput) {
            elements.searchInput.value = currentFilters.search;
        }
        if (elements.locationSelect) {
            elements.locationSelect.value = currentFilters.location;
        }
        if (elements.dateInput) {
            elements.dateInput.value = currentFilters.date;
        }
        
        // Actualizar categoría activa
        elements.categoryItems.forEach(item => {
            if (item.dataset.category === currentFilters.category) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }
    
    /**
     * Mostrar indicador de carga
     */
    function showLoading() {
        if (!elements.listingsGrid) return;
        
        // Añadir clase loading
        elements.listingsGrid.classList.add('loading');
        
        // Opcional: añadir spinner
        const existingLoader = document.querySelector('.mrb-loader');
        if (!existingLoader) {
            const loader = document.createElement('div');
            loader.className = 'mrb-loader';
            loader.innerHTML = `
                <div class="mrb-spinner">
                    <svg width="24" height="24" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" stroke-dasharray="31.4" stroke-dashoffset="10"/>
                    </svg>
                </div>
            `;
            elements.listingsContainer?.appendChild(loader);
        }
    }
    
    /**
     * Ocultar indicador de carga
     */
    function hideLoading() {
        if (!elements.listingsGrid) return;
        
        elements.listingsGrid.classList.remove('loading');
        
        // Remover spinner
        const loader = document.querySelector('.mrb-loader');
        if (loader) {
            loader.remove();
        }
    }
    
    /**
     * Mostrar mensaje de sin resultados
     */
    function showNoResults() {
        if (!elements.listingsGrid) return;
        
        elements.listingsGrid.innerHTML = `
            <div class="mrb-no-results">
                <div class="mrb-no-results-icon">🔍</div>
                <h3>No se encontraron servicios</h3>
                <p>Intenta ajustar los filtros o buscar algo diferente</p>
            </div>
        `;
        
        hideLoading();
    }
    
    // Inicializar cuando DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFilters);
    } else {
        // DOM ya está listo
        setTimeout(initFilters, 100);
    }
    
})();

// CSS para loading y no results
const style = document.createElement('style');
style.textContent = `
    .mrb-listings-grid.loading {
        position: relative;
        min-height: 400px;
    }
    
    .mrb-loader {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 100;
    }
    
    .mrb-spinner svg {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .mrb-no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
    }
    
    .mrb-no-results-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
    
    .mrb-no-results h3 {
        font-size: 22px;
        margin-bottom: 8px;
        color: var(--mrb-text-primary);
    }
    
    .mrb-no-results p {
        color: var(--mrb-text-secondary);
        font-size: 14px;
    }
`;
document.head.appendChild(style);