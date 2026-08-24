<?php
/**
 * Template Name: Blog & Journal
 * Template for /blog/ page
 *
 * @package ShishaRent
 */

get_header();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$current_cat_slug = isset($_GET['cat_filter']) ? sanitize_text_field($_GET['cat_filter']) : '';

$blog_args = [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 9,
    'paged'          => $paged,
];

if (!empty($current_cat_slug) && $current_cat_slug !== 'all') {
    $blog_args['category_name'] = $current_cat_slug;
}

$blog_query = new WP_Query($blog_args);
$categories = get_categories(['hide_empty' => false]);
?>

<!-- =========================================================================
     BLOG HERO SECTION
     ========================================================================= -->
<section class="bns-service-hero">
    <div class="bns-service-hero-bg"></div>
    <div class="bns-container">
        <div class="bns-service-hero-content">
            <span class="bns-hero-badge">
                <span class="bns-pulse-dot"></span> <?php esc_html_e('THE SHISHARENT JOURNAL', 'shisharent'); ?>
            </span>
            <h1 class="bns-service-hero-title">
                FROM THE SHISHARENT<br>
                <span class="bns-text-gradient">JOURNAL</span>
            </h1>
            <p class="bns-service-hero-desc">
                <?php esc_html_e('Expert guides, flavour pairing formulas, medical hygiene standards, and party planning insights from Kolkata’s luxury hookah curators.', 'shisharent'); ?>
            </p>
        </div>
    </div>
</section>

<!-- =========================================================================
     BLOG CATEGORIES FILTER BAR
     ========================================================================= -->
<section class="bns-blog-filter-section">
    <div class="bns-container">
        <div class="bns-blog-categories-bar">
            <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="bns-blog-cat-pill <?php echo empty($current_cat_slug) ? 'active' : ''; ?>">
                <?php esc_html_e('All Articles', 'shisharent'); ?>
            </a>
            <?php foreach ($categories as $cat) : 
                $is_active = ($current_cat_slug === $cat->slug);
            ?>
                <a href="<?php echo esc_url(add_query_arg('cat_filter', $cat->slug, home_url('/blog/'))); ?>" class="bns-blog-cat-pill <?php echo $is_active ? 'active' : ''; ?>">
                    <?php echo esc_html($cat->name); ?> (<?php echo esc_html($cat->count); ?>)
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- =========================================================================
     BLOG POSTS GRID
     ========================================================================= -->
<section class="bns-section bns-blog-grid-section">
    <div class="bns-container">
        <?php if ($blog_query->have_posts()) : ?>
            <div class="bns-blog-cards-grid">
                <?php while ($blog_query->have_posts()) : $blog_query->the_post(); 
                    $post_id = get_the_ID();
                    $img_url = bns_get_post_image_url($post_id, 'large');
                    $cats = get_the_category();
                    $first_cat = !empty($cats) ? $cats[0]->name : 'Guide';
                    $reading_time = bns_get_reading_time($post_id);
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('bns-blog-card'); ?>>
                        <div class="bns-blog-card-media">
                            <a href="<?php the_permalink(); ?>">
                                <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
                            </a>
                            <span class="bns-blog-card-cat-badge"><?php echo esc_html($first_cat); ?></span>
                        </div>
                        <div class="bns-blog-card-body">
                            <div class="bns-blog-card-meta">
                                <span style="display:inline-flex;align-items:center;gap:4px;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <?php echo get_the_date('M j, Y'); ?>
                                </span>
                                <span style="display:inline-flex;align-items:center;gap:4px;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <?php echo esc_html($reading_time); ?>
                                </span>
                            </div>
                            <h3 class="bns-blog-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <p class="bns-blog-card-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 22, '...'); ?>
                            </p>
                            <div class="bns-blog-card-footer">
                                <a href="<?php the_permalink(); ?>" class="bns-blog-read-more">
                                    <?php esc_html_e('Read Full Article', 'shisharent'); ?> →
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <!-- Pagination -->
            <div class="bns-pagination-wrapper" style="margin-top: 40px; text-align: center;">
                <?php
                echo paginate_links([
                    'total'     => $blog_query->max_num_pages,
                    'current'   => $paged,
                    'prev_text' => __('‹ Previous', 'shisharent'),
                    'next_text' => __('Next ›', 'shisharent'),
                ]);
                ?>
            </div>

        <?php else : ?>
            <div class="bns-text-center" style="padding: 60px 0;">
                <h3 style="font-size: 1.25rem; margin-bottom: 8px;"><?php esc_html_e('New articles coming soon.', 'shisharent'); ?></h3>
                <p class="bns-text-muted"><?php esc_html_e('Our editorial team is preparing fresh hookah guides and flavour mixing recipes.', 'shisharent'); ?></p>
                <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="bns-btn-gold" style="margin-top: 16px;">
                    <?php esc_html_e('View All Articles', 'shisharent'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- =========================================================================
     HEALTH & SAFETY DISCLAIMER NOTICE
     ========================================================================= -->
<section class="bns-section-compact">
    <div class="bns-container">
        <div class="bns-health-disclaimer">
            <div class="bns-disclaimer-inner">
                <span class="bns-disclaimer-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </span>
                <div class="bns-disclaimer-text">
                    <strong class="bns-disclaimer-title"><?php esc_html_e('Health & Safety Notice', 'shisharent'); ?></strong>
                    <p><?php esc_html_e('Hookah, smoking, alcohol and other intoxicating substances can pose serious risks to health. We encourage responsible choices and do not promote or encourage the use of intoxicating substances. Please comply with all applicable laws and age restrictions.', 'shisharent'); ?></p>
                </div>
                <div class="bns-age-pill">21+ Strictly</div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
