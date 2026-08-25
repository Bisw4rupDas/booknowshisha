<?php
/**
 * Template Name: Cart Page
 * Template for BookNowShisha / BookMySmoke Cart
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div class="bns-cart-page-container bns-container">
    <div class="bns-cart-page-inner">
        <?php
        if (have_posts()) {
            while (have_posts()) :
                the_post();
                $content = get_the_content();
                if (empty(trim(strip_tags($content)))) {
                    echo do_shortcode('[woocommerce_cart]');
                } else {
                    the_content();
                }
            endwhile;
        } else {
            echo do_shortcode('[woocommerce_cart]');
        }
        ?>
    </div>
</div>

<?php get_footer();
