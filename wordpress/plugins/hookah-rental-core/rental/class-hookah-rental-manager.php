<?php
/**
 * Hookah Rental Manager
 *
 * Coordinates rental attributes, package selection, and duration calculations.
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_Rental_Manager {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function init_hooks() {
        // Register shortcodes for rental booking widget
        add_shortcode('shisharent_widget', [$this, 'render_rental_widget']);
        add_shortcode('booknowshisha_rental_widget', [$this, 'render_rental_widget']);
    }

    public function render_rental_widget($atts = []) {
        ob_start();
        include HOOKAH_RENTAL_CORE_PATH . 'templates/rental-booking-widget.php';
        return ob_get_clean();
    }
}
