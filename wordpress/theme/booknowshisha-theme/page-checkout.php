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
    <div class="bns-checkout-inner">
        <?php
        while (have_posts()) :
            the_post();
            the_content();
        endwhile;
        ?>
    </div>
</div>

<?php get_footer(); ?>
