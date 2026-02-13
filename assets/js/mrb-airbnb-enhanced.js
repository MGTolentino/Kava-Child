// 🚀 MRB Airbnb Enhanced JavaScript - Funcionalidades Mejoradas

document.addEventListener('DOMContentLoaded', function() {
    console.log('🎨 MRB Airbnb Enhanced JS iniciado');
    
    // Inicializar todos los componentes
    initializeDropdowns();
    initializeSearch();
    initializeCardCarousels();
    initializeCategorySlider();
    initializeLazyLoading();
    initializeFavorites();
    initializeInfiniteScroll();
});

// ========================================
// DROPDOWN MEJORADO (Fix para el problema actual)
// ========================================
function initializeDropdowns() {
    const userMenu = document.querySelector('.mrb-user-menu');
    if (!userMenu) return;
    
    // Limpiar cualquier event listener anterior
    userMenu.replaceWith(userMenu.cloneNode(true));
    const newUserMenu = document.querySelector('.mrb-user-menu');
    
    newUserMenu.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const dropdown = this.querySelector('.mrb-dropdown-menu') || 
                        document.getElementById('user-dropdown');
        
        if (dropdown) {
            dropdown.classList.toggle('active');
            
            // Animación suave
            if (dropdown.classList.contains('active')) {
                dropdown.style.display = 'block';
                setTimeout(() => {
                    dropdown.style.opacity = '1';
                    dropdown.style.visibility = 'visible';
                }, 10);
            }
        }
    });
    
    // Cerrar al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.mrb-user-menu')) {
            const dropdowns = document.querySelectorAll('.mrb-dropdown-menu.active');
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
                setTimeout(() => {
                    dropdown.style.display = 'none';
                }, 200);
            });
        }
    });
}

// ========================================
// BÚSQUEDA CON AUTOCOMPLETADO MEJORADO
// ========================================
function initializeSearch() {
    const searchInput = document.getElementById('mrb-service-search');
    if (!searchInput) return;
    
    let searchTimeout;
    const suggestions = [
        { title: 'Salones para eventos', subtitle: '124 disponibles', icon: '🏛️' },
        { title: 'Fotografía profesional', subtitle: '89 proveedores', icon: '📸' },
        { title: 'DJ para fiestas', subtitle: '67 disponibles', icon: '🎵' },
        { title: 'Catering y banquetes', subtitle: '156 opciones', icon: '🍽️' },
        { title: 'Decoración para eventos', subtitle: '98 proveedores', icon: '🎨' },
        { title: 'Mariachi y grupos musicales', subtitle: '45 disponibles', icon: '🎺' },
        { title: 'Maquillaje y peinado', subtitle: '78 estilistas', icon: '💄' },
        { title: 'Renta de mobiliario', subtitle: '34 proveedores', icon: '🪑' }
    ];
    
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const value = e.target.value.toLowerCase();
        
        searchTimeout = setTimeout(() => {
            if (value.length > 0) {
                showSearchSuggestions(value, suggestions);
            } else {
                hideSearchSuggestions();
            }
        }, 300);
    });
    
    // Cerrar sugerencias al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.mrb-search-what')) {
            hideSearchSuggestions();
        }
    });
}

