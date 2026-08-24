<?php
/**
 * Review Order (Order Summary) Template for BookMySmoke / ShishaRent
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="bns-review-order-table-wrap">
    <table class="shop_table woocommerce-checkout-review-order-table bns-checkout-review-table">
        <thead>
            <tr>
                <th class="product-name"><?php esc_html_e('Product / Rental Setup', 'shisharent'); ?></th>
                <th class="product-total"><?php esc_html_e('Subtotal', 'shisharent'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            do_action('woocommerce_review_order_before_cart_contents');

            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
                    $product_id = $cart_item['product_id'];
                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);

                    // Extract custom rental metadata
                    $rental_option    = $cart_item['bns_rental_option'] ?? '';
                    $chillum_material = $cart_item['bns_chillum_material'] ?? 'Classic Clay';
                    $hookah_base      = $cart_item['bns_hookah_base'] ?? 'standard';
                    $hookah_base_lbl  = $cart_item['bns_hookah_base_label'] ?? '';
                    $base_price       = floatval($cart_item['bns_hookah_base_price'] ?? 0);
                    $chillum_price    = floatval($cart_item['bns_chillum_price'] ?? 0);
                    $rental_meta      = $cart_item['bns_rental'] ?? [];

                    // Base label formatting
                    $base_map = [
                        'none'     => __('No Base (Chilam Only)', 'shisharent'),
                        'standard' => __('Standard Base (Included)', 'shisharent'),
                        'ice'      => __('Ice Base (+₹100)', 'shisharent'),
                        'milk'     => __('Milk Base (+₹150)', 'shisharent'),
                        'both'     => __('Ice + Milk Base Combined (+₹200)', 'shisharent'),
                        'ice_milk' => __('Ice + Milk Base Combined (+₹200)', 'shisharent'),
                    ];
                    $base_display = $base_map[$hookah_base] ?? ($hookah_base_lbl ?: ucfirst($hookah_base));

                    // Flavour extraction
                    $flavour_name = '';
                    if (!empty($rental_meta['flavours']) && is_array($rental_meta['flavours'])) {
                        $flavour_name = implode(', ', array_map('ucwords', str_replace('-', ' ', $rental_meta['flavours'])));
                    } else {
                        $flavour_name = $_product->get_name();
                    }

                    // Product image
                    $img_url = function_exists('bns_get_product_image_url') ? bns_get_product_image_url($product_id, 'thumbnail') : '';
                    ?>
                    <tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item bns-cart-item-row', $cart_item, $cart_item_key)); ?>">
                        <td class="product-name bns-product-col">
                            <div class="bns-review-product-card">
                                <?php if (!empty($img_url)) : ?>
                                    <div class="bns-review-thumb">
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($_product->get_name()); ?>" width="64" height="64" loading="lazy" />
                                    </div>
                                <?php endif; ?>

                                <div class="bns-review-details">
                                    <div class="bns-review-title">
                                        <?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)); ?>
                                        <strong class="product-quantity">&times;&nbsp;<?php echo esc_html($cart_item['quantity']); ?></strong>
                                    </div>

                                    <!-- Configuration Specs Breakdown -->
                                    <div class="bns-rental-specs-pill-group">
                                        <?php if (!empty($rental_option)) : ?>
                                            <div class="bns-spec-row">
                                                <span class="bns-spec-label"><?php esc_html_e('Setup:', 'shisharent'); ?></span>
                                                <span class="bns-spec-val bns-gold-text"><?php echo esc_html($rental_option); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($flavour_name) && $flavour_name !== $_product->get_name()) : ?>
                                            <div class="bns-spec-row">
                                                <span class="bns-spec-label"><?php esc_html_e('Flavour:', 'shisharent'); ?></span>
                                                <span class="bns-spec-val"><?php echo esc_html($flavour_name); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($chillum_material)) : 
                                            $is_gold_c = (strcasecmp($chillum_material, 'Gold Silicone') === 0);
                                        ?>
                                            <div class="bns-spec-row">
                                                <span class="bns-spec-label"><?php esc_html_e('Chillum:', 'shisharent'); ?></span>
                                                <span class="bns-spec-val"><?php echo $is_gold_c ? esc_html__('Gold Silicone (+₹100)', 'shisharent') : esc_html__('Classic Clay', 'shisharent'); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($hookah_base)) : ?>
                                            <div class="bns-spec-row">
                                                <span class="bns-spec-label"><?php esc_html_e('Hookah Base:', 'shisharent'); ?></span>
                                                <span class="bns-spec-val"><?php echo esc_html($base_display); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($base_price > 0) : ?>
                                            <div class="bns-spec-row bns-surcharge-row">
                                                <span class="bns-spec-label"><?php esc_html_e('Base charge:', 'shisharent'); ?></span>
                                                <span class="bns-spec-val bns-gold-text">+₹<?php echo esc_html(number_format($base_price * $cart_item['quantity'], 0)); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="product-total bns-total-col">
                            <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                        </td>
                    </tr>
                    <?php
                }
            }

            do_action('woocommerce_review_order_after_cart_contents');
            ?>
        </tbody>
        <tfoot>
            <!-- Subtotal -->
            <tr class="cart-subtotal bns-summary-row">
                <th><?php esc_html_e('Subtotal', 'shisharent'); ?></th>
                <td><?php wc_cart_totals_subtotal_html(); ?></td>
            </tr>

            <!-- Coupons -->
            <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
                <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?> bns-summary-row bns-discount-row">
                    <th><?php wc_cart_totals_coupon_label($coupon); ?></th>
                    <td><?php wc_cart_totals_coupon_html($coupon); ?></td>
                </tr>
            <?php endforeach; ?>

            <!-- Delivery / Shipping -->
            <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                <?php do_action('woocommerce_review_order_before_shipping'); ?>
                <?php wc_cart_totals_shipping_html(); ?>
                <?php do_action('woocommerce_review_order_after_shipping'); ?>
            <?php else : ?>
                <tr class="bns-summary-row bns-delivery-fee-row">
                    <th><?php esc_html_e('Doorstep Delivery', 'shisharent'); ?></th>
                    <td><span class="bns-free-badge">FREE (Kolkata & 24 Pgs)</span></td>
                </tr>
            <?php endif; ?>

            <!-- Fees -->
            <?php foreach (WC()->cart->get_fees() as $fee) : ?>
                <tr class="fee bns-summary-row">
                    <th><?php echo esc_html($fee->name); ?></th>
                    <td><?php wc_cart_totals_fee_html($fee); ?></td>
                </tr>
            <?php endforeach; ?>

            <!-- Taxes -->
            <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
                <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
                    <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
                        <tr class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?> bns-summary-row">
                            <th><?php echo esc_html($tax->label); ?></th>
                            <td><?php echo wp_kses_post($tax->formatted_amount); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr class="tax-total bns-summary-row">
                        <th><?php echo esc_html(WC()->countries->tax_or_vat()); ?></th>
                        <td><?php wc_cart_totals_taxes_total_html(); ?></td>
                    </tr>
                <?php endif; ?>
            <?php endif; ?>

            <?php do_action('woocommerce_review_order_before_order_total'); ?>

            <!-- Grand Total -->
            <tr class="order-total bns-grand-total-row">
                <th><?php esc_html_e('Total Amount', 'shisharent'); ?></th>
                <td><strong><?php wc_cart_totals_order_total_html(); ?></strong></td>
            </tr>

            <?php do_action('woocommerce_review_order_after_order_total'); ?>
        </tfoot>
    </table>
</div>
