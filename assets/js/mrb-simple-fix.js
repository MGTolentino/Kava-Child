/**
 * 🔥 SOLUCIÓN SIMPLE Y DIRECTA - SOLO PARA PÁGINA AIRBNB
 * Se ejecuta al final para override cualquier otro JavaScript
 */

// VERIFICAR QUE ESTAMOS EN LA PÁGINA CORRECTA
if (!document.body.classList.contains('page-template-airbnb')) {
    console.log('🚫 MRB Simple Fix - No es página Airbnb, saliendo...');
    // NO HACER NADA si no estamos en la página correcta
} else {
    console.log('✅ MRB Simple Fix - Página Airbnb detectada, continuando...');

    // Esperar a que todo esté cargado
    setTimeout(function() {
        console.log('🔥 MRB Simple Fix ejecutándose...');
    
    // ========================================
    // 1. ARREGLAR CARDS - NUEVA PESTAÑA
    // ========================================
    const cards = document.querySelectorAll('.mrb-listing-card');
    console.log('Cards encontradas:', cards.length);
    
    cards.forEach(function(card) {
        // REMOVER TODOS los eventos existentes
        card.onclick = null;
        card.removeAttribute('onclick');
        
        // AÑADIR NUEVO evento que abre en NUEVA PESTAÑA
        card.addEventListener('click', function(e) {
            // No abrir si click en favorito
            if (e.target.closest('.mrb-card-favorite')) {
                return;
            }
            
            // Obtener URL
            let url = null;
            
            // Método 1: data-url
            url = this.dataset.url;
            
            // Método 2: desde onclick original
            if (!url) {
                const onclick = this.getAttribute('onclick');
                if (onclick) {
                    const match = onclick.match(/window\.location\.href='([^']+)'/);
                    if (match) url = match[1];
                }
            }
            
            // Método 3: construir desde ID
            if (!url) {
                const postId = this.dataset.listingId;
                if (postId) {
                    // Probar diferentes patrones de URL
                    url = '/listing/' + postId + '/';
                }
            }
            
            console.log('Click en card, URL:', url);
            
            if (url) {
                // ABRIR EN NUEVA PESTAÑA
                window.open(url, '_blank');
            } else {
                console.warn('No se pudo obtener URL del listing');
            }
        });
        
        card.style.cursor = 'pointer';
    });
    
    // ========================================
    // 2. ARREGLAR CATEGORÍAS - NUEVA PESTAÑA
    // ========================================
    const categories = document.querySelectorAll('.mrb-category-item');
    console.log('Categorías encontradas:', categories.length);
    
    // URLs REALES de tu sitio
    const realCategoryUrls = {
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
        // REMOVER eventos existentes
        category.onclick = null;
        category.removeAttribute('onclick');
        
        // AÑADIR NUEVO evento
        category.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Obtener slug
            let slug = this.dataset.category;
            
            // Si no tiene data-category, extraer del onclick
            if (!slug) {
                const onclick = this.getAttribute('onclick');
                if (onclick) {
                    const match = onclick.match(/filterByCategory\('([^']+)'\)/);
                    if (match) slug = match[1];
                }
            }
            
            // Si no tiene slug, usar texto
            if (!slug) {
                const text = this.querySelector('span')?.textContent.toLowerCase();
                if (text === 'todos') {
                    slug = 'all';
                } else if (text) {
                    slug = text.replace(/\s+/g, '-').replace(/[áàä]/g, 'a').replace(/[éèë]/g, 'e').replace(/[íìï]/g, 'i').replace(/[óòö]/g, 'o').replace(/[úùü]/g, 'u').replace(/ñ/g, 'n');
                }
            }
            
            console.log('Click en categoría:', slug);
            
            // Obtener URL
            let url = realCategoryUrls[slug];
            
            if (!url && slug !== 'all') {
                // Construir URL si no está en el mapeo
                url = '/listing-category/' + slug + '/';
            }
            
            if (!url) {
                url = '/'; // Fallback
            }
            
            console.log('Abriendo categoría en nueva pestaña:', url);
            
            // ABRIR EN NUEVA PESTAÑA
            window.open(url, '_blank');
        });
        
        category.style.cursor = 'pointer';
    });
    
    // ========================================
    // 3. ARREGLAR BÚSQUEDA - URL CORRECTA
    // ========================================
    
    // Interceptar formulario de búsqueda
    const searchButton = document.querySelector('.mrb-search-button');
    const searchInput = document.getElementById('mrb-service-search');
    
    if (searchButton && searchInput) {
        console.log('Setup de búsqueda encontrado');
        
        // REMOVER eventos existentes
        searchButton.onclick = null;
        searchButton.removeAttribute('onclick');
        
        // Función de búsqueda personalizada
        function performCustomSearch() {
            const searchTerm = searchInput.value.trim();
            
            if (searchTerm) {
                // Crear slug desde el término de búsqueda
                const slug = searchTerm.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '') // Remover caracteres especiales
                    .replace(/\s+/g, '-') // Reemplazar espacios con guiones
                    .replace(/-+/g, '-') // Múltiples guiones a uno solo
                    .replace(/^-+|-+$/g, ''); // Remover guiones del inicio/final
                
                // Construir URL correcta
                const searchUrl = '/contrata-el-servicio-de/' + slug + '/';
                
                console.log('Búsqueda:', searchTerm, '→ URL:', searchUrl);
                
                // ABRIR EN NUEVA PESTAÑA
                window.open(searchUrl, '_blank');
            }
        }
        
        // Evento en botón
        searchButton.addEventListener('click', function(e) {
            e.preventDefault();
            performCustomSearch();
        });
        
        // Evento en Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performCustomSearch();
            }
        });
    }
    
    // ========================================
    // 4. FORZAR OVERRIDE DE OTROS SCRIPTS
    // ========================================
    
    // Override funciones globales problemáticas
    window.filterByCategory = function(slug) {
        console.log('filterByCategory override llamado:', slug);
        
        const url = realCategoryUrls[slug] || '/listing-category/' + slug + '/';
        window.open(url, '_blank');
    };
    
    window.performSearch = function() {
        console.log('performSearch override llamado');
        
        const searchTerm = document.getElementById('mrb-service-search')?.value.trim();
        if (searchTerm) {
            const slug = searchTerm.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').replace(/^-+|-+$/g, '');
            const searchUrl = '/contrata-el-servicio-de/' + slug + '/';
            window.open(searchUrl, '_blank');
        }
    };
    
    console.log('✅ MRB Simple Fix aplicado completamente');
    
    }, 2000); // Esperar 2 segundos para que otros scripts se carguen primero

    // También ejecutar inmediatamente por si acaso
    document.addEventListener('DOMContentLoaded', function() {
        // VERIFICAR NUEVAMENTE que estamos en la página correcta
        if (!document.body.classList.contains('page-template-airbnb')) {
            return; // Salir si no es la página correcta
        }
        
        console.log('🔥 MRB Simple Fix - DOM ready backup');
        
        // Backup inmediato para cards
        setTimeout(function() {
            const cards = document.querySelectorAll('.mrb-listing-card[onclick]');
            cards.forEach(function(card) {
                const originalOnclick = card.getAttribute('onclick');
                card.onclick = function(e) {
                    if (e.target.closest('.mrb-card-favorite')) return;
                    
                    const match = originalOnclick.match(/window\.location\.href='([^']+)'/);
                    if (match) {
                        window.open(match[1], '_blank');
                    }
                };
            });
        }, 500);
    });

} // Cerrar el if principal