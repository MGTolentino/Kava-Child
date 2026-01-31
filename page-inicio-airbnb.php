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

    <!-- SECCIÓN: Servicios Destacados -->
    <?php
    $featured_args = array(
        'post_type' => 'hp_listing',
        'posts_per_page' => 8,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'hp_featured',
                'value' => '1',
                'compare' => '='
            )
        ),
        'orderby' => array(
            'meta_value_num' => 'DESC',
            'date' => 'DESC'
        ),
        'meta_key' => 'hp_rating'
    );
    
    $featured_query = new WP_Query($featured_args);
    
    if ($featured_query->have_posts()) : ?>
        <section class="mrb-section">
            <div class="mrb-container">
                <div class="mrb-section-header">
                    <h2 class="mrb-section-title">Servicios Destacados ⭐</h2>
                    <p class="mrb-section-subtitle">Los proveedores premium con las mejores valoraciones</p>
                </div>
                
                <div class="mrb-section-carousel">
                    <div class="mrb-section-track">
                        <?php while ($featured_query->have_posts()) : $featured_query->the_post();
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
                'terms' => 58, // ID de lugares-para-eventos
            )
        ),
        'orderby' => 'meta_value_num',
        'meta_key' => 'hp_rating',
        'order' => 'DESC'
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
                // Query simple para todos los listings, priorizando lugares-para-eventos
                $all_listings_args = array(
                    'post_type' => 'hp_listing',
                    'posts_per_page' => 20,
                    'post_status' => 'publish',
                    'orderby' => array(
                        'date' => 'DESC',
                    ),
                    'order' => 'DESC'
                );
                
                $all_listings_query = new WP_Query($all_listings_args);
                
                if ($all_listings_query->have_posts()) :
                    while ($all_listings_query->have_posts()) : $all_listings_query->the_post();
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
// Solo funciones específicas para esta plantilla que no están en mrb-functions.js
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
</script>

<?php wp_footer(); ?>
</body>
</html>