<?php
/**
 * Customer Mobile Number + OTP Authentication System
 * Direct Integration with NestJS Backend Microservice & Redis
 * Exclusively Kolkata & India Phone Verification
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_OTP_Auth {

    /**
     * NestJS Backend API Base URL
     */
    private static function get_backend_url() {
        if (defined('BNS_BACKEND_INTERNAL_URL')) {
            return rtrim(BNS_BACKEND_INTERNAL_URL, '/');
        }
        // In Docker environment, WordPress communicates with NestJS via http://backend:3000
        return 'http://backend:3000';
    }

    public function __construct() {
        // AJAX Endpoints for OTP Authentication
        add_action('wp_ajax_nopriv_bns_send_otp', [$this, 'ajax_send_otp']);
        add_action('wp_ajax_bns_send_otp', [$this, 'ajax_send_otp']);

        add_action('wp_ajax_nopriv_bns_verify_otp', [$this, 'ajax_verify_otp']);
        add_action('wp_ajax_bns_verify_otp', [$this, 'ajax_verify_otp']);

        add_action('wp_ajax_bns_customer_logout', [$this, 'ajax_customer_logout']);

        // Customer Role Restriction (Protect /wp-admin/)
        add_action('admin_init', [$this, 'restrict_admin_access']);
        add_action('after_setup_theme', [$this, 'hide_admin_bar_for_customers']);

        // Ensure WooCommerce checkout pre-fills customer phone if logged in
        add_filter('woocommerce_checkout_get_value', [$this, 'prefill_checkout_phone'], 10, 2);
    }

    /**
     * Clean and validate Indian 10-digit mobile number
     */
    public static function clean_mobile_number($raw_phone) {
        $clean = preg_replace('/[^0-9]/', '', (string)$raw_phone);
        if (strlen($clean) === 12 && substr($clean, 0, 2) === '91') {
            $clean = substr($clean, 2);
        } elseif (strlen($clean) === 11 && substr($clean, 0, 1) === '0') {
            $clean = substr($clean, 1);
        }
        if (strlen($clean) === 10 && in_array($clean[0], ['6', '7', '8', '9'])) {
            return $clean;
        }
        return false;
    }

    /**
     * AJAX: Send 6-digit OTP via NestJS Backend API
     */
    public function ajax_send_otp() {
        $raw_phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $phone = self::clean_mobile_number($raw_phone);

        if (!$phone) {
            wp_send_json_error([
                'message' => __('Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.', 'hookah-rental-core'),
            ]);
        }

        $backend_url = self::get_backend_url() . '/api/auth/otp/send';

        $response = wp_remote_post($backend_url, [
            'timeout' => 10,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            'body'    => wp_json_encode([
                'phone' => $phone,
            ]),
        ]);

        if (is_wp_error($response)) {
            // Log communication issue and fallback to internal WP transient engine if backend is down
            error_log('[BookMySmoke Auth] NestJS backend request failed: ' . $response->get_error_message());
            return $this->fallback_send_otp($phone);
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body        = json_decode(wp_remote_retrieve_body($response), true);

        if ($status_code >= 200 && $status_code < 300 && !empty($body['success'])) {
            wp_send_json_success([
                'message'          => $body['message'] ?? sprintf(__('6-digit verification code sent to +91 %s', 'hookah-rental-core'), $phone),
                'masked_phone'     => $body['maskedPhone'] ?? ('+91 ' . substr($phone, 0, 5) . ' •••' . substr($phone, -2)),
                'phone'            => $phone,
                'cooldown_seconds' => $body['cooldownSeconds'] ?? 30,
                'expires_seconds'  => $body['expiresInSeconds'] ?? 300,
            ]);
        }

        // Handle error message returned by NestJS backend (Rate limit, cooldown, or bad input)
        $error_msg = __('Failed to send OTP. Please try again.', 'hookah-rental-core');
        if (!empty($body['error']['message'])) {
            $error_msg = is_array($body['error']['message']) ? implode(', ', $body['error']['message']) : $body['error']['message'];
        } elseif (!empty($body['message'])) {
            $error_msg = is_array($body['message']) ? implode(', ', $body['message']) : $body['message'];
        }

        wp_send_json_error([
            'message' => $error_msg,
        ]);
    }

    /**
     * AJAX: Verify OTP via NestJS Backend API & Establish Customer Session
     */
    public function ajax_verify_otp() {
        $raw_phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $raw_otp   = isset($_POST['otp']) ? sanitize_text_field($_POST['otp']) : '';
        $redirect  = isset($_POST['redirect']) ? esc_url_raw($_POST['redirect']) : '';

        $phone = self::clean_mobile_number($raw_phone);
        $otp   = preg_replace('/[^0-9]/', '', $raw_otp);

        if (!$phone) {
            wp_send_json_error([
                'message' => __('Invalid mobile number format.', 'hookah-rental-core'),
            ]);
        }

        if (strlen($otp) !== 6) {
            wp_send_json_error([
                'message' => __('Please enter a valid 6-digit OTP code.', 'hookah-rental-core'),
            ]);
        }

        $backend_url = self::get_backend_url() . '/api/auth/otp/verify';

        $response = wp_remote_post($backend_url, [
            'timeout' => 10,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            'body'    => wp_json_encode([
                'phone' => $phone,
                'otp'   => $otp,
            ]),
        ]);

        if (is_wp_error($response)) {
            error_log('[BookMySmoke Auth] NestJS backend verification failed: ' . $response->get_error_message());
            return $this->fallback_verify_otp($phone, $otp, $redirect);
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body        = json_decode(wp_remote_retrieve_body($response), true);

        if ($status_code >= 200 && $status_code < 300 && !empty($body['success'])) {
            $jwt_token   = $body['accessToken'] ?? '';
            $is_new_user = !empty($body['isNewUser']);

            // Find or create customer in WordPress
            $user = $this->find_user_by_phone($phone);
            if (!$user) {
                $user_id = $this->create_customer_user($phone);
                if (is_wp_error($user_id)) {
                    wp_send_json_error([
                        'message' => __('Failed to create customer account: ', 'hookah-rental-core') . $user_id->get_error_message(),
                    ]);
                }
                $user = get_user_by('id', $user_id);
                $is_new_user = true;
            } else {
                $user_id = $user->ID;
                update_user_meta($user_id, 'billing_phone', '+91 ' . $phone);
                update_user_meta($user_id, 'bns_phone', $phone);
                update_user_meta($user_id, 'bns_phone_verified', '1');
            }

            if (!empty($jwt_token)) {
                update_user_meta($user_id, 'bns_jwt_token', $jwt_token);
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

            $user_display_name = $user->first_name ?: ($user->display_name ?: 'Customer');

            wp_send_json_success([
                'message'     => $is_new_user ? __('Account created and signed in successfully!', 'hookah-rental-core') : __('Welcome back! Signed in successfully.', 'hookah-rental-core'),
                'redirect'    => $final_redirect,
                'is_new_user' => $is_new_user,
                'accessToken' => $jwt_token,
                'user'        => [
                    'id'    => $user_id,
                    'name'  => $user_display_name,
                    'phone' => '+91 ' . $phone,
                    'email' => $user->user_email,
                ],
            ]);
        }

        // Handle error from backend (Incorrect OTP, expired, lockout)
        $error_msg = __('Invalid OTP code. Please try again.', 'hookah-rental-core');
        if (!empty($body['error']['message'])) {
            $error_msg = is_array($body['error']['message']) ? implode(', ', $body['error']['message']) : $body['error']['message'];
        } elseif (!empty($body['message'])) {
            $error_msg = is_array($body['message']) ? implode(', ', $body['message']) : $body['message'];
        }

        wp_send_json_error([
            'message' => $error_msg,
        ]);
    }

    /**
     * Fallback OTP Dispatch (in case backend is disconnected)
     */
    private function fallback_send_otp($phone) {
        $cooldown_key = 'bns_otp_cd_' . $phone;
        if (get_transient($cooldown_key)) {
            wp_send_json_error([
                'message' => __('Please wait 30 seconds before requesting another OTP.', 'hookah-rental-core'),
            ]);
        }

        $otp = (string) wp_rand(100000, 999999);
        $otp_key = 'bns_otp_data_' . $phone;
        set_transient($otp_key, [
            'hash'       => wp_hash_password($otp),
            'attempts'   => 0,
            'created_at' => time(),
            'phone'      => $phone,
        ], 300);

        set_transient($cooldown_key, 1, 30);
        do_action('bns_send_sms_otp', $phone, $otp);
        error_log("[BookMySmoke Fallback OTP] Generated OTP for +91 {$phone}: {$otp}");

        $masked_phone = '+91 ' . substr($phone, 0, 5) . ' •••' . substr($phone, -2);
        wp_send_json_success([
            'message'          => sprintf(__('6-digit verification code sent to %s', 'hookah-rental-core'), $masked_phone),
            'masked_phone'     => $masked_phone,
            'phone'            => $phone,
            'cooldown_seconds' => 30,
            'expires_seconds'  => 300,
        ]);
    }

    /**
     * Fallback OTP Verification
     */
    private function fallback_verify_otp($phone, $otp, $redirect) {
        $otp_key  = 'bns_otp_data_' . $phone;
        $otp_data = get_transient($otp_key);

        if (!$otp_data || empty($otp_data['hash'])) {
            wp_send_json_error(['message' => __('OTP has expired or is invalid. Please request a new code.', 'hookah-rental-core')]);
        }

        if ($otp_data['attempts'] >= 5) {
            delete_transient($otp_key);
            wp_send_json_error(['message' => __('Too many incorrect attempts. Please request a new OTP.', 'hookah-rental-core')]);
        }

        if (!wp_check_password($otp, $otp_data['hash'])) {
            $otp_data['attempts']++;
            set_transient($otp_key, $otp_data, 300);
            $remaining = 5 - $otp_data['attempts'];
            wp_send_json_error(['message' => sprintf(__('Incorrect OTP code. %d attempts remaining.', 'hookah-rental-core'), $remaining)]);
        }

        delete_transient($otp_key);
        $user = $this->find_user_by_phone($phone);
        $is_new_user = false;

        if (!$user) {
            $user_id = $this->create_customer_user($phone);
            $user = get_user_by('id', $user_id);
            $is_new_user = true;
        } else {
            $user_id = $user->ID;
            update_user_meta($user_id, 'billing_phone', '+91 ' . $phone);
        }

        wp_clear_auth_cookie();
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);

        wp_send_json_success([
            'message'     => $is_new_user ? __('Account created and signed in successfully!', 'hookah-rental-core') : __('Welcome back!', 'hookah-rental-core'),
            'redirect'    => $redirect ?: wc_get_page_permalink('myaccount'),
            'is_new_user' => $is_new_user,
            'user'        => [
                'id'    => $user_id,
                'name'  => $user->display_name,
                'phone' => '+91 ' . $phone,
            ],
        ]);
    }

    /**
     * Look up user by phone number
     */
    private function find_user_by_phone($phone) {
        $users = get_users([
            'meta_key'   => 'bns_phone',
            'meta_value' => $phone,
            'number'     => 1,
            'fields'     => 'all',
        ]);

        if (!empty($users)) {
            return $users[0];
        }

        $users = get_users([
            'meta_key'   => 'billing_phone',
            'meta_value' => '+91 ' . $phone,
            'number'     => 1,
            'fields'     => 'all',
        ]);

        if (!empty($users)) {
            return $users[0];
        }

        $username = 'customer_' . $phone;
        $user = get_user_by('login', $username);
        if ($user) {
            return $user;
        }

        return false;
    }

    /**
     * Create new customer user with phone number
     */
    private function create_customer_user($phone) {
        $username   = 'customer_' . $phone;
        $email      = $phone . '@bookmysmoke.local';
        $random_pwd = wp_generate_password(24, true, true);

        $user_id = wp_insert_user([
            'user_login'   => $username,
            'user_email'   => $email,
            'user_pass'    => $random_pwd,
            'first_name'   => 'Customer',
            'last_name'    => substr($phone, -4),
            'display_name' => 'Customer (' . substr($phone, -4) . ')',
            'role'         => 'customer',
        ]);

        if (!is_wp_error($user_id)) {
            update_user_meta($user_id, 'bns_phone', $phone);
            update_user_meta($user_id, 'billing_phone', '+91 ' . $phone);
            update_user_meta($user_id, 'bns_phone_verified', '1');
            update_user_meta($user_id, 'billing_country', 'IN');
            update_user_meta($user_id, 'billing_city', 'Kolkata');
            update_user_meta($user_id, 'billing_state', 'WB');
        }

        return $user_id;
    }

    /**
     * Protect WP-Admin: Redirect non-admin users to /my-account/
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
     * Auto-prefill billing_phone on checkout for logged-in verified customers
     */
    public function prefill_checkout_phone($value, $input) {
        if ($input === 'billing_phone' && empty($value) && is_user_logged_in()) {
            $user_id = get_current_user_id();
            $phone = get_user_meta($user_id, 'billing_phone', true) ?: get_user_meta($user_id, 'bns_phone', true);
            if (!empty($phone)) {
                return (strpos($phone, '+91') === 0) ? $phone : ('+91 ' . $phone);
            }
        }
        return $value;
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
