<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
if ( $listing->get_categories__id() ) :
    ?>
    <div class="hp-listing__categories">
        <?php foreach ( $listing->get_categories() as $category ) : ?>
            <span class="hp-listing__category-tag">
                <a href="<?php echo esc_url( hivepress()->router->get_url( 'listing_category_view_page', [ 'listing_category_id' => $category->get_id() ] ) ); ?>">
                    <?php echo esc_html( $category->get_name() ); ?>
                </a>
            </span>
        <?php endforeach; ?>
    </div>
    <?php
endif;