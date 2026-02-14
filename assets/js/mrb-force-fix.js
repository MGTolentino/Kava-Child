/**
 * 🔥 FORCE FIX - Solución AGRESIVA que OVERRIDE todo
 * Se ejecuta múltiples veces para asegurar que funciona
 */

// Función global para manejar favoritos
window.toggleFavorite = function(event, listingId) {
    event.preventDefault();
    event.stopPropagation();
    
    console.log('Toggle favorite for listing:', listingId);
    
    // Obtener favoritos del localStorage
    let favorites = JSON.parse(localStorage.getItem('mrb_favorites') || '[]');
    const button = event.currentTarget;
    const svg = button.querySelector('svg');
    
    if (favorites.includes(listingId)) {
        // Quitar de favoritos
        favorites = favorites.filter(id => id !== listingId);
        // Cambiar el atributo style directamente
        svg.setAttribute('style', 'display: block; fill: rgba(0, 0, 0, 0.5); height: 24px; width: 24px; stroke: white; stroke-width: 2; overflow: visible;');
        button.classList.remove('active');
        console.log('Removed from favorites:', listingId);
    } else {
        // Agregar a favoritos
        favorites.push(listingId);
        // Cambiar el atributo style directamente con color rojo
        svg.setAttribute('style', 'display: block; fill: #FF385C; height: 24px; width: 24px; stroke: white; stroke-width: 2; overflow: visible;');
        button.classList.add('active');
        console.log('Added to favorites:', listingId);
    }
    
    // Guardar en localStorage
    localStorage.setItem('mrb_favorites', JSON.stringify(favorites));
    
    return false;
};

