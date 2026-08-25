<?php
/**
 * Proceed to checkout button for BookMySmoke / ShishaRent
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="checkout-button button alt wc-forward bns-btn-proceed-checkout">
    <span><?php esc_html_e('PROCEED TO CHECKOUT ?', 'shisharent'); ?></span>
</a>
