<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
$image_count = count( (array) $listing->get_images__id() );
?>
<div class="hp-listing__image<?php if (hivepress()->request->get_context('booking_make_details_page')) echo ' hp-listing__image--booking'; ?>" data-component="carousel-slider" data-preview="false" data-url="<?php echo esc_url( hivepress()->router->get_url( 'listing_view_page', [ 'listing_id' => $listing->get_id() ] ) ); ?>">
    <a href="<?php echo esc_url( hivepress()->router->get_url( 'listing_view_page', [ 'listing_id' => $listing->get_id() ] ) ); ?>">
        <?php if ( $image_count >= 1 ) : ?>
            <img src="<?php echo esc_url( $listing->get_image__url( 'hp_landscape_small' ) ); ?>" alt="<?php echo esc_attr( $listing->get_title() ); ?>" loading="lazy">
        <?php else : ?>
            <img src="<?php echo esc_url( hivepress()->get_url() . '/assets/images/placeholders/image-landscape.svg' ); ?>" alt="<?php echo esc_attr( $listing->get_title() ); ?>" loading="lazy">
        <?php endif; ?>
    </a>
</div>