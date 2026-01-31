<?php
/**
 * Template Name: Inicio Airbnb Complete
 * Description: Página de inicio estilo Airbnb completo con todas las mejoras
 */

// No cargar header y footer del tema
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('page-template-airbnb'); ?>>

<?php
// Obtener categorías y ubicaciones
$listing_categories = get_terms(array(
    'taxonomy' => 'hp_listing_category',
    'hide_empty' => false,
    'parent' => 0
));

// Verificar usuario
$user_logged_in = is_user_logged_in();
$current_user = wp_get_current_user();
?>

<div class="mrb-wrapper">
    <!-- Header Minimalista con Dropdown -->
    <header class="mrb-custom-header">
        <div class="mrb-header-content">
            <a href="<?php echo home_url(); ?>" class="mrb-logo">
                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;fill:#FF385C;height:32px;width:32px;">
                    <path d="M16 1c2.008 0 3.463.963 4.751 3.269l.533 1.025c1.954 3.83 6.114 12.54 7.1 14.836l.145.353c.667 1.666.557 2.894-.35 3.821-.766.78-1.814.995-3.195.689-1.213-.269-2.827-.912-4.746-1.863l-.29-.145c-1.99-1.006-5.057-2.597-6.888-3.524-1.831.927-4.898 2.518-6.888 3.524l-.29.145c-1.919.951-3.533 1.594-4.746 1.863-1.381.306-2.429.091-3.195-.689-.907-.927-1.017-2.155-.35-3.82l.145-.354c.986-2.295 5.146-11.006 7.1-14.836l.533-1.025C12.537 1.963 13.992 1 16 1z"/>
                </svg>
                <span>Reservas.Events</span>
            </a>
            
            <div class="mrb-user-menu" onclick="toggleDropdown(this)">
                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;fill:none;height:16px;width:16px;stroke:currentColor;stroke-width:3;overflow:visible;">
                    <g fill="none" fill-rule="nonzero">
                        <path d="m2 16h28"></path>
                        <path d="m2 24h28"></path>
                        <path d="m2 8h28"></path>
                    </g>
                </svg>
                <div class="mrb-user-avatar">
                    <?php if ($user_logged_in) : 
                        echo strtoupper(substr($current_user->display_name, 0, 1));
                    else : ?>
                        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;height:16px;width:16px;fill:currentColor;">
                            <path d="m16 .7c-8.437 0-15.3 6.863-15.3 15.3s6.863 15.3 15.3 15.3 15.3-6.863 15.3-15.3-6.863-15.3-15.3-15.3zm0 28c-4.021 0-7.605-1.884-9.933-4.81a12.425 12.425 0 0 1 6.451-4.4 6.507 6.507 0 0 1 -3.018-5.49c0-3.584 2.916-6.5 6.5-6.5s6.5 2.916 6.5 6.5a6.513 6.513 0 0 1 -3.019 5.491 12.42 12.42 0 0 1 6.452 4.4c-2.328 2.925-5.912 4.809-9.933 4.809z"></path>
                        </svg>
                    <?php endif; ?>
                </div>
                
                <!-- Dropdown Menu -->
                <div class="mrb-dropdown-menu" id="user-dropdown">
                    <?php if ($user_logged_in) : ?>
                        <a href="/mi-cuenta" class="mrb-dropdown-item bold">Mensajes</a>
                        <a href="/mis-reservas" class="mrb-dropdown-item bold">Reservas</a>
                        <a href="/favoritos" class="mrb-dropdown-item bold">Favoritos</a>
                        <div class="mrb-dropdown-divider"></div>
                        <a href="/publicar-servicio" class="mrb-dropdown-item">Publicar servicio</a>
                        <a href="/mi-cuenta" class="mrb-dropdown-item">Cuenta</a>
                        <div class="mrb-dropdown-divider"></div>
                        <a href="<?php echo wp_logout_url(home_url()); ?>" class="mrb-dropdown-item">Cerrar sesión</a>
                    <?php else : ?>
                        <a href="/wp-login.php" class="mrb-dropdown-item bold">Iniciar sesión</a>
                        <a href="/registro" class="mrb-dropdown-item">Registrarse</a>
                        <div class="mrb-dropdown-divider"></div>
                        <a href="/publicar-servicio" class="mrb-dropdown-item">Publicar servicio</a>
                        <a href="/ayuda" class="mrb-dropdown-item">Ayuda</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero con Búsqueda -->
    <section class="mrb-hero">
        <div class="mrb-container">
            <div class="mrb-search-container">
                <div class="mrb-search-box">
                    <div class="mrb-search-field mrb-search-what">
                        <label>¿Qué necesitas?</label>
                        <input type="text" id="mrb-service-search" placeholder="Salón, DJ, Fotografía..." autocomplete="off" onkeyup="showServiceSuggestions(this.value)">
                        <div class="mrb-search-suggestions" id="service-suggestions"></div>
                    </div>
                    
                    <div class="mrb-search-divider"></div>
                    
                    <div class="mrb-search-field mrb-search-where">
                        <label>¿Dónde?</label>
                        <select id="mrb-location-search">
                            <option value="">Todas las ciudades</option>
                            <?php
                            // Función recursiva para obtener todas las ubicaciones anidadas
                            function get_recursive_locations_airbnb($parent_id = 0, $level = 0) {
                                $terms = get_terms(array(
                                    'taxonomy' => 'hp_listing_ubicacion',
                                    'hide_empty' => false,
                                    'parent' => $parent_id,
                                    'orderby' => 'name',
                                    'order' => 'ASC'
                                ));
                                
                                if (empty($terms)) return;
                                
                                foreach ($terms as $term) {
                                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                                    echo '<option value="' . esc_attr($term->slug) . '">';
                                    echo $indent . esc_html($term->name);
                                    echo '</option>';
                                    
                                    // Recursivamente obtener hijos
                                    get_recursive_locations_airbnb($term->term_id, $level + 1);
                                }
                            }
                            
                            // Mostrar todas las ubicaciones recursivamente
                            get_recursive_locations_airbnb();
                            ?>
                        </select>
                    </div>
                    
                    <div class="mrb-search-divider"></div>
                    
                    <div class="mrb-search-field mrb-search-when">
                        <label>¿Cuándo?</label>
                        <div class="mrb-calendar-wrapper">
                            <input type="date" id="mrb-date-search" placeholder="Fecha del evento">
                            <div class="mrb-calendar-icon">
                                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="presentation" focusable="false" style="display:block;fill:none;height:16px;width:16px;stroke:currentColor;stroke-width:2;overflow:visible">
                                    <path fill="none" d="M24 4v2m-8-2v2m-8-2v2m16 2H8a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2zm-9 11h6m-6 4h6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <button class="mrb-search-button" onclick="performSearch()">
                        <svg viewBox="0 0 32 32" aria-hidden="true" style="display:block;fill:none;height:16px;width:16px;stroke:currentColor;stroke-width:4;overflow:visible">
                            <g fill="none"><path d="m13 24c6.0751322 0 11-4.9248678 11-11 0-6.07513225-4.9248678-11-11-11-6.07513225 0-11 4.92486775-11 11 0 6.0751322 4.92486775 11 11 11zm8-3 9 9"></path></g>
                        </svg>
                        <span class="mrb-search-text">Buscar</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Categorías -->
    <section class="mrb-categories">
        <div class="mrb-container">
            <div class="mrb-categories-slider">
                <button class="mrb-slider-arrow mrb-slider-prev" onclick="slideCategories('prev')">‹</button>
                
                <div class="mrb-categories-track" id="categories-track">
                    <?php
                    // Array de iconos personalizados para cada categoría
                    $category_icons = array(
                        'lugares-para-eventos' => '<path d="M2 4v16a2 2 0 0 0 2 2h24a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2zm2 0h24v16H4V4zm12 3a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 2a3 3 0 1 1 0 6 3 3 0 0 0 0-6z"/>',
                        'fotografia' => '<path d="M21 10V7l5-5v23l-5-5v-3a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3zm0-8v5h-2V4a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v3H5V4a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4z"/>',
                        'musica' => '<path d="M26 3v18.05a5 5 0 1 0 2 3.95V3h-2zM24 26a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm-10-4.05a5 5 0 1 0 2 3.95V7H8v2h6v12.05zM12 25a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>',
                        'alimentos-y-bebidas' => '<path d="M26 1v7a4 4 0 0 1-4 4h-6v16h4v2H12v-2h4V12h-6a4 4 0 0 1-4-4V1h2v7a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V1h2zM7 1v10l1 1v18h2V12l1-1V1H7z"/>',
                        'decoracion' => '<path d="M16 1l6 10h11l-9 6.5L27.5 31 16 23 4.5 31 8 17.5 -1 11h11L16 1z"/>',
                        'default' => '<path d="M16 1a15 15 0 1 0 0 30 15 15 0 0 0 0-30zm0 2a13 13 0 1 1 0 26 13 13 0 0 0 0-26z"/>'
                    );
                    ?>
                    
                    <div class="mrb-category-item active" onclick="filterByCategory('all')">
                        <div class="mrb-category-icon">
                            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;height:24px;width:24px;fill:currentColor;">
                                <path d="M16 1a15 15 0 1 0 0 30 15 15 0 0 0 0-30zm0 2a13 13 0 1 1 0 26 13 13 0 0 0 0-26z"/>
                            </svg>
                        </div>
                        <span>Todos</span>
                    </div>
                    
                    <?php if (!empty($listing_categories)) :
                        foreach ($listing_categories as $category) :
                            $slug = $category->slug;
                            $icon_path = isset($category_icons[$slug]) ? $category_icons[$slug] : $category_icons['default'];
                            ?>
                            <div class="mrb-category-item" onclick="filterByCategory('<?php echo esc_attr($slug); ?>')">
                                <div class="mrb-category-icon">
                                    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;height:24px;width:24px;fill:currentColor;">
                                        <?php echo $icon_path; ?>
                                    </svg>
                                </div>
                                <span><?php echo esc_html($category->name); ?></span>
                            </div>
                        <?php endforeach;
                    endif; ?>
                </div>
                
                <button class="mrb-slider-arrow mrb-slider-next" onclick="slideCategories('next')">›</button>
            </div>
        </div>
    </section>

    <!-- SECCIÓN: Lugares Verificados -->
    <?php
    $verified_args = array(
        'post_type' => 'hp_listing',
        'posts_per_page' => 8,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'hp_verified',
                'value' => '1',
                'compare' => '='
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    $verified_query = new WP_Query($verified_args);
    
    if ($verified_query->have_posts()) : ?>
        <section class="mrb-section">
            <div class="mrb-container">
                <div class="mrb-section-header">
                    <h2 class="mrb-section-title">Servicios Verificados ✓</h2>
                    <p class="mrb-section-subtitle">Proveedores certificados con la mejor calidad garantizada</p>
                </div>
                
                <div class="mrb-section-carousel">
                    <div class="mrb-section-track">
                        <?php while ($verified_query->have_posts()) : $verified_query->the_post();
                            include 'template-parts/listing-card.php';
                        endwhile; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif;
    wp_reset_postdata();
    ?>

    <!-- SECCIÓN: Los mejores lugares para tu evento -->
    <?php
    $lugares_args = array(
        'post_type' => 'hp_listing',
        'posts_per_page' => 12,
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'hp_listing_category',
                'field' => 'term_id',
                'terms' => 28, // ID de lugares-para-eventos
            )
        ),
        'meta_key' => 'hp_featured',
        'orderby' => array(
            'meta_value_num' => 'DESC',
            'date' => 'DESC'
        )
    );
    
    $lugares_query = new WP_Query($lugares_args);
    
    if ($lugares_query->have_posts()) : ?>
        <section class="mrb-section">
            <div class="mrb-container">
                <div class="mrb-section-header">
                    <h2 class="mrb-section-title">Los mejores lugares para tu evento</h2>
                    <p class="mrb-section-subtitle">Salones, jardines y espacios únicos para hacer tu evento inolvidable</p>
                </div>
                
                <div class="mrb-listings-grid">
                    <?php while ($lugares_query->have_posts()) : $lugares_query->the_post();
                        include 'template-parts/listing-card.php';
                    endwhile; ?>
                </div>
            </div>
        </section>
    <?php endif;
    wp_reset_postdata();
    ?>

    <!-- SECCIÓN: Servicios Populares -->
    <?php
    $popular_args = array(
        'post_type' => 'hp_listing',
        'posts_per_page' => 8,
        'post_status' => 'publish',
        'meta_key' => 'hp_views',
        'orderby' => 'meta_value_num',
        'order' => 'DESC'
    );
    
    $popular_query = new WP_Query($popular_args);
    
    if ($popular_query->have_posts()) : ?>
        <section class="mrb-section">
            <div class="mrb-container">
                <div class="mrb-section-header">
                    <h2 class="mrb-section-title">Populares esta semana</h2>
                    <p class="mrb-section-subtitle">Los servicios más solicitados por nuestros clientes</p>
                </div>
                
                <div class="mrb-section-carousel">
                    <div class="mrb-section-track">
                        <?php while ($popular_query->have_posts()) : $popular_query->the_post();
                            include 'template-parts/listing-card.php';
                        endwhile; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif;
    wp_reset_postdata();
    ?>

    <!-- SECCIÓN: Nuevos en Reservas.Events -->
    <?php
    $new_args = array(
        'post_type' => 'hp_listing',
        'posts_per_page' => 8,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'date_query' => array(
            array(
                'after' => '30 days ago'
            )
        )
    );
    
    $new_query = new WP_Query($new_args);
    
    if ($new_query->have_posts()) : ?>
        <section class="mrb-section">
            <div class="mrb-container">
                <div class="mrb-section-header">
                    <h2 class="mrb-section-title">Nuevos en Reservas.Events</h2>
                    <p class="mrb-section-subtitle">Descubre los servicios recién agregados</p>
                </div>
                
                <div class="mrb-section-carousel">
                    <div class="mrb-section-track">
                        <?php while ($new_query->have_posts()) : $new_query->the_post();
                            include 'template-parts/listing-card.php';
                        endwhile; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif;
    wp_reset_postdata();
    ?>

    <!-- SECCIÓN: Todos los Servicios (Grid Completo) -->
    <section class="mrb-listings">
        <div class="mrb-container">
            <div class="mrb-section-header">
                <h2 class="mrb-section-title">Todos los Servicios</h2>
                <p class="mrb-section-subtitle">Explora nuestra colección completa de proveedores para eventos</p>
            </div>
            
            <div class="mrb-listings-grid" id="listings-grid">
                <?php
                // Query para obtener listings con prioridad a lugares-para-eventos
                $args = array(
                    'post_type' => 'hp_listing',
                    'posts_per_page' => 20,
                    'post_status' => 'publish',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'hp_listing_category',
                            'field' => 'term_id',
                            'terms' => 58, // ID de lugares-para-eventos
                            'operator' => 'IN'
                        )
                    ),
                    'orderby' => 'date',
                    'order' => 'DESC'
                );
                
                // Primer query para lugares-para-eventos
                $lugares_query = new WP_Query($args);
                
                // Segundo query para el resto si no hay suficientes
                if ($lugares_query->post_count < 20) {
                    $args2 = array(
                        'post_type' => 'hp_listing',
                        'posts_per_page' => 20 - $lugares_query->post_count,
                        'post_status' => 'publish',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'hp_listing_category',
                                'field' => 'term_id',
                                'terms' => 58,
                                'operator' => 'NOT IN'
                            )
                        ),
                        'orderby' => 'date',
                        'order' => 'DESC'
                    );
                    $otros_query = new WP_Query($args2);
                    
                    // Combinar los posts
                    $all_posts = array_merge($lugares_query->posts, $otros_query->posts);
                    $listings_query = new WP_Query();
                    $listings_query->posts = $all_posts;
                    $listings_query->post_count = count($all_posts);
                } else {
                    $listings_query = $lugares_query;
                }
                
                $listings_query = new WP_Query($args);
                
                if ($listings_query->have_posts()) :
                    while ($listings_query->have_posts()) : $listings_query->the_post();
                        include(get_template_directory() . '/template-parts/listing-card.php');
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="mrb-no-results">
                        <h3>No se encontraron servicios</h3>
                        <p>Intenta ajustar tus filtros o búsqueda</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Infinite Scroll Loader -->
            <div class="mrb-infinite-loader" id="infinite-loader">
                <div class="mrb-loader-spinner"></div>
            </div>
            
            <!-- Botón Flotante de Mapa -->
            <button class="mrb-map-toggle" onclick="toggleMapView()">
                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;fill:currentColor;height:16px;width:16px;">
                    <path d="M31.245 3.747a2.285 2.285 0 0 0-1.01-1.44A2.286 2.286 0 0 0 28.501 2l-7.515 1.67-10-2L2.5 3.557A2.286 2.286 0 0 0 .7 5.802v21.95a2.284 2.284 0 0 0 1.065 1.941A2.29 2.29 0 0 0 2.999 30a2.3 2.3 0 0 0 .501-.054l7.515-1.67 10 2 8.486-1.886a2.285 2.285 0 0 0 1.799-2.245V4.195a2.3 2.3 0 0 0-.055-.448zm-2.746 1.482v19.483l-5.999 1.333v-19.483zM2.999 4.49l6 1.333v19.483l-6-1.333zM11 25.273V5.79l10 2v19.483z"></path>
                </svg>
                <span>Mostrar mapa</span>
            </button>
        </div>
    </section>

    <!-- Modal de Lead/CRM (mantener funcionalidad existente) -->
    <?php if ($user_logged_in) : ?>
    <div id="mrb-lead-modal" class="mrb-modal">
        <div class="mrb-modal-content">
            <button class="mrb-modal-close" onclick="closeLeadModal()">×</button>
            <h2>Seleccionar Lead y Evento</h2>
            
            <div class="mrb-modal-form">
                <div class="mrb-form-group">
                    <label>Lead Actual</label>
                    <select id="lead-select">
                        <option value="">Seleccionar lead existente</option>
                        <!-- Aquí se cargarán los leads dinámicamente -->
                    </select>
                    <button class="mrb-btn-secondary" onclick="createNewLead()">Crear nuevo lead</button>
                </div>
                
                <div class="mrb-form-group">
                    <label>Evento</label>
                    <select id="event-select">
                        <option value="">Seleccionar evento</option>
                        <option value="boda">Boda</option>
                        <option value="xv">XV Años</option>
                        <option value="cumple">Cumpleaños</option>
                        <option value="corporativo">Evento Corporativo</option>
                    </select>
                    <input type="date" id="event-date" placeholder="Fecha del evento">
                </div>
                
                <button class="mrb-btn-primary" onclick="saveLeadEvent()">Guardar y continuar</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer Minimalista -->
    <footer class="mrb-mini-footer">
        <div class="mrb-footer-content">
            <div class="mrb-footer-copyright">
                © <?php echo date('Y'); ?> Reservas.Events, Inc.
            </div>
            <div class="mrb-footer-links">
                <a href="/privacidad" class="mrb-footer-link">Privacidad</a>
                <a href="/terminos" class="mrb-footer-link">Términos</a>
                <a href="/mapa-del-sitio" class="mrb-footer-link">Mapa del sitio</a>
            </div>
        </div>
    </footer>
