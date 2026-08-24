<?php
/**
 * Customer Google Authentication System (Mock & Real OAuth Placeholder Ready)
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_Google_Auth {

    public function __construct() {
        // AJAX Endpoints for Customer Google Authentication
        add_action('wp_ajax_nopriv_bns_google_login', [$this, 'ajax_google_login']);
        add_action('wp_ajax_bns_google_login', [$this, 'ajax_google_login']);

        add_action('wp_ajax_bns_customer_logout', [$this, 'ajax_customer_logout']);

        // Customer Role Restriction (Protect /wp-admin/)
        add_action('admin_init', [$this, 'restrict_admin_access']);
        add_action('after_setup_theme', [$this, 'hide_admin_bar_for_customers']);
    }

    /**
     * AJAX: Handle Google Login (Mock Flow & Real OAuth Integration)
     */
    public function ajax_google_login() {
        $id_token = isset($_POST['id_token']) ? sanitize_text_field($_POST['id_token']) : '';
        $redirect = isset($_POST['redirect']) ? esc_url_raw($_POST['redirect']) : '';

        $email       = '';
        $first_name  = '';
        $last_name   = '';
        $google_id   = '';
        $display_name = '';

        // Check if real Google ID token is provided
        if (!empty($id_token) && $id_token !== 'mock_token') {
            $google_user = $this->verify_google_id_token($id_token);
            if ($google_user) {
                $email        = sanitize_email($google_user['email']);
                $first_name   = sanitize_text_field($google_user['given_name'] ?? 'Google');
                $last_name    = sanitize_text_field($google_user['family_name'] ?? 'Customer');
                $google_id    = sanitize_text_field($google_user['sub'] ?? '');
                $display_name = sanitize_text_field($google_user['name'] ?? $first_name . ' ' . $last_name);
            }
        }

        // Mock / Development Flow if real token is not provided
        if (empty($email)) {
            $email        = 'alex.customer@gmail.com';
            $first_name   = 'Alex';
            $last_name    = 'Customer';
            $google_id    = 'mock_google_1092837465';
            $display_name = 'Alex Customer';
        }

        // Find or create customer in WordPress
        $user = get_user_by('email', $email);
        $is_new = false;

        if (!$user) {
            $username = 'google_' . substr(md5($email . time()), 0, 10);
            $random_pwd = wp_generate_password(24, true, true);

            $user_id = wp_insert_user([
                'user_login'   => $username,
                'user_email'   => $email,
                'user_pass'    => $random_pwd,
                'first_name'   => $first_name,
                'last_name'    => $last_name,
                'display_name' => $display_name,
                'role'         => 'customer',
            ]);

            if (is_wp_error($user_id)) {
                wp_send_json_error([
                    'message' => __('Failed to initialize customer account: ', 'hookah-rental-core') . $user_id->get_error_message(),
                ]);
            }

            $user = get_user_by('id', $user_id);
            $is_new = true;
        } else {
            $user_id = $user->ID;
            if (!empty($first_name) && empty($user->first_name)) {
                update_user_meta($user_id, 'first_name', $first_name);
            }
            if (!empty($last_name) && empty($user->last_name)) {
                update_user_meta($user_id, 'last_name', $last_name);
            }
        }

        if (!empty($google_id)) {
            update_user_meta($user_id, 'bns_google_id', $google_id);
        }

        // Establish WordPress customer session
        wp_clear_auth_cookie();
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
        do_action('wp_login', $user->user_login, $user);

        // Synchronize WooCommerce Customer Session
        if (function_exists('WC') && WC()->session) {
            WC()->session->init();
            WC()->customer = new WC_Customer($user_id, true);
        }

        // Determine Redirect URL
        $final_redirect = wc_get_page_permalink('myaccount');
        if (!empty($redirect) && (strpos($redirect, home_url()) === 0 || strpos($redirect, '/') === 0)) {
            $final_redirect = $redirect;
        }

        wp_send_json_success([
            'message'     => $is_new ? __('Account created with Google! Welcome to BookMySmoke.', 'hookah-rental-core') : __('Welcome back! Signed in with Google.', 'hookah-rental-core'),
            'redirect'    => $final_redirect,
            'is_new_user' => $is_new,
            'user'        => [
                'id'    => $user_id,
                'name'  => $user->display_name ?: ($first_name . ' ' . $last_name),
                'email' => $user->user_email,
            ],
        ]);
    }

    /**
     * Verify Google ID Token against Google TokenInfo Endpoint
     */
    private function verify_google_id_token($id_token) {
        $endpoint = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($id_token);
        $response = wp_remote_get($endpoint, ['timeout' => 8]);

        if (is_wp_error($response)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (!empty($body['email']) && !empty($body['sub'])) {
            return $body;
        }

        return false;
    }

    /**
     * Protect WP-Admin: Redirect regular customers to /my-account/
     */
    public function restrict_admin_access() {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        if (is_user_logged_in() && !current_user_can('manage_options')) {
            wp_safe_redirect(wc_get_page_permalink('myaccount'));
            exit;
        }
    }

    /**
     * Suppress admin toolbar for regular customers
     */
    public function hide_admin_bar_for_customers() {
        if (is_user_logged_in() && !current_user_can('manage_options')) {
            show_admin_bar(false);
        }
    }

    /**
     * AJAX: Customer Logout
     */
    public function ajax_customer_logout() {
        wp_logout();
        wp_send_json_success([
            'message'  => __('Logged out successfully.', 'hookah-rental-core'),
            'redirect' => home_url(),
        ]);
    }
}
