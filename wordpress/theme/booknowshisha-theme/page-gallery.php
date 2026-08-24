<?php
/**
 * Template Name: ShishaRent Gallery
 *
 * Premium Customer-Facing Gallery Page for ShishaRent Kolkata
 * Showcases handcrafted hookahs, luxury events, mixology, and accessories
 * with category filters and a full-screen accessible lightbox.
 *
 * @package ShishaRent
 */

get_header();

$gallery_images = ShishaRent_Gallery::get_gallery_images('all', -1);
$categories     = ShishaRent_Gallery::get_gallery_categories();
?>

<!-- =========================================================================
     GALLERY HERO SECTION
     ========================================================================= -->
<section class="bns-gallery-hero">
    <div class="bns-hero-bg-lines"></div>
    <div class="bns-gallery-hero-glow"></div>
    
    <div class="bns-container">
        <div class="bns-gallery-hero-content">
            <span class="bns-gallery-badge">
                <span class="bns-pulse-dot"></span> <?php esc_html_e('VISUAL SHOWCASE • KOLKATA', 'shisharent'); ?>
            </span>
            <h1 class="bns-gallery-title">
                SHISHARENT<br>
                <span class="bns-text-gradient">GALLERY</span>
            </h1>
            <p class="bns-gallery-subtitle">
                <?php esc_html_e('Explore the ShishaRent experience.', 'shisharent'); ?>
            </p>
            <div class="bns-gallery-meta-pills">
                <span class="bns-gallery-pill">✨ <?php echo count($gallery_images); ?> <?php esc_html_e('Curated Photographs', 'shisharent'); ?></span>
                <span class="bns-gallery-pill">📍 <?php esc_html_e('Kolkata & Suburbs', 'shisharent'); ?></span>
                <span class="bns-gallery-pill">💎 <?php esc_html_e('Dark Luxury Quality', 'shisharent'); ?></span>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     GALLERY MAIN SECTION & CATEGORY FILTER TABS
     ========================================================================= -->
