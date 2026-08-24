<?php
/**
 * Empty Cart Page Template for BookMySmoke / ShishaRent
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="bns-cart-empty-page-wrapper">
    <div class="bns-cart-empty-card">
        <div class="bns-empty-icon-box">
            <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
        </div>

        <div class="bns-empty-badge-pill">
            <span class="bns-pulse-dot"></span>
            <span>RESERVATION CART</span>
        </div>

        <h1 class="bns-empty-main-title"><?php esc_html_e('YOUR CART IS EMPTY', 'shisharent'); ?></h1>
        <p class="bns-empty-subtitle"><?php esc_html_e('Explore our curated flavours and luxury rental experiences in Kolkata.', 'shisharent'); ?></p>

        <div class="bns-empty-btn-group">
            <a href="<?php echo esc_url(home_url('/flavour-selection/')); ?>" class="button bns-btn-cart-gold">
                <?php esc_html_e('EXPLORE FLAVOURS ?', 'shisharent'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/#packages')); ?>" class="button bns-btn-cart-secondary">
                <?php esc_html_e('VIEW RENTAL PACKAGES', 'shisharent'); ?>
            </a>
        </div>
    </div>
</div>
