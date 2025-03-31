<?php
/**
 * Kava Child Theme Functions
 */

use HivePress\Helpers as hp;
use HivePress\Blocks;
use HivePress\Models;
use HivePress\Forms;

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Enqueue parent theme styles
 */
add_filter('kava-theme/assets-depends/styles', 'kava_child_styles_depends');

function kava_child_styles_depends($deps) {
    $parent_handle = 'kava-parent-theme-style';

    wp_register_style(
        $parent_handle,
        get_template_directory_uri() . '/style.css',
        array(),
        kava_theme()->version()
    );

    $deps[] = $parent_handle;

    return $deps;
}

 /* Enqueue child theme scripts and styles
 */
function kava_child_scripts() {
	
		if (!is_singular('hp_listing')) {
        return;
    }
	
    $theme = wp_get_theme();
    $theme_version = $theme->get('Version');
    // Desktop Styles
    if (!wp_is_mobile()) {
        wp_enqueue_style(
            'kava-child-listing-images',
            get_stylesheet_directory_uri() . '/assets/css/listing-images.css',
            array(),
            $theme_version
        );
        wp_enqueue_style(
            'kava-child-listing-gallery',
            get_stylesheet_directory_uri() . '/assets/css/listing-gallery.css',
            array(),
            $theme_version
        );
        wp_enqueue_style(
            'kava-child-custom',
            get_stylesheet_directory_uri() . '/assets/css/custom.css',
            array(),
            $theme_version
        );
        wp_enqueue_style(
            'kava-child-booking-form',
            get_stylesheet_directory_uri() . '/assets/css/booking-form.css',
            array(),
            $theme_version
        );
    }
    // Mobile Styles
    else {
        wp_enqueue_style(
            'kava-child-mobile-booking',
            get_stylesheet_directory_uri() . '/mobile/css/mobile-booking.css',
            array(),
            $theme_version
        );
        wp_enqueue_style(
            'kava-child-mobile-reviews',
            get_stylesheet_directory_uri() . '/mobile/css/mobile-reviews.css',
            array(),
            $theme_version
        );
		wp_enqueue_style(
            'kava-child-mobile-listing-images',
            get_stylesheet_directory_uri() . '/mobile/css/mobile-listing-images.css',
            array(),
            $theme_version
        );
		wp_enqueue_style(
            'kava-child-mobile-custom',
            get_stylesheet_directory_uri() . '/mobile/css/mobile-custom.css',
            array(),
            $theme_version
        );
		
		wp_enqueue_script(
			'kava-child-mobile-functions',
			get_stylesheet_directory_uri() . '/mobile/js/mobile-functions.js',
			array('jquery'),  // Dependencia de jQuery
			$theme_version,
			true  // Cargar en el footer
			);
    }
    // Scripts
    $scripts = array(
        'listing-gallery' => '/assets/js/listing-gallery.js',
        'sections-nav' => '/assets/js/sections-nav.js',
        'booking-form' => '/assets/js/booking-form.js',
        'reviews' => '/assets/js/reviews.js'
    );
    foreach ($scripts as $handle => $path) {
        wp_enqueue_script(
            'kava-child-' . $handle,
            get_stylesheet_directory_uri() . $path,
            array('jquery'),
            $theme_version,
            true
        );
    }
    if (wp_is_mobile()) {
        wp_enqueue_script(
            'kava-child-mobile-booking',
            get_stylesheet_directory_uri() . '/mobile/js/mobile-booking.js',
            array('jquery'),
            $theme_version,
            true
        );
    }
	
    // Localizar script de booking
    // Primero obtener el tax rate
global $wpdb;
$tax_rate = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT tax_rate FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_id = %d",
        1
    )
);
	
	

// Luego usar wp_localize_script
wp_localize_script('kava-child-booking-form', 'bvData', array(
    'currency' => get_woocommerce_currency_symbol(),
    'thousand_sep' => wc_get_price_thousand_separator(),
    'decimal_sep' => wc_get_price_decimal_separator(),
    'num_decimals' => wc_get_price_decimals(),
    'tax_rate' => floatval($tax_rate)
));
}
add_action('wp_enqueue_scripts', 'kava_child_scripts', 20);

// Agregar en functions.php
add_action('wp_enqueue_scripts', function() {
    // Verificar si estamos en la página de detalles de reserva
    if (hivepress()->router->get_current_route_name() === 'booking_make_details_page') {
        wp_enqueue_style(
            'booking-confirm-styles', 
            get_stylesheet_directory_uri() . '/assets/css/booking-confirm.css',
            [], 
            '1.0.0'
        );
    }
});

