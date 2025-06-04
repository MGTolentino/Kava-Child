<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

$currency = get_woocommerce_currency();

?>

<div class="bv-sections-nav">
    <div class="bv-nav-container">
        <ul class="bv-sections-list">
        <li><a href="#resumen" class="bv-section-link active">Summary</a></li>
        <li><a href="#extras" class="bv-section-link">Extras</a></li>
        <li><a href="#ubicacion" class="bv-section-link">Location</a></li>
        <li><a href="#resenas" class="bv-section-link">Reviews</a></li>
        <li><a href="#detalles" class="bv-section-link">Details</a></li>
    </ul>
        <div class="bv-nav-booking-actions" style="display: none;">
            <div class="bv-nav-total"></div>
            <div class="bv-nav-book-button"></div>
        </div>
    </div>
    <div class="bv-sections-indicator"></div>
</div>

<script>
    var bvCurrency = <?php echo json_encode($currency); ?>;
</script>