<?php
/**
 * Availability Checker
 *
 * Checks delivery slot and hookah inventory availability via NestJS API.
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_Availability_Checker {
    private $plugin_name;
    private $version;
    private $api_client;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->api_client = new Hookah_API_Client();
    }

    public function init_hooks() {
        add_action('wp_ajax_bns_check_availability', [$this, 'ajax_check_availability']);
        add_action('wp_ajax_nopriv_bns_check_availability', [$this, 'ajax_check_availability']);
    }

    public function ajax_check_availability() {
        check_ajax_referer('bns_rental_nonce', 'nonce');

        $postal_code = sanitize_text_field($_POST['postal_code'] ?? '');
        $date = sanitize_text_field($_POST['date'] ?? '');

        if (empty($postal_code)) {
            wp_send_json_error(['message' => __('Postal Code is required.', 'hookah-rental-core')]);
        }

        // 1. Evaluate local authoritative 3-District serviceability rule
        $local_check = Hookah_Serviceability::check_pin_serviceability($postal_code);
        if (!$local_check['deliverable']) {
            wp_send_json_success([
                'serviceable'      => false,
                'deliverable'      => false,
                'postalCode'       => $postal_code,
                'pin'              => $postal_code,
                'district'         => $local_check['district'],
                'state'            => $local_check['state'],
                'allowedDistricts' => Hookah_Serviceability::ALLOWED_DISTRICTS,
                'message'          => $local_check['message'],
            ]);
            return;
        }

        // 2. Call NestJS API check-zone endpoint
        $response = $this->api_client->request('delivery/check-zone', 'POST', [
            'postalCode' => $postal_code,
        ]);

        if ($response['success'] && !empty($response['data'])) {
            $data = $response['data'];
            wp_send_json_success($data);
        } else {
            // If backend is unreachable, use local serviceability data
            wp_send_json_success($local_check);
        }
    }
}
