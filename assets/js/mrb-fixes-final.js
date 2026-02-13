/**
 * 🔧 FIXES FINALES - Abrir en nueva pestaña y categorías correctas
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 MRB Fixes Final iniciado');
    
    fixListingCards();
    fixCategoryLinks(); 
    setupFavorites();
});

// ========================================
// 1. ARREGLAR CARDS PARA ABRIR EN NUEVA PESTAÑA
// ========================================
function fixListingCards() {
    const cards = document.querySelectorAll('.mrb-listing-card');
    
    cards.forEach(card => {
        // Limpiar eventos anteriores
        const newCard = card.cloneNode(true);
        card.parentNode.replaceChild(newCard, card);
        
        newCard.addEventListener('click', function(e) {
            // No abrir si se hace click en botones específicos
            if (e.target.closest('.mrb-card-favorite') || 
                e.target.closest('.mrb-card-nav') ||
                e.target.closest('.mrb-card-dot') ||
                e.target.closest('button')) {
                return;
            }
            
            // Obtener URL del listing
            let url = null;
            
            // Método 1: Buscar en onclick original
            const onclick = newCard.getAttribute('onclick');
            if (onclick) {
                const match = onclick.match(/window\.location\.href='([^']+)'/);
                if (match) {
                    url = match[1];
                }
            }
            
            // Método 2: Construir URL desde post ID
            if (!url) {
                const postId = newCard.dataset.listingId;
                if (postId) {
                    // Construir URL típica de WordPress/HivePress
                    url = '/listing/' + postId + '/';
                }
            }
            
            // Método 3: Buscar en cualquier link dentro de la card
            if (!url) {
                const link = newCard.querySelector('a[href]');
                if (link) {
                    url = link.href;
                }
            }
            
            // Método 4: Usar título del listing para construir URL
            if (!url) {
                const title = newCard.querySelector('.mrb-card-title');
                if (title) {
                    const slug = title.textContent.toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    url = '/listing/' + slug + '/';
                }
            }
            
            console.log('URL del listing:', url);
            
            if (url) {
                // Abrir en nueva pestaña
                window.open(url, '_blank', 'noopener,noreferrer');
            } else {
                console.warn('No se pudo determinar URL del listing');
            }
        });
        
        // Estilo visual
        newCard.style.cursor = 'pointer';
    });
    
    console.log('✅ Cards configuradas para abrir en nueva pestaña');
}

// ========================================
// 2. ARREGLAR CATEGORÍAS PARA ABRIR EN NUEVA PESTAÑA
// ========================================
function fixCategoryLinks() {
    const categories = document.querySelectorAll('.mrb-category-item');
    
    // URLs correctas de HivePress para categorías
    const categoryUrls = {
        'all': '/',
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
            
            // Obtener slug de la categoría
            let slug = 'all';
            
            // Método 1: Del atributo onclick
            const onclickStr = this.getAttribute('onclick');
            if (onclickStr) {
                const match = onclickStr.match(/filterByCategory\('([^']+)'\)/);
                if (match) slug = match[1];
            }
            
            // Método 2: Del data attribute si existe
            if (slug === 'all' && this.dataset.category) {
                slug = this.dataset.category;
            }
            
            // Método 3: Del texto del elemento (último recurso)
            if (slug === 'all') {
                const text = this.querySelector('span')?.textContent.toLowerCase();
                if (text) {
                    // Mapear nombres comunes a slugs
                    const textToSlug = {
                        'fotografía': 'fotografia',
                        'música': 'musica',
                        'joyería': 'joyeria',
                        'transporte': 'transporte',
                        'mobiliario': 'mobiliario',
                        'entretenimiento': 'entretenimiento',
                        'decoración': 'decoracion'
                    };
                    slug = textToSlug[text] || text.replace(/\s+/g, '-');
                }
            }
            
            console.log('Categoría clickeada:', slug);
            
            // Actualizar estado visual
            document.querySelectorAll('.mrb-category-item').forEach(item => {
                item.classList.remove('active');
            });
            this.classList.add('active');
            
            // Obtener URL de la categoría
            let categoryUrl = categoryUrls[slug];
            
            if (!categoryUrl && slug !== 'all') {
                // Construir URL si no está en el mapeo
                categoryUrl = '/listing-category/' + slug + '/';
            }
            
            if (!categoryUrl) {
                categoryUrl = '/'; // Fallback a home
            }
            
            console.log('Abriendo URL:', categoryUrl);
            
            // Abrir en nueva pestaña
            window.open(categoryUrl, '_blank', 'noopener,noreferrer');
        });
        
        // Estilo visual
        newCategory.style.cursor = 'pointer';
    });
    
    console.log('✅ Categorías configuradas para abrir en nueva pestaña');
}

// ========================================
// 3. SISTEMA DE FAVORITOS SIMPLIFICADO
// ========================================
function setupFavorites() {
    // Actualizar contador si existe
    updateFavoritesCount();
    
    // Manejar clicks en botones de favorito
    document.addEventListener('click', function(e) {
        if (e.target.closest('.mrb-card-favorite')) {
            e.stopPropagation(); // Evitar que abra el listing
            
            const button = e.target.closest('.mrb-card-favorite');
            const card = button.closest('.mrb-listing-card');
            let listingId = null;
            
            // Obtener ID del listing
            if (card.dataset.listingId) {
                listingId = parseInt(card.dataset.listingId);
            } else {
                // Extraer del onclick si existe
                const onclick = button.getAttribute('onclick');
                if (onclick) {
                    const match = onclick.match(/toggleFavorite\([^,]+,\s*(\d+)\)/);
                    if (match) listingId = parseInt(match[1]);
                }
            }
            
            if (listingId) {
                toggleFavorite(listingId, button);
            } else {
                console.warn('No se pudo obtener ID del listing para favoritos');
            }
        }
    });
}

// Función de favoritos mejorada
function toggleFavorite(listingId, button) {
    let favorites = JSON.parse(localStorage.getItem('mrb_favorites') || '[]');
    
    if (favorites.includes(listingId)) {
        // Quitar de favoritos
        favorites = favorites.filter(id => id !== listingId);
        button.classList.remove('active');
        showNotification('❤️ Eliminado de favoritos');
    } else {
        // Añadir a favoritos
        favorites.push(listingId);
        button.classList.add('active');
        showNotification('❤️ Añadido a favoritos');
        
        // Animación
        button.style.transform = 'scale(1.2)';
        setTimeout(() => {
            button.style.transform = 'scale(1)';
        }, 200);
    }
    
    localStorage.setItem('mrb_favorites', JSON.stringify(favorites));
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

// Mostrar notificaciones
function showNotification(message, type = 'success') {
    // Eliminar notificación anterior si existe
    const existingNotification = document.querySelector('.mrb-notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = 'mrb-notification';
    notification.textContent = message;
    
    notification.style.cssText = `
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: ${type === 'success' ? '#008A05' : '#FF385C'};
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        z-index: 10000;
        animation: slideUp 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    document.body.appendChild(notification);
    
    // Auto-eliminar después de 2 segundos
    setTimeout(() => {
        notification.style.animation = 'slideDown 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 2000);
}

// ========================================
// CSS PARA ANIMACIONES
// ========================================
const style = document.createElement('style');
style.textContent = `
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }
    
    @keyframes slideDown {
        from {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        to {
            opacity: 0;
            transform: translateX(-50%) translateY(20px);
        }
    }
    
    /* Mejorar cursor en elementos clickeables */
    .mrb-listing-card,
    .mrb-category-item {
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    
    .mrb-listing-card:hover {
        transform: translateY(-2px);
    }
    
    .mrb-category-item:hover {
        opacity: 1;
    }
    
    /* Favoritos activos */
    .mrb-card-favorite.active svg {
        fill: #FF385C !important;
        stroke: #FF385C !important;
    }
`;
document.head.appendChild(style);

// ========================================
// INICIALIZACIÓN
// ========================================
// Configurar favoritos existentes
document.addEventListener('DOMContentLoaded', function() {
    const favorites = JSON.parse(localStorage.getItem('mrb_favorites') || '[]');
    
    document.querySelectorAll('.mrb-card-favorite').forEach(button => {
        const card = button.closest('.mrb-listing-card');
        let listingId = null;
        
        if (card.dataset.listingId) {
            listingId = parseInt(card.dataset.listingId);
        } else {
            const onclick = button.getAttribute('onclick');
            if (onclick) {
                const match = onclick.match(/toggleFavorite\([^,]+,\s*(\d+)\)/);
                if (match) listingId = parseInt(match[1]);
            }
        }
        
        if (listingId && favorites.includes(listingId)) {
            button.classList.add('active');
        }
    });
});

console.log('✅ MRB Fixes Final cargado completamente');