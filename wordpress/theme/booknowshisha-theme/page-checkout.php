<?php
/**
 * Template Name: Checkout Page
 * Template for BookNowShisha / BookMySmoke Checkout
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div class="bns-checkout-page-container bns-container">
    <div class="bns-checkout-page-inner">
        <?php
        if (have_posts()) {
            while (have_posts()) :
                the_post();
                $content = get_the_content();
                if (empty(trim(strip_tags($content)))) {
                    echo do_shortcode('[woocommerce_checkout]');
                } else {
                    the_content();
                }
            endwhile;
        } else {
            echo do_shortcode('[woocommerce_checkout]');
        }
        ?>
    </div>
</div>

<?php get_footer();