// Cargar componentes
require_once get_stylesheet_directory() . '/includes/components/class-favorites.php';

// Inicializar la clase Favorites
if (!function_exists('kava_child_init_favorites')) {
    function kava_child_init_favorites() {
        new \HivePress\Components\Favorites();
    }
    add_action('init', 'kava_child_init_favorites');
}

// Registrar scripts
if (!function_exists('kava_child_enqueue_favorites_scripts')) {
    function kava_child_enqueue_favorites_scripts() {
        if (is_singular('hp_listing')) {
            wp_enqueue_script(
                'hp-favorites',
                get_stylesheet_directory_uri() . '/assets/js/favorites.js',
                ['jquery'],
                wp_get_theme()->get('Version'),
                true
            );

            wp_localize_script('hp-favorites', 'hp_favorites', [
                'ajax_url' => admin_url('admin-ajax.php')
            ]);
        }
    }
    add_action('wp_enqueue_scripts', 'kava_child_enqueue_favorites_scripts');
}

/**
 * Google Maps management
 */
function kava_child_manage_google_maps_scripts() {
    wp_deregister_script('google-maps');
    wp_deregister_script('google-maps-custom');
    wp_deregister_script('hivepress-geolocation');
    wp_deregister_script('geocomplete');

    wp_register_script(
        'google-maps-custom',
        'https://maps.googleapis.com/maps/api/js?key=AIzaSyDbWj1qA26RHzdRwF8ZC2JEt4v9iIm4KkI',
        array('jquery'),
        null,
        true
    );

    wp_register_script(
        'venue-map',
        get_stylesheet_directory_uri() . '/assets/js/venue-map.js',
        array('jquery', 'google-maps-custom'),
        '1.0',
        true
    );

    wp_enqueue_script('google-maps-custom');
    wp_enqueue_script('venue-map');
}
add_action('wp_enqueue_scripts', 'kava_child_manage_google_maps_scripts', 1);

/**
 * Vendor Filter Assets
 */
