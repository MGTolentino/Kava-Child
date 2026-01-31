/**
 * MRB - Modern Reservations Booking JavaScript
 * Funcionalidad completa para la página de inicio personalizada
 */

// Variables globales
let currentPage = 1;
let isLoading = false;
let hasMorePages = true;
let mapViewActive = false;
let selectedFilters = {
    category: '',
    location: '',
    date: '',
    priceRange: '',
    rating: '',
    type: 'all'
};

// Variables para el lead/CRM
let currentLead = null;
let currentEvent = null;

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeCategories();
    initializeFavorites();
    initializeModals();
    initializeFilters();
    initializeInfiniteScroll();
    initializeSkeletons();
    
    // Si hay datos de lead en localStorage
    if (localStorage.getItem('mrb_current_lead')) {
        currentLead = JSON.parse(localStorage.getItem('mrb_current_lead'));
        updateLeadDisplay();
    }
});

/**
 * Sistema de Búsqueda Principal
 */
function initializeSearch() {
    const serviceInput = document.getElementById('mrb-service-search');
    const locationSelect = document.getElementById('mrb-location-search');
    const dateInput = document.getElementById('mrb-date-search');
    const suggestionsDiv = document.getElementById('service-suggestions');
    
    if (!serviceInput) return;
    
    // Autocompletado para servicios
    serviceInput.addEventListener('input', function(e) {
        const value = e.target.value.toLowerCase();
        if (value.length > 2) {
            showServiceSuggestions(value);
        } else {
            hideSuggestions();
        }
    });
    
    // Cerrar sugerencias al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.mrb-search-what')) {
            hideSuggestions();
        }
    });
    
    // Fecha mínima es hoy
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }
}

// Variable para el timeout de debounce
let searchTimeout = null;

/**
 * Búsqueda con sugerencias dinámicas y debounce
 */
function searchSuggestions(query) {
    const suggestionsDiv = document.getElementById('service-suggestions');
    
    // Limpiar timeout anterior
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    if (!query || query.length < 2) {
        suggestionsDiv.style.display = 'none';
        return;
    }
    
    // Usar debounce de 300ms para evitar búsquedas excesivas
    searchTimeout = setTimeout(() => {
        performSuggestionSearch(query);
    }, 300);
}

/**
 * Realizar búsqueda de sugerencias (separada para el debounce)
 */
function performSuggestionSearch(query) {
    const suggestionsDiv = document.getElementById('service-suggestions');
    
    // Lista de servicios comunes para sugerencias
    const suggestions = [
        'Salones para eventos',
        'Jardines para bodas', 
        'Haciendas',
        'Quintas',
        'Terrazas',
        'Hoteles para eventos',
        'Fotografía profesional',
        'Video para eventos',
        'Fotografía y video',
        'DJ profesional',
        'DJ para bodas',
        'DJ para XV años',
        'Mariachi',
        'Grupo norteño',
        'Grupo versátil',
        'Banda en vivo',
        'Decoración con globos',
        'Decoración floral',
        'Decoración para bodas',
        'Catering completo',
        'Banquetes',
        'Taquizas',
        'Parrilladas',
        'Mesa de dulces',
        'Pastelería',
        'Pastel de bodas',
        'Pastel de XV años',
        'Mobiliario para eventos',
        'Sillas y mesas',
        'Sillas tiffany',
        'Mesas redondas',
        'Audio e iluminación',
        'Sonido profesional',
        'Iluminación LED',
        'Pista de baile',
        'Pista iluminada',
        'Maestro de ceremonias',
        'Animador de eventos',
        'Show infantil',
        'Payasos',
        'Botargas',
        'Maquillaje profesional',
        'Peinado para novias',
        'Maquillaje y peinado'
    ];
    
    // Primero mostrar sugerencias locales instantáneamente
    const queryLower = query.toLowerCase();
    const words = queryLower.split(' ').filter(w => w.length > 0);
    
    // Filtrar sugerencias que contengan TODAS las palabras
    const filtered = suggestions.filter(s => {
        const sLower = s.toLowerCase();
        return words.every(word => sLower.includes(word));
    });
    
    // Mostrar sugerencias locales inmediatamente
    if (filtered.length > 0) {
        displaySuggestions(filtered.slice(0, 8), query);
    }
    
    // Luego hacer búsqueda AJAX si está disponible
    if (window.mrb_ajax && window.mrb_ajax.ajax_url) {
        jQuery.ajax({
            url: window.mrb_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mrb_search_suggestions',
                nonce: window.mrb_ajax.nonce,
                query: query
            },
            success: function(response) {
                if (response.success && response.data.suggestions && response.data.suggestions.length > 0) {
                    // Combinar sugerencias AJAX con las locales
                    const combined = [...new Set([...response.data.suggestions, ...filtered])];
                    displaySuggestions(combined.slice(0, 8), query);
                }
            }
        });
    }
}

