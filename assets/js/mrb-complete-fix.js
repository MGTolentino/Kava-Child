/**
 * 🚀 COMPLETE FIX - Solución completa para todos los problemas
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 MRB Complete Fix iniciado');
    
    fixCategories();
    fixListingLinks();
    setupFavorites();
    fixSearch();
});

// ========================================
// 1. ARREGLAR FILTROS DE CATEGORÍAS
// ========================================
function fixCategories() {
    const categories = document.querySelectorAll('.mrb-category-item');
    
    // Mapeo de slugs a URLs reales de HivePress
    const categoryRoutes = {
        'fotografia': '/listing-category/fotografia/',
        'musica': '/listing-category/musica/',
        'alimentos-y-bebidas': '/listing-category/alimentos-y-bebidas/',
        'decoracion': '/listing-category/decoracion/',
        'entretenimiento': '/listing-category/entretenimiento/',
        'joyeria': '/listing-category/joyeria/',
        'mobiliario': '/listing-category/mobiliario/',
        'transporte': '/listing-category/transporte/',
        'hospedaje': '/listing-category/hospedaje/',
        'vestidos': '/listing-category/vestidos/',
        'planners': '/listing-category/planners/',
        'viajes': '/listing-category/viajes/',
        'lugares-para-eventos': '/listing-category/lugares-para-eventos/',
        'artistas': '/listing-category/artistas/',
        'comediantes': '/listing-category/comediantes/',
        'paquetes-todo-incluido': '/listing-category/paquetes-todo-incluido/',
        'pirotecnia-y-efectos-especiales': '/listing-category/pirotecnia-y-efectos-especiales/',
        'toldos-y-carpas': '/listing-category/toldos-y-carpas/'
    };
    
    categories.forEach(category => {
        // Limpiar eventos anteriores
        const newCategory = category.cloneNode(true);
        category.parentNode.replaceChild(newCategory, category);
        
        newCategory.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Obtener slug del onclick original
            const onclickStr = this.getAttribute('onclick');
            let slug = 'all';
            
            if (onclickStr) {
                const match = onclickStr.match(/filterByCategory\('([^']+)'\)/);
                if (match) slug = match[1];
            }
            
            console.log('Categoría clickeada:', slug);
            
            // Actualizar visual
            document.querySelectorAll('.mrb-category-item').forEach(item => {
                item.classList.remove('active');
            });
            this.classList.add('active');
            
            if (slug === 'all') {
                // Mostrar todos - recargar página principal
                window.location.href = '/inicio-airbnb/';
            } else if (categoryRoutes[slug]) {
                // Ir a la página de categoría
                window.location.href = categoryRoutes[slug];
            } else {
                // Filtrar via AJAX como fallback
                filterByAjax(slug);
            }
        });
    });
}

// ========================================
// 2. ABRIR LISTINGS EN NUEVA PESTAÑA
// ========================================
function fixListingLinks() {
    const cards = document.querySelectorAll('.mrb-listing-card');
    
    cards.forEach(card => {
        // Remover onclick anterior
        card.removeAttribute('onclick');
        
        // Añadir nuevo evento
        card.addEventListener('click', function(e) {
            // No abrir si se hace click en favorito o navegación
            if (e.target.closest('.mrb-card-favorite') || 
                e.target.closest('.mrb-card-nav')) {
                return;
            }
            
            const url = this.querySelector('a')?.href || 
                       this.dataset.url || 
                       getListingUrl(this);
            
            if (url) {
                window.open(url, '_blank');
            }
        });
        
        // Estilo de cursor
        card.style.cursor = 'pointer';
    });
}

// Helper para obtener URL del listing
function getListingUrl(card) {
    // Buscar en el onclick original
    const onclick = card.getAttribute('onclick');
    if (onclick) {
        const match = onclick.match(/window\.location\.href='([^']+)'/);
        if (match) return match[1];
    }
    return null;
}

// ========================================
// 3. SISTEMA DE FAVORITOS MEJORADO
// ========================================
function setupFavorites() {
    // Añadir contador al header si no existe
    if (!document.getElementById('favorites-link')) {
        const headerNav = document.querySelector('.mrb-header-nav') || 
                         document.querySelector('.mrb-user-menu');
        
        if (headerNav) {
            const favLink = document.createElement('a');
            favLink.id = 'favorites-link';
            favLink.href = '#';
            favLink.className = 'mrb-favorites-badge';
            favLink.innerHTML = `
                <svg viewBox="0 0 32 32" style="width: 20px; height: 20px; fill: #FF385C;">
                    <path d="m16 28c7-4.733 14-10 14-17 0-1.792-.683-3.583-2.05-4.95-1.367-1.366-3.158-2.05-4.95-2.05-1.791 0-3.583.684-4.949 2.05l-2.051 2.051-2.05-2.051c-1.367-1.366-3.158-2.05-4.95-2.05-1.791 0-3.583.684-4.949 2.05-1.367 1.367-2.051 3.158-2.051 4.95 0 7 7 12.267 14 17z"></path>
                </svg>
                <span class="mrb-favorites-count">0</span>
            `;
            
            favLink.addEventListener('click', function(e) {
                e.preventDefault();
                showFavoritesModal();
            });
            
            headerNav.parentNode.insertBefore(favLink, headerNav);
        }
    }
    
    updateFavoritesCount();
}

// Actualizar contador de favoritos
function updateFavoritesCount() {
    const favorites = JSON.parse(localStorage.getItem('mrb_favorites') || '[]');
    const counter = document.querySelector('.mrb-favorites-count');
    
    if (counter) {
        counter.textContent = favorites.length;
        counter.style.display = favorites.length > 0 ? 'inline-block' : 'none';
    }
}

// Mostrar modal de favoritos
function showFavoritesModal() {
    const favorites = JSON.parse(localStorage.getItem('mrb_favorites') || '[]');
    
    // Crear modal si no existe
    let modal = document.getElementById('favorites-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'favorites-modal';
        modal.className = 'mrb-favorites-modal';
        document.body.appendChild(modal);
    }
    
    // Contenido del modal
    modal.innerHTML = `
        <div class="mrb-favorites-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="font-size: 24px; font-weight: 600;">Mis Favoritos (${favorites.length})</h2>
                <button onclick="closeFavoritesModal()" style="background: none; border: none; font-size: 32px; cursor: pointer;">&times;</button>
            </div>
            <div id="favorites-grid" class="mrb-listings-grid" style="max-width: 1200px;">
                ${favorites.length === 0 ? 
                    '<p style="text-align: center; color: #717171;">No tienes favoritos guardados aún</p>' : 
                    '<p style="text-align: center;">Cargando favoritos...</p>'
                }
            </div>
        </div>
    `;
    
    modal.classList.add('active');
    
    // Cargar favoritos si hay
    if (favorites.length > 0) {
        loadFavoriteListings(favorites);
    }
}

// Cerrar modal
window.closeFavoritesModal = function() {
    const modal = document.getElementById('favorites-modal');
    if (modal) {
        modal.classList.remove('active');
    }
};

// ========================================
// 4. BÚSQUEDA MEJORADA
// ========================================
function fixSearch() {
    const searchInput = document.getElementById('mrb-service-search');
    const locationSelect = document.getElementById('mrb-location-search');
    const dateInput = document.getElementById('mrb-date-search');
    
    if (!searchInput) return;
    
    // Mejorar búsqueda para buscar por título Y contenido
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.toLowerCase();
        
        searchTimeout = setTimeout(() => {
            performSmartSearch(query);
        }, 500);
    });
    
    // Búsqueda combinada al presionar Enter
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performCombinedSearch();
        }
    });
}

// Búsqueda inteligente
function performSmartSearch(query) {
    if (query.length < 2) return;
    
    // Normalizar búsqueda
    const normalizedQuery = query
        .toLowerCase()
        .replace(/[áàä]/g, 'a')
        .replace(/[éèë]/g, 'e')
        .replace(/[íìï]/g, 'i')
        .replace(/[óòö]/g, 'o')
        .replace(/[úùü]/g, 'u')
        .replace(/ñ/g, 'n');
    
    console.log('Búsqueda inteligente:', normalizedQuery);
    
    // Aquí conectar con AJAX para búsqueda flexible
    filterByAjax('all', normalizedQuery);
}

// Búsqueda combinada de los 3 filtros
function performCombinedSearch() {
    const service = document.getElementById('mrb-service-search')?.value || '';
    const location = document.getElementById('mrb-location-search')?.value || '';
    const date = document.getElementById('mrb-date-search')?.value || '';
    
    console.log('Búsqueda combinada:', { service, location, date });
    
    // Construir URL con parámetros
    const params = new URLSearchParams();
    if (service) params.set('buscar', service);
    if (location) params.set('ubicacion', location);
    if (date) params.set('fecha', date);
    
    const url = '/inicio-airbnb/?' + params.toString();
    
    // Actualizar sin recargar
    window.history.pushState({}, '', url);
    
    // Filtrar via AJAX
    filterByAjax('all', service, location, date);
}

// ========================================
// 5. FILTRADO AJAX MEJORADO
// ========================================
function filterByAjax(category = 'all', search = '', location = '', date = '') {
    const grid = document.getElementById('listings-grid');
    if (!grid) return;
    
    // Mostrar loading
    grid.style.opacity = '0.5';
    grid.innerHTML += '<div class="mrb-loading-overlay"><div class="mrb-spinner"></div></div>';
    
    // Hacer request AJAX
    fetch(mrb_ajax?.ajax_url || '/wp-admin/admin-ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'mrb_filter_listings',
            nonce: mrb_ajax?.nonce || '',
            category: category,
            search: search || document.getElementById('mrb-service-search')?.value || '',
            location: location || document.getElementById('mrb-location-search')?.value || '',
            date: date || document.getElementById('mrb-date-search')?.value || '',
            paged: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            grid.innerHTML = data.data.html;
            grid.style.opacity = '1';
            
            // Reinicializar eventos en nuevas cards
            fixListingLinks();
            
            // Mostrar mensaje si no hay resultados
            if (data.data.found_posts === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px;">
                        <h3>No se encontraron resultados</h3>
                        <p>Intenta con otros filtros o términos de búsqueda</p>
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        grid.style.opacity = '1';
        document.querySelector('.mrb-loading-overlay')?.remove();
    });
}

// ========================================
// EXPORTAR FUNCIONES GLOBALES
// ========================================
window.toggleFavorite = function(event, listingId) {
    event.stopPropagation();
    
    let favorites = JSON.parse(localStorage.getItem('mrb_favorites') || '[]');
    const button = event.currentTarget;
    
    if (favorites.includes(listingId)) {
        favorites = favorites.filter(id => id !== listingId);
        button.classList.remove('active');
    } else {
        favorites.push(listingId);
        button.classList.add('active');
    }
    
    localStorage.setItem('mrb_favorites', JSON.stringify(favorites));
    updateFavoritesCount();
    
    // Animación
    button.style.transform = 'scale(1.2)';
    setTimeout(() => {
        button.style.transform = 'scale(1)';
    }, 200);
};

console.log('✅ MRB Complete Fix cargado completamente');