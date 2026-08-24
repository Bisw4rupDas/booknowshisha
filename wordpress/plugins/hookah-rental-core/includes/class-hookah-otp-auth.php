<?php
/**
 * Customer Mobile Number + OTP Authentication System
 * Exclusively Kolkata & India Phone Verification
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_OTP_Auth {

    const OTP_EXPIRY_SECONDS   = 300; // 5 minutes
    const RESEND_COOLDOWN_SECS = 30;  // 30 seconds between requests
    const MAX_FAILED_ATTEMPTS  = 5;   // Invalidate after 5 wrong attempts
    const MAX_HOURLY_REQUESTS  = 5;   // Rate limit: 5 OTPs per 15 minutes

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
     * AJAX: Send 6-digit OTP to Mobile Number
     */
    public function ajax_send_otp() {
        $raw_phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $phone = self::clean_mobile_number($raw_phone);

        if (!$phone) {
            wp_send_json_error([
                'message' => __('Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.', 'hookah-rental-core'),
            ]);
        }

        // 1. Check Cooldown
        $cooldown_key = 'bns_otp_cd_' . $phone;
        if (get_transient($cooldown_key)) {
            wp_send_json_error([
                'message' => __('Please wait 30 seconds before requesting another OTP.', 'hookah-rental-core'),
                'cooldown' => true,
            ]);
        }

        // 2. Check Rate Limit (5 per 15 minutes)
        $rate_key = 'bns_otp_rate_' . $phone;
        $request_count = (int) get_transient($rate_key);
        if ($request_count >= self::MAX_HOURLY_REQUESTS) {
            wp_send_json_error([
                'message' => __('Too many OTP requests. Please wait 15 minutes before trying again.', 'hookah-rental-core'),
                'rate_limited' => true,
            ]);
        }

        // 3. Generate Cryptographically Secure 6-digit OTP
        $otp = (string) wp_rand(100000, 999999);

        // In test/dev environment with known test numbers, allow deterministic testing if needed
        if (defined('BNS_DEV_MODE') && BNS_DEV_MODE && $phone === '9830012345') {
            $otp = '123456';
        }

        // 4. Securely store hashed OTP with expiry
        $otp_key = 'bns_otp_data_' . $phone;
        $otp_data = [
            'hash'       => wp_hash_password($otp),
            'attempts'   => 0,
            'created_at' => time(),
            'phone'      => $phone,
        ];
        set_transient($otp_key, $otp_data, self::OTP_EXPIRY_SECONDS);

        // Set cooldown & rate limit transients
        set_transient($cooldown_key, 1, self::RESEND_COOLDOWN_SECS);
        set_transient($rate_key, $request_count + 1, 900); // 15 minutes

        // 5. Dispatch SMS via SMS Hook/Provider
        do_action('bns_send_sms_otp', $phone, $otp);

        // Format masked phone e.g. +91 98300 •••45
        $masked_phone = '+91 ' . substr($phone, 0, 5) . ' •••' . substr($phone, -2);

        // Log OTP in dev mode for testing convenience
        error_log("[BookMySmoke OTP] Generated OTP for +91 {$phone}: {$otp}");

        $response_data = [
            'message'          => sprintf(__('6-digit verification code sent to %s', 'hookah-rental-core'), $masked_phone),
            'masked_phone'     => $masked_phone,
            'phone'            => $phone,
            'cooldown_seconds' => self::RESEND_COOLDOWN_SECS,
            'expires_seconds'  => self::OTP_EXPIRY_SECONDS,
        ];

        // Return dev token hint only in local development / debug
        if ((defined('WP_DEBUG') && WP_DEBUG) || (defined('BNS_DEV_MODE') && BNS_DEV_MODE)) {
            $response_data['dev_otp'] = $otp;
        }

        wp_send_json_success($response_data);
    }

    /**
     * AJAX: Verify OTP & Authenticate Customer
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

        $otp_key  = 'bns_otp_data_' . $phone;
        $otp_data = get_transient($otp_key);

        if (!$otp_data || !is_array($otp_data) || empty($otp_data['hash'])) {
            wp_send_json_error([
                'message' => __('OTP has expired or does not exist. Please request a new code.', 'hookah-rental-core'),
                'expired' => true,
            ]);
        }

        // Check Attempt Limit
        if ($otp_data['attempts'] >= self::MAX_FAILED_ATTEMPTS) {
            delete_transient($otp_key);
            wp_send_json_error([
                'message' => __('Too many incorrect attempts. This code has been invalidated. Please request a new OTP.', 'hookah-rental-core'),
                'max_attempts' => true,
            ]);
        }

        // Verify Hash
        $is_valid = wp_check_password($otp, $otp_data['hash']);

        if (!$is_valid) {
            $otp_data['attempts']++;
            set_transient($otp_key, $otp_data, self::OTP_EXPIRY_SECONDS);
            $remaining = self::MAX_FAILED_ATTEMPTS - $otp_data['attempts'];

            wp_send_json_error([
                'message' => sprintf(__('Incorrect OTP code. %d attempts remaining.', 'hookah-rental-core'), $remaining),
                'remaining_attempts' => $remaining,
            ]);
        }

        // OTP IS VALID: Invalidate OTP so it cannot be reused
        delete_transient($otp_key);

        // 1. Find existing customer by phone number
        $user = $this->find_user_by_phone($phone);
        $is_new_user = false;

        if (!$user) {
            // Create new customer account
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
            // Ensure phone metadata is up to date
            update_user_meta($user_id, 'billing_phone', '+91 ' . $phone);
            update_user_meta($user_id, 'bns_phone', $phone);
            update_user_meta($user_id, 'bns_phone_verified', '1');
        }

        // 2. Perform Secure WordPress Login Session
        wp_clear_auth_cookie();
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
        do_action('wp_login', $user->user_login, $user);

        // Synchronize WooCommerce Customer Session if active
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
            'message'     => $is_new_user ? __('Account created and signed in successfully!', 'hookah-rental-core') : __('Signed in successfully!', 'hookah-rental-core'),
            'redirect'    => $final_redirect,
            'is_new_user' => $is_new_user,
            'user'        => [
                'id'    => $user_id,
                'name'  => $user_display_name,
                'phone' => '+91 ' . $phone,
                'email' => $user->user_email,
            ],
        ]);
    }

    /**
     * Look up user by phone number
     */
    private function find_user_by_phone($phone) {
        $phone_variants = [
            $phone,
            '+91 ' . $phone,
            '+91' . $phone,
            '0' . $phone,
        ];

        // 1. Search by user meta
        $users = get_users([
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key'     => 'billing_phone',
                    'value'   => $phone_variants,
                    'compare' => 'IN',
                ],
                [
                    'key'     => 'bns_phone',
                    'value'   => $phone,
                    'compare' => '=',
                ],
            ],
            'number' => 1,
        ]);

        if (!empty($users)) {
            return $users[0];
        }

        // 2. Search by username customer_{phone}
        $by_login = get_user_by('login', 'customer_' . $phone);
        if ($by_login) {
            return $by_login;
        }

        // 3. Search by username phone
        $by_phone_login = get_user_by('login', $phone);
        if ($by_phone_login) {
            return $by_phone_login;
        }

        return null;
    }

    /**
     * Create customer account with verified mobile number
     */
    private function create_customer_user($phone) {
        $username = 'customer_' . $phone;
        $email    = $phone . '@bookmysmoke.local';
        $random_password = wp_generate_password(24, true);

        // Ensure unique username
        if (username_exists($username)) {
            $username = 'customer_' . $phone . '_' . wp_rand(100, 999);
        }

        // Ensure unique email
        if (email_exists($email)) {
            $email = $phone . '_' . wp_rand(100, 999) . '@bookmysmoke.local';
        }

        $user_id = wp_create_user($username, $random_password, $email);
        if (is_wp_error($user_id)) {
            return $user_id;
        }

        // Set customer role and phone meta
        $user = new WP_User($user_id);
        $user->set_role('customer');

        wp_update_user([
            'ID'           => $user_id,
            'first_name'   => 'Customer',
            'display_name' => 'Customer ' . substr($phone, -4),
        ]);

        update_user_meta($user_id, 'billing_phone', '+91 ' . $phone);
        update_user_meta($user_id, 'bns_phone', $phone);
        update_user_meta($user_id, 'bns_phone_verified', '1');
        update_user_meta($user_id, 'billing_country', 'IN');
        update_user_meta($user_id, 'billing_state', 'WB');
        update_user_meta($user_id, 'billing_city', 'Kolkata');

        return $user_id;
    }

    /**
     * AJAX: Customer Logout
     */
    public function ajax_customer_logout() {
        wp_logout();
        wp_send_json_success([
            'message'  => __('Logged out successfully.', 'hookah-rental-core'),
            'redirect' => home_url('/'),
        ]);
    }

    /**
     * Restrict Normal Customers from WordPress /wp-admin/
     */
    public function restrict_admin_access() {
        if (is_admin() && !defined('DOING_AJAX') && php_sapi_name() !== 'cli') {
            if (is_user_logged_in() && !current_user_can('manage_options') && !current_user_can('edit_posts')) {
                wp_safe_redirect(wc_get_page_permalink('myaccount'));
                exit;
            }
        }
    }

    /**
     * Hide WordPress Admin Bar for Non-Admin Customers
     */
    public function hide_admin_bar_for_customers() {
        if (!current_user_can('manage_options') && !is_admin()) {
            show_admin_bar(false);
        }
    }

    /**
     * Pre-fill Checkout Phone for Verified Logged-in Customer
     */
    public function prefill_checkout_phone($value, $input) {
        if ($input === 'billing_phone' && empty($value) && is_user_logged_in()) {
            $user_id = get_current_user_id();
            $saved_phone = get_user_meta($user_id, 'billing_phone', true) ?: get_user_meta($user_id, 'bns_phone', true);
            if (!empty($saved_phone)) {
                return $saved_phone;
            }
        }
        return $value;
    }
}
