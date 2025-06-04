<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<a href="<?php echo esc_url( hivepress()->router->get_url( 'listing_view_page', [ 'listing_id' => $listing->get_id() ] ) ); ?>" class="hp-listing__title-link"><?php echo esc_html( $listing->get_title() ); ?></a>