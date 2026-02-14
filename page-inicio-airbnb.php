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
    
    <!-- 🎯 CSS MASTER FINAL - SIN CONFLICTOS -->
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/mrb-master-final.css?v=<?php echo time(); ?>">
    <!-- 🔥 FORCE FIX - Script agresivo que override todo -->
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/mrb-force-fix.js?v=<?php echo time(); ?>"></script>
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
                <?php
                $custom_logo_id = get_theme_mod('custom_logo');
                if ($custom_logo_id) {
                    $logo_image = wp_get_attachment_image($custom_logo_id, 'full', false, array(
                        'style' => 'height: 32px; width: auto; max-width: 150px;'
                    ));
                    echo $logo_image;
                } else {
                    echo '<span style="font-weight: 600; font-size: 18px; color: #FF385C;">Reservas.Events</span>';
                }
                ?>
            </a>
            
            <div class="mrb-user-menu" data-dropdown="user" aria-label="Menú de usuario" role="button" tabindex="0">
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
                        <input type="text" id="mrb-service-search" placeholder="Salón, DJ, Fotografía..." autocomplete="off">
                        <div class="mrb-search-suggestions" id="service-suggestions"></div>
                    </div>
                    
                    <div class="mrb-search-divider"></div>
                    
                    <div class="mrb-search-field mrb-search-where">
                        <label>¿Dónde?</label>
                        <select id="mrb-location-search">
                            <option value="">Todas las ciudades</option>
                            <?php
                            // Función SIMPLE para mostrar TODAS las ubicaciones sin jerarquía
                            $all_locations = get_terms(array(
                                'taxonomy' => 'hp_listing_ubicacion',
                                'hide_empty' => false,
                                'orderby' => 'name',
                                'order' => 'ASC'
                            ));
                            
                            echo '<!-- Debug Ubicaciones: Total encontradas: ' . (is_array($all_locations) ? count($all_locations) : 0) . ' -->';
                            
                            if (!empty($all_locations) && !is_wp_error($all_locations)) {
                                foreach ($all_locations as $location) {
                                    echo '<option value="' . esc_attr($location->slug) . '">';
                                    echo esc_html($location->name) . ' (' . $location->count . ' listings)';
                                    echo '</option>';
                                }
                            } else {
                                echo '<!-- Debug: No locations found or WP_Error -->';
                            }
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
                    
                    <button class="mrb-search-button" id="search-btn">
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
                    // Array COMPLETO de iconos para TODAS las categorías
                    $category_icons = array(
                        'lugares-para-eventos' => '<path d="M25 4v18a2 2 0 0 1 2 2v4h-2v-4H7v4H5v-4a2 2 0 0 1 2-2V4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2zm-2 0H9v18h14V4z"/>',
                        'fotografia' => '<path d="M21 10V6h-4a2 2 0 0 0-2 2v2h-2V8a4 4 0 0 1 4-4h4V2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2zm-10 0H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h24a2 2 0 0 0 2-2V12a2 2 0 0 0-2-2h-7v2h7v16H4V12h7v-2zm5 4a6 6 0 1 0 0 12 6 6 0 0 0 0-12zm0 2a4 4 0 1 1 0 8 4 4 0 0 1 0-8z"/>',
                        'musica' => '<path d="M26 3v18.05a5 5 0 1 0 2 3.95V3h-2zM24 26a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm-10-4.05a5 5 0 1 0 2 3.95V7H8v2h6v12.05zM12 25a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>',
                        'alimentos-y-bebidas' => '<path d="M26 1v7a4 4 0 0 1-4 4h-6v16h4v2H12v-2h4V12h-6a4 4 0 0 1-4-4V1h2v7a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V1h2zM7 1v10l1 1v18h2V12l1-1V1H7z"/>',
                        'decoracion' => '<path d="M16 1l6 10h11l-9 6.5L27.5 31 16 23 4.5 31 8 17.5 -1 11h11L16 1z"/>',
                        'entretenimiento' => '<path d="M16 3a10 10 0 0 1 10 10c0 5.52-4.48 10-10 10S6 18.52 6 13A10 10 0 0 1 16 3zm0 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16zm-1 3v6l5 3 1-2-4-2V8h-2z"/>',
                        'artistas' => '<path d="M16 3a13 13 0 0 1 13 13c0 6.88-5.44 12.58-12.32 12.97L16 29a13 13 0 0 1 0-26zm0 2a11 11 0 0 0 0 22 11 11 0 0 0 0-22zm0 3a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"/>',
                        'joyeria' => '<path d="M16 3l4 8h12l-10 7 4 11-10-7-10 7 4-11L0 11h12l4-8z"/>',
                        'mobiliario' => '<path d="M25 4v18a2 2 0 0 1 2 2v4h-2v-4H7v4H5v-4a2 2 0 0 1 2-2V4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2zm-2 0H9v18h14V4z"/>',
                        'transporte' => '<path d="M29 10H17V6a4 4 0 0 0-4-4H3v28h3c0-2.76 2.24-5 5-5s5 2.24 5 5h6c0-2.76 2.24-5 5-5s5 2.24 5 5h1V14l-4-4zM11 27a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm16 0a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm2-15h-12v-2h10l2 2z"/>',
                        'hospedaje' => '<path d="M27 4v24H5V4h22zm0-2H5a2 2 0 0 0-2 2v24a2 2 0 0 0 2 2h22a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM16 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 8a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm-3 8h6v2h-6v-2z"/>',
                        'vestidos' => '<path d="M21 2h-2l-2-2h-2l-2 2h-2a2 2 0 0 0-2 2v4l-2 1v21a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-2-1V4a2 2 0 0 0-2-2z"/>',
                        'planners' => '<path d="M28 4v24a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h20a2 2 0 0 1 2 2zM12 18h8v2h-8v-2zm0-4h8v2h-8v-2zm0-4h8v2h-8v-2z"/>',
                        'viajes' => '<path d="M27.5 3h-23a1.5 1.5 0 0 0-1.5 1.5v23a1.5 1.5 0 0 0 1.5 1.5h23a1.5 1.5 0 0 0 1.5-1.5v-23A1.5 1.5 0 0 0 27.5 3zM16 25L6 16l10-9 10 9-10 9z"/>',
                        // 🆕 ICONOS NUEVOS PARA CATEGORÍAS FALTANTES
                        'comediantes' => '<path d="M16 3a13 13 0 0 1 13 13c0 7.18-5.82 13-13 13S3 23.18 3 16A13 13 0 0 1 16 3zm0 2a11 11 0 1 0 0 22 11 11 0 0 0 0-22zm-4.5 7.5a1.5 1.5 0 0 1 3 0 1.5 1.5 0 0 1-3 0zm7 0a1.5 1.5 0 0 1 3 0 1.5 1.5 0 0 1-3 0zm-6.5 6c0 2.21 1.79 4 4 4s4-1.79 4-4"/>',
                        'paquetes-todo-incluido' => '<path d="M6 2a2 2 0 0 0-2 2v24a2 2 0 0 0 2 2h20a2 2 0 0 0 2-2V8.83a2 2 0 0 0-.59-1.42L22.58 2.6A2 2 0 0 0 21.17 2H6zm14 2v4a2 2 0 0 0 2 2h4v16H6V4h14zM8 12h16v2H8v-2zm0 4h16v2H8v-2zm0 4h12v2H8v-2z"/>',
                        'pirotecnia-y-efectos-especiales' => '<path d="M16 2l4 8 8 2-6 6 2 8-8-4-8 4 2-8-6-6 8-2 4-8zm0 4.5L14 10l-4 1 3 3-.5 4 3.5-2 3.5 2-.5-4 3-3-4-1-2-3.5z"/>',
                        'toldos-y-carpas' => '<path d="M16 4L4 14h4v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V14h4L16 4zm0 3.66L21.17 12H20v12H12V12h-1.17L16 7.66zM10 18h2v6h-2v-6zm10 0h2v6h-2v-6z"/>',
                        'default' => '<path d="M16 3a13 13 0 0 1 13 13c0 7.18-5.82 13-13 13S3 23.18 3 16A13 13 0 0 1 16 3zm0 2a11 11 0 1 0 0 22 11 11 0 0 0 0-22z"/>'
                    );
                    ?>
                    
                    <div class="mrb-category-item active" 
                         data-category="all" 
                         data-url="/">
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
                            <div class="mrb-category-item" 
                                 data-category="<?php echo esc_attr($slug); ?>" 
                                 data-url="/listing-category/<?php echo esc_attr($slug); ?>/">
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
        'posts_per_page' => 12,
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
                
                <div class="mrb-listings-grid">
                    <?php while ($verified_query->have_posts()) : $verified_query->the_post();
                        include 'template-parts/listing-card.php';
                    endwhile; ?>
                </div>
            </div>
        </section>
    <?php endif;
    wp_reset_postdata();
    ?>

    <!-- SECCIÓN: Servicios Destacados -->
    <?php
    // Primero intentar con hp_featured
    $featured_args = array(
        'post_type' => 'hp_listing',
        'posts_per_page' => 12,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'hp_featured',
                'value' => '1',
                'compare' => '='
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    $featured_query = new WP_Query($featured_args);
    
    // Debug
    echo '<!-- Debug Destacados: Found ' . $featured_query->found_posts . ' featured posts -->';
    
    // Si no hay destacados con hp_featured, usar los más recientes
    if (!$featured_query->have_posts()) {
        $featured_args = array(
            'post_type' => 'hp_listing',
            'posts_per_page' => 12,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        );
        $featured_query = new WP_Query($featured_args);
        echo '<!-- Debug Destacados Fallback: Found ' . $featured_query->found_posts . ' recent posts -->';
    }
    
    if ($featured_query->have_posts()) : ?>
        <section class="mrb-section">
            <div class="mrb-container">
                <div class="mrb-section-header">
                    <h2 class="mrb-section-title">Servicios Destacados ⭐</h2>
                    <p class="mrb-section-subtitle">Los proveedores premium con las mejores valoraciones</p>
                </div>
                
                <div class="mrb-listings-grid">
                    <?php while ($featured_query->have_posts()) : $featured_query->the_post();
                        echo '<!-- Debug Destacado: Post ID: ' . get_the_ID() . ' -->';
                        include 'template-parts/listing-card.php';
                    endwhile; ?>
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
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    $lugares_query = new WP_Query($lugares_args);
    
    // Debug
    echo '<!-- Debug Lugares: Total found: ' . $lugares_query->found_posts . ' -->';
    
    if ($lugares_query->have_posts()) : ?>
        <section class="mrb-section">
            <div class="mrb-container">
                <div class="mrb-section-header">
                    <h2 class="mrb-section-title">Los mejores lugares para tu evento</h2>
                    <p class="mrb-section-subtitle">Salones, jardines y espacios únicos para hacer tu evento inolvidable</p>
                </div>
                
                <div class="mrb-listings-grid">
                    <?php while ($lugares_query->have_posts()) : $lugares_query->the_post();
                        echo '<!-- Debug Lugar: Post ID: ' . get_the_ID() . ' -->';
                        include 'template-parts/listing-card.php';
                    endwhile; ?>
                </div>
            </div>
        </section>
    <?php else : ?>
        <!-- Si no hay lugares específicos, mostrar algunos listados generales -->
        <section class="mrb-section">
            <div class="mrb-container">
                <div class="mrb-section-header">
                    <h2 class="mrb-section-title">Servicios para tu evento</h2>
                    <p class="mrb-section-subtitle">Encuentra todo lo que necesitas para hacer tu evento inolvidable</p>
                </div>
                
                <div class="mrb-listings-grid">
                    <?php
                    $general_args = array(
                        'post_type' => 'hp_listing',
                        'posts_per_page' => 8,
                        'post_status' => 'publish',
                        'orderby' => 'date',
                        'order' => 'DESC'
                    );
                    $general_query = new WP_Query($general_args);
                    
                    if ($general_query->have_posts()) :
                        while ($general_query->have_posts()) : $general_query->the_post();
                            include 'template-parts/listing-card.php';
                        endwhile;
                    endif;
                    wp_reset_postdata();
                    ?>
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
        <div class="mrb-container mrb-listings-container">
            <div class="mrb-section-header">
                <h2 class="mrb-section-title">Todos los Servicios</h2>
                <p class="mrb-section-subtitle">Explora nuestra colección completa de proveedores para eventos</p>
            </div>
            
            <div class="mrb-listings-grid" id="listings-grid">
                <?php
                // Query simple para todos los listings
                $all_listings_args = array(
                    'post_type' => 'hp_listing',
                    'posts_per_page' => 16,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC'
                );
                
                $all_listings_query = new WP_Query($all_listings_args);
                
                // Debug: mostrar información del query
                echo '<!-- Debug: Total posts found: ' . $all_listings_query->found_posts . ' -->';
                
                if ($all_listings_query->have_posts()) :
                    while ($all_listings_query->have_posts()) : $all_listings_query->the_post();
                        // Debug cada post
                        echo '<!-- Debug: Post ID: ' . get_the_ID() . ', Title: ' . get_the_title() . ' -->';
                        include('template-parts/listing-card.php');
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="mrb-no-results">
                        <h3>No se encontraron servicios</h3>
                        <p>Intenta buscar algo específico o revisa más tarde</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Paginación simple -->
            <?php if ($all_listings_query->found_posts > 16) : ?>
            <div class="mrb-pagination">
                <button class="mrb-load-more-btn" onclick="loadMoreListings()">
                    Ver más servicios
                </button>
            </div>
            <?php endif; ?>
            
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
// Funciones específicas para esta plantilla
function toggleDropdown(element) {
    console.log('toggleDropdown called - element:', element); // Debug mejorado
    console.log('User logged in:', <?php echo $user_logged_in ? 'true' : 'false'; ?>); // Debug login status
    
    const dropdown = element.querySelector('.mrb-dropdown-menu');
    if (!dropdown) {
        console.error('Dropdown menu not found in element:', element);
        // Buscar dropdown por ID como backup
        const dropdownById = document.getElementById('user-dropdown');
        if (dropdownById) {
            console.log('Found dropdown by ID as backup');
            dropdown = dropdownById;
        } else {
            console.error('No dropdown found by ID either');
            return;
        }
    } else {
        console.log('Dropdown found:', dropdown);
    }
    
    // Toggle la clase active
    const isActive = dropdown.classList.contains('active');
    console.log('Dropdown current state - isActive:', isActive);
    
    // Cerrar otros dropdowns primero
    document.querySelectorAll('.mrb-dropdown-menu.active').forEach(menu => {
        menu.classList.remove('active');
        console.log('Closed other dropdown');
    });
    
    // Toggle el dropdown actual
    if (!isActive) {
        dropdown.classList.add('active');
        console.log('Dropdown opened - classes:', dropdown.className);
        
        // Verificar CSS
        const computedStyle = window.getComputedStyle(dropdown);
        console.log('Dropdown display style:', computedStyle.display);
        console.log('Dropdown visibility:', computedStyle.visibility);
        
        // Cerrar al hacer click fuera
        const closeDropdown = function(e) {
            if (!element.contains(e.target)) {
                dropdown.classList.remove('active');
                console.log('Dropdown closed by outside click');
                document.removeEventListener('click', closeDropdown);
            }
        };
        
        // Delay para evitar que se cierre inmediatamente
        setTimeout(() => {
            document.addEventListener('click', closeDropdown);
        }, 100); // Incrementar delay
    } else {
        console.log('Dropdown was already open, closing it');
    }
}

// Función alternativa usando event delegation mejorada
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up dropdown events');
    
    // SOLO UN event listener, sin duplicados
    const userMenu = document.querySelector('.mrb-user-menu');
    if (userMenu) {
        console.log('User menu found, adding SINGLE event');
        
        // REMOVER cualquier onclick anterior
        userMenu.onclick = null;
        userMenu.removeAttribute('onclick');
        
        // UN SOLO event listener
        userMenu.addEventListener('click', function(e) {
            console.log('User menu clicked ONCE');
            e.preventDefault();
            e.stopPropagation();
            toggleDropdown(this);
        }, { once: false });
        
        // Verificar que el dropdown existe
        const dropdown = userMenu.querySelector('.mrb-dropdown-menu');
        if (dropdown) {
            console.log('Dropdown menu found in DOM');
        } else {
            console.error('Dropdown menu NOT found in DOM');
        }
    } else {
        console.error('User menu NOT found in DOM');
    }
    
    // Global click handler para cerrar dropdowns
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.mrb-user-menu')) {
            const activeDropdowns = document.querySelectorAll('.mrb-dropdown-menu.active');
            activeDropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
                console.log('Closed dropdown via global click handler');
            });
        }
    });
});

