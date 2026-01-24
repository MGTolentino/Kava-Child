<?php
/**
 * Template Name: Inicio Modern Booking
 * Description: Página de inicio personalizada estilo Airbnb para reservas de eventos
 */

get_header(); 

// Obtener categorías reales de HivePress
$listing_categories = get_terms(array(
    'taxonomy' => 'hp_listing_category',
    'hide_empty' => true,
    'parent' => 0,
    'number' => 12
));

// Obtener ubicaciones reales
$locations = get_terms(array(
    'taxonomy' => 'hp_listing_ubicacion',
    'hide_empty' => true,
));

// Verificar si el usuario está logueado
$user_logged_in = is_user_logged_in();
$current_user = wp_get_current_user();
?>

<div class="mrb-wrapper">
    <!-- Hero Section Minimalista estilo Airbnb -->
    <section class="mrb-hero">
        <div class="mrb-container">
            <div class="mrb-hero-content">
            
            <!-- Barra de búsqueda moderna -->
            <div class="mrb-search-container">
                <div class="mrb-search-box">
                    <div class="mrb-search-field mrb-search-what">
                        <label>¿Qué necesitas?</label>
                        <input type="text" id="mrb-service-search" placeholder="Salón, DJ, Fotografía..." autocomplete="off" onkeyup="searchSuggestions(this.value)">
                        <div class="mrb-search-suggestions" id="service-suggestions" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #DDDDDD; border-radius: 12px; margin-top: 8px; max-height: 300px; overflow-y: auto; z-index: 100; box-shadow: 0 2px 8px rgba(0,0,0,0.15);"></div>
                    </div>
                    
                    <div class="mrb-search-divider"></div>
                    
                    <div class="mrb-search-field mrb-search-where">
                        <label>¿Dónde?</label>
                        <select id="mrb-location-search">
                            <option value="">Todas las ciudades</option>
                            <optgroup label="Nuevo León">
                                <option value="monterrey">Monterrey</option>
                                <option value="san-pedro">San Pedro Garza García</option>
                                <option value="guadalupe">Guadalupe</option>
                                <option value="apodaca">Apodaca</option>
                                <option value="escobedo">Escobedo</option>
                                <option value="santa-catarina">Santa Catarina</option>
                            </optgroup>
                            <optgroup label="Coahuila">
                                <option value="saltillo">Saltillo</option>
                                <option value="torreon">Torreón</option>
                                <option value="monclova">Monclova</option>
                                <option value="arteaga">Arteaga</option>
                            </optgroup>
                            <optgroup label="Texas">
                                <option value="mcallen">McAllen</option>
                                <option value="houston">Houston</option>
                                <option value="san-antonio">San Antonio</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div class="mrb-search-divider"></div>
                    
                    <div class="mrb-search-field mrb-search-when">
                        <label>¿Cuándo?</label>
                        <input type="date" id="mrb-date-search" placeholder="Fecha del evento">
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


    <!-- Categorías con Iconos -->
    <section class="mrb-categories">
        <div class="mrb-container">
            <div class="mrb-categories-header">
                <h2>Explora por categoría</h2>
                <button class="mrb-categories-all">Ver todas las categorías</button>
            </div>
            
            <div class="mrb-categories-slider">
                <button class="mrb-slider-arrow mrb-slider-prev" onclick="slideCategories('prev')">‹</button>
                
                <div class="mrb-categories-track" id="categories-track">
                    <div class="mrb-category-item active" onclick="filterByCategory('all')">
                        <div class="mrb-category-icon">
                            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;height:24px;width:24px;fill:currentColor;">
                                <path d="M13 0a13 13 0 0 1 9.87 21.52l8.3 8.3a1 1 0 0 1-1.32 1.5l-.1-.08-8.3-8.3a13 13 0 1 1-8.45-22.94zm0 2a11 11 0 1 0 0 22 11 11 0 0 0 0-22z"></path>
                            </svg>
                        </div>
                        <span>Todos</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('lugares-para-eventos')">
                        <div class="mrb-category-icon">
                            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;height:24px;width:24px;fill:currentColor;">
                                <path d="M28 4v24H4V4h24zm0-2H4a2 2 0 0 0-2 2v24a2 2 0 0 0 2 2h24a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM16 7a9 9 0 1 0 0 18 9 9 0 0 0 0-18zm0 2a7 7 0 1 1 0 14 7 7 0 0 1 0-14z"></path>
                            </svg>
                        </div>
                        <span>Lugares para Eventos</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('fotografia')">
                        <div class="mrb-category-icon">
                            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;height:24px;width:24px;fill:currentColor;">
                                <path d="M21 10V6h-4a2 2 0 0 0-2 2v2h-2V8a4 4 0 0 1 4-4h4V2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2zm-10 0H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h24a2 2 0 0 0 2-2V12a2 2 0 0 0-2-2h-7v2h7v16H4V12h7v-2zm5 4a6 6 0 1 0 0 12 6 6 0 0 0 0-12zm0 2a4 4 0 1 1 0 8 4 4 0 0 1 0-8z"></path>
                            </svg>
                        </div>
                        <span>Fotografía y Video</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('musica')">
                        <div class="mrb-category-icon">
                            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;height:24px;width:24px;fill:currentColor;">
                                <path d="M26 3v18.05a5 5 0 1 0 2 3.95V3h-2zM24 26a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm-10-4.05a5 5 0 1 0 2 3.95V7H8v2h6v12.05zM12 25a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path>
                            </svg>
                        </div>
                        <span>Música y DJ</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('alimentos-y-bebidas')">
                        <div class="mrb-category-icon">
                            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;height:24px;width:24px;fill:currentColor;">
                                <path d="M26 1v7a4 4 0 0 1-4 4h-6v16h4v2H12v-2h4V12h-6a4 4 0 0 1-4-4V1h2v7a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V1h2zM7 1v10l1 1v18h2V12l1-1V1H7z"></path>
                            </svg>
                        </div>
                        <span>Alimentos y Bebidas</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('decoracion')">
                        <div class="mrb-category-icon">
                            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;height:24px;width:24px;fill:currentColor;">
                                <path d="M16 1l6 10h11l-9 6.5L27.5 31 16 23 4.5 31 8 17.5 -1 11h11L16 1z"></path>
                            </svg>
                        </div>
                        <span>Decoración</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('artistas')">
                        <div class="mrb-category-icon">
                            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;height:24px;width:24px;fill:currentColor;">
                                <path d="M16 3a13 13 0 0 1 13 13c0 6.88-5.44 12.58-12.32 12.97L16 29a13 13 0 0 1 0-26zm0 2a11 11 0 0 0 0 22 11 11 0 0 0 0-22zm0 3a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"></path>
                            </svg>
                        </div>
                        <span>Artistas</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('entretenimiento')">
                        <div class="mrb-category-icon">
                            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;height:24px;width:24px;fill:currentColor;">
                                <path d="M20.5 3a8.5 8.5 0 0 1 8.5 8.5c0 3.97-7.16 12.06-10.76 15.74a2 2 0 0 1-2.48 0C12.16 23.56 5 15.47 5 11.5A8.5 8.5 0 0 1 13.5 3a8.46 8.46 0 0 1 3.5.75A8.46 8.46 0 0 1 20.5 3z"></path>
                            </svg>
                        </div>
                        <span>Entretenimiento</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('mobiliario')">
                        <div class="mrb-category-icon">
                            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="display:block;height:24px;width:24px;fill:currentColor;">
                                <path d="M25 4v18a2 2 0 0 1 2 2v4h-2v-4H7v4H5v-4a2 2 0 0 1 2-2V4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2zm-2 0H9v18h14V4z"></path>
                            </svg>
                        </div>
                        <span>Mobiliario</span>
                    </div>
                </div>
                
                <button class="mrb-slider-arrow mrb-slider-next" onclick="slideCategories('next')">›</button>
            </div>
        </div>
    </section>

    <!-- Listado de Servicios -->
    <section class="mrb-listings">
        <div class="mrb-container">
            
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
                        $listing_id = get_the_ID();
                        $featured_image = get_the_post_thumbnail_url($listing_id, 'large');
                        $price = get_post_meta($listing_id, 'hp_price', true);
                        $location = wp_get_post_terms($listing_id, 'hp_listing_ubicacion');
                        $category = wp_get_post_terms($listing_id, 'hp_listing_category');
                        $rating = get_post_meta($listing_id, 'hp_rating', true);
                        $reviews_count = get_post_meta($listing_id, 'hp_reviews_count', true);
                        ?>
                        
                        <div class="mrb-listing-card" onclick="window.location.href='<?php echo get_permalink(); ?>'">
                            <div class="mrb-card-slider">
                                <?php 
                                // Obtener la imagen destacada o placeholder
                                if ($featured_image) : ?>
                                    <img class="mrb-card-image" src="<?php echo esc_url($featured_image); ?>" alt="<?php the_title(); ?>" loading="lazy">
                                <?php else : ?>
                                    <img class="mrb-card-image" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.svg" alt="<?php the_title(); ?>">
                                <?php endif; ?>
                                
                                <button class="mrb-card-favorite" onclick="toggleFavorite(event, <?php echo $listing_id; ?>)">
                                    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="presentation" focusable="false" style="display: block; fill: rgba(0, 0, 0, 0.5); height: 24px; width: 24px; stroke: white; stroke-width: 2; overflow: visible;">
                                        <path d="m16 28c7-4.733 14-10 14-17 0-1.792-.683-3.583-2.05-4.95-1.367-1.366-3.158-2.05-4.95-2.05-1.791 0-3.583.684-4.949 2.05l-2.051 2.051-2.05-2.051c-1.367-1.366-3.158-2.05-4.95-2.05-1.791 0-3.583.684-4.949 2.05-1.367 1.367-2.051 3.158-2.051 4.95 0 7 7 12.267 14 17z"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="mrb-card-content">
                                <div class="mrb-card-row-1">
                                    <span class="mrb-card-title"><?php the_title(); ?></span>
                                    <?php if ($rating && $rating > 0) : ?>
                                        <span class="mrb-card-rating">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" aria-hidden="true" role="presentation" focusable="false" style="display: block; height: 12px; width: 12px; fill: currentcolor;">
                                                <path fill-rule="evenodd" d="m15.1 1.58-4.13 8.88-9.86 1.27a1 1 0 0 0-.54 1.74l7.3 6.57-1.97 9.85a1 1 0 0 0 1.48 1.06l8.62-5 8.63 5a1 1 0 0 0 1.48-1.06l-1.97-9.85 7.3-6.57a1 1 0 0 0-.55-1.73l-9.86-1.28-4.12-8.88a1 1 0 0 0-1.82 0z"></path>
                                            </svg>
                                            <?php echo number_format($rating, 2); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mrb-card-row-2">
                                    <?php if (!empty($category)) : ?>
                                        <span class="mrb-card-subtitle"><?php echo $category[0]->name; ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($location)) : ?>
                                        <span class="mrb-card-subtitle">• <?php echo $location[0]->name; ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($price) : ?>
                                    <div class="mrb-card-row-3">
                                        <span class="mrb-card-price">
                                            <span class="mrb-price-amount">$<?php echo number_format($price, 0, '.', ','); ?> MXN</span>
                                            <span class="mrb-price-unit"> por evento</span>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                    <?php 
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
            
            <!-- Botón cargar más -->
            <div class="mrb-load-more-container">
                <button class="mrb-load-more" onclick="loadMoreListings()">
                    Mostrar más servicios
                </button>
            </div>
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
</div>

<?php get_footer(); ?>