/**
 * Mostrar sugerencias en el dropdown
 */
function displaySuggestions(suggestions, query) {
    const suggestionsDiv = document.getElementById('service-suggestions');
    
    if (suggestions.length > 0) {
        suggestionsDiv.innerHTML = suggestions.map(s => `
            <div class="mrb-suggestion-item" onclick="selectSuggestion('${s.replace(/'/g, "\\'")}')" style="padding: 12px 16px; cursor: pointer; transition: background 0.2s;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <svg width="16" height="16" fill="#6A6A6A">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.027.026.056.048.085.071l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.072-.086zm-5.242 1.156a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
                    </svg>
                    <span style="color: #222; font-size: 14px;">${highlightMatch(s, query)}</span>
                </div>
            </div>
        `).join('');
        
        suggestionsDiv.style.display = 'block';
        
        // Agregar hover effects
        suggestionsDiv.querySelectorAll('.mrb-suggestion-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.background = '#F7F7F7';
            });
            item.addEventListener('mouseleave', function() {
                this.style.background = 'transparent';
            });
        });
    } else {
        suggestionsDiv.style.display = 'none';
    }
}

/**
 * Resaltar coincidencias en las sugerencias
 */
function highlightMatch(text, query) {
    const regex = new RegExp(`(${query})`, 'gi');
    return text.replace(regex, '<strong>$1</strong>');
}

function selectSuggestion(value) {
    document.getElementById('mrb-service-search').value = value;
    hideSuggestions();
}

function hideSuggestions() {
    const suggestionsDiv = document.getElementById('service-suggestions');
    if (suggestionsDiv) {
        suggestionsDiv.style.display = 'none';
    }
}

/**
 * Búsqueda principal
 */
function performSearch() {
    const service = document.getElementById('mrb-service-search').value;
    const location = document.getElementById('mrb-location-search').value;
    const date = document.getElementById('mrb-date-search').value;
    
    // Guardar búsqueda en el historial
    saveSearchHistory({service, location, date});
    
    // Construir URL de búsqueda
    let searchUrl = '/servicios?';
    if (service) searchUrl += `search=${encodeURIComponent(service)}&`;
    if (location) searchUrl += `location=${encodeURIComponent(location)}&`;
    if (date) searchUrl += `date=${encodeURIComponent(date)}&`;
    
    // Redirigir a la página de resultados
    window.location.href = searchUrl;
}

/**
 * Búsqueda rápida por tag
 */
function quickSearch(tag) {
    document.getElementById('mrb-service-search').value = tag;
    performSearch();
}

/**
 * Sistema de Categorías con Slider
 */
function initializeCategories() {
    const track = document.getElementById('categories-track');
    if (!track) return;
    
    let isDown = false;
    let startX;
    let scrollLeft;
    
    // Hacer el track draggable en desktop
    track.addEventListener('mousedown', (e) => {
        isDown = true;
        track.style.cursor = 'grabbing';
        startX = e.pageX - track.offsetLeft;
        scrollLeft = track.scrollLeft;
    });
    
    track.addEventListener('mouseleave', () => {
        isDown = false;
        track.style.cursor = 'grab';
    });
    
    track.addEventListener('mouseup', () => {
        isDown = false;
        track.style.cursor = 'grab';
    });
    
    track.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - track.offsetLeft;
        const walk = (x - startX) * 2;
        track.scrollLeft = scrollLeft - walk;
    });
}

