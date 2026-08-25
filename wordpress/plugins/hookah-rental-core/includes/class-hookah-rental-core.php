<?php
/**
 * The core plugin class orchestrating hooks and loader.
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_Rental_Core {
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->version = HOOKAH_RENTAL_CORE_VERSION;
        $this->plugin_name = 'hookah-rental-core';
        $this->load_dependencies();
    }

    private function load_dependencies() {
        require_once HOOKAH_RENTAL_CORE_PATH . 'includes/class-hookah-rental-i18n.php';
        require_once HOOKAH_RENTAL_CORE_PATH . 'includes/class-hookah-serviceability.php';
        require_once HOOKAH_RENTAL_CORE_PATH . 'api/class-hookah-api-client.php';
        require_once HOOKAH_RENTAL_CORE_PATH . 'woo/class-hookah-woo-integration.php';
        require_once HOOKAH_RENTAL_CORE_PATH . 'rental/class-hookah-rental-manager.php';
        require_once HOOKAH_RENTAL_CORE_PATH . 'checkout/class-hookah-checkout-fields.php';
        require_once HOOKAH_RENTAL_CORE_PATH . 'availability/class-hookah-availability-checker.php';
        require_once HOOKAH_RENTAL_CORE_PATH . 'admin/class-hookah-admin-settings.php';
        require_once HOOKAH_RENTAL_CORE_PATH . 'includes/class-hookah-email-auth.php';
    }

    public function run() {
        // Disable WooCommerce Coming Soon mode
        add_filter('woocommerce_is_coming_soon', '__return_false', 9999);
        add_filter('woocommerce_show_coming_soon_banner', '__return_false', 9999);
        add_filter('woocommerce_coming_soon_exclude', '__return_true', 9999);

        $i18n = new Hookah_Rental_i18n();
        add_action('plugins_loaded', [$i18n, 'load_plugin_textdomain']);

        // Initialize sub-modules
        $admin = new Hookah_Admin_Settings($this->plugin_name, $this->version);
        $admin->init_hooks();

        $woo = new Hookah_Woo_Integration($this->plugin_name, $this->version);
        $woo->init_hooks();

        $rental = new Hookah_Rental_Manager($this->plugin_name, $this->version);
        $rental->init_hooks();

        $checkout = new Hookah_Checkout_Fields($this->plugin_name, $this->version);
        $checkout->init_hooks();

        $availability = new Hookah_Availability_Checker($this->plugin_name, $this->version);
        $availability->init_hooks();

        $email_auth = new Hookah_Email_Auth();

        // Register Driver & Admin Portals
        add_action('template_redirect', [$this, 'handle_portal_routing']);
        add_shortcode('shisharent_courier_portal', [$this, 'render_courier_portal_shortcode']);
        add_shortcode('shisharent_admin_dashboard', [$this, 'render_admin_dashboard_shortcode']);
    }

    public function handle_portal_routing() {
        if (isset($_GET['shisharent_portal'])) {
            $portal = sanitize_key($_GET['shisharent_portal']);
            if ($portal === 'courier') {
                include HOOKAH_RENTAL_CORE_PATH . 'templates/courier-driver-portal.php';
                exit;
            } elseif ($portal === 'admin') {
                include HOOKAH_RENTAL_CORE_PATH . 'templates/admin-command-center.php';
                exit;
            }
        }
    }

    public function render_courier_portal_shortcode() {
        ob_start();
        include HOOKAH_RENTAL_CORE_PATH . 'templates/courier-driver-portal.php';
        return ob_get_clean();
    }

    public function render_admin_dashboard_shortcode() {
        ob_start();
        include HOOKAH_RENTAL_CORE_PATH . 'templates/admin-command-center.php';
        return ob_get_clean();
    }
}
