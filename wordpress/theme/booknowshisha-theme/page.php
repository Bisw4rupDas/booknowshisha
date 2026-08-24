<?php
/**
 * Page template for BookNowShisha Theme
 *
 * @package BookNowShisha
 */

get_header(); ?>

<div class="bns-page-wrapper bns-container">
    <?php
    while (have_posts()) : the_post();
        ?>
        <article id="page-<?php the_ID(); ?>" <?php post_class('bns-page-card'); ?>>
            <header class="bns-page-header">
                <h1 class="bns-page-title"><?php the_title(); ?></h1>
            </header>
            <div class="bns-page-body">
                <?php the_content(); ?>
            </div>
        </article>
        <?php
    endwhile;
    ?>
</div>

<?php get_footer(); ?>