</div>

<script>
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

// Variable para el timeout de debounce
let searchTimeout = null;

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

// Dropdown Menu Toggle
function toggleDropdown(element) {
    const dropdown = element.querySelector('.mrb-dropdown-menu');
    dropdown.classList.toggle('active');
    
    // Cerrar al hacer click fuera
    document.addEventListener('click', function closeDropdown(e) {
        if (!element.contains(e.target)) {
            dropdown.classList.remove('active');
            document.removeEventListener('click', closeDropdown);
        }
    });
}

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

/**
 * Búsqueda con sugerencias dinámicas y debounce
 */
function showServiceSuggestions(query) {
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
        suggestionsDiv.style.position = 'absolute';
        suggestionsDiv.style.top = '100%';
        suggestionsDiv.style.left = '0';
        suggestionsDiv.style.right = '0';
        suggestionsDiv.style.background = 'white';
        suggestionsDiv.style.border = '1px solid #DDDDDD';
        suggestionsDiv.style.borderRadius = '12px';
        suggestionsDiv.style.marginTop = '8px';
        suggestionsDiv.style.maxHeight = '300px';
        suggestionsDiv.style.overflowY = 'auto';
        suggestionsDiv.style.zIndex = '100';
        suggestionsDiv.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
        
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
    
    // Construir URL de búsqueda
    let searchUrl = '/servicios?';
    if (service) searchUrl += `search=${encodeURIComponent(service)}&`;
    if (location) searchUrl += `location=${encodeURIComponent(location)}&`;
    if (date) searchUrl += `date=${encodeURIComponent(date)}&`;
    
    // Redirigir a la página de resultados
    window.location.href = searchUrl;
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
    
    // En caso de error, usar filtrado local
    filterListingsLocally();
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
 * Toggle Map View
 */
function toggleMapView() {
    mapViewActive = !mapViewActive;
    const button = document.querySelector('.mrb-map-toggle');
    
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
    
    // Mostrar loader
    const loader = document.getElementById('infinite-loader');
    if (loader) {
        loader.classList.add('active');
    }
    
    // Hacer petición AJAX real
    setTimeout(() => {
        isLoading = false;
        if (loader) {
            loader.classList.remove('active');
        }
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
</script>

<?php wp_footer(); ?>
</body>
</html>