<?php
/**
 * NestJS Backend API Client
 *
 * Handles authenticated communication between WordPress and the NestJS backend.
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_API_Client {
    private $base_url;
    private $api_key;

    public function __construct() {
        $this->base_url = rtrim(get_option('hookah_rental_api_url', 'http://backend:3000/api'), '/');
        $this->api_key = get_option('hookah_rental_shared_secret', '');
    }

    /**
     * Perform HTTP request to NestJS Backend
     */
    public function request($endpoint, $method = 'GET', $body = [], $headers = []) {
        $url = $this->base_url . '/' . ltrim($endpoint, '/');

        $default_headers = [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'X-Core-Secret' => $this->api_key,
        ];

        $args = [
            'method'  => $method,
            'headers' => array_merge($default_headers, $headers),
            'timeout' => 15,
        ];

        if (!empty($body) && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
            $args['body'] = wp_json_encode($body);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'error'   => $response->get_error_message(),
            ];
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = json_decode(wp_remote_retrieve_body($response), true);

        return [
            'success' => ($response_code >= 200 && $response_code < 300),
            'status'  => $response_code,
            'data'    => $response_body,
        ];
    }
}
