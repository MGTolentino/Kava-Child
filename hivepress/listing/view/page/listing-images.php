<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

if ($listing->get_images__id()):
    $image_urls = [];
    if (get_option('hp_listing_enable_image_zoom')):
        $image_urls = $listing->get_images__url('large');
    endif;
    ?>
    <div class="bv-listing-gallery">
        <div class="bv-gallery-grid">
            <?php
            $images = $listing->get_images();
            $total_images = count($images);
            
            foreach ($images as $image_index => $image):
                $image_url = hivepress()->helper->get_array_value($image_urls, $image_index, '');
                $image_class = 'bv-gallery-item';
                
                // Asignar clases especiales según la posición
                if ($image_index === 0) {
                    $image_class .= ' bv-gallery-main';
                } elseif ($image_index <= 9) {
                    $image_class .= ' bv-gallery-secondary';
                } else {
                    continue; // Skip después de 5 imágenes
                }
                
                // Verificar si es video o imagen
                if (strpos($image->get_mime_type(), 'video') === 0):
                    ?>
                    <div class="<?php echo esc_attr($image_class); ?>">
                        <video data-full="<?php echo esc_url($image_url); ?>" controls>
                            <source src="<?php echo esc_url($image->get_url()); ?>#t=0.001" 
                                    type="<?php echo esc_attr($image->get_mime_type()); ?>">
                        </video>
                    </div>
                <?php else: ?>
                    <div class="<?php echo esc_attr($image_class); ?>">
                        <img src="<?php echo esc_url($image->get_url('hp_landscape_large')); ?>"
                             data-full="<?php echo esc_url($image_url); ?>"
                             alt="<?php echo esc_attr($listing->get_title()); ?>"
                             loading="lazy">
                        
						<?php if ($image_index === 4 && $total_images > 4): ?>						
                            <button class="bv-gallery-view-all">
                                Ver galería
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif;
            endforeach;
            ?>
        </div>
    </div>

    <!-- Lightbox Container -->
    <div class="bv-lightbox">
        <button class="bv-lightbox-close">&times;</button>
        <button class="bv-lightbox-prev">&lt;</button>
        <button class="bv-lightbox-next">&gt;</button>
        <div class="bv-lightbox-content"></div>
        <div class="bv-lightbox-counter"></div>
    </div>
<?php endif; ?>