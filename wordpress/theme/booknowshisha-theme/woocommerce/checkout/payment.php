<?php
/**
 * Checkout Payment Section Template for BookMySmoke / ShishaRent
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!wp_doing_ajax()) {
    do_action('woocommerce_review_order_before_payment');
}
?>
<div id="payment" class="woocommerce-checkout-payment bns-payment-container">
    <?php if (WC()->cart->needs_payment()) : ?>
        <div class="bns-upi-headline-block">
            <div class="bns-upi-title-row">
                <span class="bns-upi-lock-icon">??</span>
                <h4 class="bns-upi-head-title"><?php esc_html_e('SELECT PAYMENT METHOD', 'shisharent'); ?></h4>
            </div>
            <p class="bns-upi-head-desc"><?php esc_html_e('Pay securely using UPI apps such as Google Pay, PhonePe, Paytm, BHIM or your banking app.', 'shisharent'); ?></p>
            
            <!-- UPI Accepted Apps Visual Badges -->
            <div class="bns-upi-badges-row">
                <span class="bns-upi-chip bns-chip-gpay">GPay</span>
                <span class="bns-upi-chip bns-chip-phonepe">PhonePe</span>
                <span class="bns-upi-chip bns-chip-paytm">Paytm</span>
                <span class="bns-upi-chip bns-chip-bhim">BHIM</span>
                <span class="bns-upi-chip bns-chip-cred">CRED</span>
                <span class="bns-upi-chip bns-chip-any">Any UPI App</span>
            </div>
        </div>

        <ul class="wc_payment_methods payment_methods methods bns-payment-methods-list">
            <?php
            if (!empty($available_gateways)) {
                foreach ($available_gateways as $gateway) {
                    wc_get_template('checkout/payment-method.php', ['gateway' => $gateway]);
                }
            } else {
                echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . esc_html(apply_filters('woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__('Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce') : esc_html__('Please fill in your details above to see available payment methods.', 'woocommerce'))) . '</li>';
            }
            ?>
        </ul>
    <?php endif; ?>

    <div class="form-row place-order bns-place-order-wrap">
        <noscript>
            <?php
            /* translators: $1 and $2 opening and closing emphasis tags respectively */
            printf(esc_html__('Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce'), '<em>', '</em>');
            ?>
            <br/><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e('Update totals', 'woocommerce'); ?>"><?php esc_html_e('Update totals', 'woocommerce'); ?></button>
        </noscript>

        <?php wc_get_template('checkout/terms.php'); ?>

        <?php do_action('woocommerce_review_order_before_submit'); ?>

        <?php
        $order_button_text = apply_filters('woocommerce_order_button_text', __('CONFIRM & PLACE RENTAL ORDER ?', 'shisharent'));
        echo apply_filters('woocommerce_order_button_html', '<button type="submit" class="button alt bns-btn-place-order" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">
            <span class="bns-btn-lock">??</span>
            <span class="bns-btn-label">' . esc_html($order_button_text) . '</span>
        </button>');
        ?>

        <div class="bns-place-order-subtext">
            <span>??? 256-Bit Encrypted Secure Checkout</span> • <span>Government Photo ID required on delivery</span>
        </div>

        <?php do_action('woocommerce_review_order_after_submit'); ?>

        <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
    </div>
</div>
<?php
if (!wp_doing_ajax()) {
    do_action('woocommerce_review_order_after_payment');
}
?>