/**
 * Control del slider de categorías
 */
function slideCategories(direction) {
    const track = document.getElementById('categories-track');
    if (!track) return;
    
    const scrollAmount = 300;
    
    if (direction === 'prev') {
        track.scrollLeft -= scrollAmount;
    } else {
        track.scrollLeft += scrollAmount;
    }
}

/**
 * Filtrar por categoría
 */
function filterByCategory(category) {
    selectedFilters.category = category;
    applyFilters();
}

/**
 * Filtros especiales
 */
function filterByOfficial() {
    selectedFilters.type = 'official';
    applyFilters();
}

function filterByPromoters() {
    selectedFilters.type = 'promoters';
    applyFilters();
}

function filterByOffers() {
    selectedFilters.type = 'offers';
    applyFilters();
}

/**
 * Sistema de Filtros de Listados
 */
function initializeFilters() {
    // Añadir event listeners a los pills
    document.querySelectorAll('.mrb-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            // Remover active de todos
            document.querySelectorAll('.mrb-pill').forEach(p => p.classList.remove('active'));
            // Añadir active al clickeado
            this.classList.add('active');
        });
    });
}

function filterListings(type) {
    selectedFilters.type = type;
    applyFilters();
}

/**
 * Aplicar todos los filtros
 */
function applyFilters() {
    showLoadingState();
    
    // Construir parámetros de búsqueda
    const params = new URLSearchParams();
    
    Object.keys(selectedFilters).forEach(key => {
        if (selectedFilters[key]) {
            params.append(key, selectedFilters[key]);
        }
    });
    
    // Hacer petición AJAX a WordPress
    fetch(`/wp-json/mrb/v1/listings?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            updateListingsGrid(data.listings);
            hideLoadingState();
        })
        .catch(error => {
            console.error('Error:', error);
            hideLoadingState();
            // En caso de error, usar filtrado local
            filterListingsLocally();
        });
}

/**
 * Filtrado local como fallback
 */
function filterListingsLocally() {
    const cards = document.querySelectorAll('.mrb-listing-card');
    
    cards.forEach(card => {
        let show = true;
        
        // Aplicar filtros según los criterios
        if (selectedFilters.type === 'popular') {
            // Mostrar solo los que tienen rating > 4.5
            const rating = card.querySelector('.mrb-card-rating span');
            if (rating && parseFloat(rating.textContent) < 4.5) {
                show = false;
            }
        } else if (selectedFilters.type === 'new') {
            // Por ahora mostrar todos (idealmente checkear fecha de creación)
            show = true;
        } else if (selectedFilters.type === 'promo') {
            // Mostrar solo los que tienen badge de promoción
            const badge = card.querySelector('.mrb-card-badge');
            if (!badge || !badge.textContent.includes('Oferta')) {
                show = false;
            }
        }
        
        // Mostrar u ocultar
        card.style.display = show ? 'block' : 'none';
    });
}

/**
 * Actualizar grid de listados
 */
function updateListingsGrid(listings) {
    const grid = document.getElementById('listings-grid');
    if (!grid || !listings) return;
    
    if (listings.length === 0) {
        grid.innerHTML = `
            <div class="mrb-no-results">
                <h3>No se encontraron servicios</h3>
                <p>Intenta ajustar tus filtros o búsqueda</p>
            </div>
        `;
        return;
    }
    
    grid.innerHTML = listings.map(listing => createListingCard(listing)).join('');
    initializeFavorites(); // Re-inicializar favoritos
}

/**
 * Crear tarjeta de listado
 */
function createListingCard(listing) {
    return `
        <div class="mrb-listing-card" onclick="window.location.href='${listing.url}'">
            <div class="mrb-card-image-container">
                <img class="mrb-card-image" src="${listing.image}" alt="${listing.title}">
                <button class="mrb-card-favorite" onclick="toggleFavorite(event, ${listing.id})">
                    <svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m16 28c7-4.733 14-10 14-17 0-1.792-.683-3.583-2.05-4.95-1.367-1.366-3.158-2.05-4.95-2.05-1.791 0-3.583.684-4.949 2.05l-2.051 2.051-2.05-2.051c-1.367-1.366-3.158-2.05-4.95-2.05-1.791 0-3.583.684-4.949 2.05-1.367 1.367-2.051 3.158-2.051 4.95 0 7 7 12.267 14 17z"></path>
                    </svg>
                </button>
                ${listing.verified ? '<div class="mrb-card-badge">Verificado</div>' : ''}
            </div>
            
            <div class="mrb-card-content">
                <div class="mrb-card-header">
                    <h3 class="mrb-card-title">${listing.title}</h3>
                    ${listing.rating ? `
                        <div class="mrb-card-rating">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <span>${listing.rating}</span>
                            ${listing.reviews ? `<span class="mrb-card-reviews">(${listing.reviews})</span>` : ''}
                        </div>
                    ` : ''}
                </div>
                
                <div class="mrb-card-meta">
                    ${listing.category ? `<span class="mrb-card-category">${listing.category}</span>` : ''}
                    ${listing.location ? `<span class="mrb-card-location">${listing.location}</span>` : ''}
                </div>
                
                ${listing.price ? `
                    <div class="mrb-card-price">
                        <span class="mrb-price-amount">$${listing.price}</span>
                        <span class="mrb-price-unit">MXN</span>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
}

/**
 * Toggle Map View
 */
function toggleMapView() {
    mapViewActive = !mapViewActive;
    const button = document.querySelector('.mrb-map-toggle');
    const grid = document.getElementById('listings-grid');
    
    if (mapViewActive) {
        button.innerHTML = `
            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;fill:currentColor;height:16px;width:16px;">
                <path d="M13 0a13 13 0 0 1 9.87 21.52l8.3 8.3a1 1 0 0 1-1.32 1.5l-.1-.08-8.3-8.3a13 13 0 1 1-8.45-22.94zm0 2a11 11 0 1 0 0 22 11 11 0 0 0 0-22z"></path>
            </svg>
            <span>Mostrar lista</span>
        `;
        // Aquí iría la lógica para mostrar el mapa
        showNotification('Vista de mapa en desarrollo');
    } else {
        button.innerHTML = `
            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;fill:currentColor;height:16px;width:16px;">
                <path d="M31.245 3.747a2.285 2.285 0 0 0-1.01-1.44A2.286 2.286 0 0 0 28.501 2l-7.515 1.67-10-2L2.5 3.557A2.286 2.286 0 0 0 .7 5.802v21.95a2.284 2.284 0 0 0 1.065 1.941A2.29 2.29 0 0 0 2.999 30a2.3 2.3 0 0 0 .501-.054l7.515-1.67 10 2 8.486-1.886a2.285 2.285 0 0 0 1.799-2.245V4.195a2.3 2.3 0 0 0-.055-.448zm-2.746 1.482v19.483l-5.999 1.333v-19.483zM2.999 4.49l6 1.333v19.483l-6-1.333zM11 25.273V5.79l10 2v19.483z"></path>
            </svg>
            <span>Mostrar mapa</span>
        `;
    }
}

/**
 * Infinite Scroll
 */
function initializeInfiniteScroll() {
    window.addEventListener('scroll', function() {
        if (isLoading || !hasMorePages) return;
        
        const scrollHeight = document.documentElement.scrollHeight;
        const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        const clientHeight = document.documentElement.clientHeight;
        
        if (scrollTop + clientHeight >= scrollHeight - 200) {
            loadMoreListings();
        }
    });
}

/**
 * Initialize Loading Skeletons
 */
function initializeSkeletons() {
    // Agregar skeletons a las imágenes mientras cargan
    const images = document.querySelectorAll('.mrb-card-image');
    images.forEach(img => {
        if (!img.complete) {
            img.classList.add('mrb-skeleton');
            img.addEventListener('load', function() {
                this.classList.remove('mrb-skeleton');
            });
        }
    });
}

/**
 * Cargar más listados con infinite scroll
 */
function loadMoreListings() {
    if (isLoading) return;
    
    isLoading = true;
    currentPage++;
    
    const button = document.querySelector('.mrb-load-more');
    const originalText = button.textContent;
    button.innerHTML = '<span class="mrb-loading"></span> Cargando...';
    button.disabled = true;
    
    // Mostrar loader
    const loader = document.getElementById('infinite-loader');
    if (loader) {
        loader.classList.add('active');
    }
    
    // Hacer petición AJAX real
    setTimeout(() => {
        // Aquí iría la petición real a WordPress
        fetch(`/wp-json/mrb/v1/listings?page=${currentPage}`)
            .then(response => response.json())
            .then(data => {
                if (data.listings && data.listings.length > 0) {
                    const grid = document.getElementById('listings-grid');
                    data.listings.forEach(listing => {
                        grid.insertAdjacentHTML('beforeend', createListingCard(listing));
                    });
                    
                    // Si no hay más resultados, desactivar infinite scroll
                    if (data.listings.length < 12) {
                        hasMorePages = false;
                    }
                } else {
                    hasMorePages = false;
                }
            })
            .catch(error => {
                console.error('Error cargando más listados:', error);
            })
            .finally(() => {
                isLoading = false;
                if (loader) {
                    loader.classList.remove('active');
                }
            });
    }, 500);
}

/**
 * Sistema de Favoritos
 */
function initializeFavorites() {
    // Cargar favoritos del localStorage
    const favorites = JSON.parse(localStorage.getItem('mrb_favorites') || '[]');
    
    // Marcar favoritos existentes
    favorites.forEach(id => {
        const button = document.querySelector(`[onclick="toggleFavorite(event, ${id})"]`);
        if (button) {
            button.classList.add('active');
        }
    });
}

function toggleFavorite(event, listingId) {
    event.stopPropagation(); // Prevenir click en la tarjeta
    
    const button = event.currentTarget;
    const favorites = JSON.parse(localStorage.getItem('mrb_favorites') || '[]');
    
    if (favorites.includes(listingId)) {
        // Remover de favoritos
        const index = favorites.indexOf(listingId);
        favorites.splice(index, 1);
        button.classList.remove('active');
        showNotification('Removido de favoritos');
    } else {
        // Añadir a favoritos
        favorites.push(listingId);
        button.classList.add('active');
        showNotification('Añadido a favoritos');
        
        // Animación de corazón
        button.style.transform = 'scale(1.2)';
        setTimeout(() => {
            button.style.transform = '';
        }, 300);
    }
    
    // Guardar en localStorage
    localStorage.setItem('mrb_favorites', JSON.stringify(favorites));
    
    // Si el usuario está logueado, sincronizar con el servidor
    if (typeof wp !== 'undefined' && wp.ajax) {
        wp.ajax.post('mrb_toggle_favorite', {
            listing_id: listingId,
            action: favorites.includes(listingId) ? 'add' : 'remove'
        });
    }
}

/**
 * Sistema de Modales
 */
function initializeModals() {
    // Cerrar modal al hacer click fuera
    document.querySelectorAll('.mrb-modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });
    
    // Esc para cerrar modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.mrb-modal.active').forEach(modal => {
                closeModal(modal.id);
            });
        }
    });
}

function openLeadModal() {
    document.getElementById('mrb-lead-modal').classList.add('active');
    loadExistingLeads();
}

function closeLeadModal() {
    closeModal('mrb-lead-modal');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

/**
 * Cargar leads existentes
 */
function loadExistingLeads() {
    // Aquí se cargarían los leads del usuario desde WordPress
    if (typeof wp !== 'undefined' && wp.ajax) {
        wp.ajax.post('mrb_get_user_leads', {})
            .done(function(response) {
                const select = document.getElementById('lead-select');
                select.innerHTML = '<option value="">Seleccionar lead existente</option>';
                response.leads.forEach(lead => {
                    select.innerHTML += `<option value="${lead.id}">${lead.name} - ${lead.email}</option>`;
                });
            });
    }
}

/**
 * Crear nuevo lead
 */
function createNewLead() {
    // Aquí podrías abrir otro modal o expandir el formulario
    const formHtml = `
        <div class="mrb-form-group">
            <label>Nombre</label>
            <input type="text" id="new-lead-name" placeholder="Nombre completo">
        </div>
        <div class="mrb-form-group">
            <label>Email</label>
            <input type="email" id="new-lead-email" placeholder="correo@ejemplo.com">
        </div>
        <div class="mrb-form-group">
            <label>Teléfono</label>
            <input type="tel" id="new-lead-phone" placeholder="8112345678">
        </div>
    `;
    
    // Insertar formulario (simplificado para ejemplo)
    const container = document.querySelector('.mrb-modal-form');
    const div = document.createElement('div');
    div.innerHTML = formHtml;
    container.insertBefore(div, container.lastElementChild);
}

/**
 * Guardar lead y evento
 */
function saveLeadEvent() {
    const leadId = document.getElementById('lead-select').value;
    const eventType = document.getElementById('event-select').value;
    const eventDate = document.getElementById('event-date').value;
    
    if (!eventType || !eventDate) {
        showNotification('Por favor completa todos los campos', 'error');
        return;
    }
    
    // Guardar en localStorage
    currentLead = {id: leadId, type: eventType, date: eventDate};
    localStorage.setItem('mrb_current_lead', JSON.stringify(currentLead));
    
    // Cerrar modal
    closeLeadModal();
    
    // Actualizar display
    updateLeadDisplay();
    
    // Mostrar notificación
    showNotification('Lead y evento guardados correctamente');
}

/**
 * Actualizar display del lead actual
 */
function updateLeadDisplay() {
    // Aquí actualizarías el UI para mostrar el lead actual
    // Por ejemplo, en un badge o en el header
}

/**
 * Sistema de Notificaciones
 */
function showNotification(message, type = 'success') {
    // Crear notificación
    const notification = document.createElement('div');
    notification.className = `mrb-notification mrb-notification-${type}`;
    notification.textContent = message;
    
    // Estilos inline para la notificación
    notification.style.cssText = `
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        background: ${type === 'success' ? '#10B981' : '#EF4444'};
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideUp 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notification.style.animation = 'slideDown 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

/**
 * Estados de carga
 */
function showLoadingState() {
    const grid = document.getElementById('listings-grid');
    if (grid) {
        grid.style.opacity = '0.5';
        grid.style.pointerEvents = 'none';
    }
}

function hideLoadingState() {
    const grid = document.getElementById('listings-grid');
    if (grid) {
        grid.style.opacity = '1';
        grid.style.pointerEvents = 'auto';
    }
}

/**
 * Guardar historial de búsqueda
 */
function saveSearchHistory(searchData) {
    let history = JSON.parse(localStorage.getItem('mrb_search_history') || '[]');
    
    // Añadir timestamp
    searchData.timestamp = Date.now();
    
    // Añadir al principio del array
    history.unshift(searchData);
    
    // Mantener solo las últimas 10 búsquedas
    history = history.slice(0, 10);
    
    localStorage.setItem('mrb_search_history', JSON.stringify(history));
}

/**
 * Animaciones CSS necesarias
 */
const style = document.createElement('style');
style.textContent = `
    @keyframes slideUp {
        from {
            transform: translateX(-50%) translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes slideDown {
        from {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
        to {
            transform: translateX(-50%) translateY(100%);
            opacity: 0;
        }
    }
    
    .mrb-suggestion-item {
        padding: 12px 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }
    
    .mrb-suggestion-item:hover {
        background: #f7f7f7;
    }
    
    #service-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 8px 28px rgba(0,0,0,0.16);
        margin-top: 8px;
        display: none;
        z-index: 100;
        overflow: hidden;
    }
`;
document.head.appendChild(style);