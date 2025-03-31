<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

// Obtener el listing y su configuración
$listing = hivepress()->request->get_context('listing');

if (!$listing) {
    return;
}

// Obtener parámetros de booking
$booking_offset = get_post_meta($listing->get_id(), 'hp_booking_offset', true) ?: 0;
$booking_window = get_post_meta($listing->get_id(), 'hp_booking_window', true) ?: 365;
$booking_ranges = get_post_meta($listing->get_id(), 'hp_booking_ranges', true) ?: [];
error_log('Booking Ranges Raw: ' . print_r($booking_ranges, true));

// Log más detallado
if (!empty($booking_ranges)) {
    error_log('Booking Ranges Structure:');
    foreach ($booking_ranges as $index => $range) {
        error_log("Range {$index}:");
        if (isset($range['days']) && is_array($range['days'])) {
            error_log('- Days: ' . print_r($range['days'], true));
        }
        if (isset($range['price'])) {
            error_log('- Price: ' . $range['price']);
        }
    }
}

// Función para obtener fechas bloqueadas
function get_blocked_dates($listing_id) {
    $blocked_dates = array();
    
    // Obtener parámetros de booking
    $offset = intval(get_post_meta($listing_id, 'hp_booking_offset', true)) ?: 0;
    $window = intval(get_post_meta($listing_id, 'hp_booking_window', true)) ?: 365;
    
    // Agregar fechas de offset
    $current = new DateTime();
    for($i = 0; $i < $offset; $i++) {
        $blocked_dates[] = $current->format('Y-m-d');
        $current->modify('+1 day');
    }
    
    // Agregar fechas fuera del window
    $window_start = new DateTime();
    $window_start->modify('+' . $window . ' days');
    $window_end = new DateTime();
    $window_end->modify('+2 years');
    
    while($window_start <= $window_end) {
        $blocked_dates[] = $window_start->format('Y-m-d');
        $window_start->modify('+1 day');
    }
    
    // Obtener reservas existentes
    $bookings = get_posts(array(
        'post_type' => 'hp_booking',
        'post_status' => array('publish', 'draft','private'),
        'post_parent' => $listing_id,
        'posts_per_page' => -1,
    ));
    
    foreach ($bookings as $booking) {
        $start_timestamp = intval(get_post_meta($booking->ID, 'hp_start_time', true));
        $end_timestamp = intval(get_post_meta($booking->ID, 'hp_end_time', true));
        
        $start_date = date('Y-m-d', $start_timestamp);
        $end_date = date('Y-m-d', $end_timestamp);
        
        if ($start_date === $end_date) {
            $blocked_dates[] = $start_date;
        } else {
            $blocked_dates[] = array(
                'from' => $start_date,
                'to' => $end_date
            );
        }
    }
    
    return array_unique($blocked_dates, SORT_REGULAR);
}

// Obtener las fechas bloqueadas
$blocked_dates = get_blocked_dates($listing->get_id());

// Obtener configuración existente
$base_price = $listing->get_price();
$max_length = $listing->get_booking_max_length();
$min_length = $listing->get_booking_min_length();
?>

<div class="bv-booking-form">
    <div class="bv-booking-header">
        <div class="bv-price-base">
            <span><?php echo hivepress()->woocommerce->format_price($base_price); ?></span> Precio inicial
        </div>
    </div>

    <form class="bv-booking-form-container" method="GET" action="<?php echo esc_url(hivepress()->router->get_url('booking_make_page')); ?>">
        <input type="hidden" name="listing" value="<?php echo esc_attr($listing->get_id()); ?>">

        <div class="bv-fields-container">
            <div class="bv-form-row bv-datetime-row <?php echo ($listing->get_booking_min_quantity() == 1 && $listing->get_booking_max_quantity() == 1) ? 'no-quantity' : ''; ?>">
                <?php if (!($listing->get_booking_min_quantity() == 1 && $listing->get_booking_max_quantity() == 1)): ?>
                <div class="bv-field-block">
                    <div class="bv-field-content">
                        <span class="bv-block-label">Cantidad</span>
                        <div class="bv-block-value">    <?php echo esc_html($listing->get_booking_min_quantity()); ?>
