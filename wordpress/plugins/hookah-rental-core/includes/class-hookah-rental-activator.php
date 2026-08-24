<?php
/**
 * Fired during plugin activation
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_Rental_Activator {
    public static function activate() {
        // Set default options if not existing
        if (!get_option('hookah_rental_api_url')) {
            update_option('hookah_rental_api_url', 'http://localhost:3000/api');
        }
    }
}