function kava_child_enqueue_vendor_filter_assets() {
    if (!is_singular('hp_vendor')) {
        return;
    }

    $theme = wp_get_theme();
    
    wp_enqueue_style(
        'kava-child-vendor-filter',
        get_stylesheet_directory_uri() . '/assets/css/vendor-filter.css',
        array(),
        $theme->get('Version')
    );

    wp_enqueue_script(
        'kava-child-vendor-filter',
        get_stylesheet_directory_uri() . '/assets/js/vendor-filter.js',
        array('jquery'),
        $theme->get('Version'),
        true
    );

    wp_localize_script('kava-child-vendor-filter', 'bvVendorFilter', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('bv_vendor_filter_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'kava_child_enqueue_vendor_filter_assets');



/**
 * HivePress Template Modifications
 */
add_filter('hivepress/v1/templates/listing_view_page', function($template) {
    if (isset($template['blocks']['page_container']['blocks']['page_columns'])) {
        $booking_form = isset($template['blocks']['page_container']['blocks']['page_columns']['blocks']['page_sidebar']['blocks']['booking_make_form']) ? 
            $template['blocks']['page_container']['blocks']['page_columns']['blocks']['page_sidebar']['blocks']['booking_make_form'] : null;
        $listing_id = get_the_ID();
        unset($template['blocks']['page_container']['blocks']['page_columns']['blocks']['page_sidebar']);
        
        $template['blocks']['page_container']['blocks']['page_columns']['blocks']['page_content']['attributes']['class'] = [
            'hp-page__content',
            'hp-col-xs-12',
            'hp-col-sm-12'
        ];

        $template['blocks']['page_container']['blocks']['page_columns']['blocks']['page_content']['blocks'] = [
            'listing_sections_nav' => [
                'type' => 'part',
                'path' => 'listing/view/page/listing-sections-nav',
                '_order' => 5,
            ],
            'listing_title' => [
					'type' => 'part',
					'path' => 'listing/view/page/listing-title-with-favorite',
					'_order' => 10,
				],
            'listing_manage_menu' => [
                'type' => 'menu',
                'menu' => 'listing_manage',
                'wrap' => false,
                '_order' => 15,
            ],
            'listing_images' => [
                'type' => 'part',
                'path' => 'listing/view/page/listing-images',
                '_order' => 20,
            ],
            'content_with_booking' => [
                'type' => 'container',
                '_order' => 30,
                'attributes' => [
                    'class' => ['bv-content-booking-wrapper'],
                ],
                'blocks' => [
                    'main_content' => [
                        'type' => 'container',
                        '_order' => 10,
                        'attributes' => [
                            'class' => ['bv-main-content'],
                        ],
                        'blocks' => [
                            'resume_section' => [
                                'type' => 'part',
                                'path' => 'listing/view/page/listing-resume-section',
                                '_order' => 10,
                            ],
                        ],
                    ],
                    'booking_sidebar' => [
                        'type' => 'container',
                        '_order' => 20,
                        'attributes' => [
                            'class' => ['bv-booking-sidebar', 'hp-sticky'],
                        ],
                        'blocks' => [
                            'booking_form' => [
                                'type' => 'part',
                                'path' => 'booking/custom-booking-form',
                                'context' => [
                                    'listing_id' => $listing_id
                                ]
                            ]
                        ],
                    ],
                ],
            ],
            'location_section' => [
                'type' => 'part',
                'path' => 'listing/view/page/listing-location-section',
                '_order' => 80,
            ],
            'reviews_section' => [
                'type' => 'part',
                'path' => 'listing/view/page/listing-reviews-section',
                '_order' => 90,
            ],
            'details_section' => [
                'type' => 'part',
                'path' => 'listing/view/page/listing-details-section',
                '_order' => 100,
            ],
        ];
    }
    return $template;
}, 100);

// Vendor template modifications
add_filter('hivepress/v1/templates/vendor_view_page', function($template) {
    return hp\merge_trees(
        $template,
        [
            'blocks' => [
                'page_container' => [
                    'blocks' => [
                        'vendor_filter' => [
                            'type' => 'container',
                            '_order' => 5, // Antes de page_columns que tiene _order: 10
                            'attributes' => [
                                'class' => ['hp-vendor-filter-wrap'],
                            ],
                            'blocks' => [
                                'filter_content' => [
                                    'type' => 'part',
                                    'path' => 'vendor/view/page/vendor-filter',
                                    '_order' => 10,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]
    );
}, 20);

// Booking form template modifications
add_filter('hivepress/v1/templates/booking_make_form', function($template) {
    $original_fields = $template['blocks']['fields']['blocks'] ?? [];
    
    return [
        'blocks' => [
            'booking_header' => [
                'type' => 'container',
                '_order' => 5,
                'attributes' => [
                    'class' => ['bv-booking-header'],
                ],
                'blocks' => [
                    'price_base' => [
                        'type' => 'container',
                        'attributes' => [
                            'class' => ['bv-price-base'],
                        ],
                        'blocks' => [
                            'price_amount' => [
                                'type' => 'part',
                                'path' => 'booking/price-base',
                            ],
                        ],
                    ],
                ],
            ],
            'fields' => [
                'type' => 'container',
                '_order' => 10,
                'attributes' => [
                    'class' => ['bv-fields-wrapper'],
                ],
                'blocks' => [
                    'fields_grid' => [
                        'type' => 'container',
                        'attributes' => [
                            'class' => ['bv-fields-grid'],
                        ],
                        'blocks' => array_merge(
                            [
                                'date_row' => [
                                    'type' => 'container',
                                    'attributes' => [
                                        'class' => ['bv-form-row', 'bv-date-row'],
                                    ],
                                    '_order' => 10,
                                ],
                                'options_row' => [
                                    'type' => 'container',
                                    'attributes' => [
                                        'class' => ['bv-form-row', 'bv-options-row'],
                                    ],
                                    '_order' => 20,
                                ],
                            ],
                            $original_fields
                        ),
                    ],
                ],
            ],
            'booking_info' => [
                'type' => 'container',
                '_order' => 15,
                'attributes' => [
                    'class' => ['bv-booking-info'],
                ],
                'blocks' => [
                    'info_text' => [
                        'type' => 'text',
                        'text' => esc_html__('Aún no se cobrará nada', 'booking-vista'),
                    ],
                ],
            ],
            'submit' => $template['blocks']['submit'] ?? [],
            'totals' => [
                'type' => 'container',
                '_order' => 25,
                'attributes' => [
                    'class' => ['bv-totals-container'],
                ],
            ],
        ],
    ];
}, 100);

// Ajax handlers for vendor filter
add_action('wp_ajax_bv_vendor_filter', 'kava_child_ajax_filter_vendor_listings');
add_action('wp_ajax_nopriv_bv_vendor_filter', 'kava_child_ajax_filter_vendor_listings');

function kava_child_ajax_filter_vendor_listings() {
    // Verificación de nonce
    if (!check_ajax_referer('bv_vendor_filter_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce');
        return;
    }

     // Obtener y verificar vendor_id
    $vendor_id = isset($_POST['vendor_id']) ? intval($_POST['vendor_id']) : 0;
    if (!$vendor_id) {
        wp_send_json_error('Invalid vendor ID');
        return;
    }

    try {
        // Argumentos base para la consulta
        $args = array(
				'post_type' => 'hp_listing',
				'post_status' => 'publish',
				'post_parent' => $vendor_id,  // Este es el cambio clave
				'posts_per_page' => -1
			);

        // Filtro por nombre si existe
        $nombre = isset($_POST['nombre']) ? sanitize_text_field($_POST['nombre']) : '';
        if (!empty($nombre)) {
            $args['s'] = $nombre;
        }

        // Obtener listings
        $listings_query = new WP_Query($args);
        
        // Filtro por fecha si existe
        $fecha = isset($_POST['fecha']) ? sanitize_text_field($_POST['fecha']) : '';
        $filtered_posts = [];
        
        if ($listings_query->have_posts()) {
            while ($listings_query->have_posts()) {
                $listings_query->the_post();
                $listing_id = get_the_ID();
                
                // Si hay filtro de fecha, verificar disponibilidad
                if (!empty($fecha)) {
                    $fecha_inicio = strtotime($fecha . ' 00:00:00');
                    $fecha_fin = strtotime($fecha . ' 23:59:59');
                    
                    // Verificar si hay reservas para esta fecha
                    $bookings = get_posts(array(
                        'post_type' => 'hp_booking',
                        'post_status' => array('publish', 'draft', 'private'),
                        'post_parent' => $listing_id,
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key' => 'hp_start_time',
                                'value' => $fecha_fin,
                                'compare' => '<=',
                                'type' => 'NUMERIC'
                            ),
                            array(
                                'key' => 'hp_end_time',
                                'value' => $fecha_inicio,
                                'compare' => '>=',
                                'type' => 'NUMERIC'
                            )
                        )
                    ));
					
					
                    
                    // Si hay reservas, saltar este listing
                    if (!empty($bookings)) {
                        continue;
                    }
                }
                
                // Añadir el listing a los resultados filtrados
                $filtered_posts[] = $listing_id;
            }
            wp_reset_postdata();
        }

        ob_start();

        if (!empty($filtered_posts)) {
            echo '<div class="hp-listings hp-block hp-grid"><div class="hp-row">';
            
            foreach ($filtered_posts as $post_id) {
                $post = get_post($post_id);
                setup_postdata($post);
                
                // Obtener meta datos
                $price = get_post_meta($post_id, 'hp_price', true);
                $max_capacity = get_post_meta($post_id, 'hp_square_footage', true);
                $min_capacity = get_post_meta($post_id, 'hp_min_capacity', true);
                $features = get_post_meta($post_id, 'hp_service_features', true);
                
                // Obtener categorías
                $categories = get_the_terms($post_id, 'hp_listing_category');
                
                // Obtener rating
                $rating_args = array(
                    'post_type' => 'hp_review',
                    'post_parent' => $post_id,
                    'post_status' => 'publish'
                );
                $reviews = get_posts($rating_args);
                $rating_sum = 0;
                $rating_count = count($reviews);
                
                foreach ($reviews as $review) {
                    $rating = get_post_meta($review->ID, 'rating', true);
                    $rating_sum += (float) $rating;
                }
                
                $rating_avg = $rating_count > 0 ? round($rating_sum / $rating_count, 1) : 0;
                
                ?>
                <div class="hp-grid__item hp-col-sm-6 hp-col-xs-12">
                    <article class="hp-listing hp-listing--view-block">
                        <header class="hp-listing__header">
    <?php
    // Este div SIEMPRE se mostrará, tenga o no imagen
    ?>
    <div class="hp-listing__image" data-component="carousel-slider" data-preview="false" data-url="<?php echo esc_url(get_permalink($post_id)); ?>">
        <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
            <?php 
            // Solo lo que está dentro del if/else cambia
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, 'medium', ['loading' => 'lazy']);
            } else {
                ?>
                <img src="<?php echo esc_url(hivepress()->get_url() . '/assets/images/placeholders/image-landscape.svg'); ?>" 
                     alt="<?php echo esc_attr(get_the_title($post_id)); ?>" 
                     loading="lazy">
                <?php
            }
            ?>
        </a>
    </div>
</header>

                        <div class="hp-listing__content">
                            <h4 class="hp-listing__title">
                                <a href="<?php echo esc_url(get_permalink()); ?>">
                                    <?php echo esc_html(get_the_title()); ?>
                                </a>
                            </h4>

                            <div class="hp-listing__details hp-listing__details--primary">
                                <?php if (!empty($categories) && !is_wp_error($categories)): ?>
                                    <div class="hp-listing__categories hp-listing__category">
                                        <?php foreach ($categories as $category): ?>
                                            <a href="<?php echo esc_url(get_term_link($category)); ?>">
                                                <?php echo esc_html($category->name); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <time class="hp-listing__created-date hp-listing__date hp-meta" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo esc_html(sprintf(__('Added on %s', 'hivepress'), get_the_date())); ?>
                                </time>

                                <?php if ($rating_count > 0): ?>
                                    <div class="hp-listing__rating hp-rating">
                                        <div class="hp-rating__stars hp-rating-stars" data-component="rating" data-value="<?php echo esc_attr($rating_avg); ?>">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i data-alt="<?php echo esc_attr($i); ?>" 
                                                   class="fas fa-star<?php echo $i <= $rating_avg ? ' active' : ''; ?>" 
                                                   title=""></i>&nbsp;
                                            <?php endfor; ?>
                                        </div>
                                        <a href="<?php echo esc_url(get_permalink() . '#reviews'); ?>" class="hp-rating__details">
                                            <span class="hp-rating__value"><?php echo esc_html($rating_avg); ?></span>
                                            <span class="hp-rating__count">(<?php echo esc_html($rating_count); ?>)</span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($min_capacity) || !empty($max_capacity) || !empty($features)): ?>
                                <div class="hp-block hp-listing__attributes hp-listing__attributes--secondary">
                                    <div class="hp-row">
                                        <?php if (!empty($max_capacity)): ?>
                                            <div class="hp-col-lg-6 hp-col-xs-12">
                                                <div class="hp-listing__attribute hp-listing__attribute--max-capacity">
                                                    <?php echo esc_html($max_capacity); ?> 
                                                    <i class="hp-icon fas fa-fw fa-restroom"></i>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($features)): ?>
                                            <div class="hp-col-lg-6 hp-col-xs-12">
                                                <div class="hp-listing__attribute hp-listing__attribute--service-features">
                                                    <?php echo esc_html($features); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($min_capacity)): ?>
                                            <div class="hp-col-lg-6 hp-col-xs-12">
                                                <div class="hp-listing__attribute hp-listing__attribute--min-capacity">
                                                    <i class="hp-icon fas fa-fw fa-restroom"></i>
                                                    <strong><?php esc_html_e('Minimum Capacity', 'hivepress'); ?></strong>:
                                                    <?php echo esc_html($min_capacity); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <footer class="hp-listing__footer">
                            <div class="hp-block hp-listing__attributes hp-listing__attributes--primary">
<div class="hp-listing__attribute hp-listing__attribute--price">
    <?php 
    // Obtener y procesar el precio
    $price = get_post_meta($post_id, 'hp_price', true);
    
    // Obtener y procesar el símbolo de moneda
    $currency_symbol = hivepress()->get_currency_symbol();
    if(is_array($currency_symbol)) {
        $currency_symbol = reset($currency_symbol);
    }
    
    // Formatear y mostrar
    echo esc_html($currency_symbol);
    echo ' ' . number_format(floatval($price), 2); 
    ?> / day
</div>
                            </div>

                            <div class="hp-listing__actions hp-listing__actions--primary">
                                <div id="message_send_modal_<?php echo esc_attr($post_id); ?>" class="hp-modal" data-component="modal">
                                    <h3 class="hp-modal__title"><?php esc_html_e('Reply to Listing', 'hivepress'); ?></h3>
                                    <?php
                                    if (function_exists('hivepress') && method_exists(hivepress()->template, 'render')) {
                                        echo hivepress()->template->render('listing_message_modal', ['listing_id' => $post_id]);
                                    }
                                    ?>
                                </div>

                                <a href="#message_send_modal_<?php echo esc_attr($post_id); ?>" 
                                   title="<?php esc_attr_e('Reply to Listing', 'hivepress'); ?>" 
                                   class="hp-listing__action hp-listing__action--message">
                                    <i class="hp-icon fas fa-comment"></i>
                                </a>

                                <a class="hp-listing__action hp-listing__action--favorite" 
                                   href="#" 
                                   data-component="toggle" 
                                   data-url="<?php echo esc_url(hivepress()->router->get_url('listing_favorite_action', ['listing_id' => $post_id])); ?>" 
                                   data-icon="heart" 
                                   data-caption="<?php esc_attr_e('Remove from Favorites', 'hivepress'); ?>" 
                                   title="<?php esc_attr_e('Add to Favorites', 'hivepress'); ?>">
                                    <i class="hp-icon fas fa-heart"></i>
                                </a>
                            </div>
                        </footer>
                    </article>
                </div>
                <?php
            }
            
            echo '</div></div>';
        } else {
            echo '<p class="hp-no-results">' . esc_html__('No services found matching your criteria.', 'booking-vista') . '</p>';
        }

        $html = ob_get_clean();

        wp_send_json_success([
            'html' => $html,
            'pagination' => [
                'currentPage' => 1,
                'totalPages' => 1
            ]
        ]);

    } catch (Exception $e) {
        error_log('Error en bv_ajax_filter_vendor_listings: ' . $e->getMessage());
        wp_send_json_error('Error processing request: ' . $e->getMessage());
    }
}

add_action('wp', function() {
    if (hivepress()->router->get_current_route_name() === 'booking_make_page') {
        $booking = Models\Booking::query()->filter([
            'status' => 'auto-draft',
            'drafted' => true,
            'user' => get_current_user_id(),
        ])->get_first();

        if (!$booking) {
            $booking = (new Models\Booking())->fill([
                'status' => 'auto-draft',
                'drafted' => true,
                'user' => get_current_user_id(),
            ]);
            $booking->save(['status', 'drafted', 'user']);
        }

        if (isset($_GET['_dates']) && !empty($_GET['_dates'])) {

            if (!is_array($dates)) {
                // Si es un string, podría ser un único valor o un valor separado por comas
                $dates = [$dates]; // Convertir a array con un único elemento
            }

            $dates = $_GET['_dates'];
            if (count($dates) === 1) {
                $start_time = strtotime($dates[0]);
                $end_time = strtotime($dates[0] . ' +1 day') - 1;
            } else if (count($dates) === 2) {
                $start_time = strtotime($dates[0]);
                $end_time = strtotime($dates[1] . ' +1 day') - 1;
            }

            if ($start_time && $end_time) {
                $booking->fill([
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'listing' => absint($_GET['listing'])
                ]);
                
                // Guardar los extras seleccionados
                if (isset($_GET['_extras'])) {
                    $booking->fill(['hp_price_extras' => $_GET['_extras']]);
                }
                
                // Guardar todo
                $booking->save(['start_time', 'end_time', 'listing', 'hp_price_extras']);

                if(isset($_GET['_calculated_price'])) {
                    update_post_meta($booking->get_id(), '_calculated_price', floatval($_GET['_calculated_price']));
                    
                    // Mantener price_details solo para visualización
                    if(isset($_GET['price_details'])) {
                        $price_details = json_decode(stripslashes($_GET['price_details']), true);
                        if($price_details) {
                            update_post_meta($booking->get_id(), 'price_details', $price_details);
                        }
                    }
                }
            }
        }
    }
}, 5);

// Debugging para la página de detalles de reserva
add_action('wp', function() {
    if (hivepress()->router->get_current_route_name() === 'booking_make_details_page') {
        $booking = hivepress()->request->get_context('booking');
        if ($booking) {
            error_log('Booking ID: ' . $booking->get_id());
            error_log('Booking Data: ' . print_r($booking->serialize(), true));
            error_log('Variable Extras: ' . print_r(get_post_meta($booking->get_id(), 'variable_quantity_extras', true), true));
        }
    }
}, 20);

// Debugging para actualizaciones AJAX de precios
add_action('wp_ajax_nopriv_hp_update_booking_price', function() {
    error_log('=== AJAX Booking Price Update ===');
    error_log('POST Data: ' . print_r($_POST, true));
}, 1);

add_action('wp_ajax_hp_update_booking_price', function() {
    error_log('=== AJAX Booking Price Update (Logged in) ===');
    error_log('POST Data: ' . print_r($_POST, true));
}, 1);

// Modificaciones al menú de gestión de listings
add_filter('hivepress/v1/menus/listing_manage/items', function($items) {
    if (hivepress()->router->get_current_route_name() === 'listing_view_page') {
        if (isset($items['listing_view'])) {
            unset($items['listing_view']);
        }
    }
    
    $listing = hivepress()->request->get_context('listing');
    if ($listing) {
        $vendor = $listing->get_vendor();
        if ($vendor && get_current_user_id() === $vendor->get_user__id()) {
            $items['vendor_store'] = [
                'label' => esc_html__('My Store', 'bookingvista'),
                'url' => hivepress()->router->get_url('vendor_view_page', ['vendor_id' => $vendor->get_id()]),
                'order' => 30,
            ];
        }
    }
    
    return $items;
}, 20);

// Soporte para Elementor
function kava_child_support_elementor() {
    add_theme_support('elementor');
    
    if (function_exists('elementor_theme_do_location')) {
        register_nav_menus([
            'header' => __('Header Menu', 'kava-child'),
            'footer' => __('Footer Menu', 'kava-child')
        ]);
    }
}
add_action('after_setup_theme', 'kava_child_support_elementor');

// Modificar paginación para el filtro de vendor
function kava_child_modify_pagination_links($link) {
    if (!is_singular('hp_vendor')) {
        return $link;
    }

    $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
    $nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';

    if (!empty($fecha)) {
        $link = add_query_arg('fecha', urlencode($fecha), $link);
    }

    if (!empty($nombre)) {
        $link = add_query_arg('nombre', urlencode($nombre), $link);
    }

    return $link;
}
add_filter('paginate_links', 'kava_child_modify_pagination_links');

// Modificar template del filtro
function kava_child_modify_filter_template($template) {
    if (isset($template['blocks']['vendor_filter'])) {
        $fecha = isset($_GET['fecha']) ? esc_attr($_GET['fecha']) : '';
        $nombre = isset($_GET['nombre']) ? esc_attr($_GET['nombre']) : '';

        $template['blocks']['vendor_filter']['blocks']['filter_content']['data'] = [
            'fecha' => $fecha,
            'nombre' => $nombre,
        ];
    }

    return $template;
}
add_filter('hivepress/v1/templates/vendor_view_page', 'kava_child_modify_filter_template', 20);

require_once get_stylesheet_directory() . '/includes/fields/class-booking-details.php';

add_filter(
    'hivepress/v1/routes',
    function( $routes ) {
        if(isset($routes['booking_make_details_page'])){
            $routes['booking_make_details_page']['title'] = 'Confirm Reservation Details';    
        }
        return $routes;
    },
    1000
);

add_filter('hivepress/v1/forms/booking_confirm', function($form_args, $form) {
   $booking = $form->get_model();
   if (!$booking) {
       return $form_args;
   }

   // Obtener listing y precio base
   $listing = $booking->get_listing();
   
   // Obtener price_details que contiene los precios calculados
   $price_details = get_post_meta($booking->get_id(), 'price_details', true);
   
   // Construir HTML para extras y precio base
   $extras_html = '';
   
   if ($price_details && isset($price_details['extras'])) {
       // Primero, buscar el precio base en los totales
       $base_item = null;
       foreach ($price_details['extras'] as $item) {
           if (strpos($item['name'], 'Base price') === 0) {
               $base_item = $item;
               continue;
           }
           // Construir html para los otros extras
           $extras_html .= sprintf(
               '<div class="hp-price-row">
                   <span>%s</span>
                   <span>%s</span>
               </div>',
               esc_html($item['name']),
               hivepress()->woocommerce->format_price($item['amount'])
           );
       }
   }

   // Ocultar campos originales
   $hide_style = 'display: none !important; height: 0 !important; width: 0 !important; margin: 0 !important; padding: 0 !important; opacity: 0 !important; position: absolute !important;';
   
   if (isset($form_args['fields']['start_time'])) {
       $form_args['fields']['start_time']['attributes']['class'][] = 'hp-hidden-field';
       $form_args['fields']['start_time']['wrapper_class'] = 'hp-hidden-field';
   }
   if (isset($form_args['fields']['end_time'])) {
       $form_args['fields']['end_time']['attributes']['style'] = $hide_style;
   }
   if (isset($form_args['fields']['quantity'])) {
       $form_args['fields']['quantity']['attributes']['style'] = $hide_style;
   }
   if (isset($form_args['fields']['_price'])) {
       $form_args['fields']['_price']['attributes']['style'] = $hide_style;
   }

   // Agregar nuestro campo custom
   $form_args['fields'] = array_merge(
       [
           'booking_details' => [
               'type' => 'booking_details',
               'value' => sprintf(
                   '<div class="hp-booking-details">
                       <div class="hp-booking-section">
                           <div class="hp-booking-header">
                               <div class="hp-dates-places">
                                   <div class="hp-dates">
                                       <span>Start Time - End Time:</span>
                                       <span>%s - %s</span>
                                   </div>
                                   %s
                               </div>
                           </div>
                           <div class="hp-price-details">
                               <h3>Price Details</h3>
                               <div class="hp-booking-price">
                                   <div class="hp-price-row">
                                       <span>%s</span>
                                       <span>%s</span>
                                   </div>
                                   %s
                                   <div class="hp-price-row hp-price-total">
                                       <span>Total (incl. taxes)</span>
                                       <span>%s</span>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>',
                   wp_date('M j, Y', $booking->get_start_time()),
                   wp_date('M j, Y', $booking->get_end_time()),
                   get_option('hp_booking_enable_quantity') ? sprintf(
                       '<div class="hp-places">
                           <span>Places:</span>
                           <span>%d</span>
                       </div>',
                       $booking->get_quantity()
                   ) : '',
                   $base_item ? esc_html($base_item['name']) : 'Base price',  // Usar el nombre con multiplicadores
                   $base_item ? hivepress()->woocommerce->format_price($base_item['amount']) : hivepress()->woocommerce->format_price($listing->get_price()),  // Usar el precio calculado
                   $extras_html,
                   hivepress()->woocommerce->format_price(get_post_meta($booking->get_id(), '_calculated_price', true))
               ),
               '_order' => 1
           ],
       ],
       $form_args['fields']
   );

   return $form_args;
}, 20, 2);

function kava_child_load_booking_form_assets() {
    // Añadir logs para depuración
    error_log('=== Iniciando kava_child_load_booking_form_assets ===');
    
    global $post;
    $should_load = false;

    error_log('Is front page: ' . (is_front_page() ? 'true' : 'false'));
    error_log('Is home: ' . (is_home() ? 'true' : 'false'));
    
    if (is_front_page() || is_home()) {
        $should_load = true;
        error_log('Should load por front/home page');
    }

    if (is_singular() && $post) {
        if (has_shortcode($post->post_content, 'cotizador_eventos')) {
            $should_load = true;
            error_log('Should load por shortcode encontrado');
        }
    }

    error_log('Should load final: ' . ($should_load ? 'true' : 'false'));

    if (!$should_load) {
        error_log('Retornando sin cargar assets');
        return;
    }

    error_log('Procediendo a cargar assets');
    error_log('Theme directory: ' . get_stylesheet_directory_uri());
    error_log('CSS path: ' . get_stylesheet_directory_uri() . '/assets/css/booking-form.css');
    error_log('JS path: ' . get_stylesheet_directory_uri() . '/assets/js/booking-form.js');


    $theme = wp_get_theme();
    $theme_version = $theme->get('Version');

    // Cargar CSS del formulario
    wp_enqueue_style(
        'kava-child-booking-form',
        get_stylesheet_directory_uri() . '/assets/css/booking-form.css',
        array(),
        $theme_version
    );

    // Cargar JS del formulario
    wp_enqueue_script(
        'kava-child-booking-form',
        get_stylesheet_directory_uri() . '/assets/js/booking-form.js',
        array('jquery', 'flatpickr'),
        $theme_version,
        true
    );

    // Obtener tax rate
    global $wpdb;
    $tax_rate = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT tax_rate FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_id = %d",
            1
        )
    );

    // Localizar script
    wp_localize_script('kava-child-booking-form', 'bvData', array(
        'currency' => get_woocommerce_currency_symbol(),
        'thousand_sep' => wc_get_price_thousand_separator(),
        'decimal_sep' => wc_get_price_decimal_separator(),
        'num_decimals' => wc_get_price_decimals(),
        'tax_rate' => floatval($tax_rate)
    ));
}

add_action('wp_enqueue_scripts', 'kava_child_load_booking_form_assets', 20);

function cargar_fuentes_montserrat() {
  // Preconectar con Google Fonts y Gstatic
  echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
  echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
  // Cargar la fuente Montserrat con los pesos y estilos especificados
  echo '<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">' . "\n";
}
add_action( 'wp_head', 'cargar_fuentes_montserrat' );