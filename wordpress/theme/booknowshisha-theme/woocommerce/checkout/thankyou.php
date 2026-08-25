<?php
/**
 * Thankyou (Order Received) Template for BookMySmoke / ShishaRent
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="woocommerce-order bns-thankyou-wrapper">

    <?php
    if ($order) :
        do_action('woocommerce_before_thankyou', $order->get_id());
        ?>

        <?php if ($order->has_status('failed')) : ?>

            <div class="bns-card bns-thankyou-status-card bns-status-failed">
                <div class="bns-status-icon">⚠️</div>
                <h2 class="bns-status-title"><?php esc_html_e('Payment Unsuccessful', 'shisharent'); ?></h2>
                <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?></p>

                <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                    <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="button pay bns-btn-gold"><?php esc_html_e('Pay Now', 'woocommerce'); ?></a>
                    <?php if (is_user_logged_in()) : ?>
                        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="button pay bns-btn-secondary"><?php esc_html_e('My Account', 'woocommerce'); ?></a>
                    <?php endif; ?>
                </p>
            </div>

        <?php else : ?>

            <div class="bns-thankyou-hero">
                <div class="bns-thankyou-badge">
                    <span class="bns-pulse-point"></span>
                    <span>RENTAL RESERVATION CONFIRMED</span>
                </div>
                <h1 class="bns-thankyou-title"><?php esc_html_e('THANK YOU FOR YOUR ORDER', 'shisharent'); ?></h1>
                <p class="bns-thankyou-sub"><?php esc_html_e('Your hookah rental reservation has been received and is being prepared by our concierge team in Kolkata.', 'shisharent'); ?></p>
            </div>

            <!-- Order Key Metrics Card -->
            <div class="bns-card bns-order-metrics-card">
                <div class="bns-metric-col">
                    <span class="bns-metric-label"><?php esc_html_e('Order Number:', 'shisharent'); ?></span>
                    <strong class="bns-metric-val bns-gold-text">#<?php echo $order->get_order_number(); ?></strong>
                </div>
                <div class="bns-metric-col">
                    <span class="bns-metric-label"><?php esc_html_e('Date:', 'shisharent'); ?></span>
                    <strong class="bns-metric-val"><?php echo wc_format_datetime($order->get_date_created()); ?></strong>
                </div>
                <div class="bns-metric-col">
                    <span class="bns-metric-label"><?php esc_html_e('Total Amount:', 'shisharent'); ?></span>
                    <strong class="bns-metric-val bns-gold-text"><?php echo $order->get_formatted_order_total(); ?></strong>
                </div>
                <div class="bns-metric-col">
                    <span class="bns-metric-label"><?php esc_html_e('Payment Method:', 'shisharent'); ?></span>
                    <strong class="bns-metric-val"><?php echo wp_kses_post($order->get_payment_method_title()); ?></strong>
                </div>
            </div>

            <!-- Next Steps & Verification Notice -->
            <div class="bns-card bns-delivery-instructions-card">
                <h3 class="bns-card-header-title">📋 <?php esc_html_e('Delivery & Inspection Protocol', 'shisharent'); ?></h3>
                <div class="bns-protocol-grid">
                    <div class="bns-protocol-step">
                        <span class="bns-step-num">1</span>
                        <div class="bns-step-text">
                            <strong><?php esc_html_e('Kolkata White-Glove Dispatch', 'shisharent'); ?></strong>
                            <p><?php esc_html_e('Our certified courier specialist will deliver and set up your sanitised hookah hardware at your designated address.', 'shisharent'); ?></p>
                        </div>
                    </div>
                    <div class="bns-protocol-step">
                        <span class="bns-step-num">2</span>
                        <div class="bns-step-text">
                            <strong><?php esc_html_e('Government Photo ID Check', 'shisharent'); ?></strong>
                            <p><?php esc_html_e('Please keep original Government Photo ID (21+ age verification) ready for mandatory digital verification upon arrival.', 'shisharent'); ?></p>
                        </div>
                    </div>
                    <div class="bns-protocol-step">
                        <span class="bns-step-num">3</span>
                        <div class="bns-step-text">
                            <strong><?php esc_html_e('Seamless Return Pickup', 'shisharent'); ?></strong>
                            <p><?php esc_html_e('At the end of your rental period, our courier will inspect and intake the equipment with 100% refundable security deposit clearance.', 'shisharent'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
        <?php do_action('woocommerce_thankyou', $order->get_id()); ?>

    <?php else : ?>

        <div class="bns-card bns-thankyou-hero">
            <h1 class="bns-thankyou-title"><?php esc_html_e('Thank you. Your order has been received.', 'woocommerce'); ?></h1>
        </div>

    <?php endif; ?>

</div>
