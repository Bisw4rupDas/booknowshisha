<?php
/**
 * WooCommerce Custom Hooks and Product Integration
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_Woo_Integration {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function init_hooks() {
        // Enqueue public assets on WooCommerce product/cart/checkout pages
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_assets']);

        // Single Product Rental Options
        add_action('woocommerce_before_add_to_cart_button', [$this, 'render_product_rental_options']);

        // Cart Item Data & Validation
        add_filter('woocommerce_add_to_cart_validation', [$this, 'validate_add_to_cart_rental_data'], 10, 3);
        add_filter('woocommerce_add_cart_item_data', [$this, 'add_rental_data_to_cart_item'], 10, 3);
        add_filter('woocommerce_get_item_data', [$this, 'display_rental_data_in_cart'], 10, 2);

        // Order Line Item Persistence
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'save_rental_data_to_order_items'], 10, 4);
    }

    public function enqueue_public_assets() {
        wp_enqueue_style(
            $this->plugin_name . '-public',
            HOOKAH_RENTAL_CORE_URL . 'assets/css/rental-public.css',
            [],
            $this->version,
            'all'
        );

        wp_enqueue_script(
            $this->plugin_name . '-public',
            HOOKAH_RENTAL_CORE_URL . 'assets/js/rental-public.js',
            ['jquery'],
            $this->version,
            true
        );

        wp_localize_script(
            $this->plugin_name . '-public',
            'bnsRentalData',
            [
                'ajaxUrl'   => admin_url('admin-ajax.php'),
                'nonce'     => wp_create_nonce('bns_rental_nonce'),
                'apiUrl'    => get_option('hookah_rental_api_url', 'http://localhost:3000/api'),
            ]
        );
    }

    /**
     * Render the rental configuration widget on single product page
     */
    public function render_product_rental_options() {
        include HOOKAH_RENTAL_CORE_PATH . 'templates/rental-product-options.php';
    }

    /**
     * Validate rental fields before adding product to cart
     */
    public function validate_add_to_cart_rental_data($passed, $product_id, $quantity) {
        if (isset($_POST['bns_rental_date']) && empty($_POST['bns_rental_date'])) {
            wc_add_notice(__('Please select a valid rental start date.', 'hookah-rental-core'), 'error');
            return false;
        }

        if (isset($_POST['bns_postal_code']) && (empty($_POST['bns_postal_code']) || strlen(trim($_POST['bns_postal_code'])) !== 6)) {
            wc_add_notice(__('Please enter a valid 6-digit delivery postal PIN code.', 'hookah-rental-core'), 'error');
            return false;
        }

        if (isset($_POST['bns_delivery_slot']) && empty($_POST['bns_delivery_slot'])) {
            wc_add_notice(__('Please select a preferred delivery slot.', 'hookah-rental-core'), 'error');
            return false;
        }

        return $passed;
    }

    /**
     * Store custom rental metadata in WooCommerce cart item
     */
    public function add_rental_data_to_cart_item($cart_item_data, $product_id, $variation_id) {
        if (!empty($_POST['bns_rental_date'])) {
            $cart_item_data['bns_rental'] = [
                'rental_date'   => sanitize_text_field($_POST['bns_rental_date']),
                'duration'      => isset($_POST['bns_duration']) ? intval($_POST['bns_duration']) : 24,
                'postal_code'   => isset($_POST['bns_postal_code']) ? sanitize_text_field($_POST['bns_postal_code']) : '',
                'delivery_slot' => isset($_POST['bns_delivery_slot']) ? sanitize_text_field($_POST['bns_delivery_slot']) : '',
                'flavours'      => isset($_POST['bns_flavours']) && is_array($_POST['bns_flavours']) 
                    ? array_map('sanitize_text_field', $_POST['bns_flavours']) 
                    : ['blueberry-mint', 'love-66'],
            ];
            $cart_item_data['unique_key'] = md5(microtime() . rand());
        }
        return $cart_item_data;
    }

    /**
     * Display rental metadata in WooCommerce cart and checkout tables
     */
    public function display_rental_data_in_cart($item_data, $cart_item) {
        if (!empty($cart_item['bns_rental'])) {
            $rental = $cart_item['bns_rental'];

            $item_data[] = [
                'key'   => __('Rental Date', 'hookah-rental-core'),
                'value' => esc_html($rental['rental_date']),
            ];

            $item_data[] = [
                'key'   => __('Duration', 'hookah-rental-core'),
                'value' => esc_html($rental['duration'] . ' Hours'),
            ];

            if (!empty($rental['delivery_slot'])) {
                $item_data[] = [
                    'key'   => __('Delivery Window', 'hookah-rental-core'),
                    'value' => esc_html($rental['delivery_slot']),
                ];
            }

            if (!empty($rental['postal_code'])) {
                $item_data[] = [
                    'key'   => __('Delivery PIN', 'hookah-rental-core'),
                    'value' => esc_html($rental['postal_code']),
                ];
            }

            if (!empty($rental['flavours'])) {
                $item_data[] = [
                    'key'   => __('Flavours', 'hookah-rental-core'),
                    'value' => esc_html(implode(', ', $rental['flavours'])),
                ];
            }
        }
        return $item_data;
    }

    /**
     * Persist rental metadata into WooCommerce Order Line Items
     */
    public function save_rental_data_to_order_items($item, $cart_item_key, $values, $order) {
        if (!empty($values['bns_rental'])) {
            $rental = $values['bns_rental'];
            $item->add_meta_data('_bns_rental_date', $rental['rental_date'], true);
            $item->add_meta_data('_bns_duration', $rental['duration'], true);
            $item->add_meta_data('_bns_postal_code', $rental['postal_code'], true);
            $item->add_meta_data('_bns_delivery_slot', $rental['delivery_slot'], true);
            $item->add_meta_data('_bns_flavours', maybe_serialize($rental['flavours']), true);
        }
    }
}