function showSearchSuggestions(query, suggestions) {
    const container = document.getElementById('service-suggestions');
    if (!container) return;
    
    const filtered = suggestions.filter(item => 
        item.title.toLowerCase().includes(query) ||
        item.subtitle.toLowerCase().includes(query)
    );
    
    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="mrb-suggestion-item">
                <div class="mrb-suggestion-text">
                    <div class="mrb-suggestion-title">No se encontraron resultados</div>
                    <div class="mrb-suggestion-subtitle">Intenta con otros términos</div>
                </div>
            </div>
        `;
    } else {
        container.innerHTML = filtered.map(item => `
            <div class="mrb-suggestion-item" onclick="selectSuggestion('${item.title}')">
                <div class="mrb-suggestion-icon">${item.icon}</div>
                <div class="mrb-suggestion-text">
                    <div class="mrb-suggestion-title">${item.title}</div>
                    <div class="mrb-suggestion-subtitle">${item.subtitle}</div>
                </div>
            </div>
        `).join('');
    }
    
    container.classList.add('active');
    container.style.display = 'block';
}

function hideSearchSuggestions() {
    const container = document.getElementById('service-suggestions');
    if (container) {
        container.classList.remove('active');
        setTimeout(() => {
            container.style.display = 'none';
        }, 200);
    }
}

function selectSuggestion(value) {
    const searchInput = document.getElementById('mrb-service-search');
    if (searchInput) {
        searchInput.value = value;
        hideSearchSuggestions();
    }
}

// ========================================
// CAROUSEL DE IMÁGENES EN CARDS
// ========================================
function initializeCardCarousels() {
    const cards = document.querySelectorAll('.mrb-listing-card');
    
    cards.forEach(card => {
        const slider = card.querySelector('.mrb-card-slider');
        if (!slider) return;
        
        // Simular múltiples imágenes (en producción vendrían del backend)
        const images = [
            slider.querySelector('.mrb-card-image')?.src,
            slider.querySelector('.mrb-card-image')?.src,
            slider.querySelector('.mrb-card-image')?.src
        ].filter(Boolean);
        
        if (images.length <= 1) return;
        
        // Crear estructura del carousel
        const imagesHtml = images.map((src, index) => `
            <img class="mrb-card-image ${index === 0 ? 'active' : ''}" 
                 src="${src}" 
                 alt="Imagen ${index + 1}"
                 loading="lazy">
        `).join('');
        
        const dotsHtml = images.map((_, index) => `
            <span class="mrb-card-dot ${index === 0 ? 'active' : ''}" 
                  data-index="${index}"></span>
        `).join('');
        
        const existingImage = slider.querySelector('.mrb-card-image');
        if (existingImage) {
            existingImage.remove();
        }
        
        slider.innerHTML += `
            <div class="mrb-card-images">${imagesHtml}</div>
            <button class="mrb-card-nav mrb-card-prev" onclick="changeCardImage(this, -1)">‹</button>
            <button class="mrb-card-nav mrb-card-next" onclick="changeCardImage(this, 1)">›</button>
            <div class="mrb-card-dots">${dotsHtml}</div>
        `;
        
        // Evento para los dots
        const dots = slider.querySelectorAll('.mrb-card-dot');
        dots.forEach(dot => {
            dot.addEventListener('click', function(e) {
                e.stopPropagation();
                const index = parseInt(this.dataset.index);
                showCardImage(slider, index);
            });
        });
    });
}

function changeCardImage(button, direction) {
    event.stopPropagation();
    const slider = button.closest('.mrb-card-slider');
    const images = slider.querySelectorAll('.mrb-card-image');
    const currentIndex = Array.from(images).findIndex(img => img.classList.contains('active'));
    
    let newIndex = currentIndex + direction;
    if (newIndex >= images.length) newIndex = 0;
    if (newIndex < 0) newIndex = images.length - 1;
    
    showCardImage(slider, newIndex);
}

function showCardImage(slider, index) {
    const images = slider.querySelectorAll('.mrb-card-image');
    const dots = slider.querySelectorAll('.mrb-card-dot');
    
    images.forEach(img => img.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    if (images[index]) images[index].classList.add('active');
    if (dots[index]) dots[index].classList.add('active');
}

// ========================================
// SLIDER DE CATEGORÍAS
// ========================================
function initializeCategorySlider() {
    const track = document.getElementById('categories-track');
    if (!track) return;
    
    const prevBtn = document.querySelector('.mrb-slider-prev');
    const nextBtn = document.querySelector('.mrb-slider-next');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            track.scrollBy({ left: -200, behavior: 'smooth' });
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            track.scrollBy({ left: 200, behavior: 'smooth' });
        });
    }
    
    // Ocultar/mostrar flechas según el scroll
    function updateArrows() {
        if (!track || !prevBtn || !nextBtn) return;
        
        const scrollLeft = track.scrollLeft;
        const maxScroll = track.scrollWidth - track.clientWidth;
        
        prevBtn.style.display = scrollLeft > 0 ? 'flex' : 'none';
        nextBtn.style.display = scrollLeft < maxScroll - 5 ? 'flex' : 'none';
    }
    
    if (track) {
        track.addEventListener('scroll', updateArrows);
        updateArrows();
    }
}

// ========================================
// LAZY LOADING DE IMÁGENES
// ========================================
function initializeLazyLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    // Añadir efecto de fade-in
                    img.style.opacity = '0';
                    img.style.transition = 'opacity 0.3s';
                    
                    img.onload = function() {
                        img.style.opacity = '1';
                    };
                    
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });
        
        // Observar todas las imágenes con data-src
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
}

// ========================================
// SISTEMA DE FAVORITOS
// ========================================
function initializeFavorites() {
    // Obtener favoritos del localStorage
    let favorites = JSON.parse(localStorage.getItem('mrb_favorites') || '[]');
    
    // Actualizar UI con favoritos guardados
    updateFavoritesUI(favorites);
}

function toggleFavorite(event, listingId) {
    event.stopPropagation();
    
    let favorites = JSON.parse(localStorage.getItem('mrb_favorites') || '[]');
    const button = event.currentTarget;
    
    if (favorites.includes(listingId)) {
        favorites = favorites.filter(id => id !== listingId);
        button.classList.remove('active');
        showNotification('Eliminado de favoritos');
    } else {
        favorites.push(listingId);
        button.classList.add('active');
        showNotification('Añadido a favoritos');
        
        // Animación de corazón
        animateHeart(button);
    }
    
    localStorage.setItem('mrb_favorites', JSON.stringify(favorites));
    updateFavoritesUI(favorites);
}

function updateFavoritesUI(favorites) {
    document.querySelectorAll('.mrb-card-favorite').forEach(button => {
        const listingId = parseInt(button.getAttribute('onclick')?.match(/\d+/)?.[0]);
        if (listingId && favorites.includes(listingId)) {
            button.classList.add('active');
        }
    });
}

function animateHeart(button) {
    button.style.animation = 'pulse 0.3s ease';
    setTimeout(() => {
        button.style.animation = '';
    }, 300);
}

// ========================================
// INFINITE SCROLL
// ========================================
function initializeInfiniteScroll() {
    let page = 1;
    let loading = false;
    
    const loader = document.getElementById('infinite-loader');
    const grid = document.getElementById('listings-grid');
    
    if (!loader || !grid) return;
    
    const scrollObserver = new IntersectionObserver((entries) => {
        const entry = entries[0];
        
        if (entry.isIntersecting && !loading) {
            loadMoreListings();
        }
    }, {
        rootMargin: '100px'
    });
    
    // Observar el loader
    scrollObserver.observe(loader);
    
    function loadMoreListings() {
        loading = true;
        loader.style.display = 'block';
        
        // Simular carga de más listados
        setTimeout(() => {
            // Aquí harías la llamada AJAX real
            console.log('Cargando página', page + 1);
            
            // Añadir skeleton loaders mientras carga
            addSkeletonLoaders(6);
            
            // Simular respuesta después de 1 segundo
            setTimeout(() => {
                removeSkeletonLoaders();
                // Aquí añadirías los listados reales
                
                page++;
                loading = false;
                loader.style.display = 'none';
            }, 1000);
        }, 500);
    }
}

// ========================================
// SKELETON LOADERS
// ========================================
function addSkeletonLoaders(count = 6) {
    const grid = document.getElementById('listings-grid');
    if (!grid) return;
    
    for (let i = 0; i < count; i++) {
        const skeleton = document.createElement('div');
        skeleton.className = 'mrb-listing-card mrb-skeleton-card';
        skeleton.innerHTML = `
            <div class="mrb-card-slider mrb-skeleton"></div>
            <div class="mrb-card-content">
                <div class="mrb-skeleton" style="height: 20px; width: 70%; margin-bottom: 8px;"></div>
                <div class="mrb-skeleton" style="height: 16px; width: 50%; margin-bottom: 8px;"></div>
                <div class="mrb-skeleton" style="height: 18px; width: 40%;"></div>
            </div>
        `;
        grid.appendChild(skeleton);
    }
}

function removeSkeletonLoaders() {
    const skeletons = document.querySelectorAll('.mrb-skeleton-card');
    skeletons.forEach(skeleton => skeleton.remove());
}

// ========================================
// NOTIFICACIONES
// ========================================
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `mrb-notification mrb-notification-${type}`;
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
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideDown 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}

// ========================================
// FILTROS DE CATEGORÍAS
// ========================================
function filterByCategory(slug) {
    const items = document.querySelectorAll('.mrb-category-item');
    
    // Actualizar estado activo
    items.forEach(item => item.classList.remove('active'));
    event.currentTarget.classList.add('active');
    
    // Mostrar loading
    const grid = document.getElementById('listings-grid');
    if (grid) {
        grid.style.opacity = '0.5';
        grid.style.pointerEvents = 'none';
    }
    
    // Aquí harías la llamada AJAX para filtrar
    console.log('Filtrando por categoría:', slug);
    
    // Simular respuesta
    setTimeout(() => {
        if (grid) {
            grid.style.opacity = '1';
            grid.style.pointerEvents = 'auto';
        }
        showNotification(`Mostrando: ${slug === 'all' ? 'Todos los servicios' : slug}`);
    }, 500);
}

// ========================================
// BÚSQUEDA PRINCIPAL
// ========================================
function performSearch() {
    const service = document.getElementById('mrb-service-search')?.value;
    const location = document.getElementById('mrb-location-search')?.value;
    const date = document.getElementById('mrb-date-search')?.value;
    
    console.log('Buscando:', { service, location, date });
    
    // Mostrar loading
    showNotification('Buscando...', 'info');
    
    // Aquí harías la búsqueda real
    setTimeout(() => {
        showNotification('Búsqueda completada');
    }, 1000);
}

// ========================================
// ANIMACIONES CSS
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
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }
`;
document.head.appendChild(style);

// ========================================
// EXPORTAR FUNCIONES GLOBALES
// ========================================
window.toggleFavorite = toggleFavorite;
window.filterByCategory = filterByCategory;
window.performSearch = performSearch;
window.changeCardImage = changeCardImage;
window.selectSuggestion = selectSuggestion;
window.slideCategories = function(direction) {
    const track = document.getElementById('categories-track');
    if (track) {
        track.scrollBy({ 
            left: direction === 'next' ? 200 : -200, 
            behavior: 'smooth' 
        });
    }
};

console.log('✅ MRB Airbnb Enhanced JS cargado completamente');