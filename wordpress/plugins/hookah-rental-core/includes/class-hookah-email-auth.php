<?php
/**
 * Customer Email Authentication System (Login, Sign-Up, Forgot Password)
 * Exclusively Email/Password Authentication for BookNowShisha / ShishaRent
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_Email_Auth {
    /**
     * Send clean JSON response without premature output or headers issues
     */
    private function send_clean_json($success, $data, $status_code = null) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8');
            header('X-Content-Type-Options: nosniff');
            if ($status_code) {
                status_header($status_code);
            }
        }
        $message = '';
        if (is_array($data) && isset($data['message'])) {
            $message = $data['message'];
        } elseif (is_string($data)) {
            $message = $data;
            $data = ['message' => $message];
        }
        $response = [
            'success' => (bool)$success,
            'message' => $message,
            'data'    => $data,
        ];
        echo wp_json_encode($response);
        if (function_exists('wp_die')) {
            wp_die('', '', ['response' => null]);
        } else {
            exit;
        }
    }


    public function __construct() {
        // AJAX Endpoints for Customer Email Login
        add_action('wp_ajax_nopriv_bns_email_login', [$this, 'ajax_email_login']);
        add_action('wp_ajax_bns_email_login', [$this, 'ajax_email_login']);

        // AJAX Endpoints for Customer Email Registration / Sign-Up
        add_action('wp_ajax_nopriv_bns_email_register', [$this, 'ajax_email_register']);
        add_action('wp_ajax_bns_email_register', [$this, 'ajax_email_register']);

        // AJAX Endpoints for Password Reset / Forgot Password
        add_action('wp_ajax_nopriv_bns_forgot_password', [$this, 'ajax_forgot_password']);
        add_action('wp_ajax_bns_forgot_password', [$this, 'ajax_forgot_password']);

        // AJAX Endpoint for Customer Logout
        add_action('wp_ajax_bns_customer_logout', [$this, 'ajax_customer_logout']);
        add_action('wp_ajax_nopriv_bns_customer_logout', [$this, 'ajax_customer_logout']);

        // Standard POST Handler Fallback (If JavaScript is disabled or form submitted traditionally)
        add_action('init', [$this, 'handle_traditional_post_auth']);

        // Customer Role Restriction (Protect /wp-admin/)
        add_action('admin_init', [$this, 'restrict_admin_access']);
        add_action('after_setup_theme', [$this, 'hide_admin_bar_for_customers']);
    }

    /**
     * AJAX: Handle Customer Email Login
     */
    public function ajax_email_login() {
        // CSRF Check (Permissive if nonce not passed, strict if passed)
        if (!empty($_POST['security']) && !wp_verify_nonce($_POST['security'], 'bns_auth_nonce')) {
            $this->send_clean_json(false, [
                'message' => __('Security token expired. Please refresh the page and try again.', 'hookah-rental-core'),
            ]);
        }

        $login_input = isset($_POST['email']) ? trim($_POST['email']) : (isset($_POST['user_login']) ? trim($_POST['user_login']) : '');
        $password    = isset($_POST['password']) ? trim($_POST['password']) : '';
        $remember    = !empty($_POST['remember']);
        $redirect    = isset($_POST['redirect']) ? esc_url_raw($_POST['redirect']) : '';

        if (empty($login_input)) {
            $this->send_clean_json(false, [
                'message' => __('Please enter your email address.', 'hookah-rental-core'),
            ]);
        }

        if (empty($password)) {
            $this->send_clean_json(false, [
                'message' => __('Please enter your password.', 'hookah-rental-core'),
            ]);
        }

        // If email was entered, lookup corresponding WordPress username
        $user_to_auth = $login_input;
        if (is_email($login_input)) {
            $user_obj = get_user_by('email', strtolower($login_input));
            if ($user_obj) {
                $user_to_auth = $user_obj->user_login;
            }
        }

        // Authenticate credentials against WordPress user store
        $user = wp_authenticate($user_to_auth, $password);

        if (is_wp_error($user)) {
            $error_code = $user->get_error_code();
            $error_message = __('Incorrect email or password. Please try again.', 'hookah-rental-core');

            if ($error_code === 'empty_username' || $error_code === 'empty_password') {
                $error_message = __('Please provide both email address and password.', 'hookah-rental-core');
            }

            $this->send_clean_json(false, [
                'message' => $error_message,
            ]);
        }

        // Check if user is active
        if (isset($user->user_status) && $user->user_status != 0) {
            $this->send_clean_json(false, [
                'message' => __('Your account has been deactivated. Please contact customer support.', 'hookah-rental-core'),
            ]);
        }

        // Establish WordPress customer session
        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, $remember);
        do_action('wp_login', $user->user_login, $user);

        // Synchronize WooCommerce Customer Session
        if (function_exists('WC') && WC()->session) {
            WC()->session->init();
            WC()->customer = new WC_Customer($user->ID, true);
        }

        // Determine Redirect URL
        $final_redirect = wc_get_page_permalink('myaccount');
        if (!empty($redirect) && (strpos($redirect, home_url()) === 0 || strpos($redirect, '/') === 0)) {
            $final_redirect = $redirect;
        }

        $display_name = $user->display_name ?: ($user->first_name ? $user->first_name . ' ' . $user->last_name : $user->user_login);

        $this->send_clean_json(true, [
            'message'  => sprintf(__('Welcome back, %s!', 'hookah-rental-core'), esc_html($display_name)),
            'redirect' => $final_redirect,
            'user'     => [
                'id'    => $user->ID,
                'name'  => $display_name,
                'email' => $user->user_email,
            ],
        ]);
    }

    /**
     * AJAX: Handle Customer Email Sign-Up / Registration
     */
    public function ajax_email_register() {
        if (!empty($_POST['security']) && !wp_verify_nonce($_POST['security'], 'bns_auth_nonce')) {
            $this->send_clean_json(false, [
                'message' => __('Security token expired. Please refresh the page and try again.', 'hookah-rental-core'),
            ]);
        }

        $name             = isset($_POST['name']) ? sanitize_text_field(trim($_POST['name'])) : '';
        $email            = isset($_POST['email']) ? sanitize_email(trim($_POST['email'])) : '';
        $password         = isset($_POST['password']) ? trim($_POST['password']) : '';
        $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
        $redirect         = isset($_POST['redirect']) ? esc_url_raw($_POST['redirect']) : '';

        // Validation: Required fields
        if (empty($name)) {
            $this->send_clean_json(false, [
                'message' => __('Please enter your full name.', 'hookah-rental-core'),
            ]);
        }

        if (empty($email) || !is_email($email)) {
            $this->send_clean_json(false, [
                'message' => __('Please enter a valid email address.', 'hookah-rental-core'),
            ]);
        }

        // AIRTIGHT DUPLICATE EMAIL PREVENTION
        $normalized_email = strtolower($email);
        if (email_exists($normalized_email) || get_user_by('email', $normalized_email) || username_exists($normalized_email)) {
            $this->send_clean_json(false, [
                'message' => __('An account with this email already exists. Please sign in.', 'hookah-rental-core'),
            ]);
        }

        if (empty($password)) {
            $this->send_clean_json(false, [
                'message' => __('Please choose a password.', 'hookah-rental-core'),
            ]);
        }

        if (strlen($password) < 8) {
            $this->send_clean_json(false, [
                'message' => __('Password must be at least 8 characters long.', 'hookah-rental-core'),
            ]);
        }

        if ($password !== $confirm_password) {
            $this->send_clean_json(false, [
                'message' => __('Passwords do not match. Please re-enter your confirm password.', 'hookah-rental-core'),
            ]);
        }

        // Split Full Name into First & Last Name
        $name_parts   = explode(' ', $name, 2);
        $first_name   = sanitize_text_field($name_parts[0]);
        $last_name    = isset($name_parts[1]) ? sanitize_text_field($name_parts[1]) : '';
        $display_name = $name;

        // Generate clean unique username from email
        $base_username = sanitize_user(strstr($normalized_email, '@', true), true);
        if (empty($base_username)) {
            $base_username = 'customer_' . substr(md5($normalized_email . microtime()), 0, 8);
        }
        $username = $base_username;
        $counter = 1;
        while (username_exists($username) || email_exists($username)) {
            $username = $base_username . $counter;
            $counter++;
        }

        // Create user via WooCommerce helper or native wp_insert_user
        if (function_exists('wc_create_new_customer')) {
            $user_id = wc_create_new_customer($normalized_email, $username, $password, [
                'first_name'   => $first_name,
                'last_name'    => $last_name,
                'display_name' => $display_name,
                'role'         => 'customer',
            ]);
        } else {
            $user_id = wp_insert_user([
                'user_login'   => $username,
                'user_email'   => $normalized_email,
                'user_pass'    => $password,
                'first_name'   => $first_name,
                'last_name'    => $last_name,
                'display_name' => $display_name,
                'role'         => 'customer',
            ]);
        }

        if (is_wp_error($user_id)) {
            error_log('[ShishaRent Registration Error] ' . $user_id->get_error_message());
            $this->send_clean_json(false, [
                'message' => $user_id->get_error_message(),
                'code'    => $user_id->get_error_code(),
            ]);
        }

        update_user_meta($user_id, 'first_name', $first_name);
        update_user_meta($user_id, 'last_name', $last_name);
        update_user_meta($user_id, 'billing_first_name', $first_name);
        update_user_meta($user_id, 'billing_last_name', $last_name);
        update_user_meta($user_id, 'billing_email', $normalized_email);

        $user = get_user_by('id', $user_id);

        // Establish WordPress customer session immediately
        try {
            wp_clear_auth_cookie();
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id, true);
            if ($user) {
                do_action('wp_login', $user->user_login, $user);
            }
        } catch (\Throwable $e) {
            error_log('[ShishaRent Auth] Login session exception: ' . $e->getMessage());
        }

        // Fire WooCommerce Customer Created Hook safely
        try {
            if (function_exists('wc_get_page_permalink')) {
                do_action('woocommerce_created_customer', $user_id, [
                    'user_email' => $normalized_email,
                    'user_pass'  => $password,
                ], false);
            }
        } catch (\Throwable $e) {
            error_log('[ShishaRent Auth] WC hook exception: ' . $e->getMessage());
        }

        // Synchronize WooCommerce Customer Session
        try {
            if (function_exists('WC') && WC()->session) {
                WC()->session->init();
                WC()->customer = new WC_Customer($user_id, true);
            }
        } catch (\Throwable $e) {
            error_log('[ShishaRent Auth] WC customer session exception: ' . $e->getMessage());
        }

        // Determine Redirect URL
        $final_redirect = wc_get_page_permalink('myaccount');
        if (!empty($redirect) && (strpos($redirect, home_url()) === 0 || strpos($redirect, '/') === 0)) {
            $final_redirect = $redirect;
        }

        $this->send_clean_json(true, [
            'message'     => __('Account created successfully! Welcome to ShishaRent.', 'hookah-rental-core'),
            'redirect'    => $final_redirect,
            'is_new_user' => true,
            'user'        => [
                'id'    => $user_id,
                'name'  => $display_name,
                'email' => $normalized_email,
            ],
        ]);
    }

    /**
     * AJAX: Handle Forgot Password Request
     */
    public function ajax_forgot_password() {
        if (!empty($_POST['security']) && !wp_verify_nonce($_POST['security'], 'bns_auth_nonce')) {
            $this->send_clean_json(false, [
                'message' => __('Security token expired. Please refresh the page and try again.', 'hookah-rental-core'),
            ]);
        }

        $email = isset($_POST['email']) ? sanitize_email(trim($_POST['email'])) : '';

        if (empty($email) || !is_email($email)) {
            $this->send_clean_json(false, [
                'message' => __('Please enter a valid email address.', 'hookah-rental-core'),
            ]);
        }

        $user = get_user_by('email', strtolower($email));
        if (!$user) {
            $user = get_user_by('login', $email);
        }

        if ($user) {
            $allow = apply_filters('allow_password_reset', true, $user->ID);
            if ($allow) {
                $key = get_password_reset_key($user);
                if (!is_wp_error($key)) {
                    $site_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
                    $reset_url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login');
                    if (function_exists('wc_get_endpoint_url')) {
                        $reset_url = add_query_arg([
                            'key' => $key,
                            'id'  => $user->ID,
                        ], wc_get_endpoint_url('lost-password', '', wc_get_page_permalink('myaccount')));
                    }

                    $message = __('Someone has requested a password reset for your ShishaRent customer account:', 'hookah-rental-core') . "\r\n\r\n";
                    $message .= sprintf(__('Site Name: %s', 'hookah-rental-core'), $site_name) . "\r\n";
                    $message .= sprintf(__('Account Email: %s', 'hookah-rental-core'), $user->user_email) . "\r\n\r\n";
                    $message .= __('If you did not make this request, you can safely ignore this email.', 'hookah-rental-core') . "\r\n\r\n";
                    $message .= __('To reset your password, visit the following link:', 'hookah-rental-core') . "\r\n\r\n";
                    $message .= $reset_url . "\r\n\r\n";
                    $message .= __('This link will remain active for 24 hours.', 'hookah-rental-core') . "\r\n";

                    $title = sprintf(__('[%s] Password Reset Request', 'hookah-rental-core'), $site_name);
                    $headers = ['Content-Type: text/plain; charset=UTF-8'];
                    wp_mail($user->user_email, wp_specialchars_decode($title), $message, $headers);
                }
            }
        }

        // Generic response for security
        $this->send_clean_json(true, [
            'message' => __('If that email is registered, a password reset link has been sent to your inbox. Please check your inbox and spam folder.', 'hookah-rental-core'),
        ]);
    }

    /**
     * Fallback for standard HTTP POST requests (if JS disabled)
     */
    public function handle_traditional_post_auth() {
        if (isset($_POST['bns_traditional_auth_action'])) {
            $action = sanitize_text_field($_POST['bns_traditional_auth_action']);
            if ($action === 'login') {
                $this->ajax_email_login();
            } elseif ($action === 'register') {
                $this->ajax_email_register();
            }
        }
    }

    /**
     * AJAX: Customer Logout
     */
    public function ajax_customer_logout() {
        wp_logout();
        $this->send_clean_json(true, [
            'message'  => __('Logged out successfully.', 'hookah-rental-core'),
            'redirect' => home_url(),
        ]);
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
}