</div>
                    </div>
                    <input type="number" 
                           name="_quantity" 
                           class="bv-quantity-input hidden"
                           style="display: none;"
                           min="<?php echo esc_attr($listing->get_booking_min_quantity()); ?>"
                           max="<?php echo esc_attr($listing->get_booking_max_quantity()); ?>"
                           value="<?php echo esc_attr($listing->get_booking_min_quantity()); ?>"
                           required>
                </div>
                <?php endif; ?>

                <div class="bv-field-block">
                    <div class="bv-field-content">
                        <span class="bv-block-label">Fecha</span>
                        <div class="bv-block-value">Seleccionar</div>
                    </div>
                    <input type="date" 
                           name="_dates[]" 
                           class="bv-date-input hidden"
                           style="display: none;"
                           required
                           min="<?php echo date('Y-m-d'); ?>"
                           data-min-length="<?php echo esc_attr($min_length); ?>"
                           data-max-length="<?php echo esc_attr($max_length); ?>"
                           data-booking-offset="<?php echo esc_attr($booking_offset); ?>"
                           data-booking-window="<?php echo esc_attr($booking_window); ?>"
							data-blocked-dates='<?php echo json_encode($blocked_dates, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
							data-booking-ranges='<?php echo json_encode($booking_ranges, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                           data-date-format="Y-m-d">
                    <?php if ($max_length > 1): ?>
                    <input type="date" 
                           name="_dates[]" 
                           class="bv-date-input hidden"
                           style="display: none;"
                           required
                           min="<?php echo date('Y-m-d'); ?>"
                           data-date-format="Y-m-d">
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($listing->get_price_extras()): ?>
            <div class="bv-form-row bv-extras-row">
                <div class="bv-field-block">
                    <div class="bv-field-content">
                        <span class="bv-block-label">Extras</span>
                        <div class="bv-block-value">Selecccionar</div>
                    </div>
                    <div class="bv-extras-dropdown">
                        <?php foreach ($listing->get_price_extras() as $index => $extra): 
                            $is_variable = isset($extra['type']) && $extra['type'] === 'variable_quantity';
                        ?>
                            <div class="bv-extra-item <?php echo $is_variable ? 'bv-extra-variable' : ''; ?>">
                                <label class="bv-extra-label">
                                    <?php if ($is_variable): ?>
                                        <div class="bv-extra-quantity-wrap">
                                            <input type="number" 
       name="_variable_quantity_<?php echo esc_attr($index); ?>"
       class="bv-extra-quantity"
       data-index="<?php echo esc_attr($index); ?>"
       min="<?php echo (isset($extra['required']) && $extra['required']) ? '1' : '0'; ?>"
       placeholder=""
       data-price="<?php echo esc_attr($extra['price']); ?>"
       data-name="<?php echo esc_attr($extra['name']); ?>"
       data-type="variable_quantity"
       <?php echo (isset($extra['required']) && $extra['required']) ? 'value="1" data-required="true"' : ''; ?>>
                                        </div>
                                    <?php else: ?>
                                       <input type="checkbox" 
       name="_extras[]" 
       value="<?php echo esc_attr($index); ?>"
       data-price="<?php echo esc_attr($extra['price']); ?>"
       data-name="<?php echo esc_attr($extra['name']); ?>"
       data-type="<?php echo esc_attr($extra['type']); ?>"
       <?php echo (isset($extra['required']) && $extra['required']) ? 'checked disabled data-required="true"' : ''; ?>>

                                    <?php endif; ?>
                                    <span class="bv-extra-name">
    <?php echo esc_html($extra['name']); ?>
    <?php if (isset($extra['required']) && $extra['required']): ?>
        <span class="bv-required-badge">Required</span>
    <?php endif; ?>
</span>
                                    <span class="bv-extra-price">
                                        <?php echo hivepress()->woocommerce->format_price($extra['price']); ?>
                                    </span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="bv-booking-info">
            <span>No se realizará el cobro todavía</span>
        </div>

        <input type="hidden" name="_calculated_price" value="0">
		<input type="hidden" name="price_details" value="">

        <button type="submit" class="bv-booking-button">
    <?php echo $listing->is_booking_moderated() ? 'Solicitar Reserva' : 'Reservar Ahora'; ?>
</button>
		
   <?php if ( current_user_can( 'administrator' ) ) : ?>
    <button type="button" 
            class="eq-quote-button booking-quote" 
            data-listing-id="<?php echo esc_attr($listing->get_id()); ?>"
            data-quote-state="add">
        + Quote
    </button>
<?php endif; ?>


<!-- Contenedor para notificaciones -->
<div class="eq-notification-container"></div>

        <div class="bv-totals-container"></div>
    </form>
</div>