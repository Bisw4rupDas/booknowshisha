<?php
/**
 * WooCommerce Checkout Integration for Hookah Rentals
 *
 * Adds delivery slot, rental date, and age-verification checkout requirements.
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class Hookah_Checkout_Fields {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function init_hooks() {
        add_action('woocommerce_after_order_notes', [$this, 'add_rental_checkout_fields']);
        add_action('woocommerce_checkout_process', [$this, 'validate_rental_checkout_fields']);
        add_action('woocommerce_checkout_update_order_meta', [$this, 'save_rental_order_meta']);
        add_action('woocommerce_checkout_order_processed', [$this, 'sync_order_with_nestjs_engine'], 10, 3);
        add_action('woocommerce_thankyou', [$this, 'render_thankyou_rental_instructions'], 20);
    }

    public function add_rental_checkout_fields($checkout) {
        if (did_action('bns_rental_timing_rendered')) {
            return;
        }
        echo '<div id="bns_rental_checkout_fields" class="bns-checkout-section">';
        echo '<h3>' . esc_html__('Rental Delivery & Verification', 'hookah-rental-core') . '</h3>';

        woocommerce_form_field('bns_rental_date', [
            'type'        => 'date',
            'class'       => ['form-row-wide'],
            'label'       => __('Preferred Delivery Date', 'hookah-rental-core'),
            'required'    => false,
        ], $checkout->get_value('bns_rental_date'));

        woocommerce_form_field('bns_delivery_slot', [
            'type'        => 'select',
            'class'       => ['form-row-wide'],
            'label'       => __('Delivery Time Slot', 'hookah-rental-core'),
            'options'     => [
                ''            => __('Select a slot...', 'hookah-rental-core'),
                '14:00-16:00' => '2:00 PM - 4:00 PM (Afternoon Express)',
                '16:00-18:00' => '4:00 PM - 6:00 PM (Evening Sunset)',
                '18:00-20:00' => '6:00 PM - 8:00 PM (Prime Evening)',
                '20:00-22:00' => '8:00 PM - 10:00 PM (Late Night Session)',
            ],
            'required'    => false,
        ], $checkout->get_value('bns_delivery_slot'));

        woocommerce_form_field('bns_age_verification', [
            'type'        => 'checkbox',
            'class'       => ['form-row-wide'],
            'label'       => __('I confirm I am 21 years of age or older and agree to present Government ID upon delivery.', 'hookah-rental-core'),
            'required'    => true,
        ], $checkout->get_value('bns_age_verification'));

        echo '</div>';
    }

    public function validate_rental_checkout_fields() {
        // 1. Mandatory 21+ Age Verification Checkbox
        if (empty($_POST['bns_age_verification'])) {
            wc_add_notice(__('You must verify that you are at least 21 years of age to rent a hookah.', 'hookah-rental-core'), 'error');
        }

        // 2. Mandatory Indian Mobile Number Validation
        $raw_phone = !empty($_POST['billing_phone']) ? sanitize_text_field($_POST['billing_phone']) : '';
        $clean_phone = preg_replace('/[^0-9]/', '', $raw_phone);
        if (strlen($clean_phone) === 12 && substr($clean_phone, 0, 2) === '91') {
            $clean_phone = substr($clean_phone, 2);
        } elseif (strlen($clean_phone) === 11 && substr($clean_phone, 0, 1) === '0') {
            $clean_phone = substr($clean_phone, 1);
        }

        if (empty($clean_phone)) {
            wc_add_notice(__('Mobile Number is required. Please enter your 10-digit Indian mobile number.', 'hookah-rental-core'), 'error');
        } elseif (strlen($clean_phone) !== 10 || !in_array($clean_phone[0], ['6', '7', '8', '9'])) {
            wc_add_notice(__('Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9 (e.g. 98300 12345).', 'hookah-rental-core'), 'error');
        }

        // 3. Validate Serviceability against the strict 3-District rule (Kolkata, North 24 Parganas, South 24 Parganas)
        $postcode = '';
        if (!empty($_POST['ship_to_different_address']) && !empty($_POST['shipping_postcode'])) {
            $postcode = sanitize_text_field($_POST['shipping_postcode']);
        } elseif (!empty($_POST['billing_postcode'])) {
            $postcode = sanitize_text_field($_POST['billing_postcode']);
        } elseif (!empty($_POST['bns_postal_code'])) {
            $postcode = sanitize_text_field($_POST['bns_postal_code']);
        }

        if (!empty($postcode)) {
            $serviceability = Hookah_Serviceability::check_pin_serviceability($postcode);
            if (!$serviceability['deliverable']) {
                wc_add_notice(
                    sprintf(
                        __('✕ DELIVERY NOT AVAILABLE: Sorry, BookMySmoke currently delivers only within Kolkata, North 24 Parganas and South 24 Parganas. (Entered PIN: %s)', 'hookah-rental-core'),
                        esc_html($postcode)
                    ),
                    'error'
                );
            }
        }
    }

    public function save_rental_order_meta($order_id) {
        if (!empty($_POST['bns_rental_date'])) {
            update_post_meta($order_id, '_bns_rental_date', sanitize_text_field($_POST['bns_rental_date']));
        }
        if (!empty($_POST['bns_delivery_slot'])) {
            update_post_meta($order_id, '_bns_delivery_slot', sanitize_text_field($_POST['bns_delivery_slot']));
        }
        if (!empty($_POST['bns_age_verification'])) {
            update_post_meta($order_id, '_bns_age_verified', 'yes');
        }

        // Ensure normalized Indian phone number is stored on order and billing meta
        if (!empty($_POST['billing_phone'])) {
            $raw_phone = sanitize_text_field($_POST['billing_phone']);
            $clean_phone = preg_replace('/[^0-9]/', '', $raw_phone);
            if (strlen($clean_phone) === 12 && substr($clean_phone, 0, 2) === '91') {
                $clean_phone = substr($clean_phone, 2);
            } elseif (strlen($clean_phone) === 11 && substr($clean_phone, 0, 1) === '0') {
                $clean_phone = substr($clean_phone, 1);
            }
            if (strlen($clean_phone) === 10) {
                $formatted_phone = '+91 ' . $clean_phone;
                update_post_meta($order_id, '_billing_phone', $formatted_phone);
                update_post_meta($order_id, '_bns_customer_phone', $formatted_phone);
            }
        }

        $order = wc_get_order($order_id);
        if ($order) {
            $postcode = $order->get_shipping_postcode() ?: $order->get_billing_postcode();
            if ($postcode) {
                $serviceability = Hookah_Serviceability::check_pin_serviceability($postcode);
                update_post_meta($order_id, '_bns_resolved_district', $serviceability['district'] ?? 'Unserviceable Area');
                update_post_meta($order_id, '_bns_resolved_area', $serviceability['area'] ?? '');
            }
        }
    }

    /**
     * Bridge WooCommerce Order with NestJS Backend Rental Engine (Idempotent)
     */
    public function sync_order_with_nestjs_engine($order_id, $posted_data, $order) {
        // Check if order already has an assigned booking ID (prevent duplicate booking on retry)
        $existing_booking_id = get_post_meta($order_id, '_bns_booking_id', true);
        if (!empty($existing_booking_id)) {
            return;
        }

        $api_client = new Hookah_API_Client();
        $postal_code = $order->get_shipping_postcode() ?: $order->get_billing_postcode();
        $delivery_address = trim($order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2() . ', ' . $order->get_shipping_city());
        $rental_date = get_post_meta($order_id, '_bns_rental_date', true) ?: date('Y-m-d');
        $delivery_slot = get_post_meta($order_id, '_bns_delivery_slot', true) ?: '18:00-20:00';
        $payment_method = $order->get_payment_method() === 'cod' ? 'COD' : 'UPI';

        // Extract package & flavour items from order line items
        $items = $order->get_items();
        $flavour_list = ['blueberry-mint', 'love-66'];
        $package_slug = 'solo-standard-24h';
        $duration = 24;

        foreach ($items as $item) {
            $item_flavours = $item->get_meta('_bns_flavours');
            if (!empty($item_flavours)) {
                $unserialized = maybe_unserialize($item_flavours);
                if (is_array($unserialized) && !empty($unserialized)) {
                    $flavour_list = $unserialized;
                }
            }
            $item_duration = $item->get_meta('_bns_duration');
            if (!empty($item_duration)) {
                $duration = intval($item_duration);
            }
        }

        if ($duration == 48) {
            $package_slug = 'duo-weekend-48h';
        } elseif ($duration >= 72) {
            $package_slug = 'vip-party-72h';
        }

        // Construct booking creation payload for NestJS
        $slot_parts = explode('-', $delivery_slot);
        $slot_start_time = !empty($slot_parts[0]) ? trim($slot_parts[0]) : '18:00';
        $rental_start_iso = date('c', strtotime($rental_date . ' ' . $slot_start_time . ':00'));

        $payload = [
            'packageId'       => $package_slug,
            'flavourIds'      => $flavour_list,
            'rentalStart'     => $rental_start_iso,
            'deliverySlotId'  => $delivery_slot,
            'deliveryAddress' => !empty($delivery_address) ? $delivery_address : 'Customer Delivery Address',
            'postalCode'      => !empty($postal_code) ? $postal_code : '700091',
            'customerEmail'   => $order->get_billing_email(),
            'customerPhone'   => $order->get_billing_phone(),
            'customerName'    => $order->get_formatted_billing_full_name(),
            'wpOrderId'       => intval($order_id),
            'paymentMethod'   => $payment_method,
            'notes'           => $order->get_customer_note(),
        ];

        // Send API request to NestJS backend
        $response = $api_client->request('bookings', 'POST', $payload);

        if ($response['success'] && !empty($response['data']['data'])) {
            $data = $response['data']['data'];

            update_post_meta($order_id, '_bns_sync_status', 'synced');
            update_post_meta($order_id, '_bns_booking_id', $data['booking']['id'] ?? '');
            update_post_meta($order_id, '_bns_booking_number', $data['booking']['bookingNumber'] ?? '');
            update_post_meta($order_id, '_bns_rental_id', $data['rental']['id'] ?? '');
            update_post_meta($order_id, '_bns_rental_number', $data['rental']['rentalNumber'] ?? '');
            update_post_meta($order_id, '_bns_assigned_unit', $data['assignedUnit'] ?? '');
            update_post_meta($order_id, '_bns_payment_method', $payment_method);
            update_post_meta($order_id, '_bns_delivery_pin', $postal_code);

            if (!empty($data['upiIntent'])) {
                update_post_meta($order_id, '_bns_upi_intent', $data['upiIntent']);
            }
            if (!empty($data['upiQrPayload'])) {
                update_post_meta($order_id, '_bns_upi_qr', $data['upiQrPayload']);
            }

            $order->add_order_note(
                sprintf(
                    __('✓ NestJS Booking Confirmed: #%s | Rental: #%s | Hookah Serial: %s | Delivery: %s (%s)', 'hookah-rental-core'),
                    $data['booking']['bookingNumber'] ?? '',
                    $data['rental']['rentalNumber'] ?? '',
                    $data['assignedUnit'] ?? 'Auto-Assigned',
                    $postal_code,
                    $payment_method
                )
            );
        } else {
            $error_message = $response['error'] ?? ($response['data']['message'] ?? __('Unknown error during NestJS sync', 'hookah-rental-core'));
            update_post_meta($order_id, '_bns_sync_status', 'failed');
            update_post_meta($order_id, '_bns_sync_error', $error_message);
            $order->add_order_note(
                sprintf(__('⚠️ Hookah Rental Bridge Warning: Could not synchronize with NestJS backend (%s)', 'hookah-rental-core'), is_array($error_message) ? implode(', ', $error_message) : $error_message)
            );
        }
    }

    /**
     * Render rental details and UPI QR code on the WooCommerce Order Received (Thank You) page
     */
    public function render_thankyou_rental_instructions($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }

        $booking_number = get_post_meta($order_id, '_bns_booking_number', true);
        $rental_number = get_post_meta($order_id, '_bns_rental_number', true);
        $assigned_unit = get_post_meta($order_id, '_bns_assigned_unit', true);
        $payment_method = get_post_meta($order_id, '_bns_payment_method', true) ?: ($order->get_payment_method() === 'cod' ? 'COD' : 'UPI');
        $upi_intent = get_post_meta($order_id, '_bns_upi_intent', true);

        echo '<div class="bns-card bns-thankyou-box" style="margin: 24px 0; padding: 24px; border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 12px; background: #0e0e10; color: #f5f5f7;">';
        echo '<h3 style="color: #d4af37; margin-top: 0;">' . esc_html__('✨ Hookah Rental Reservation Details', 'hookah-rental-core') . '</h3>';

        if (!empty($booking_number)) {
            echo '<p><strong>' . esc_html__('Booking Reference:', 'hookah-rental-core') . '</strong> ' . esc_html($booking_number) . '</p>';
        }
        if (!empty($rental_number)) {
            echo '<p><strong>' . esc_html__('Rental Contract:', 'hookah-rental-core') . '</strong> ' . esc_html($rental_number) . '</p>';
        }
        if (!empty($assigned_unit)) {
            echo '<p><strong>' . esc_html__('Reserved Hardware Serial:', 'hookah-rental-core') . '</strong> <code>' . esc_html($assigned_unit) . '</code></p>';
        }

        if ($payment_method === 'UPI') {
            echo '<div style="margin-top: 16px; padding: 16px; background: rgba(212,175,55,0.08); border-radius: 8px; border-left: 4px solid #d4af37;">';
            echo '<h4 style="margin: 0 0 8px 0; color: #d4af37;">' . esc_html__('📲 Fast UPI Payment', 'hookah-rental-core') . '</h4>';
            echo '<p style="margin: 0 0 12px 0;">' . esc_html__('Scan with any UPI app (GPay, PhonePe, Paytm, CRED) to complete payment:', 'hookah-rental-core') . '</p>';
            if (!empty($upi_intent)) {
                echo '<p><a href="' . esc_url($upi_intent) . '" class="button bns-btn-gold" style="display:inline-block; padding: 10px 20px; background:#d4af37; color:#0b0c10; font-weight:700; border-radius:6px; text-decoration:none;">' . esc_html__('Pay via UPI App →', 'hookah-rental-core') . '</a></p>';
            }
            echo '</div>';
        } else {
            echo '<div style="margin-top: 16px; padding: 16px; background: rgba(255,255,255,0.05); border-radius: 8px;">';
            echo '<p style="margin:0;"><strong>' . esc_html__('💵 Cash on Delivery:', 'hookah-rental-core') . '</strong> ' . esc_html__('Please keep exact cash ready upon arrival of our courier specialist. Government photo ID required.', 'hookah-rental-core') . '</p>';
            echo '</div>';
        }

        echo '<p style="margin-top: 16px; font-size: 13px; color: #9da3af;">🛡️ ' . esc_html__('Security deposits are 100% refundable after digital post-rental return inspection.', 'hookah-rental-core') . '</p>';
        echo '</div>';
    }
}

