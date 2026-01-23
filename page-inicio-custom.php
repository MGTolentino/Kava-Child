<?php
/**
 * Template Name: Inicio Modern Booking
 * Description: Página de inicio personalizada estilo moderno para reservas de eventos
 */

get_header(); 

// Obtener categorías de HivePress
$listing_categories = get_terms(array(
    'taxonomy' => 'hp_listing_category',
    'hide_empty' => false,
    'parent' => 0
));

// Obtener ubicaciones
$locations = get_terms(array(
    'taxonomy' => 'hp_listing_ubicacion',
    'hide_empty' => false,
));

// Verificar si el usuario está logueado
$user_logged_in = is_user_logged_in();
$current_user = wp_get_current_user();
?>

<div class="mrb-wrapper">
    <!-- Hero Section con Búsqueda -->
    <section class="mrb-hero">
        <div class="mrb-hero-bg"></div>
        <div class="mrb-hero-content">
            <h1 class="mrb-hero-title">Encuentra el servicio perfecto para tu evento</h1>
            <p class="mrb-hero-subtitle">Más de 1,000 proveedores de confianza para hacer tu evento inolvidable</p>
            
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
                
                <!-- Búsquedas rápidas -->
                <div class="mrb-quick-searches">
                    <span class="mrb-quick-label">Búsquedas populares:</span>
                    <button class="mrb-quick-tag" onclick="quickSearch('salones')">Salones</button>
                    <button class="mrb-quick-tag" onclick="quickSearch('fotografos')">Fotógrafos</button>
                    <button class="mrb-quick-tag" onclick="quickSearch('mariachi')">Mariachi</button>
                    <button class="mrb-quick-tag" onclick="quickSearch('jardin')">Jardines</button>
                    <button class="mrb-quick-tag" onclick="quickSearch('dj')">DJ</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Promotores y Tiendas Oficiales -->
    <section class="mrb-featured-section">
        <div class="mrb-container">
            <div class="mrb-featured-badges">
                <div class="mrb-badge mrb-badge-official">
                    <svg class="mrb-badge-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                    <div class="mrb-badge-content">
                        <h3>Tiendas Oficiales</h3>
                        <p>Proveedores verificados y certificados</p>
                    </div>
                    <button class="mrb-badge-button" onclick="filterByOfficial()">Ver todas</button>
                </div>
                
                <div class="mrb-badge mrb-badge-promoter">
                    <svg class="mrb-badge-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <div class="mrb-badge-content">
                        <h3>Promotores Premium</h3>
                        <p>Los mejores calificados por clientes</p>
                    </div>
                    <button class="mrb-badge-button" onclick="filterByPromoters()">Explorar</button>
                </div>
                
                <div class="mrb-badge mrb-badge-new">
                    <svg class="mrb-badge-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                    </svg>
                    <div class="mrb-badge-content">
                        <h3>Ofertas Especiales</h3>
                        <p>Descuentos y promociones activas</p>
                    </div>
                    <button class="mrb-badge-button" onclick="filterByOffers()">Ver ofertas</button>
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
                    <div class="mrb-category-item" onclick="filterByCategory('lugares')">
                        <div class="mrb-category-icon">
                            <img src="https://a0.muscache.com/pictures/732edad8-3ae0-49a8-a451-29a8010dcc0c.jpg" alt="Lugares">
                        </div>
                        <span>Lugares para Eventos</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('fotografia')">
                        <div class="mrb-category-icon">
                            <img src="https://a0.muscache.com/pictures/8e507f16-4943-4be9-b707-59bd38d56309.jpg" alt="Fotografía">
                        </div>
                        <span>Fotografía y Video</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('musica')">
                        <div class="mrb-category-icon">
                            <img src="https://a0.muscache.com/pictures/3726d94b-534a-42b8-bca0-a0304d912260.jpg" alt="Música">
                        </div>
                        <span>Música y DJ</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('catering')">
                        <div class="mrb-category-icon">
                            <img src="https://a0.muscache.com/pictures/c5a4f6fc-c92c-4ae8-87dd-57f1ff1b89a6.jpg" alt="Catering">
                        </div>
                        <span>Alimentos y Bebidas</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('decoracion')">
                        <div class="mrb-category-icon">
                            <img src="https://a0.muscache.com/pictures/89faf9ae-bbbc-4bc4-aecd-cc15bf36cbca.jpg" alt="Decoración">
                        </div>
                        <span>Decoración</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('entretenimiento')">
                        <div class="mrb-category-icon">
                            <img src="https://a0.muscache.com/pictures/f60700bc-8ab5-424c-912b-6ef17abc479a.jpg" alt="Entretenimiento">
                        </div>
                        <span>Entretenimiento</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('mobiliario')">
                        <div class="mrb-category-icon">
                            <img src="https://a0.muscache.com/pictures/4d4a4eba-c7e4-43eb-9ce2-95e1d200d10e.jpg" alt="Mobiliario">
                        </div>
                        <span>Mobiliario</span>
                    </div>
                    
                    <div class="mrb-category-item" onclick="filterByCategory('paquetes')">
                        <div class="mrb-category-icon">
                            <img src="https://a0.muscache.com/pictures/677a041d-7264-4c45-bb72-52bff21eb6e8.jpg" alt="Paquetes">
                        </div>
                        <span>Paquetes Todo Incluido</span>
                    </div>
                </div>
                
                <button class="mrb-slider-arrow mrb-slider-next" onclick="slideCategories('next')">›</button>
            </div>
        </div>
    </section>

    <!-- Listado de Servicios -->
    <section class="mrb-listings">
        <div class="mrb-container">
            <div class="mrb-listings-header">
                <h2>Servicios destacados</h2>
                <div class="mrb-filter-pills">
                    <button class="mrb-pill active" onclick="filterListings('all')">Todos</button>
                    <button class="mrb-pill" onclick="filterListings('popular')">Más populares</button>
                    <button class="mrb-pill" onclick="filterListings('new')">Nuevos</button>
                    <button class="mrb-pill" onclick="filterListings('promo')">En promoción</button>
                </div>
            </div>
            
            <div class="mrb-listings-grid" id="listings-grid">
                <?php
                // Query para obtener listings de HivePress
                $args = array(
                    'post_type' => 'hp_listing',
                    'posts_per_page' => 12,
                    'post_status' => 'publish',
                    'meta_key' => '_featured',
                    'orderby' => 'meta_value_num',
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
                            <div class="mrb-card-image-container">
                                <img class="mrb-card-image" src="<?php echo $featured_image ?: '/placeholder.jpg'; ?>" alt="<?php the_title(); ?>">
                                <button class="mrb-card-favorite" onclick="toggleFavorite(event, <?php echo $listing_id; ?>)">
                                    <svg viewBox="0 0 32 32" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m16 28c7-4.733 14-10 14-17 0-1.792-.683-3.583-2.05-4.95-1.367-1.366-3.158-2.05-4.95-2.05-1.791 0-3.583.684-4.949 2.05l-2.051 2.051-2.05-2.051c-1.367-1.366-3.158-2.05-4.95-2.05-1.791 0-3.583.684-4.949 2.05-1.367 1.367-2.051 3.158-2.051 4.95 0 7 7 12.267 14 17z"></path>
                                    </svg>
                                </button>
                                <?php if (get_post_meta($listing_id, 'hp_verified', true)) : ?>
                                    <div class="mrb-card-badge">Verificado</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mrb-card-content">
                                <div class="mrb-card-header">
                                    <h3 class="mrb-card-title"><?php the_title(); ?></h3>
                                    <?php if ($rating) : ?>
                                        <div class="mrb-card-rating">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            <span><?php echo number_format($rating, 1); ?></span>
                                            <?php if ($reviews_count) : ?>
                                                <span class="mrb-card-reviews">(<?php echo $reviews_count; ?>)</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mrb-card-meta">
                                    <?php if (!empty($category)) : ?>
                                        <span class="mrb-card-category"><?php echo $category[0]->name; ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($location)) : ?>
                                        <span class="mrb-card-location"><?php echo $location[0]->name; ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($price) : ?>
                                    <div class="mrb-card-price">
                                        <span class="mrb-price-amount">$<?php echo number_format($price); ?></span>
                                        <span class="mrb-price-unit">MXN</span>
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