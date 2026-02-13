<?php
/**
 * Template part para mostrar una card de listing MEJORADA
 * Con carousel de imágenes, lazy loading y animaciones
 */

$listing_id = get_the_ID();
$price = get_post_meta($listing_id, 'hp_price', true);
$location = wp_get_post_terms($listing_id, 'hp_listing_ubicacion');
$category = wp_get_post_terms($listing_id, 'hp_listing_category');
$rating = get_post_meta($listing_id, 'hp_rating', true);
$reviews_count = get_post_meta($listing_id, 'hp_reviews_count', true);
$verified = get_post_meta($listing_id, 'hp_verified', true);
$featured = get_post_meta($listing_id, 'hp_featured', true);

// Obtener múltiples imágenes
$gallery = get_post_meta($listing_id, 'hp_gallery', true);
$images = array();

// Imagen destacada
$featured_image = get_the_post_thumbnail_url($listing_id, 'large');
if ($featured_image) {
    $images[] = $featured_image;
}

// Añadir imágenes de la galería
if ($gallery && is_array($gallery)) {
    foreach ($gallery as $image_id) {
        $image_url = wp_get_attachment_image_url($image_id, 'large');
        if ($image_url) {
            $images[] = $image_url;
        }
    }
}

// Si no hay imágenes, usar placeholder
if (empty($images)) {
    $images[] = get_stylesheet_directory_uri() . '/assets/images/placeholder.svg';
}

// Limitar a 5 imágenes máximo
$images = array_slice($images, 0, 5);

// Verificar si es nuevo (últimos 30 días)
$post_date = get_the_date('U');
$thirty_days_ago = strtotime('-30 days');
$is_new = ($post_date > $thirty_days_ago);

// Obtener el extracto o descripción corta
$excerpt = wp_trim_words(get_the_excerpt(), 15, '...');
?>