// SOLO para página Airbnb
if (document.body && !document.body.classList.contains('page-template-airbnb')) {
    console.log('🚫 No es página Airbnb - saliendo');
    // Salir si no es la página correcta
} else {
    console.log('✅ Página Airbnb detectada - aplicando fixes FORZADOS');

    // Función para restaurar favoritos guardados
    function restoreFavorites() {
        const favorites = JSON.parse(localStorage.getItem('mrb_favorites') || '[]');
        if (favorites.length > 0) {
            console.log('Restaurando favoritos guardados:', favorites);
            favorites.forEach(listingId => {
                const card = document.querySelector(`[data-listing-id="${listingId}"]`);
                if (card) {
                    const button = card.querySelector('.mrb-card-favorite');
                    if (button) {
                        const svg = button.querySelector('svg');
                        if (svg) {
                            // Cambiar el atributo style directamente
                            svg.setAttribute('style', 'display: block; fill: #FF385C; height: 24px; width: 24px; stroke: white; stroke-width: 2; overflow: visible;');
                            button.classList.add('active');
                        }
                    }
                }
            });
        }
    }
    
    // Función principal de fixes
    function applyAllFixes() {
        console.log('🔧 Aplicando todos los fixes...');
        
        // Restaurar favoritos
        restoreFavorites();
        
        // ========================================
        // 1. FORZAR CARDS - NUEVA PESTAÑA
        // ========================================
        const cards = document.querySelectorAll('.mrb-listing-card');
        
        cards.forEach(function(card) {
            // LIMPIAR TODO
            card.onclick = null;
            card.removeEventListener('click', null);
            const oldOnclick = card.getAttribute('onclick');
            card.removeAttribute('onclick');
            
            // Crear nuevo handler
            function handleCardClick(e) {
                // Prevenir propagación
                e.preventDefault();
                e.stopPropagation();
                
                // No abrir si es favorito o cualquier parte del botón
                if (e.target.closest('.mrb-card-favorite') || 
                    e.target.classList.contains('mrb-card-favorite') ||
                    e.target.tagName === 'svg' || 
                    e.target.tagName === 'path') {
                    console.log('Click en favorito detectado, no abrir card');
                    return false;
                }
                
                // Obtener URL
                let url = card.dataset.url || null;
                
                if (!url && oldOnclick) {
                    const match = oldOnclick.match(/window\.location\.href='([^']+)'/);
                    if (match) url = match[1];
                }
                
                if (!url && card.dataset.listingId) {
                    url = '/listing/' + card.dataset.listingId + '/';
                }
                
                console.log('CARD CLICK → URL:', url);
                
                if (url) {
                    // FORZAR NUEVA PESTAÑA
                    window.open(url, '_blank', 'noopener,noreferrer');
                    return false;
                }
            }
            
            // Añadir múltiples listeners para asegurar
            card.addEventListener('click', handleCardClick, true);
            card.onclick = handleCardClick;
            card.style.cursor = 'pointer';
            
            // Marcar como procesada
            card.dataset.fixApplied = 'true';
        });
        
        console.log('✅ Cards procesadas:', cards.length);
        
        // ========================================
        // 2. FORZAR CATEGORÍAS - NUEVA PESTAÑA
        // ========================================
        const categories = document.querySelectorAll('.mrb-category-item');
        
        const categoryUrls = {
            'all': '/',
            'fotografia': '/listing-category/fotografia/',
            'musica': '/listing-category/musica/',
            'joyeria': '/listing-category/joyeria/',
            'mobiliario': '/listing-category/mobiliario/',
            'transporte': '/listing-category/transporte/',
            'entretenimiento': '/listing-category/entretenimiento/',
            'decoracion': '/listing-category/decoracion/',
            'alimentos-y-bebidas': '/listing-category/alimentos-y-bebidas/',
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
        
        categories.forEach(function(category) {
            // LIMPIAR TODO
            category.onclick = null;
            category.removeAttribute('onclick');
            
            // Crear nuevo handler SIMPLE
            function handleCategoryClick(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Obtener URL directamente del data-url
                const url = category.dataset.url || categoryUrls[category.dataset.category] || '/';
                
                console.log('CATEGORÍA CLICK → URL:', url);
                
                // FORZAR NUEVA PESTAÑA
                window.open(url, '_blank', 'noopener,noreferrer');
                return false;
            }
            
            // Añadir listener
            category.addEventListener('click', handleCategoryClick, true);
            category.style.cursor = 'pointer';
            
            // Marcar como procesada
            category.dataset.fixApplied = 'true';
        });
        
        console.log('✅ Categorías procesadas:', categories.length);
        
        // ========================================
        // 3. NO TOCAR BÚSQUEDA - Ahora redirije al Cotizador
        // ========================================
        console.log('ℹ️ Búsqueda no modificada - usa script de redirección al Cotizador');
        
        // ========================================
        // 4. OVERRIDE FUNCIONES GLOBALES
        // ========================================
        window.filterByCategory = function(slug) {
            console.log('filterByCategory OVERRIDE:', slug);
            const url = categoryUrls[slug] || '/listing-category/' + slug + '/';
            window.open(url, '_blank');
            return false;
        };
        
        // performSearch ya no se override - usa redirección al Cotizador
        
        console.log('✅ Funciones globales override completado');
    }
    
    // ========================================
    // APLICAR FIXES MÚLTIPLES VECES
    // ========================================
    
    // Aplicar inmediatamente si el DOM está listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyAllFixes);
    } else {
        applyAllFixes();
    }
    
    // Aplicar después de 500ms
    setTimeout(applyAllFixes, 500);
    
    // Aplicar después de 1 segundo
    setTimeout(applyAllFixes, 1000);
    
    // Aplicar después de 2 segundos
    setTimeout(applyAllFixes, 2000);
    
    // Aplicar después de 3 segundos (último intento)
    setTimeout(function() {
        applyAllFixes();
        console.log('🎯 FIXES FINALES APLICADOS - todos los intentos completados');
    }, 3000);
    
    // Observer para detectar nuevas cards añadidas dinámicamente
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    // Buscar nuevas cards
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            if (node.classList && node.classList.contains('mrb-listing-card') && 
                                !node.dataset.fixApplied) {
                                setTimeout(applyAllFixes, 100);
                            }
                        }
                    });
                }
            });
        });
        
        // Observar el body para cambios
        if (document.body) {
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }
    
    console.log('🚀 MRB Force Fix iniciado - aplicándose múltiples veces');
}