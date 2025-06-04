<div class="hp-listing__title-container">
   <h1 class="hp-listing__title"><?php echo esc_html(get_the_title()); ?></h1>
   <?php
   if (function_exists('hivepress')) {
    $listing_id = get_the_ID();
    $favorite_ids = [];
    if (is_user_logged_in()) {
        $favorites = \HivePress\Models\Favorite::query()
            ->filter(['user' => get_current_user_id()])
            ->get();
        $favorite_ids = array_map(function($favorite) {
            return $favorite->get_listing__id();
        }, $favorites->serialize());
    }
    $is_favorite = in_array($listing_id, $favorite_ids);
    $listing = \HivePress\Models\Listing::query()->get_by_id($listing_id);
    if ($listing) {
        echo '<div class="favorite-container">';
        // Cambios principales aquí:
        echo '<div class="hp-link' . ($is_favorite ? ' hp-state-active' : '') . '" data-listing-id="' . esc_attr($listing_id) . '">';
        echo '<i class="hp-icon fas fa-heart"></i>';
        echo '<span>' . ($is_favorite ? 'Remove from Favorites' : 'Add to Favorites') . '</span>';
        echo '</div>';
        echo '</div>';
    }
}
   ?>
</div>

<style>
.hp-listing__title-container {
   display: flex !important;
   align-items: center !important;
   gap: 15px !important;
   flex-direction: row !important;
   margin-top: 20px; /* Ajusta este valor según necesites */
}

.hp-listing__title {
   margin: 0;
   flex: 1;
}

.favorite-container {
   flex-shrink: 0;
   margin-left: 15px;
	margin-top: -40px;
}

.favorite-container .hp-link {
   display: flex !important;
   align-items: center !important;
   gap: 8px !important;
}

.favorite-container .hp-icon {
   font-size: 24px !important;
   color: rgba(0, 0, 0, 0.6) !important;
   transition: all 0.3s ease !important;
}

.favorite-container .hp-link:hover .hp-icon {
   transform: scale(1.1) !important;
}

.favorite-container .hp-link.hp-state-active .hp-icon {
   color: #ff69b4 !important;
}

.favorite-container .hp-link span {
   font-size: 14px !important;
   color: #666 !important;
   display: inline !important;
}

.favorite-container .hp-link:hover span {
   color: #333 !important;
}

.favorite-container .hp-link.hp-state-active span {
   color: #666 !important;
}
	
	/* Solución específica para la página del listing */
.hp-listing__title-container .hp-link {
   position: relative !important;
   top: auto !important;
   right: auto !important;
   margin-left: 15px !important;
}

/* Mantener los estilos originales para el cotizador */
.evento-thumbnail .hp-link {
   position: absolute;
   top: 10px;
   right: 10px;
   z-index: 11;
}
</style>