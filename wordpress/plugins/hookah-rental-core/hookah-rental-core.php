<?php
/**
 * Plugin Name: Hookah Rental Core
 * Plugin URI: https://booknowshisha.com
 * Description: Core WooCommerce integration plugin connecting the BookNowShisha storefront with the NestJS rental & availability engine.
 * Version: 1.0.0
 * Author: BookNowShisha Engineering
 * Author URI: https://booknowshisha.com
 * Text Domain: hookah-rental-core
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * WC requires at least: 8.0
 * WC tested up to: 9.0
 * License: Proprietary
 */

if (!defined('ABSPATH')) {
    exit;
}

define('HOOKAH_RENTAL_CORE_VERSION', '1.0.0');
define('HOOKAH_RENTAL_CORE_PATH', plugin_dir_path(__FILE__));
define('HOOKAH_RENTAL_CORE_URL', plugin_dir_url(__FILE__));
define('HOOKAH_RENTAL_CORE_BASENAME', plugin_basename(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_hookah_rental_core() {
    require_once HOOKAH_RENTAL_CORE_PATH . 'includes/class-hookah-rental-activator.php';
    Hookah_Rental_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_hookah_rental_core() {
    require_once HOOKAH_RENTAL_CORE_PATH . 'includes/class-hookah-rental-deactivator.php';
    Hookah_Rental_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_hookah_rental_core');
register_deactivation_hook(__FILE__, 'deactivate_hookah_rental_core');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require HOOKAH_RENTAL_CORE_PATH . 'includes/class-hookah-rental-core.php';

/**
 * Begins execution of the plugin.
 */
function run_hookah_rental_core() {
    $plugin = new Hookah_Rental_Core();
    $plugin->run();
}
run_hookah_rental_core();
