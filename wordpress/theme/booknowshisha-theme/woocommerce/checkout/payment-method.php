<?php
/**
 * Output a single payment method for BookMySmoke / ShishaRent
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}

$gateway_id = $gateway->id;
$is_upi = ($gateway_id === 'wc-upi');
$is_cod = ($gateway_id === 'cod');

// Refine title and description for India context
$display_title = $gateway->get_title();
$display_icon = $gateway->get_icon();

if ($is_upi) {
    $display_title = __('UPI PAYMENT (Instant GPay / PhonePe / Paytm / BHIM / CRED)', 'shisharent');
} elseif ($is_cod) {
    $display_title = __('CASH ON DELIVERY (COD)', 'shisharent');
}
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr($gateway_id); ?> bns-payment-method-item <?php echo $gateway->chosen ? 'bns-active-payment' : ''; ?>">
    <div class="bns-payment-header-row">
        <label for="payment_method_<?php echo esc_attr($gateway_id); ?>" class="bns-payment-radio-label">
            <input id="payment_method_<?php echo esc_attr($gateway_id); ?>" type="radio" class="input-radio bns-custom-radio" name="payment_method" value="<?php echo esc_attr($gateway_id); ?>" <?php checked($gateway->chosen, true); ?> data-order_button_text="<?php echo esc_attr($gateway->order_button_text); ?>" />
            <span class="bns-radio-disc"></span>
            <span class="bns-payment-title-text">
                <strong><?php echo esc_html($display_title); ?></strong>
            </span>
        </label>
        <?php if (!empty($display_icon)) : ?>
            <div class="bns-payment-icon-wrap">
                <?php echo $display_icon; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($gateway->has_fields() || $gateway->get_description()) : ?>
        <div class="payment_box payment_method_<?php echo esc_attr($gateway_id); ?> bns-payment-desc-box" <?php if (!$gateway->chosen) : ?>style="display:none;"<?php endif; ?>>
            <?php if ($is_upi) : ?>
                <div class="bns-upi-inner-desc">
                    <p class="bns-upi-prompt">⚡ <strong>Pay directly via UPI:</strong> Scan QR code or trigger UPI intent on the next confirmation screen using Google Pay, PhonePe, Paytm, CRED or any BHIM UPI app.</p>
                </div>
            <?php endif; ?>
            <?php $gateway->payment_fields(); ?>
        </div>
    <?php endif; ?>
</li>
