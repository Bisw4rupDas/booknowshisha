<?php
/**
 * Cart Page Template for BookMySmoke / ShishaRent
 * Premium Luxury 2-Column Responsive Cart Experience
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_cart'); ?>

<div class="bns-cart-main-wrapper">
    <!-- Header Block -->
    <div class="bns-cart-hero-header">
        <div class="bns-cart-hero-badge">
            <span class="bns-pulse-point"></span>
            <span>SHISHARENT RESERVATION CART</span>
        </div>
        <h1 class="bns-cart-hero-title"><?php esc_html_e('YOUR CART', 'shisharent'); ?></h1>
        <p class="bns-cart-hero-subtitle"><?php esc_html_e('Review your selected hookah setups, curated flavours, and rental add-ons.', 'shisharent'); ?></p>
    </div>

    <form class="woocommerce-cart-form bns-cart-page-grid" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <?php do_action('woocommerce_before_cart_table'); ?>

        <!-- ====================================================================
             LEFT COLUMN: Cart Items Cards & Coupon Section
             ==================================================================== -->
        <div class="bns-cart-left-col">
            <div class="bns-cart-items-card">
                <div class="bns-items-card-header">
                    <div class="bns-items-header-left">
                        <span class="bns-header-step-num">1</span>
                        <div>
                            <h2 class="bns-items-header-title"><?php esc_html_e('RESERVATION ITEMS', 'shisharent'); ?></h2>
                            <p class="bns-items-header-desc"><?php echo WC()->cart->get_cart_contents_count(); ?> <?php echo WC()->cart->get_cart_contents_count() === 1 ? 'item selected' : 'items selected'; ?> for white-glove delivery in Kolkata</p>
                        </div>
                    </div>
                </div>

                <div class="bns-cart-items-list">
                    <?php do_action('woocommerce_before_cart_contents'); ?>

                    <?php
                    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                        $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                        $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                            $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);

                            // Extract rental custom configurations
                            $rental_option    = $cart_item['bns_rental_option'] ?? '';
                            $chillum_material = $cart_item['bns_chillum_material'] ?? 'Classic Clay';
                            $hookah_base      = $cart_item['bns_hookah_base'] ?? 'standard';
                            $hookah_base_lbl  = $cart_item['bns_hookah_base_label'] ?? '';
                            $base_price       = floatval($cart_item['bns_hookah_base_price'] ?? 0);
                            $chillum_price    = floatval($cart_item['bns_chillum_price'] ?? 0);
                            $rental_meta      = $cart_item['bns_rental'] ?? [];

                            // Base label mapping
                            $base_map = [
                                'none'     => __('No Base (Chilam Only)', 'shisharent'),
                                'standard' => __('Standard Base (Included)', 'shisharent'),
                                'ice'      => __('Ice Base (+₹100)', 'shisharent'),
                                'milk'     => __('Milk Base (+₹150)', 'shisharent'),
                                'both'     => __('Ice + Milk Base Combined (+₹200)', 'shisharent'),
                                'ice_milk' => __('Ice + Milk Base Combined (+₹200)', 'shisharent'),
                            ];
                            $base_display = $base_map[$hookah_base] ?? ($hookah_base_lbl ?: ucfirst($hookah_base));
                            $is_gold_c = (strcasecmp($chillum_material, 'Gold Silicone') === 0);

                            // Flavour Name
                            $flavour_name = '';
                            if (!empty($rental_meta['flavours']) && is_array($rental_meta['flavours'])) {
                                $flavour_name = implode(', ', array_map('ucwords', str_replace('-', ' ', $rental_meta['flavours'])));
                            } else {
                                $flavour_name = $_product->get_name();
                            }

                            // Thumbnail
                            $img_url = function_exists('bns_get_product_image_url') ? bns_get_product_image_url($product_id, 'medium') : '';
                            ?>
                            <div class="bns-cart-item-row woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>" data-item-key="<?php echo esc_attr($cart_item_key); ?>">

                                <!-- Left: Product Image -->
                                <div class="bns-item-thumb-box">
                                    <?php if (!empty($img_url)) : ?>
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($_product->get_name()); ?>" class="bns-item-thumb-img" loading="lazy" />
                                    <?php endif; ?>
                                </div>

                                <!-- Center: Product Info & Configuration -->
                                <div class="bns-item-info-col">
                                    <div class="bns-item-title-top">
                                        <h3 class="bns-item-product-name">
                                            <?php
                                            if (!$product_permalink) {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key));
                                            } else {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s" class="bns-item-link">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                            }
                                            ?>
                                        </h3>
                                        <!-- Remove Item Action -->
                                        <?php
                                        echo apply_filters(
                                            'woocommerce_cart_item_remove_link',
                                            sprintf(
                                                '<a href="%s" class="remove bns-item-remove-btn" aria-label="%s" data-product_id="%s" data-product_sku="%s" data-item_key="%s" title="%s">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                                    </svg>
                                                </a>',
                                                esc_url(wc_get_cart_remove_url($cart_item_key)),
                                                esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), $_product->get_name())),
                                                esc_attr($product_id),
                                                esc_attr($_product->get_sku()),
                                                esc_attr($cart_item_key),
                                                esc_attr__('Remove item', 'shisharent')
                                            ),
                                            $cart_item_key
                                        );
                                        ?>
                                    </div>

                                    <!-- Rental Configuration Specs -->
                                    <div class="bns-item-specs-grid">
                                        <?php if (!empty($rental_option)) : ?>
                                            <div class="bns-spec-chip">
                                                <span class="bns-chip-label"><?php esc_html_e('Rental Setup:', 'shisharent'); ?></span>
                                                <strong class="bns-chip-value bns-gold-text"><?php echo esc_html($rental_option); ?></strong>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($flavour_name) && $flavour_name !== $_product->get_name()) : ?>
                                            <div class="bns-spec-chip">
                                                <span class="bns-chip-label"><?php esc_html_e('Flavour:', 'shisharent'); ?></span>
                                                <strong class="bns-chip-value"><?php echo esc_html($flavour_name); ?></strong>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($chillum_material)) : ?>
                                            <div class="bns-spec-chip">
                                                <span class="bns-chip-label"><?php esc_html_e('Chillum Material:', 'shisharent'); ?></span>
                                                <strong class="bns-chip-value"><?php echo $is_gold_c ? esc_html__('Gold Silicone (+₹100)', 'shisharent') : esc_html__('Classic Clay (Included)', 'shisharent'); ?></strong>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($hookah_base)) : ?>
                                            <div class="bns-spec-chip">
                                                <span class="bns-chip-label"><?php esc_html_e('Hookah Base:', 'shisharent'); ?></span>
                                                <strong class="bns-chip-value"><?php echo esc_html($base_display); ?></strong>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Bottom Row: Price, Quantity Stepper, Subtotal -->
                                    <div class="bns-item-actions-bar">
                                        <div class="bns-action-col bns-col-price">
                                            <span class="bns-sub-label"><?php esc_html_e('Price:', 'shisharent'); ?></span>
                                            <span class="bns-unit-price-text"><?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?></span>
                                        </div>

                                        <div class="bns-action-col bns-col-qty">
                                            <span class="bns-sub-label"><?php esc_html_e('Quantity:', 'shisharent'); ?></span>
                                            <div class="bns-qty-stepper-box">
                                                <button type="button" class="bns-stepper-btn bns-stepper-minus" aria-label="Decrease quantity">-</button>
                                                <?php
                                                if ($_product->is_sold_individually()) {
                                                    $min_quantity = 1;
                                                    $max_quantity = 1;
                                                } else {
                                                    $min_quantity = 0;
                                                    $max_quantity = $_product->get_max_purchase_quantity();
                                                }

                                                $product_quantity = woocommerce_quantity_input(
                                                    [
                                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                                        'input_value'  => $cart_item['quantity'],
                                                        'max_value'    => $max_quantity,
                                                        'min_value'    => $min_quantity,
                                                        'product_name' => $_product->get_name(),
                                                        'classes'      => ['input-text', 'qty', 'text', 'bns-native-qty-input'],
                                                    ],
                                                    $_product,
                                                    false
                                                );
                                                echo $product_quantity;
                                                ?>
                                                <button type="button" class="bns-stepper-btn bns-stepper-plus" aria-label="Increase quantity">+</button>
                                            </div>
                                        </div>

                                        <div class="bns-action-col bns-col-subtotal">
                                            <span class="bns-sub-label"><?php esc_html_e('Subtotal:', 'shisharent'); ?></span>
                                            <span class="bns-line-subtotal-text bns-gold-text">
                                                <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <?php do_action('woocommerce_cart_contents'); ?>
                </div>

                <!-- Bottom Bar: Clean Coupon Row & Update Trigger -->
                <div class="bns-cart-items-footer">
                    <?php if (wc_coupons_enabled()) : ?>
                        <div class="bns-coupon-block">
                            <div class="bns-coupon-label-row">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d4a95f" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                <span><?php esc_html_e('Have a coupon?', 'shisharent'); ?></span>
                            </div>
                            <div class="bns-coupon-input-group">
                                <input type="text" name="coupon_code" class="input-text bns-coupon-field" id="coupon_code" value="" placeholder="<?php esc_attr_e('Enter coupon code', 'shisharent'); ?>" />
                                <button type="submit" class="button bns-btn-apply-coupon" name="apply_coupon" value="<?php esc_attr_e('APPLY', 'shisharent'); ?>">
                                    <?php esc_html_e('APPLY', 'shisharent'); ?>
                                </button>
                                <?php do_action('woocommerce_cart_coupon'); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="bns-cart-update-action-wrap">
                        <button type="submit" class="button bns-btn-cart-update" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
                            <?php esc_html_e('Update cart', 'woocommerce'); ?>
                        </button>
                    </div>

                    <?php do_action('woocommerce_cart_actions'); ?>
                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                </div>

                <?php do_action('woocommerce_after_cart_contents'); ?>
            </div>
        </div>

        <!-- ====================================================================
             RIGHT COLUMN: Cart Summary Card (Sticky Desktop)
             ==================================================================== -->
        <div class="bns-cart-right-col">
            <?php do_action('woocommerce_before_cart_collaterals'); ?>

            <div class="cart-collaterals bns-cart-collaterals-wrap">
                <?php
                /**
                 * Cart collaterals hook.
                 *
                 * @hooked woocommerce_cross_sell_display
                 * @hooked woocommerce_cart_totals - 10
                 */
                do_action('woocommerce_cart_collaterals');
                ?>
            </div>
        </div>

        <?php do_action('woocommerce_after_cart_table'); ?>
    </form>
</div>

<?php do_action('woocommerce_after_cart'); ?>
