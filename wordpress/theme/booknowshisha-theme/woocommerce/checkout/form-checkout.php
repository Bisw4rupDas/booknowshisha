<?php
/**
 * Checkout Form Template for BookMySmoke / ShishaRent
 * Exclusively Kolkata, North 24 Parganas & South 24 Parganas, India
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}

// Display notices (coupons, login prompts, errors)
do_action('woocommerce_before_checkout_form', $checkout);

// If registration is required and user not logged in
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}
?>

<div class="bns-checkout-wrapper">
    <!-- Main Checkout Header -->
    <div class="bns-checkout-header-block">
        <div class="bns-checkout-badge">
            <span class="bns-pulse-point"></span>
            <span>KOLKATA • NORTH 24 PGS • SOUTH 24 PGS</span>
        </div>
        <h1 class="bns-checkout-title">CHECKOUT</h1>
        <p class="bns-checkout-subtitle">Enter your delivery details to complete your ShishaRent rental.</p>
    </div>

    <form name="checkout" method="post" class="checkout woocommerce-checkout bns-checkout-layout-grid" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

        <!-- ====================================================================
             LEFT COLUMN: Contact, Delivery, Verification, Payment & Notes
             ==================================================================== -->
        <div class="bns-checkout-col-main">

            <!-- 1. CONTACT INFORMATION -->
            <div class="bns-checkout-panel" id="bns-contact-info-panel">
                <div class="bns-panel-heading">
                    <div class="bns-step-badge">1</div>
                    <div class="bns-heading-content">
                        <h2 class="bns-panel-title">CONTACT INFORMATION</h2>
                        <p class="bns-panel-desc">We will use these details to coordinate your delivery and rental verification.</p>
                    </div>
                </div>
                <div class="bns-panel-body">
                    <div class="bns-form-grid">
                        <?php
                        $fields = $checkout->get_checkout_fields('billing');
                        
                        // Full Name
                        if (isset($fields['billing_first_name'])) {
                            woocommerce_form_field('billing_first_name', $fields['billing_first_name'], $checkout->get_value('billing_first_name'));
                        }
                        // Hidden Last Name to satisfy internal WC if needed
                        if (isset($fields['billing_last_name'])) {
                            woocommerce_form_field('billing_last_name', $fields['billing_last_name'], $checkout->get_value('billing_last_name'));
                        }
                        // Mobile Number
                        if (isset($fields['billing_phone'])) {
                            woocommerce_form_field('billing_phone', $fields['billing_phone'], $checkout->get_value('billing_phone'));
                        }
                        // Email Address
                        if (isset($fields['billing_email'])) {
                            woocommerce_form_field('billing_email', $fields['billing_email'], $checkout->get_value('billing_email'));
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- 2. DELIVERY ADDRESS -->
            <div class="bns-checkout-panel" id="bns-delivery-address-panel">
                <div class="bns-panel-heading">
                    <div class="bns-step-badge">2</div>
                    <div class="bns-heading-content">
                        <h2 class="bns-panel-title">DELIVERY ADDRESS</h2>
                        <p class="bns-panel-desc">Enter the complete address where you want your hookah & flavours delivered.</p>
                    </div>
                </div>
                <div class="bns-panel-body">

                    <!-- Delivery Serviceability Info Box -->
                    <div class="bns-delivery-location-card">
                        <div class="bns-dlc-icon-wrap">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <div class="bns-dlc-content">
                            <span class="bns-dlc-tag">DELIVERY LOCATION</span>
                            <p class="bns-dlc-main">Currently serving <strong>Kolkata</strong>, <strong>North 24 Parganas</strong> and <strong>South 24 Parganas</strong>.</p>
                            <p class="bns-dlc-sub">Please enter the exact delivery address and PIN code so we can verify serviceability.</p>
                        </div>
                    </div>

                    <div class="bns-form-grid">
                        <?php
                        // Complete Delivery Address
                        if (isset($fields['billing_address_1'])) {
                            woocommerce_form_field('billing_address_1', $fields['billing_address_1'], $checkout->get_value('billing_address_1'));
                        }
                        // Apartment / Flat / Floor / Building (Optional)
                        if (isset($fields['billing_address_2'])) {
                            woocommerce_form_field('billing_address_2', $fields['billing_address_2'], $checkout->get_value('billing_address_2'));
                        }
                        // Area / Locality
                        if (isset($fields['billing_area'])) {
                            woocommerce_form_field('billing_area', $fields['billing_area'], $checkout->get_value('billing_area'));
                        }
                        // City (Kolkata)
                        if (isset($fields['billing_city'])) {
                            woocommerce_form_field('billing_city', $fields['billing_city'], $checkout->get_value('billing_city') ?: 'Kolkata');
                        }
                        // State (West Bengal)
                        if (isset($fields['billing_state'])) {
                            woocommerce_form_field('billing_state', $fields['billing_state'], $checkout->get_value('billing_state') ?: 'WB');
                        }
                        // Country (India)
                        if (isset($fields['billing_country'])) {
                            woocommerce_form_field('billing_country', $fields['billing_country'], $checkout->get_value('billing_country') ?: 'IN');
                        }
                        // PIN Code (6-digit)
                        if (isset($fields['billing_postcode'])) {
                            woocommerce_form_field('billing_postcode', $fields['billing_postcode'], $checkout->get_value('billing_postcode'));
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- 3. RENTAL TIMING & AGE VERIFICATION -->
            <div class="bns-checkout-panel" id="bns-rental-timing-panel">
                <div class="bns-panel-heading">
                    <div class="bns-step-badge">3</div>
                    <div class="bns-heading-content">
                        <h2 class="bns-panel-title">RENTAL TIMING & VERIFICATION</h2>
                        <p class="bns-panel-desc">Choose your preferred setup slot and confirm legal age compliance.</p>
                    </div>
                </div>
                <div class="bns-panel-body">
                    <div class="bns-form-grid">
                        <?php
                        do_action('bns_rental_timing_rendered');

                        // Delivery Date
                        woocommerce_form_field('bns_rental_date', [
                            'type'        => 'date',
                            'class'       => ['form-row-first', 'bns-form-field'],
                            'label'       => __('PREFERRED DELIVERY DATE', 'shisharent') . ' <span class="required">*</span>',
                            'required'    => true,
                            'custom_attributes' => [
                                'min' => date('Y-m-d'),
                            ],
                            'default'     => date('Y-m-d'),
                        ], $checkout->get_value('bns_rental_date') ?: date('Y-m-d'));

                        // Delivery Slot
                        woocommerce_form_field('bns_delivery_slot', [
                            'type'        => 'select',
                            'class'       => ['form-row-last', 'bns-form-field'],
                            'label'       => __('DELIVERY TIME SLOT', 'shisharent') . ' <span class="required">*</span>',
                            'options'     => [
                                '18:00-20:00' => '6:00 PM - 8:00 PM (Prime Evening)',
                                '14:00-16:00' => '2:00 PM - 4:00 PM (Afternoon Express)',
                                '16:00-18:00' => '4:00 PM - 6:00 PM (Evening Sunset)',
                                '20:00-22:00' => '8:00 PM - 10:00 PM (Late Night Session)',
                            ],
                            'required'    => true,
                            'default'     => '18:00-20:00',
                        ], $checkout->get_value('bns_delivery_slot') ?: '18:00-20:00');

                        // Age Verification Checkbox
                        woocommerce_form_field('bns_age_verification', [
                            'type'        => 'checkbox',
                            'class'       => ['form-row-wide', 'bns-form-field', 'bns-age-checkbox-wrap'],
                            'label'       => __('I confirm I am 21 years of age or older and agree to present valid Government Photo ID upon delivery.', 'shisharent') . ' <span class="required">*</span>',
                            'required'    => true,
                            'default'     => 1,
                        ], $checkout->get_value('bns_age_verification') ?: 1);
                        ?>
                    </div>
                </div>
            </div>

            <!-- 4. ORDER NOTES (Optional) -->
            <div class="bns-checkout-panel" id="bns-order-notes-panel">
                <div class="bns-panel-heading">
                    <div class="bns-step-badge bns-optional-badge">✎</div>
                    <div class="bns-heading-content">
                        <h2 class="bns-panel-title">ADD A NOTE TO YOUR ORDER <span class="bns-optional-tag">(Optional)</span></h2>
                        <p class="bns-panel-desc">Optional — add delivery instructions, landmark details or other information for our team.</p>
                    </div>
                </div>
                <div class="bns-panel-body">
                    <?php
                    $order_fields = $checkout->get_checkout_fields('order');
                    if (isset($order_fields['order_comments'])) {
                        $order_fields['order_comments']['label'] = false;
                        $order_fields['order_comments']['placeholder'] = __('Optional — add delivery instructions, landmark details or special requests for our team...', 'shisharent');
                        woocommerce_form_field('order_comments', $order_fields['order_comments'], $checkout->get_value('order_comments'));
                    }
                    ?>
                </div>
            </div>

            <!-- 5. PAYMENT METHOD -->
            <div class="bns-checkout-panel bns-payment-panel" id="bns-payment-method-panel">
                <div class="bns-panel-heading">
                    <div class="bns-step-badge">4</div>
                    <div class="bns-heading-content">
                        <h2 class="bns-panel-title">PAYMENT METHOD</h2>
                        <p class="bns-panel-desc">Pay securely using India UPI apps or verified Cash on Delivery.</p>
                    </div>
                </div>
                <div class="bns-panel-body">
                    <!-- Embedded Payment Gateways rendered by review-order / payment hook -->
                    <div id="bns-checkout-payment-box">
                        <?php woocommerce_checkout_payment(); ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- ====================================================================
             RIGHT COLUMN: Sticky Order Summary & Fast Booking Confirmation
             ==================================================================== -->
        <div class="bns-checkout-col-summary">
            <div class="bns-sticky-summary-card">
                <div class="bns-summary-top">
                    <div class="bns-summary-title-row">
                        <h3 class="bns-summary-title">ORDER SUMMARY</h3>
                        <span class="bns-summary-badge"><?php echo WC()->cart->get_cart_contents_count(); ?> <?php echo WC()->cart->get_cart_contents_count() === 1 ? 'ITEM' : 'ITEMS'; ?></span>
                    </div>
                    <p class="bns-summary-sub">Review your selected hookah, flavour, base and chillum configuration.</p>
                </div>

                <div id="order_review" class="woocommerce-checkout-review-order bns-order-review-container">
                    <?php woocommerce_order_review(); ?>
                </div>

                <!-- Luxury Assurance Points -->
                <div class="bns-assurance-list">
                    <div class="bns-assurance-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d4a95f" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>100% Sanitized & Hospital-Grade Cleaned Hookahs</span>
                    </div>
                    <div class="bns-assurance-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d4a95f" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Doorstep Delivery & Hassle-Free Return Pickup</span>
                    </div>
                    <div class="bns-assurance-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d4a95f" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        <span>Instant UPI QR / GPay / PhonePe / Paytm / BHIM</span>
                    </div>
                </div>
            </div>
        </div>

    </form>

</div>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
