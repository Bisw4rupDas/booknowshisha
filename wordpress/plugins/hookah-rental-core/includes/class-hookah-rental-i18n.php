<?php
/**
 * Define the internationalization functionality
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_Rental_i18n {
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'hookah-rental-core',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}