<section class="bns-section bns-gallery-main-section" id="gallery-grid-section">
    <div class="bns-container">
        
        <!-- Category Filter Tabs Bar -->
        <div class="bns-gallery-filter-bar">
            <div class="bns-filter-tabs" role="tablist" aria-label="<?php esc_attr_e('Filter Gallery by Category', 'shisharent'); ?>">
                <button type="button" class="bns-filter-tab active" data-category="all" role="tab" aria-selected="true">
                    <span><?php esc_html_e('ALL', 'shisharent'); ?></span>
                    <span class="bns-tab-count"><?php echo count($gallery_images); ?></span>
                </button>
                <button type="button" class="bns-filter-tab" data-category="hookahs" role="tab" aria-selected="false">
                    <span><?php esc_html_e('HOOKAHS', 'shisharent'); ?></span>
                    <span class="bns-tab-count"><?php echo $categories['hookahs']['count'] ?? 0; ?></span>
                </button>
                <button type="button" class="bns-filter-tab" data-category="events" role="tab" aria-selected="false">
                    <span><?php esc_html_e('EVENTS & CATERING', 'shisharent'); ?></span>
                    <span class="bns-tab-count"><?php echo $categories['events']['count'] ?? 0; ?></span>
                </button>
                <button type="button" class="bns-filter-tab" data-category="flavours" role="tab" aria-selected="false">
                    <span><?php esc_html_e('FLAVOURS & MIXOLOGY', 'shisharent'); ?></span>
                    <span class="bns-tab-count"><?php echo $categories['flavours']['count'] ?? 0; ?></span>
                </button>
                <button type="button" class="bns-filter-tab" data-category="accessories" role="tab" aria-selected="false">
                    <span><?php esc_html_e('ACCESSORIES', 'shisharent'); ?></span>
                    <span class="bns-tab-count"><?php echo $categories['accessories']['count'] ?? 0; ?></span>
                </button>
            </div>
        </div>

        <!-- Gallery Grid Layout (Masonry / Balanced Responsive Grid) -->
        <div class="bns-gallery-grid" id="bns-main-gallery-grid">
            <?php if (!empty($gallery_images)) : ?>
                <?php foreach ($gallery_images as $index => $image) : 
                    $is_eager = ($index < 8);
                ?>
                    <div class="bns-gallery-item" 
                         data-category="<?php echo esc_attr($image['category']); ?>" 
                         data-index="<?php echo esc_attr($index); ?>"
                         data-id="<?php echo esc_attr($image['id']); ?>"
                         data-full-src="<?php echo esc_url($image['full_url']); ?>"
                         data-large-src="<?php echo esc_url($image['large_url']); ?>"
                         data-title="<?php echo esc_attr($image['title']); ?>"
                         data-alt="<?php echo esc_attr($image['alt']); ?>"
                         data-cat-name="<?php echo esc_attr($image['cat_name']); ?>"
                         data-orientation="<?php echo esc_attr($image['orientation']); ?>">
                        
                        <div class="bns-gallery-card">
                            <div class="bns-gallery-img-wrap">
                                <?php 
                                echo wp_get_attachment_image(
                                    $image['id'],
                                    'large',
                                    false,
                                    [
                                        'class'   => 'bns-gallery-img',
                                        'alt'     => esc_attr($image['alt']),
                                        'loading' => $is_eager ? 'eager' : 'lazy',
                                    ]
                                ); 
                                ?>
                                <div class="bns-gallery-item-overlay">
                                    <div class="bns-gallery-item-info">
                                        <span class="bns-gallery-cat-tag"><?php echo esc_html($image['cat_name']); ?></span>
                                        <h3 class="bns-gallery-item-title"><?php echo esc_html($image['title']); ?></h3>
                                    </div>
                                    <span class="bns-gallery-zoom-icon" aria-hidden="true">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                            <line x1="11" y1="8" x2="11" y2="14"></line>
                                            <line x1="8" y1="11" x2="14" y2="11"></line>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="bns-gallery-empty">
                    <p><?php esc_html_e('Gallery images are loading. Please check back shortly.', 'shisharent'); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bottom CTA -->
        <div class="bns-gallery-cta-block">
            <div class="bns-gallery-cta-inner">
                <span class="bns-tier-badge bns-badge-gold"><?php esc_html_e('EXPERIENCE IT LIVE', 'shisharent'); ?></span>
                <h3 class="bns-gallery-cta-title"><?php esc_html_e('Ready to Elevate Your Evening in Kolkata?', 'shisharent'); ?></h3>
                <p class="bns-gallery-cta-desc">
                    <?php esc_html_e('All-inclusive luxury rental packages starting from ₹1,499 / 24h with express doorstep delivery, medical-grade hygiene, and white-glove setup across Kolkata, Salt Lake, and New Town.', 'shisharent'); ?>
                </p>
                <div class="bns-hero-cta-group" style="justify-content: center;">
                    <a href="<?php echo esc_url(home_url('/#packages')); ?>" class="bns-btn-gold bns-btn-lg bns-glow-btn">
                        <?php esc_html_e('Rent a Hookah Now (From ₹1,499)', 'shisharent'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/bartending-party-services/')); ?>" class="bns-btn-outline bns-btn-lg">
                        <?php esc_html_e('Event & Party Services', 'shisharent'); ?>
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- =========================================================================
     FULL-SCREEN IMAGE LIGHTBOX MODAL
     ========================================================================= -->
<div id="bns-gallery-lightbox" class="bns-lightbox" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Image Lightbox', 'shisharent'); ?>" style="display: none;">
    <div class="bns-lightbox-backdrop" id="bns-lightbox-backdrop"></div>
    
    <!-- Lightbox Controls Top Bar -->
    <div class="bns-lightbox-topbar">
        <div class="bns-lightbox-counter" id="bns-lightbox-counter">
            <span id="bns-lb-current">1</span> / <span id="bns-lb-total">100</span>
        </div>
        <button type="button" class="bns-lightbox-btn bns-lightbox-close" id="bns-lightbox-close" aria-label="<?php esc_attr_e('Close Lightbox (Esc)', 'shisharent'); ?>" title="<?php esc_attr_e('Close (Esc)', 'shisharent'); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- Main Lightbox Image Stage -->
    <div class="bns-lightbox-stage" id="bns-lightbox-stage">
        <!-- Prev Button -->
        <button type="button" class="bns-lightbox-nav bns-lightbox-prev" id="bns-lightbox-prev" aria-label="<?php esc_attr_e('Previous Image (Left Arrow)', 'shisharent'); ?>" title="<?php esc_attr_e('Previous (Left Arrow)', 'shisharent'); ?>">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>

        <!-- Image Container -->
        <div class="bns-lightbox-img-container" id="bns-lightbox-img-container">
            <div class="bns-lightbox-spinner" id="bns-lightbox-spinner"></div>
            <img src="" alt="" class="bns-lightbox-img" id="bns-lightbox-img" />
        </div>

        <!-- Next Button -->
        <button type="button" class="bns-lightbox-nav bns-lightbox-next" id="bns-lightbox-next" aria-label="<?php esc_attr_e('Next Image (Right Arrow)', 'shisharent'); ?>" title="<?php esc_attr_e('Next (Right Arrow)', 'shisharent'); ?>">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>

    <!-- Lightbox Caption Bottom Bar -->
    <div class="bns-lightbox-bottombar">
        <div class="bns-lightbox-caption-wrap">
            <span class="bns-lightbox-category" id="bns-lightbox-category">Hookahs</span>
            <h4 class="bns-lightbox-title" id="bns-lightbox-title">ShishaRent Handcrafted Hookah</h4>
        </div>
    </div>
</div>

<?php
get_footer();
