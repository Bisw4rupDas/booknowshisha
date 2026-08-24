<?php
/**
 * Fired during plugin deactivation
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_Rental_Deactivator {
    public static function deactivate() {
        // Flush rewrite rules or temporary cached transients
        delete_transient('hookah_rental_availability_cache');
    }
}
