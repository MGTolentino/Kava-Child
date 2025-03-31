<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;
// Obtener el listing actual
$listing = hivepress()->request->get_context('listing');
$vendor = $listing->get_vendor();
?>
<div class="bv-details-section" id="detalles">
    <div class="bv-main-content">
        <h2 class="bv-section-title">Service Provider</h2>
        
        <?php if ($vendor): ?>
            <div class="bv-vendor">
                <div class="bv-vendor-col-1">
                    <!-- Imagen del vendedor -->
                    <div class="hp-vendor__image">
                        <?php 
                        $image_url = $vendor->get_image__url('hp_square_small');
                        if ($image_url) : ?>
                            <img src="<?php echo esc_url($image_url); ?>" 
                                 alt="<?php echo esc_attr($vendor->get_name()); ?>" 
                                 loading="lazy">
                        <?php else : ?>
                            <img src="<?php echo esc_url(hivepress()->get_url() . '/assets/images/placeholders/user-square.svg'); ?>" 
                                 alt="<?php echo esc_attr($vendor->get_name()); ?>" 
                                 loading="lazy">
                        <?php endif; ?>
                    </div>

                    <!-- Rating y Reviews -->
                    <?php if ($vendor->get_rating()) : ?>
                        <div class="hp-vendor__rating hp-rating">
                            <div class="hp-rating__stars hp-rating-stars" data-component="rating" data-value="<?php echo esc_attr($vendor->get_rating()); ?>"></div>
                            <div class="hp-rating__details">
                                <span class="hp-rating__value"><?php echo esc_html($vendor->display_rating()); ?></span>
                                <span class="hp-rating__count">(<?php echo esc_html($vendor->display_rating_count()); ?>)</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bv-vendor-col-2">
                    <!-- Nombre del vendedor y badge -->
                    <div class="bv-vendor-header">
                        <h4 class="hp-vendor__name">
                            <?php echo esc_html($vendor->get_name()); ?>
                            <?php if ($vendor->is_verified()) : ?>
                                <span class="bv-vendor-verified-badge">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            <?php endif; ?>
                        </h4>
                    </div>

                    <!-- Detalles principales -->
                    <div class="bv-vendor-details">
                        <time class="hp-vendor__registered-date hp-meta">
                            <?php
                            printf(
                                esc_html__('Member since %s', 'hivepress'),
                                $vendor->display_registered_date()
                            );
                            ?>
                        </time>

                        <?php if ($vendor->get_location()) : ?>
                            <div class="bv-vendor-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo esc_html($vendor->get_location()); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Atributos del vendedor -->
                    <?php
                    $attributes = hivepress()->attribute->get_attributes('vendor');
                    $vendor_data = $vendor->serialize();

                    if ($attributes) : 
                        $visible_attributes = array_filter($attributes, function($attribute) {
                            return !empty($attribute['display_areas']);
                        });

                        if ($visible_attributes) : ?>
                            <div class="bv-vendor-attributes">
                                <?php foreach ($visible_attributes as $attribute_name => $attribute) :
                                    if (isset($vendor_data[$attribute_name]) && !empty($vendor_data[$attribute_name])) : ?>
                                        <div class="bv-vendor-attribute">
                                            <?php 
                                            $display_value = '';
                                            if (isset($attribute['edit_field']['options']) && $attribute['edit_field']['options'] === 'terms') {
                                                $terms = get_terms([
                                                    'taxonomy' => $attribute['edit_field']['option_args']['taxonomy'],
                                                    'include' => (array)$vendor_data[$attribute_name],
                                                    'hide_empty' => false
                                                ]);
                                                
                                                if (!is_wp_error($terms) && !empty($terms)) {
                                                    $term_names = array_map(function($term) {
                                                        return $term->name;
                                                    }, $terms);
                                                    $display_value = implode(', ', $term_names);
                                                }
                                            } else {
                                                $display_value = $vendor_data[$attribute_name];
                                            }

                                            if (!empty($display_value)) : 
                                                echo str_replace(
                                                    ['%value%', '%label%'],
                                                    [$display_value, $attribute['label']],
                                                    $attribute['display_format']
                                                );
                                            endif;
                                            ?>
                                        </div>
                                    <?php endif;
                                endforeach; ?>
                            </div>
                        <?php endif;
                    endif; ?>

                    <!-- Footer con acciones -->
                    <div class="bv-vendor-footer">
    <?php if (is_user_logged_in()): ?>
        <!-- Modal para enviar mensaje -->
        <div id="message_send_modal_<?php echo esc_attr($listing->get_id()); ?>" 
             class="hp-modal" 
             data-component="modal">
            <h3 class="hp-modal__title"><?php echo esc_html(hivepress()->translator->get_string('send_message')); ?></h3>
            
            <form data-model="message"
                  data-message="<?php esc_attr_e('Your message has been sent.', 'hivepress-messages'); ?>"
                  action="#"
                  data-action="<?php echo esc_url(hivepress()->router->get_url('message_send_action')); ?>"
                  method="POST"
                  data-component="form"
                  class="hp-form hp-form--message-send">
                
                <div class="hp-form__messages" data-component="messages"></div>
                
                <div class="hp-form__fields">
                    <input type="hidden" 
                           name="recipient" 
                           value="<?php echo esc_attr($vendor->get_user__id()); ?>"
                           data-component="number"
                           class="hp-field hp-field--hidden">
                           
                    <input type="hidden" 
                           name="listing" 
                           value="<?php echo esc_attr($listing->get_id()); ?>"
                           data-component="number"
                           class="hp-field hp-field--hidden">
                           
                    <div class="hp-form__field hp-form__field--textarea">
                        <label class="hp-field__label hp-form__label">
                            <span><?php esc_html_e('Message', 'hivepress-messages'); ?></span>
                        </label>
                        <textarea name="text" 
                                maxlength="2048" 
                                required="required"
                                class="hp-field hp-field--textarea"></textarea>
                    </div>
                </div>
                
                <div class="hp-form__footer">
                    <button type="submit" class="hp-form__button button-primary alt button hp-field hp-field--submit">
                        <span><?php esc_html_e('Send Message', 'hivepress-messages'); ?></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Botón para abrir el modal -->
        <button type="button" 
                class="hp-vendor__action hp-vendor__action--message button button--large button--primary alt"
                data-component="link"
                data-url="#message_send_modal_<?php echo esc_attr($listing->get_id()); ?>">
            <?php echo esc_html(hivepress()->translator->get_string('send_message')); ?>
        </button>
    <?php else: ?>
        <!-- Si el usuario no está logueado, mostrar enlace al modal de login -->
        <a href="#user_login_modal" 
           class="hp-vendor__action hp-vendor__action--message button button--large button--primary alt">
            <?php echo esc_html(hivepress()->translator->get_string('send_message')); ?>
        </a>
    <?php endif; ?>

    <!-- Link a la tienda del vendedor -->
    <a href="<?php echo esc_url(hivepress()->router->get_url('vendor_view_page', ['vendor_id' => $vendor->get_id()])); ?>" 
       class="bv-vendor-profile-link">
        <?php esc_html_e('View more services from the provider', 'bookingvista'); ?>
    </a>
</div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>