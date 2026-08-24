<?php
/**
 * Main template file for BookNowShisha Theme
 *
 * @package BookNowShisha
 */

get_header(); ?>

<div class="bns-page-content bns-container">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('bns-card'); ?>>
                <header class="bns-entry-header">
                    <?php the_title('<h1 class="bns-entry-title">', '</h1>'); ?>
                </header>
                <div class="bns-entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
            <?php
        endwhile;
    else :
        ?>
        <p><?php esc_html_e('No content found.', 'booknowshisha'); ?></p>
        <?php
    endif;
    ?>
</div>

<?php get_footer(); ?>
