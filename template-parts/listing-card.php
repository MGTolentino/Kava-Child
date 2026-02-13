<?php
/**
 * Template part para mostrar una card de listing
 * Usado en múltiples secciones de la página de inicio
 */

$listing_id = get_the_ID();
$featured_image = get_the_post_thumbnail_url($listing_id, 'large');
$price = get_post_meta($listing_id, 'hp_price', true);
$location = wp_get_post_terms($listing_id, 'hp_listing_ubicacion');
$category = wp_get_post_terms($listing_id, 'hp_listing_category');
$rating = get_post_meta($listing_id, 'hp_rating', true);
$reviews_count = get_post_meta($listing_id, 'hp_reviews_count', true);
$verified = get_post_meta($listing_id, 'hp_verified', true);
$featured = get_post_meta($listing_id, 'hp_featured', true);

// Verificar si es nuevo (últimos 30 días)
$post_date = get_the_date('U');
$thirty_days_ago = strtotime('-30 days');
$is_new = ($post_date > $thirty_days_ago);
?>

<div class="mrb-listing-card mrb-section-card" 
     data-listing-id="<?php echo $listing_id; ?>" 
     data-url="<?php echo get_permalink(); ?>"
     onclick="window.location.href='<?php echo get_permalink(); ?>'">
    <div class="mrb-card-slider">
        <?php if ($featured_image) : ?>
            <img class="mrb-card-image" src="<?php echo esc_url($featured_image); ?>" alt="<?php the_title(); ?>" loading="lazy">
        <?php else : ?>
            <img class="mrb-card-image" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.svg" alt="<?php the_title(); ?>">
        <?php endif; ?>
        
        <!-- Badges -->
        <?php if ($verified) : ?>
            <div class="mrb-badge-verified">
                <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 6L7 10.5 4.5 8l.9-.9L7 8.7l3.6-3.6.9.9z"/>
                </svg>
                Verificado
            </div>
        <?php elseif ($featured) : ?>
            <div class="mrb-badge-featured">Destacado</div>
        <?php elseif ($is_new) : ?>
            <div class="mrb-badge-new">Nuevo</div>
        <?php endif; ?>
        
        <!-- Favorite Button -->
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
                    <?php if ($reviews_count) : ?>
                        <span style="color: #717171;">(<?php echo $reviews_count; ?>)</span>
                    <?php endif; ?>
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