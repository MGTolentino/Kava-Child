<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

// Obtener el listing actual
$listing = hivepress()->request->get_context('listing');

// Obtener la dirección del listing desde el campo hp_domicilio
function get_listing_address($listing) {
    if (!$listing) return false;
    
    // Obtener el valor del campo meta hp_domicilio del listing actual
    $address = get_post_meta($listing->get_id(), 'hp_domicilio', true);
    
    return !empty($address) ? $address : false;
}

// Función para verificar si es Event Venue o categoría hija
function is_event_venue_category() {
    $terms = get_the_terms(get_the_ID(), 'hp_listing_category');
    if ($terms && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            if ($term->slug === 'lugares-para-eventos') return true;
            
            $parent_id = $term->parent;
            if ($parent_id) {
                $parent_term = get_term($parent_id, 'hp_listing_category');
                if ($parent_term && !is_wp_error($parent_term)) {
                    if ($parent_term->slug === 'lugares-para-eventos') return true;
                }
            }
        }
    }
    return false;
}
?>
<div class="bv-location-section" id="ubicacion">
    <div class="bv-main-content">
        <h2 class="bv-section-title">Location</h2>
        
        <?php 
        $address = get_listing_address($listing);
        if(is_event_venue_category() && $address): ?>
            <div class="bv-venue-location">
                <p class="bv-venue-address"><?php echo esc_html($address); ?></p>
                
                <div class="bv-venue-map" style="height: 400px; width: 100%;"></div>
            </div>
        <?php else: ?>
            <div class="bv-service-locations">
                <?php
                $locations = get_the_terms($listing->get_id(), 'hp_listing_ubicacion');
                if($locations && !is_wp_error($locations)): ?>
                    <div class="bv-location-circles">
                        <?php foreach($locations as $location): ?>
                            <div class="bv-location-circle">
                                <?php echo esc_html($location->name); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>