// Función de debug para verificar estado
function debugDropdown() {
    const userMenu = document.querySelector('.mrb-user-menu');
    const dropdown = document.querySelector('.mrb-dropdown-menu');
    console.log('=== DROPDOWN DEBUG ===');
    console.log('User menu element:', userMenu);
    console.log('Dropdown element:', dropdown);
    if (dropdown) {
        console.log('Dropdown classes:', dropdown.className);
        console.log('Dropdown display:', window.getComputedStyle(dropdown).display);
        console.log('Dropdown visibility:', window.getComputedStyle(dropdown).visibility);
    }
    console.log('User logged in:', <?php echo $user_logged_in ? 'true' : 'false'; ?>);
    console.log('======================');
}

// Hacer función de debug disponible globalmente
window.debugDropdown = debugDropdown;
console.log('Dropdown debugging functions loaded. Run debugDropdown() in console for info.');
</script>

<?php 
// Añadir configuración AJAX directamente antes de los scripts
?>
<script>
// Configuración AJAX global
var mrb_ajax_obj = {
    ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
    nonce: '<?php echo wp_create_nonce('mrb_nonce'); ?>'
};
console.log('✅ Configuración AJAX cargada:', mrb_ajax_obj);
</script>

<?php
// Cargar scripts
wp_enqueue_script('jquery');

// NUEVO: Script de redirección al Cotizador en lugar de filtros AJAX
wp_enqueue_script(
    'mrb-redirect-to-cotizador',
    get_stylesheet_directory_uri() . '/assets/js/mrb-redirect-to-cotizador.js',
    array('jquery'),
    filemtime(get_stylesheet_directory() . '/assets/js/mrb-redirect-to-cotizador.js'),
    true
);

// Script de fixes para favoritos y navegación
wp_enqueue_script(
    'mrb-force-fix',
    get_stylesheet_directory_uri() . '/assets/js/mrb-force-fix.js',
    array('jquery'),
    filemtime(get_stylesheet_directory() . '/assets/js/mrb-force-fix.js'),
    true
);

wp_footer(); 
?>
</body>
</html>