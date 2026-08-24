<?php
/**
 * WooCommerce Global Wrapper Template
 *
 * @package ShishaRent
 */

get_header(); ?>

<div class="bns-page-wrapper bns-container">
    <div class="bns-page-card bns-wc-wrapper">
        <?php woocommerce_content(); ?>
    </div>
</div>

<?php get_footer(); ?>
