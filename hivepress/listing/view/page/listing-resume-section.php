<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

// Obtener el listing actual
$listing = hivepress()->request->get_context('listing');

// Obtener los atributos configurados
$all_attributes = hivepress()->attribute->get_attributes('listing');

// Array para almacenar los atributos filtrados
$attributes = [];

// Iterar sobre los atributos
foreach ($all_attributes as $attribute_name => $attribute) {
    // Verificar si el atributo debe mostrarse en las áreas secundarias
    if (!empty($attribute['display_areas']) && (
        in_array('view_block_secondary', $attribute['display_areas']) || 
        in_array('view_page_secondary', $attribute['display_areas'])
    )) {
        // Obtener el valor usando el método dinámico
        $method = 'get_' . $attribute_name;
        $value = $listing->$method();
        
        // Solo agregar si tiene valor
        if (!empty($value)) {
            // Obtener el valor formateado
            $display_method = 'display_' . $attribute_name;
            $display_value = $listing->$display_method();
            
            // Obtener el ícono del post meta
            $icon = get_post_meta($attribute['id'], 'hp_icon', true);
            // Si no hay ícono, usar uno por defecto
            $icon = $icon ? 'fa-' . $icon : 'fa-info-circle';
            
            // Agregar al array de atributos
            $attributes[] = [
                'icon' => $icon,
                'value' => $attribute['label'] . ': ' . $display_value,
            ];
        }
    }
}
?>
<div class="bv-resume-section" id="resumen">
    <h2 class="bv-section-title">Service Information</h2>
    
    <!-- Atributos -->
    <div class="bv-attributes-grid">
        <?php foreach ($attributes as $attribute): ?>
            <?php if (!empty($attribute['value'])): ?>
                <div class="bv-attribute">
                    <i class="fas <?php echo esc_attr($attribute['icon']); ?>"></i>
                    <span><?php echo esc_html($attribute['value']); ?></span>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <!-- Separador -->
    <div class="bv-separator"></div>
    
    <!-- Descripción del Listing -->
    <?php if ($listing->get_description()): ?>
        <div class="hp-listing__description">
            <?php echo $listing->display_description(); ?>
        </div>
    <?php endif; ?>
    <!-- Tags del Listing -->
    <?php if ($listing->get_tags__id()): ?>
        <div class="hp-listing__tags hp-section tagcloud">
            <?php foreach ($listing->get_tags() as $tag): ?>
                <a href="<?php echo esc_url(hivepress()->router->get_url('listing_tag_view_page', ['listing_tag_id' => $tag->get_id()])); ?>" class="tag-cloud-link">
                    <?php echo esc_html($tag->get_name()); ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>