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
        while (have_posts()) :
            the_post();
            the_content();
        endwhile;
        ?>
    </div>
</div>

<?php get_footer(); ?>