<div class="mrb-listing-card mrb-section-card" 
     onclick="window.location.href='<?php echo get_permalink(); ?>'"
     data-listing-id="<?php echo $listing_id; ?>"
     role="article"
     aria-label="<?php the_title(); ?>">
    
    <div class="mrb-card-slider">
        <!-- Imágenes del carousel -->
        <div class="mrb-card-images">
            <?php foreach ($images as $index => $image_url) : ?>
                <img class="mrb-card-image <?php echo $index === 0 ? 'active' : ''; ?>" 
                     <?php if ($index === 0) : ?>
                         src="<?php echo esc_url($image_url); ?>"
                     <?php else : ?>
                         data-src="<?php echo esc_url($image_url); ?>"
                         loading="lazy"
                     <?php endif; ?>
                     alt="<?php the_title(); ?> - Imagen <?php echo $index + 1; ?>"
                     width="300"
                     height="300">
            <?php endforeach; ?>
        </div>
        
        <?php if (count($images) > 1) : ?>
            <!-- Controles del carousel -->
            <button class="mrb-card-nav mrb-card-prev" 
                    onclick="changeCardImage(this, -1)" 
                    aria-label="Imagen anterior">
                <svg viewBox="0 0 32 32" width="12" height="12">
                    <path fill="currentColor" d="M14.19 16.005l7.869 7.868-2.129 2.129L9.937 16.004 19.96 5.998l2.127 2.129z"></path>
                </svg>
            </button>
            <button class="mrb-card-nav mrb-card-next" 
                    onclick="changeCardImage(this, 1)"
                    aria-label="Siguiente imagen">
                <svg viewBox="0 0 32 32" width="12" height="12">
                    <path fill="currentColor" d="M18.629 16.055l-7.565-7.565 2.129-2.129 9.695 9.695-9.695 9.695-2.129-2.129z"></path>
                </svg>
            </button>
            
            <!-- Indicadores (dots) -->
            <div class="mrb-card-dots">
                <?php foreach ($images as $index => $image) : ?>
                    <span class="mrb-card-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                          data-index="<?php echo $index; ?>"
                          role="button"
                          aria-label="Ver imagen <?php echo $index + 1; ?>"></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Badges -->
        <?php if ($verified) : ?>
            <div class="mrb-badge-verified">
                <svg viewBox="0 0 16 16" width="12" height="12" fill="currentColor">
                    <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 6L7 10.5 4.5 8l.9-.9L7 8.7l3.6-3.6.9.9z"/>
                </svg>
                <span>Verificado</span>
            </div>
        <?php elseif ($featured) : ?>
            <div class="mrb-badge-featured">
                <svg viewBox="0 0 16 16" width="12" height="12" fill="currentColor">
                    <path d="M8 0l2.5 5.3 5.5.8-4 4.1.9 5.8L8 13.3 3.1 16l.9-5.8-4-4.1 5.5-.8z"/>
                </svg>
                <span>Destacado</span>
            </div>
        <?php elseif ($is_new) : ?>
            <div class="mrb-badge-new">Nuevo</div>
        <?php endif; ?>
        
        <!-- Botón de favorito -->
        <button class="mrb-card-favorite" 
                onclick="toggleFavorite(event, <?php echo $listing_id; ?>)"
                aria-label="Añadir a favoritos">
            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" 
                 aria-hidden="true" 
                 style="display: block; fill: rgba(0, 0, 0, 0.5); height: 24px; width: 24px; stroke: white; stroke-width: 2;">
                <path d="m16 28c7-4.733 14-10 14-17 0-1.792-.683-3.583-2.05-4.95-1.367-1.366-3.158-2.05-4.95-2.05-1.791 0-3.583.684-4.949 2.05l-2.051 2.051-2.05-2.051c-1.367-1.366-3.158-2.05-4.95-2.05-1.791 0-3.583.684-4.949 2.05-1.367 1.367-2.051 3.158-2.051 4.95 0 7 7 12.267 14 17z"></path>
            </svg>
        </button>
    </div>
    
    <div class="mrb-card-content">
        <!-- Fila 1: Título y rating -->
        <div class="mrb-card-row-1">
            <h3 class="mrb-card-title"><?php the_title(); ?></h3>
            <?php if ($rating && $rating > 0) : ?>
                <span class="mrb-card-rating">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" 
                         width="12" height="12" fill="currentColor">
                        <path d="m15.1 1.58-4.13 8.88-9.86 1.27a1 1 0 0 0-.54 1.74l7.3 6.57-1.97 9.85a1 1 0 0 0 1.48 1.06l8.62-5 8.63 5a1 1 0 0 0 1.48-1.06l-1.97-9.85 7.3-6.57a1 1 0 0 0-.55-1.73l-9.86-1.28-4.12-8.88a1 1 0 0 0-1.82 0z"></path>
                    </svg>
                    <span><?php echo number_format($rating, 1); ?></span>
                    <?php if ($reviews_count) : ?>
                        <span class="mrb-reviews-count">(<?php echo $reviews_count; ?>)</span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </div>
        
        <!-- Fila 2: Categoría y ubicación -->
        <div class="mrb-card-row-2">
            <?php if (!empty($category)) : ?>
                <span class="mrb-card-category"><?php echo esc_html($category[0]->name); ?></span>
            <?php endif; ?>
            <?php if (!empty($location)) : ?>
                <span class="mrb-card-location">
                    <?php if (!empty($category)) : ?>•<?php endif; ?>
                    <?php echo esc_html($location[0]->name); ?>
                </span>
            <?php endif; ?>
        </div>
        
        <!-- Fila 3: Descripción corta (opcional) -->
        <?php if ($excerpt && strlen($excerpt) > 10) : ?>
            <div class="mrb-card-row-desc">
                <span class="mrb-card-excerpt"><?php echo esc_html($excerpt); ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Fila 4: Precio -->
        <?php if ($price) : ?>
            <div class="mrb-card-row-3">
                <span class="mrb-card-price">
                    <span class="mrb-price-amount">$<?php echo number_format($price, 0, '.', ','); ?> MXN</span>
                    <span class="mrb-price-unit">por evento</span>
                </span>
            </div>
        <?php else : ?>
            <div class="mrb-card-row-3">
                <span class="mrb-card-price">
                    <span class="mrb-price-contact">Precio a consultar</span>
                </span>
            </div>
        <?php endif; ?>
        
        <!-- Información adicional hover (se muestra al pasar el mouse) -->
        <div class="mrb-card-hover-info">
            <?php 
            $amenities = get_post_meta($listing_id, 'hp_amenities', true);
            if ($amenities && is_array($amenities)) : 
                $amenities_shown = array_slice($amenities, 0, 3);
            ?>
                <div class="mrb-card-amenities">
                    <?php foreach ($amenities_shown as $amenity) : ?>
                        <span class="mrb-amenity-tag"><?php echo esc_html($amenity); ?></span>
                    <?php endforeach; ?>
                    <?php if (count($amenities) > 3) : ?>
                        <span class="mrb-amenity-more">+<?php echo count($amenities) - 3; ?> más</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>