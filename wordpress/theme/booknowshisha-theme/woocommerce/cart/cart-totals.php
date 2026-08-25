<?php
/**
 * Cart Totals Summary Card Template for BookMySmoke / ShishaRent
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?> bns-cart-summary-sticky-card">

    <?php do_action( 'woocommerce_before_cart_totals' ); ?>

    <div class="bns-cart-summary-header">
        <div class="bns-summary-title-line">
            <h2 class="bns-summary-head-title"><?php esc_html_e('CART SUMMARY', 'shisharent'); ?></h2>
            <span class="bns-summary-count-badge"><?php echo WC()->cart->get_cart_contents_count(); ?> <?php echo WC()->cart->get_cart_contents_count() === 1 ? 'ITEM' : 'ITEMS'; ?></span>
        </div>
        <p class="bns-summary-head-desc"><?php esc_html_e('Pricing inclusive of setup, sanitised hardware & doorstep pickup.', 'shisharent'); ?></p>
    </div>

    <table class="shop_table shop_table_responsive bns-summary-table" cellspacing="0">

        <!-- Subtotal -->
        <tr class="cart-subtotal bns-sum-row">
            <th><?php esc_html_e('Subtotal', 'shisharent'); ?></th>
            <td data-title="<?php esc_attr_e('Subtotal', 'shisharent'); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
        </tr>

        <!-- Coupons / Discounts -->
        <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
            <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?> bns-sum-row bns-discount-line">
                <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                <td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
            </tr>
        <?php endforeach; ?>

        <!-- Shipping / Delivery -->
        <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
            <?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>
            <?php wc_cart_totals_shipping_html(); ?>
            <?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
        <?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>
            <tr class="shipping bns-sum-row">
                <th><?php esc_html_e('Delivery Service', 'shisharent'); ?></th>
                <td data-title="<?php esc_attr_e('Delivery', 'shisharent'); ?>"><?php woocommerce_shipping_calculator(); ?></td>
            </tr>
        <?php else : ?>
            <tr class="bns-sum-row bns-delivery-row">
                <th><?php esc_html_e('Doorstep Delivery', 'shisharent'); ?></th>
                <td><span class="bns-free-pill">FREE (Kolkata & 24 Pgs)</span></td>
            </tr>
        <?php endif; ?>

        <!-- Fees -->
        <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
            <tr class="fee bns-sum-row">
                <th><?php echo esc_html( $fee->name ); ?></th>
                <td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td>
            </tr>
        <?php endforeach; ?>

        <!-- Taxes -->
        <?php
        if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
            $taxable_address = WC()->customer->get_taxable_address();
            $estimated_text  = '';

            if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
                /* translators: %s location. */
                $estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'woocommerce' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
            }

            if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
                foreach ( WC()->cart->get_tax_totals() as $code => $tax ) {
                    ?>
                    <tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?> bns-sum-row">
                        <th><?php echo esc_html( $tax->label ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
                        <td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr class="tax-total bns-sum-row">
                    <th><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
                    <td data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
                </tr>
                <?php
            }
        }
        ?>

        <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

        <!-- Grand Total -->
        <tr class="order-total bns-sum-total-row">
            <th><?php esc_html_e('TOTAL', 'shisharent'); ?></th>
            <td data-title="<?php esc_attr_e('Total', 'shisharent'); ?>"><strong><?php wc_cart_totals_order_total_html(); ?></strong></td>
        </tr>

        <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

    </table>

    <!-- Proceed to Checkout Action -->
    <div class="wc-proceed-to-checkout bns-proceed-checkout-wrap">
        <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
    </div>

    <!-- Assurance Points -->
    <div class="bns-cart-assurance-block">
        <div class="bns-assurance-row">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#d4a95f" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>100% Hospital-Grade Cleaned & Sanitised</span>
        </div>
        <div class="bns-assurance-row">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#d4a95f" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>Prompt Doorstep Delivery & Hassle-Free Intake</span>
        </div>
        <div class="bns-assurance-row">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#d4a95f" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <span>Secure Checkout with Instant UPI Support</span>
        </div>
    </div>

    <?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
