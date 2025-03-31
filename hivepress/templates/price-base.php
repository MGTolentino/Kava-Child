<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

// Obtener el listing actual
$listing = hivepress()->request->get_context('listing');
?>
<div class="bv-price-amount">
    <?php if ($listing && $listing->get_price()): ?>
        <span class="bv-price-value">
            <?php echo hivepress()->woocommerce->format_price($listing->get_price()); ?>
        </span>
        <span class="bv-price-label">
            <?php esc_html_e('precio base', 'booking-vista'); ?>
        </span>
    <?php endif; ?>
</div>