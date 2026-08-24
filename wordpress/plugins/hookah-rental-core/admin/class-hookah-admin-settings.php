<?php
/**
 * Admin Settings for Hookah Rental Core
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_Admin_Settings {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function init_hooks() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Hookah Rentals', 'hookah-rental-core'),
            __('Hookah Rentals', 'hookah-rental-core'),
            'manage_woocommerce',
            'hookah-rental-settings',
            [$this, 'render_settings_page'],
            'dashicons-smoke',
            56
        );
    }

    public function register_settings() {
        register_setting('hookah_rental_settings_group', 'hookah_rental_api_url');
        register_setting('hookah_rental_settings_group', 'hookah_rental_shared_secret');
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('hookah_rental_settings_group');
                do_settings_sections('hookah_rental_settings_group');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('NestJS API Base URL', 'hookah-rental-core'); ?></th>
                        <td>
                            <input type="url" name="hookah_rental_api_url" class="regular-text" value="<?php echo esc_attr(get_option('hookah_rental_api_url', 'http://backend:3000/api')); ?>" />
                            <p class="description"><?php esc_html_e('Internal docker or external endpoint for the NestJS API.', 'hookah-rental-core'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Shared API Secret', 'hookah-rental-core'); ?></th>
                        <td>
                            <input type="password" name="hookah_rental_shared_secret" class="regular-text" value="<?php echo esc_attr(get_option('hookah_rental_shared_secret', '')); ?>" />
                            <p class="description"><?php esc_html_e('Shared secret key for authenticating WordPress to NestJS API requests.', 'hookah-rental-core'); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
