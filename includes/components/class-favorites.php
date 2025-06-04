<?php
namespace HivePress\Components;

use HivePress\Helpers as hp;
use HivePress\Models;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Favorites component.
 */
final class Favorites extends Component {

    /**
     * Class constructor.
     *
     * @param array $args Component arguments.
     */
    public function __construct($args = []) {
    // Hooks existentes
    add_action('hivepress/v1/models/user/delete', [$this, 'delete_favorites']);
    if (!is_admin()) {
        add_action('wp', [$this, 'set_favorites'], 100);
    }
    
    // Hooks de AJAX
    add_action('wp_ajax_toggle_custom_favorite', [$this, 'toggle_favorite']);
    add_action('wp_ajax_nopriv_toggle_custom_favorite', [$this, 'handle_unauthorized']);

    parent::__construct($args);
}

    /**
     * Deletes favorites.
     *
     * @param int $user_id User ID.
     */
    public function delete_favorites($user_id) {
        Models\Favorite::query()->filter(
            [
                'user' => $user_id,
            ]
        )->delete();
    }

    /**
     * Sets favorites.
     */
    public function set_favorites() {
        // Check authentication
        if (!is_user_logged_in()) {
            return;
        }

        // Set query
        $query = Models\Favorite::query()->filter(
            [
                'user' => get_current_user_id(),
            ]
        )->order(['added_date' => 'desc']);

        // Get cached IDs
        $favorite_ids = hivepress()->cache->get_user_cache(
            get_current_user_id(),
            array_merge($query->get_args(), ['fields' => 'listing_ids']),
            'models/favorite'
        );

        if (is_null($favorite_ids)) {
            // Get favorite IDs
            $favorite_ids = array_map(
                function($favorite) {
                    return $favorite->get_listing__id();
                },
                $query->get()->serialize()
            );

            // Cache IDs
            if (count($favorite_ids) <= 1000) {
                hivepress()->cache->set_user_cache(
                    get_current_user_id(),
                    array_merge($query->get_args(), ['fields' => 'listing_ids']),
                    'models/favorite',
                    $favorite_ids
                );
            }
        }

        // Set request context
        hivepress()->request->set_context('favorite_ids', $favorite_ids);
    }
	
	/**
 * Toggle favorite status.
 */
public function toggle_favorite() {
    if (!class_exists('HivePress\Models\Favorite')) {
        wp_send_json_error('HivePress Favorites no está activo');
        return;
    }

    $listing_id = intval($_POST['listing_id']);
    
    if (!$listing_id) {
        wp_send_json_error('ID de listing inválido');
        return;
    }

    $favorites = Models\Favorite::query()->filter([
        'user' => get_current_user_id(),
        'listing' => $listing_id,
    ])->get();

    if ($favorites->count()) {
        $favorites->get()->delete();
        wp_send_json_success(['status' => 'removed']);
    } else {
        $favorite = new Models\Favorite();
        $favorite->fill([
            'user' => get_current_user_id(),
            'listing' => $listing_id,
        ]);
        $favorite->save();
        wp_send_json_success(['status' => 'added']);
    }
}
}