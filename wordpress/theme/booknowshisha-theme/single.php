<?php
/**
 * Single Post Template for ShishaRent Theme
 * Renders individual blog post articles
 *
 * @package ShishaRent
 */

get_header();

while (have_posts()) : the_post();
    $post_id      = get_the_ID();
    $img_url      = bns_get_post_image_url($post_id, 'full');
    $cats         = get_the_category();
    $first_cat    = !empty($cats) ? $cats[0] : null;
    $reading_time = bns_get_reading_time($post_id);
?>

<!-- =========================================================================
     ARTICLE HEADER & BREADCRUMBS
     ========================================================================= -->
<article id="post-<?php the_ID(); ?>" <?php post_class('bns-single-article'); ?>>
    <header class="bns-article-header">
        <div class="bns-container bns-article-container">
            
            <!-- Breadcrumbs -->
            <nav class="bns-breadcrumbs" aria-label="Breadcrumbs">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'shisharent'); ?></a>
                <span class="bns-breadcrumb-sep">/</span>
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog/')); ?>"><?php esc_html_e('Journal', 'shisharent'); ?></a>
                <?php if ($first_cat) : ?>
                    <span class="bns-breadcrumb-sep">/</span>
                    <a href="<?php echo esc_url(get_category_link($first_cat->term_id)); ?>"><?php echo esc_html($first_cat->name); ?></a>
                <?php endif; ?>
            </nav>

            <?php if ($first_cat) : ?>
                <a href="<?php echo esc_url(get_category_link($first_cat->term_id)); ?>" class="bns-article-cat-badge">
                    <?php echo esc_html($first_cat->name); ?>
                </a>
            <?php endif; ?>

            <h1 class="bns-article-title"><?php the_title(); ?></h1>

            <div class="bns-article-meta-bar">
                <div class="bns-author-info">
                    <span class="bns-author-avatar" style="display:inline-flex;align-items:center;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </span>
                    <span class="bns-author-name"><?php echo esc_html(get_the_author() ?: 'ShishaRent Editorial Team'); ?></span>
                </div>
                <span class="bns-meta-dot">•</span>
                <span class="bns-meta-date" style="display:inline-flex;align-items:center;gap:4px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <?php echo get_the_date('F j, Y'); ?>
                </span>
                <span class="bns-meta-dot">•</span>
                <span class="bns-meta-reading" style="display:inline-flex;align-items:center;gap:4px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <?php echo esc_html($reading_time); ?>
                </span>
            </div>

        </div>
    </header>

    <!-- Featured Image Banner -->
    <div class="bns-container bns-article-container">
        <div class="bns-article-featured-media">
            <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>" />
        </div>
    </div>

    <!-- Main Article Content -->
    <div class="bns-container bns-article-container">
        <div class="bns-article-layout">
            
            <div class="bns-article-body">
                <?php the_content(); ?>

                <!-- Article Tags -->
                <?php
                $post_tags = get_the_tags();
                if (!empty($post_tags)) :
                ?>
                <div class="bns-article-tags-box" style="margin-top: 28px; padding-top: 18px; border-top: 1px solid var(--bns-border-card, #e2e8f0); display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
                    <strong style="font-size: 0.85rem; color: var(--bns-text-muted, #64748b);"><?php esc_html_e('Tags:', 'shisharent'); ?></strong>
                    <?php foreach ($post_tags as $tag) : ?>
                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="bns-tag-pill" style="font-size: 0.78rem; padding: 4px 10px; border-radius: 6px; background: rgba(184,134,59,0.08); color: var(--bns-accent-gold, #b8863b); border: 1px solid rgba(184,134,59,0.22); text-decoration: none;">
                            #<?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Back to Blog & Social Share Bar -->
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-top: 30px; padding: 16px 20px; background: var(--bns-bg-surface, #f8fafc); border-radius: 8px; border: 1px solid var(--bns-border-card, #e2e8f0);">
                    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog/')); ?>" class="bns-btn-outline bns-btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                        ← <?php esc_html_e('Back to All Articles', 'shisharent'); ?>
                    </a>

                    <div class="bns-share-buttons" style="display: flex; gap: 8px;">
                        <a href="https://wa.me/?text=<?php echo rawurlencode(get_the_title() . ' - ' . get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="bns-share-btn bns-share-wa">
                            WhatsApp
                        </a>
                        <a href="https://t.me/share/url?url=<?php echo rawurlencode(get_permalink()); ?>&text=<?php echo rawurlencode(get_the_title()); ?>" target="_blank" rel="noopener noreferrer" class="bns-share-btn bns-share-tg">
                            Telegram
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=<?php echo rawurlencode(get_the_title()); ?>&url=<?php echo rawurlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="bns-share-btn bns-share-tw">
                            X
                        </a>
                    </div>
                </div>

                <!-- Author Bio Card -->
                <div class="bns-author-bio-card">
                    <div class="bns-author-bio-avatar" style="display:flex;align-items:center;justify-content:center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 1 0-16 0"></path></svg>
                    </div>
                    <div class="bns-author-bio-text">
                        <h4><?php esc_html_e('ShishaRent Editorial Team', 'shisharent'); ?></h4>
                        <p><?php esc_html_e('Curating premium hookah experiences, hygiene standards, mixology recipes, and luxury party hospitality across Kolkata, North 24 Parganas, and South 24 Parganas.', 'shisharent'); ?></p>
                    </div>
                </div>

                <!-- Article Health & Safety Notice -->
                <div class="bns-health-disclaimer" style="margin-top: 32px;">
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

        </div>
    </div>
</article>

<!-- =========================================================================
     RELATED ARTICLES SECTION
     ========================================================================= -->
<?php
$related_args = [
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post__not_in'   => [$post_id],
    'orderby'        => 'rand',
];

if ($first_cat) {
    $related_args['cat'] = $first_cat->term_id;
}

$related_query = new WP_Query($related_args);

if ($related_query->have_posts()) :
?>
<section class="bns-section bns-related-posts-section">
    <div class="bns-container">
        <div class="bns-section-header bns-text-center">
            <span class="bns-section-subtitle"><?php esc_html_e('EXPLORE MORE', 'shisharent'); ?></span>
            <h2 class="bns-section-title"><?php esc_html_e('RELATED JOURNAL ARTICLES', 'shisharent'); ?></h2>
        </div>

        <div class="bns-blog-cards-grid">
            <?php while ($related_query->have_posts()) : $related_query->the_post(); 
                $r_post_id = get_the_ID();
                $r_img_url = bns_get_post_image_url($r_post_id, 'large');
                $r_cats = get_the_category();
                $r_first_cat = !empty($r_cats) ? $r_cats[0]->name : 'Article';
                $r_reading = bns_get_reading_time($r_post_id);
            ?>
                <article class="bns-blog-card">
                    <div class="bns-blog-card-media">
                        <a href="<?php the_permalink(); ?>">
                            <img src="<?php echo esc_url($r_img_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
                        </a>
                        <span class="bns-blog-card-cat-badge"><?php echo esc_html($r_first_cat); ?></span>
                    </div>
                    <div class="bns-blog-card-body">
                        <div class="bns-blog-card-meta">
                            <span style="display:inline-flex;align-items:center;gap:4px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                <?php echo get_the_date('M j, Y'); ?>
                            </span>
                            <span style="display:inline-flex;align-items:center;gap:4px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <?php echo esc_html($r_reading); ?>
                            </span>
                        </div>
                        <h3 class="bns-blog-card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <p class="bns-blog-card-excerpt">
                            <?php echo wp_trim_words(get_the_excerpt(), 18, '...'); ?>
                        </p>
                        <div class="bns-blog-card-footer">
                            <a href="<?php the_permalink(); ?>" class="bns-blog-read-more">
                                <?php esc_html_e('Read Article', 'shisharent'); ?> →
                            </a>
                        </div>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
endwhile;
get_footer();
