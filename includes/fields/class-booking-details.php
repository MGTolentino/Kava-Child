<?php
namespace HivePress\Fields;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Booking details field.
 */
class Booking_Details extends Field {

    /**
     * Class initializer.
     *
     * @param array $meta Class meta values.
     */
    public static function init($meta = []) {
        $meta = hp\merge_arrays(
            [
                'label' => esc_html__('Booking Details', 'hivepress-bookings'),
                'filterable' => false,
                'sortable' => false,
            ],
            $meta
        );

        parent::init($meta);
    }

    /**
     * Bootstraps field properties.
     */
    protected function boot() {
        parent::boot();
    }

    /**
     * Normalizes field value.
     */
    protected function normalize() {
        parent::normalize();

        if (!is_null($this->value)) {
            $this->value = trim(wp_unslash($this->value));
        }
    }

    /**
     * Sanitizes field value.
     */
    protected function sanitize() {
        // Permitir HTML en este campo
        if (!is_null($this->value)) {
            $this->value = wp_kses_post($this->value);
        }
    }

    /**
     * Validates field value.
     *
     * @return bool
     */
    public function validate() {
        return true; // No necesitamos validación especial
    }

    /**
     * Renders field HTML.
     *
     * @return string
     */
    public function render() {
        return $this->value;
    }
}