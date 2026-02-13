<?php
/**
 * Sistema de Filtros AJAX para Listings
 * Basado en el sistema exitoso del Cotizador de Eventos
 */

class MRB_Ajax_Filters {
    
    public function __construct() {
        // Registrar endpoints AJAX
        add_action('wp_ajax_mrb_filter_listings', array($this, 'filter_listings'));
        add_action('wp_ajax_nopriv_mrb_filter_listings', array($this, 'filter_listings'));
        
        // Registrar script de filtros
        add_action('wp_enqueue_scripts', array($this, 'enqueue_filter_scripts'));
    }
    
    /**
     * Filtrar listings via AJAX
     */
    public function filter_listings() {
        // Verificar nonce para seguridad
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mrb_filters_nonce')) {
            wp_die('Seguridad: Nonce inválido');
        }
        
        // Obtener parámetros de filtros
        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
        $location = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '';
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : '';
        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
        
        // Construir query
        $args = array(
            'post_type' => 'hp_listing',
            'post_status' => 'publish',
            'posts_per_page' => 18, // 6 columnas x 3 filas
            'paged' => $paged,
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        // Filtro por categoría
        if (!empty($category) && $category !== 'all') {
            $args['tax_query'][] = array(
                'taxonomy' => 'hp_listing_category',
                'field' => 'slug',
                'terms' => $category
            );
        }
        
        // Filtro por ubicación
        if (!empty($location)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'hp_listing_ubicacion',
                'field' => 'slug',
                'terms' => $location
            );
        }
        
        // Búsqueda por texto
        if (!empty($search)) {
            $args['s'] = $search;
        }
        
        // Si hay múltiples taxonomías, establecer relación AND
        if (isset($args['tax_query']) && count($args['tax_query']) > 1) {
            $args['tax_query']['relation'] = 'AND';
        }
        
        // Ejecutar query
        $query = new WP_Query($args);
        
        // Preparar respuesta
        ob_start();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                // Usar el template de card original que funciona
                include get_stylesheet_directory() . '/template-parts/listing-card.php';
            }
        } else {
            ?>
            <div class="mrb-no-results" style="grid-column: 1/-1; text-align: center; padding: 60px 20px;">
                <svg viewBox="0 0 32 32" style="width: 64px; height: 64px; margin: 0 auto 20px; fill: #717171;">
                    <path d="M13 0a13 13 0 0 1 10.5 20.67l7.91 7.92-2.82 2.82-7.92-7.91A12.94 12.94 0 0 1 13 26a13 13 0 0 1 0-26zm0 4a9 9 0 1 0 0 18 9 9 0 0 0 0-18z"></path>
                </svg>
                <h3 style="font-size: 22px; margin-bottom: 8px;">No se encontraron resultados</h3>
                <p style="color: #717171;">Intenta ajustar los filtros o buscar algo diferente</p>
            </div>
            <?php
        }
        
        $html = ob_get_clean();
        
        // Preparar datos de paginación
        $total_pages = $query->max_num_pages;
        $total_posts = $query->found_posts;
        
        // Enviar respuesta JSON
        wp_send_json_success(array(
            'html' => $html,
            'found_posts' => $total_posts,
            'max_pages' => $total_pages,
            'current_page' => $paged
        ));
        
        wp_die();
    }
    
    /**
     * Cargar scripts de filtros
     */
    public function enqueue_filter_scripts() {
        if (is_page_template('page-inicio-airbnb.php')) {
            wp_enqueue_script(
                'mrb-filters',
                get_stylesheet_directory_uri() . '/assets/js/mrb-filters.js',
                array('jquery'),
                time(),
                true
            );
            
            // Pasar datos a JavaScript
            wp_localize_script('mrb-filters', 'mrb_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mrb_filters_nonce'),
                'loading_text' => 'Cargando...'
            ));
        }
    }
}

// Inicializar clase
new MRB_Ajax_Filters();