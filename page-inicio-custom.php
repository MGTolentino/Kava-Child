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
                        <input type="text" id="mrb-service-search" placeholder="Salón, DJ, Fotografía..." autocomplete="off">
                        <div class="mrb-search-suggestions" id="service-suggestions"></div>
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
                    <?php
                    // Iconos SVG para categorías
                    $category_icons = array(
                        'default' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="presentation" focusable="false" style="display: block; height: 24px; width: 24px; fill: currentcolor;"><path d="M16 1c8.284 0 15 6.716 15 15 0 8.284-6.716 15-15 15-8.284 0-15-6.716-15-15C1 7.716 7.716 1 16 1zm0 2C8.82 3 3 8.82 3 16s5.82 13 13 13 13-5.82 13-13S23.18 3 16 3z"></path></svg>',
                        'lugares' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="presentation" focusable="false" style="display: block; height: 24px; width: 24px; fill: currentcolor;"><path d="M28 4a2 2 0 0 1 1.995 1.85L30 6v14a2 2 0 0 1-1.85 1.995L28 22h-6v4h2a2 2 0 0 1 1.995 1.85L26 28a2 2 0 0 1-1.85 1.995L24 30H8a2 2 0 0 1-1.995-1.85L6 28a2 2 0 0 1 1.85-1.995L8 26h2v-4H4a2 2 0 0 1-1.995-1.85L2 20V6a2 2 0 0 1 1.85-1.995L4 4zm0 2H4v14h24V6zm-6 20h-8v2h8v-2zm-5-14a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"></path></svg>',
                        'musica' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="presentation" focusable="false" style="display: block; height: 24px; width: 24px; fill: currentcolor;"><path d="M26 3a5 5 0 0 1 5 5v11a5 5 0 0 1-5 5h-1.15a5 5 0 0 1-3.519 3.457l-.331.052V28a2 2 0 0 1-1.85 1.995L19 30H7a2 2 0 0 1-1.995-1.85L5 28V8a5 5 0 0 1 4.995-5L10 3h16z"></path></svg>',
                        'fotografia' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="presentation" focusable="false" style="display: block; height: 24px; width: 24px; fill: currentcolor;"><path d="M16 4a2 2 0 0 1 1.744 1.018l.156.282 2.1 4.2H28a2 2 0 0 1 1.995 1.85L30 11.5V26a2 2 0 0 1-1.85 1.995L28 28H4a2 2 0 0 1-1.995-1.85L2 26V11.5a2 2 0 0 1 1.85-1.995L4 9.5h8l2.1-4.2A2 2 0 0 1 15.894 4H16zm0 2h-.472l-2.1 4.2a1 1 0 0 1-.77.794L12.5 11h-8v15h23V11h-8.5a1 1 0 0 1-.928-1.006l2.1-4.2L16.472 6H16zm0 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"></path></svg>',
                        'decoracion' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="presentation" focusable="false" style="display: block; height: 24px; width: 24px; fill: currentcolor;"><path d="M16 1l.324.001a16.5 16.5 0 0 1 11.27 5.454l.2.206A16.501 16.501 0 0 1 31 16.5V31h-2V16.5a14.5 14.5 0 0 0-2.821-8.605l-.179-.257A14.5 14.5 0 0 0 16.5 3L16 3zm0 5a11 11 0 0 1 11 10.988V31h-2V17c0-4.89-3.579-8.945-8.258-9.724L16.5 7.17 16 7.083z"></path></svg>',
                        'catering' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="presentation" focusable="false" style="display: block; height: 24px; width: 24px; fill: currentcolor;"><path d="M26 1a3 3 0 0 1 3 3c0 .28 0 .55-.01.82A208.62 208.62 0 0 1 26.98 29l-.02.23V30h-2V29a206.63 206.63 0 0 0 2-24c.01-.24.01-.48.01-.73a1 1 0 0 0-2-.27 194.14 194.14 0 0 1-2 23l-.02.23V30h-2V27.1a192.15 192.15 0 0 0 2-22.9V3a1 1 0 0 0-1.75-.66A218.13 218.13 0 0 1 18.98 28l-.02.23V30h-2V28.2A216.13 216.13 0 0 0 19 2.33 3 3 0 0 1 21.82.07c.06-.01.12-.02.18-.02L22.1.04a3 3 0 0 1 2.73-.97zM16 1v7a4 4 0 0 1-3 3.86V30h-2V11.86a4 4 0 0 1-3-3.6L8 8V1h2v7a2 2 0 0 0 1 1.73V1h2v8.73A2 2 0 0 0 14 8V1h2zM5 1v10a4 4 0 0 1-3 3.86V30h2V14.86a4 4 0 0 0 3-3.6L7 11V1H5z"></path></svg>'
                    );
                    
                    if (!empty($listing_categories)) :
                        foreach ($listing_categories as $category) :
                            $slug = $category->slug;
                            $icon = isset($category_icons[$slug]) ? $category_icons[$slug] : $category_icons['default'];
                            ?>
                            <div class="mrb-category-item" onclick="filterByCategory('<?php echo esc_attr($slug); ?>')">
                                <div class="mrb-category-icon">
                                    <?php echo $icon; ?>
                                </div>
                                <span><?php echo esc_html($category->name); ?></span>
                            </div>
                            <?php
                        endforeach;
                    endif;
                    ?>
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
                // Query para obtener TODOS los listings reales de HivePress
                $args = array(
                    'post_type' => 'hp_listing',
                    'posts_per_page' => 24,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC'
                );
                
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