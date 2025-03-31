<?php 
$vendor_id = get_queried_object_id();
?>

<div class="hp-vendor-filter-container">
    <div class="hp-filter-section">
<form id="bv-vendor-filter-form" method="get" data-vendor-id="<?php 
    global $post;
    echo esc_attr($post->ID);
?>">			
            <div class="hp-filter-row">
                <div class="hp-filter-field">
                    <label for="bv-event-date"><?php esc_html_e('Event Date', 'booking-vista'); ?></label>
                    <input 
                        type="date" 
                        id="bv-event-date" 
                        name="fecha" 
                        class="hp-date-input"
                        min="<?php echo esc_attr(date('Y-m-d')); ?>"
                        value="<?php echo isset($data['fecha']) ? esc_attr($data['fecha']) : ''; ?>"
                    >
                </div>
                
                <div class="hp-filter-field">
                    <label for="bv-service-search"><?php esc_html_e('Search Service', 'booking-vista'); ?></label>
                    <input 
                        type="text" 
                        id="bv-service-search" 
                        name="nombre" 
                        placeholder="<?php esc_attr_e('Search by service name', 'booking-vista'); ?>"
                        class="hp-search-input"
                        value="<?php echo isset($data['nombre']) ? esc_attr($data['nombre']) : ''; ?>"
                    >
                </div>
                
               <div class="hp-filter-field hp-filter-submit">
    <div class="hp-filter-buttons">
        <button type="submit" class="hp-filter-button">
            <?php esc_html_e('Search', 'booking-vista'); ?>
        </button>
        <button type="button" class="hp-filter-reset" id="bv-filter-reset">
            <?php esc_html_e('Reset', 'booking-vista'); ?>
        </button>
    </div>
</div>
            </div>
        </form>
    </div>
</div>