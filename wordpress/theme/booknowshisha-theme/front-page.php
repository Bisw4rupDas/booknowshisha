<?php
/**
 * Front Page Template for ShishaRent Theme
 * Recreates the premium luxury lounge aesthetic (Kolkata Edition)
 *
 * @package ShishaRent
 */

get_header();

// Fetch dynamic BMS flavour products from WooCommerce
$bms_flavours_home = wc_get_products([
    'limit'    => 8,
    'status'   => 'publish',
    'orderby'  => 'menu_order title',
    'order'    => 'ASC',
]);
?>

<!-- =========================================================================
     SECTION 2: HERO SECTION
     ========================================================================= -->
<section class="bns-hero-section" id="hero">
    <!-- Atmospheric Background Lights & Geometric Glow -->
    <div class="bns-hero-bg-lines"></div>
    <div class="bns-hero-radial-glow"></div>

    <div class="bns-container bns-hero-container">
        <div class="bns-hero-grid">
            
            <!-- Left Hero Column: Headline & Primary CTA -->
            <div class="bns-hero-content">
                <span class="bns-hero-badge">
                    <span class="bns-pulse-dot"></span> <?php esc_html_e('PREMIUM HOOKAH LOUNGE AT HOME • KOLKATA', 'shisharent'); ?>
                </span>
                <h1 class="bns-hero-title">
                    HOOKAHS<br>
                    <span class="bns-text-gradient">SMOKERS LOUNGE</span>
                </h1>
                <p class="bns-hero-subtitle">
                    <?php esc_html_e('Curated handcrafted hookahs, premium organic charcoal, and authentic SR international molasses delivered to your door in 60-90 minutes across Kolkata & surrounding areas.', 'shisharent'); ?>
                </p>
                <div class="bns-hero-cta-group">
                    <a href="#rentals" class="bns-btn-gold bns-btn-lg bns-glow-btn">
                        <?php esc_html_e('Rent a Hookah / Chilam', 'shisharent'); ?>
                    </a>
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop')) ?: home_url('/shop/')); ?>" class="bns-btn-outline bns-btn-lg">
                        <?php esc_html_e('Explore 23 SR Flavours', 'shisharent'); ?>
                    </a>
                </div>

                <!-- Scroll Down Indicator -->
                <div class="bns-scroll-down">
                    <a href="#about" class="bns-scroll-link">
                        <span class="bns-scroll-line"></span>
                        <span class="bns-scroll-arrow">↓</span>
                    </a>
                </div>
            </div>

            <!-- Center Hero Column: Minimalist Atmospheric Smoke Art -->
            <div class="bns-hero-centerpiece">
                <div class="bns-smoke-artwork-container" aria-hidden="true">
                    <div class="bns-smoke-ambient-glow"></div>
                    <svg class="bns-smoke-svg" viewBox="0 0 500 650" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="bnsSmokeGrad1" x1="250" y1="650" x2="250" y2="50" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#ffffff" stop-opacity="0" />
                                <stop offset="30%" stop-color="#f8fafc" stop-opacity="0.18" />
                                <stop offset="65%" stop-color="#e2e8f0" stop-opacity="0.10" />
                                <stop offset="100%" stop-color="#d4a95f" stop-opacity="0" />
                            </linearGradient>
                            <linearGradient id="bnsSmokeGrad2" x1="200" y1="600" x2="300" y2="0" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#d4a95f" stop-opacity="0" />
                                <stop offset="40%" stop-color="#ffffff" stop-opacity="0.14" />
                                <stop offset="80%" stop-color="#f8fafc" stop-opacity="0.06" />
                                <stop offset="100%" stop-color="#ffffff" stop-opacity="0" />
                            </linearGradient>
                            <linearGradient id="bnsSmokeGrad3" x1="280" y1="580" x2="180" y2="20" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#ffffff" stop-opacity="0" />
                                <stop offset="50%" stop-color="#f5cf87" stop-opacity="0.10" />
                                <stop offset="85%" stop-color="#e2e8f0" stop-opacity="0.05" />
                                <stop offset="100%" stop-color="#ffffff" stop-opacity="0" />
                            </linearGradient>
                            <filter id="bnsSmokeBlur" x="-20%" y="-20%" width="140%" height="140%">
                                <feGaussianBlur stdDeviation="14" />
                            </filter>
                            <filter id="bnsSmokeSoftBlur" x="-20%" y="-20%" width="140%" height="140%">
                                <feGaussianBlur stdDeviation="7" />
                            </filter>
                        </defs>
                        <!-- Smoke Wisp Layer 1 (Rising central plume) -->
                        <path class="bns-smoke-wisp bns-wisp-1" filter="url(#bnsSmokeBlur)"
                              d="M250 620 C230 520, 290 440, 240 350 C190 260, 280 180, 230 80 C205 30, 240 10, 250 0 C260 10, 295 30, 270 80 C220 180, 310 260, 260 350 C210 440, 270 520, 250 620 Z" 
                              fill="url(#bnsSmokeGrad1)" />
                        <!-- Smoke Wisp Layer 2 (Drifting left curl) -->
                        <path class="bns-smoke-wisp bns-wisp-2" filter="url(#bnsSmokeSoftBlur)"
                              d="M245 590 C180 500, 210 410, 160 320 C110 230, 220 150, 175 60 C150 15, 190 5, 205 0 C220 5, 260 15, 235 60 C190 150, 300 230, 250 320 C200 410, 230 500, 245 590 Z" 
                              fill="url(#bnsSmokeGrad2)" />
                        <!-- Smoke Wisp Layer 3 (Drifting right curl with subtle gold aura) -->
                        <path class="bns-smoke-wisp bns-wisp-3" filter="url(#bnsSmokeSoftBlur)"
                              d="M255 580 C320 490, 280 400, 340 310 C400 220, 290 140, 335 50 C360 10, 320 0, 305 0 C290 0, 250 10, 275 50 C320 140, 210 220, 270 310 C330 400, 290 490, 255 580 Z" 
                              fill="url(#bnsSmokeGrad3)" />
                    </svg>
                </div>
            </div>

            <!-- Right Hero Column: Product Spotlight & Social Links -->
            <div class="bns-hero-sidebar">
                <div class="bns-spotlight-card">
                    <span class="bns-spotlight-tag"><?php esc_html_e('FEATURED FLAVOUR', 'shisharent'); ?></span>
                    <h3 class="bns-spotlight-title"><?php esc_html_e('SR Chief Commissioner', 'shisharent'); ?></h3>
                    <p class="bns-spotlight-text">
                        <?php esc_html_e('Royal Kashmiri saffron, edible silver vark foil, and aged betel paan essence for Kolkata connoisseurs.', 'shisharent'); ?>
                    </p>
                    <a href="<?php echo esc_url(home_url('/flavour-selection/?rental=' . urlencode('SR SPECIAL HOOKAH'))); ?>" class="bns-spotlight-more">
                        <?php esc_html_e('Pair with Hookah Rental', 'shisharent'); ?> →
                    </a>
                    <div class="bns-spotlight-price-box">
                        <span class="bns-price-prefix"><?php esc_html_e('Price', 'shisharent'); ?></span>
                        <div class="bns-spotlight-price">₹650.00 <span class="bns-price-term">/ head</span></div>
                    </div>
                </div>

                <!-- Social Media Icons -->
                <div class="bns-hero-socials">
                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="bns-hero-social-link" title="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                    </a>
                    <a href="https://wa.me/919903556825" target="_blank" rel="noopener noreferrer" class="bns-hero-social-link" title="WhatsApp: +91 99035 56825">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    </a>
                    <a href="https://t.me" target="_blank" rel="noopener noreferrer" class="bns-hero-social-link" title="Telegram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- =========================================================================
     SECTION 3: ABOUT / BRAND SECTION
     ========================================================================= -->
<section class="bns-section bns-about-section" id="about">
    <div class="bns-container">
        <div class="bns-about-grid">
            
            <!-- Left Text Column -->
            <div class="bns-about-text-col">
                <span class="bns-section-subtitle"><?php esc_html_e('THE SHISHARENT EXPERIENCE', 'shisharent'); ?></span>
                <h2 class="bns-section-title"><?php esc_html_e('ABOUT US', 'shisharent'); ?></h2>
                <div class="bns-about-paragraphs">
                    <p>
                        <?php esc_html_e('We are Kolkata’s premier on-demand shisha rental boutique, operating from our twin hubs in Ballygunge and Park Street Chaurangi More to bring authentic five-star lounge setups directly to private residences, penthouses, and events across Kolkata, Salt Lake, and New Town.', 'shisharent'); ?>
                    </p>
                    <p>
                        <?php esc_html_e('Every hookah is ultrasonically sanitized, equipped with medical-grade silicone hoses, high-heat phunnel bowls, and premium natural coconut coals paired with 23 authentic SR flavours.', 'shisharent'); ?>
                    </p>
                </div>

                <div class="bns-about-features-grid">
                    <div class="bns-feature-item">
                        <div class="bns-feature-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        </div>
                        <div>
                            <strong><?php esc_html_e('100% Medical Grade Hygiene', 'shisharent'); ?></strong>
                            <p><?php esc_html_e('Sterilized after each session with sealed single-use mouthpieces.', 'shisharent'); ?></p>
                        </div>
                    </div>
                    <div class="bns-feature-item">
                        <div class="bns-feature-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        </div>
                        <div>
                            <strong><?php esc_html_e('60-90 Min Express Delivery', 'shisharent'); ?></strong>
                            <p><?php esc_html_e('Same-day doorstep delivery & white-glove setup service in Kolkata.', 'shisharent'); ?></p>
                        </div>
                    </div>
                    <div class="bns-feature-item">
                        <div class="bns-feature-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                        <div>
                            <strong><?php esc_html_e('Zero Cleaning Required', 'shisharent'); ?></strong>
                            <p><?php esc_html_e('Enjoy your session without worry. We collect and clean everything.', 'shisharent'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Image Grid -->
            <div class="bns-about-images-col">
                <div class="bns-about-gallery">
                    <div class="bns-gallery-card bns-gallery-card-1">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>" 
                             alt="<?php esc_attr_e('Lounge Gathering Experience', 'shisharent'); ?>" style="padding: 20px; background:#0e121a;" />
                        <div class="bns-gallery-overlay">
                            <span><?php esc_html_e('Social Lounge Atmosphere', 'shisharent'); ?></span>
                        </div>
                    </div>
                    <div class="bns-gallery-card bns-gallery-card-2">
                        <img src="<?php echo esc_url(home_url('/wp-content/uploads/flavours/bms-bms-zafraan-paan.jpeg')); ?>" 
                             alt="<?php esc_attr_e('SR Zafraan Paan', 'shisharent'); ?>" />
                        <div class="bns-gallery-overlay">
                            <span><?php esc_html_e('Kashmiri Saffron & Paan', 'shisharent'); ?></span>
                        </div>
                    </div>
                    <div class="bns-gallery-card bns-gallery-card-3">
                        <img src="<?php echo esc_url(home_url('/wp-content/uploads/flavours/bms-bms-chief-commissioner.jpeg')); ?>" 
                             alt="<?php esc_attr_e('SR Chief Commissioner', 'shisharent'); ?>" />
                        <div class="bns-gallery-overlay">
                            <span><?php esc_html_e('Royal Silver Vark Flavour', 'shisharent'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="bns-gallery-nav">
                    <a href="#rentals" class="bns-circle-arrow-btn" title="<?php esc_attr_e('View Rentals', 'shisharent'); ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- =========================================================================
     SECTION 4: HOMEPAGE RENTAL OPTIONS (CHILAM RENTALS & HOOKAH RENTALS)
     ========================================================================= -->
<?php
$rental_catalog = function_exists('bns_get_rental_packages') ? bns_get_rental_packages() : [];
?>
<section class="bns-section bns-rental-options-section" id="rentals">
    <div class="bns-container">
        <div class="bns-section-header bns-text-center">
            <span class="bns-section-subtitle"><?php esc_html_e('STEP 1 OF 2 • CHOOSE YOUR RENTAL PACKAGE', 'shisharent'); ?></span>
            <h2 class="bns-section-title"><?php esc_html_e('LUXURY RENTAL COLLECTION', 'shisharent'); ?></h2>
            <p class="bns-section-desc">
                <?php esc_html_e('Select your preferred hookah pipe or pre-packed chilam setup below to begin. Clicking any rental opens the curated 23 SR flavour selection.', 'shisharent'); ?>
            </p>
        </div>

        <?php if (!empty($rental_catalog)) : ?>
            <!-- 1. CHILAM RENTALS CATEGORY (Ascending: Basic -> Regular -> Priyam -> Special) -->
            <div class="bns-rental-category-group">
                <div class="bns-rental-group-header">
                    <div class="bns-group-meta">
                        <span class="bns-group-badge"><?php esc_html_e('COLLECTION 01', 'shisharent'); ?></span>
                        <h3 class="bns-group-title"><?php esc_html_e('CHILAM RENTALS', 'shisharent'); ?></h3>
                    </div>
                    <p class="bns-group-desc"><?php esc_html_e('Pre-packed artisanal phunnel bowls with heat management and 100% natural coconut charcoal in Kolkata.', 'shisharent'); ?></p>
                </div>

                <div class="bns-rental-options-grid bns-grid-4">
                    <?php foreach ($rental_catalog['chilam']['items'] as $item) : ?>
                        <div class="bns-rental-card bns-rental-tier-<?php echo esc_attr(strtolower($item['tier'])); ?>">
                            <div class="bns-rental-card-top">
                                <span class="bns-rental-tier-pill">[ <?php echo esc_html($item['tier']); ?> ]</span>
                                <span class="bns-rental-price-tag"><?php echo esc_html($item['price_fmt']); ?></span>
                            </div>
                            
                            <div class="bns-rental-card-media">
                                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/rentals/' . $item['image']); ?>" 
                                     alt="<?php echo esc_attr($item['title']); ?>" 
                                     class="bns-rental-card-img" loading="lazy" />
                            </div>
                            
                            <div class="bns-rental-card-body">
                                <span class="bns-rental-tagline"><?php echo esc_html($item['tagline']); ?></span>
                                <h4 class="bns-rental-card-title"><?php echo esc_html($item['title']); ?></h4>
                                <p class="bns-rental-card-text"><?php echo esc_html($item['description']); ?></p>
                                <div class="bns-rental-specs-line"><?php echo esc_html($item['specs']); ?></div>
                                
                                <a href="<?php echo esc_url(home_url('/flavour-selection/?rental=' . urlencode($item['title']))); ?>" class="bns-btn-rental-cta">
                                    <span><?php esc_html_e('SELECT FLAVOUR', 'shisharent'); ?></span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 2. HOOKAH RENTALS CATEGORY (Ascending: Basic -> Regular -> Priyam -> Special -> Combo) -->
            <div class="bns-rental-category-group" style="margin-top: 80px;">
                <div class="bns-rental-group-header">
                    <div class="bns-group-meta">
                        <span class="bns-group-badge"><?php esc_html_e('COLLECTION 02', 'shisharent'); ?></span>
                        <h3 class="bns-group-title"><?php esc_html_e('HOOKAH RENTALS', 'shisharent'); ?></h3>
                    </div>
                    <p class="bns-group-desc"><?php esc_html_e('Full complete hookah pipe rentals with sanitized pipes, hoses, coals, and doorstep setup in Kolkata.', 'shisharent'); ?></p>
                </div>

                <div class="bns-rental-options-grid bns-grid-5">
                    <?php foreach ($rental_catalog['hookah']['items'] as $item) : ?>
                        <div class="bns-rental-card bns-rental-tier-<?php echo esc_attr(strtolower($item['tier'])); ?>">
                            <div class="bns-rental-card-top">
                                <span class="bns-rental-tier-pill">[ <?php echo esc_html($item['tier']); ?> ]</span>
                                <span class="bns-rental-price-tag"><?php echo esc_html($item['price_fmt']); ?></span>
                            </div>
                            
                            <div class="bns-rental-card-media">
                                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/rentals/' . $item['image']); ?>" 
                                     alt="<?php echo esc_attr($item['title']); ?>" 
                                     class="bns-rental-card-img" loading="lazy" />
                            </div>
                            
                            <div class="bns-rental-card-body">
                                <span class="bns-rental-tagline"><?php echo esc_html($item['tagline']); ?></span>
                                <h4 class="bns-rental-card-title"><?php echo esc_html($item['title']); ?></h4>
                                <p class="bns-rental-card-text"><?php echo esc_html($item['description']); ?></p>
                                <div class="bns-rental-specs-line"><?php echo esc_html($item['specs']); ?></div>
                                
                                <a href="<?php echo esc_url(home_url('/flavour-selection/?rental=' . urlencode($item['title']))); ?>" class="bns-btn-rental-cta">
                                    <span><?php esc_html_e('SELECT FLAVOUR', 'shisharent'); ?></span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

    </div>
</section>

<!-- =========================================================================
     SECTION 6 & 7: 23 SR ARTISANAL FLAVOURS SHOWCASE
     ========================================================================= -->
<section class="bns-section bns-flavours-section" id="flavours">
    <div class="bns-container">
        <div class="bns-section-header bns-text-center">
            <span class="bns-section-subtitle"><?php esc_html_e('GENUINE SR FLAVOUR CATALOG', 'shisharent'); ?></span>
            <h2 class="bns-section-title"><?php esc_html_e('23 ARTISANAL SR FLAVOURS', 'shisharent'); ?></h2>
            <p class="bns-section-desc">
                <?php esc_html_e('Hand-blended artisanal shisha molasses crafted for ultra-thick clouds, rich taste, and long-lasting session duration in Kolkata.', 'shisharent'); ?>
            </p>
        </div>

        <div class="bns-flavour-cards-grid">
            <?php if (!empty($bms_flavours_home)) : ?>
                <?php foreach ($bms_flavours_home as $flv) : 
                    $f_id = $flv->get_id();
                    $f_name = $flv->get_name();
                    $f_price = $flv->get_price();
                    $f_img_id = $flv->get_image_id();
                    $f_img_url = $f_img_id ? wp_get_attachment_image_url($f_img_id, 'medium') : '';
                    $f_desc = $flv->get_short_description();
                    ?>
                    <div class="bns-flavour-pill-card">
                        <div class="bns-flavour-pill-img-box">
                            <?php if ($f_img_url) : ?>
                                <img src="<?php echo esc_url($f_img_url); ?>" alt="<?php echo esc_attr($f_name); ?>" class="bns-flavour-thumb" loading="lazy" />
                            <?php endif; ?>
                        </div>
                        <div class="bns-flavour-pill-info">
                            <h4><?php echo esc_html($f_name); ?></h4>
                            <span class="bns-brand-tag">SR • ₹<?php echo esc_html(number_format((float)$f_price, 2)); ?></span>
                            <p><?php echo esc_html(wp_strip_all_tags($f_desc)); ?></p>
                            <a href="<?php echo esc_url(home_url('/flavour-selection/')); ?>" class="bns-gold-link" style="font-size: 0.8rem; margin-top: 6px; display: inline-block;">
                                <?php esc_html_e('Select in Rental', 'shisharent'); ?> →
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="bns-text-center" style="margin-top: 40px;">
            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop')) ?: home_url('/shop/')); ?>" class="bns-btn-gold bns-btn-lg bns-glow-btn">
                <?php esc_html_e('VIEW ALL 23 FLAVOURS IN SHOP', 'shisharent'); ?> →
            </a>
        </div>
    </div>
</section>

<!-- =========================================================================
     SECTION 8: HOW IT WORKS
     ========================================================================= -->
<section class="bns-section bns-how-section" id="how-it-works">
    <div class="bns-container">
        <div class="bns-section-header bns-text-center">
            <span class="bns-section-subtitle"><?php esc_html_e('SEAMLESS 4-STEP PROCESS', 'shisharent'); ?></span>
            <h2 class="bns-section-title"><?php esc_html_e('HOW IT WORKS', 'shisharent'); ?></h2>
            <p class="bns-section-desc">
                <?php esc_html_e('Renting a five-star hookah in Kolkata has never been easier.', 'shisharent'); ?>
            </p>
        </div>

        <div class="bns-steps-grid">
            
            <div class="bns-step-card">
                <div class="bns-step-num">01</div>
                <div class="bns-step-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                </div>
                <h4 class="bns-step-title"><?php esc_html_e('1. Choose Rental Setup', 'shisharent'); ?></h4>
                <p class="bns-step-desc"><?php esc_html_e('Select from our 9 signature hookah & chilam rental options right on the home page.', 'shisharent'); ?></p>
            </div>

            <div class="bns-step-card">
                <div class="bns-step-num">02</div>
                <div class="bns-step-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <h4 class="bns-step-title"><?php esc_html_e('2. Select SR Flavour', 'shisharent'); ?></h4>
                <p class="bns-step-desc"><?php esc_html_e('Pick from 23 handcrafted SR flavours including Chief Commissioner, Zafraan Paan, and Blueberry Blast.', 'shisharent'); ?></p>
            </div>

            <div class="bns-step-card">
                <div class="bns-step-num">03</div>
                <div class="bns-step-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <h4 class="bns-step-title"><?php esc_html_e('3. Express 60-90m Delivery', 'shisharent'); ?></h4>
                <p class="bns-step-desc"><?php esc_html_e('Our delivery concierge delivers and sets up your gear anywhere in Kolkata.', 'shisharent'); ?></p>
            </div>

            <div class="bns-step-card">
                <div class="bns-step-num">04</div>
                <div class="bns-step-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><path d="M20 6L9 17l-5-5"></path></svg>
                </div>
                <h4 class="bns-step-title"><?php esc_html_e('4. Zero Cleaning Pickup', 'shisharent'); ?></h4>
                <p class="bns-step-desc"><?php esc_html_e('Enjoy your session. When done, our team handles pickup and deep sterilization.', 'shisharent'); ?></p>
            </div>

        </div>
    </div>
</section>

<!-- =========================================================================
     SECTION 8.5: HOMEPAGE GALLERY PREVIEW
     ========================================================================= -->
<?php
$home_preview_images = class_exists('ShishaRent_Gallery') ? ShishaRent_Gallery::get_homepage_preview_images(6) : [];
?>
<section class="bns-section bns-home-gallery-preview-section" id="gallery-preview">
    <div class="bns-container">
        <div class="bns-section-header bns-text-center">
            <span class="bns-section-subtitle"><?php esc_html_e('VISUAL SHOWCASE', 'shisharent'); ?></span>
            <h2 class="bns-section-title"><?php esc_html_e('OUR GALLERY', 'shisharent'); ?></h2>
            <p class="bns-section-desc">
                <?php esc_html_e('Explore the ShishaRent experience.', 'shisharent'); ?>
            </p>
        </div>

        <?php if (!empty($home_preview_images)) : ?>
            <div class="bns-home-gallery-grid">
                <?php foreach ($home_preview_images as $idx => $pimg) : ?>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('gallery')) ?: home_url('/gallery/')); ?>" class="bns-home-gallery-card" title="<?php echo esc_attr($pimg['title']); ?>">
                        <div class="bns-home-gallery-img-wrap">
                            <?php 
                            echo wp_get_attachment_image(
                                $pimg['id'],
                                'large',
                                false,
                                [
                                    'class'   => 'bns-home-gallery-img',
                                    'alt'     => esc_attr($pimg['alt']),
                                    'loading' => 'lazy',
                                ]
                            );
                            ?>
                            <div class="bns-home-gallery-overlay">
                                <span class="bns-home-gallery-tag"><?php echo esc_html($pimg['cat_name']); ?></span>
                                <strong class="bns-home-gallery-title"><?php echo esc_html($pimg['title']); ?></strong>
                                <span class="bns-home-gallery-arrow"><?php esc_html_e('View in Gallery →', 'shisharent'); ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="bns-text-center" style="margin-top: 40px;">
            <a href="<?php echo esc_url(get_permalink(get_page_by_path('gallery')) ?: home_url('/gallery/')); ?>" class="bns-btn-gold bns-btn-lg bns-glow-btn">
                <?php esc_html_e('VIEW FULL GALLERY', 'shisharent'); ?> →
            </a>
        </div>
    </div>
</section>

<!-- =========================================================================
     SECTION 9: DELIVERY PIN CHECKER & SERVICE ZONES
     ========================================================================= -->
<section class="bns-section bns-checker-section" id="checker">
    <div class="bns-container">
        <div class="bns-checker-grid">
            
            <!-- Left Column: Delivery Zones Info -->
            <div class="bns-checker-info">
                <span class="bns-section-subtitle"><?php esc_html_e('SERVICE COVERAGE', 'shisharent'); ?></span>
                <h2 class="bns-section-title"><?php esc_html_e('DELIVERY NETWORK', 'shisharent'); ?></h2>
                <p class="bns-checker-desc">
                    <?php esc_html_e('We deliver strictly within Kolkata, North 24 Parganas, and South 24 Parganas with express white-glove logistics.', 'shisharent'); ?>
                </p>

                <div class="bns-zones-list">
                    <div class="bns-zone-badge-card">
                        <div class="bns-zone-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div>
                            <strong>Salt Lake & New Town</strong>
                            <p>Sector I-V, Action Area I-III, Eco Park, City Centre (PIN: 700064, 700091, 700156, 700160)</p>
                        </div>
                    </div>
                    <div class="bns-zone-badge-card">
                        <div class="bns-zone-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div>
                            <strong>South Kolkata & Ballygunge</strong>
                            <p>Ballygunge, Alipore, Gariahat, Jodhpur Park, Southern Ave (PIN: 700019, 700027, 700029, 700033)</p>
                        </div>
                    </div>
                    <div class="bns-zone-badge-card">
                        <div class="bns-zone-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div>
                            <strong>Central Kolkata & Park Street</strong>
                            <p>Park Street, Camac Street, Chowringhee, Esplanade, Theatre Rd (PIN: 700016, 700017, 700071, 700069)</p>
                        </div>
                    </div>
                    <div class="bns-zone-badge-card">
                        <div class="bns-zone-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div>
                            <strong>Rajarhat & North Hubs</strong>
                            <p>Chinar Park, Baguiati, Lake Town, Kestopur, Dum Dum (PIN: 700135, 700136, 700089, 700048)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Interactive Plugin Availability Checker Widget -->
            <div class="bns-checker-widget-box">
                <?php
                if (shortcode_exists('shisharent_widget')) {
                    echo do_shortcode('[shisharent_widget]');
                } elseif (shortcode_exists('booknowshisha_rental_widget')) {
                    echo do_shortcode('[booknowshisha_rental_widget]');
                } else {
                    $widget_file = WP_PLUGIN_DIR . '/hookah-rental-core/templates/rental-booking-widget.php';
                    if (file_exists($widget_file)) {
                        include $widget_file;
                    }
                }
                ?>
            </div>

        </div>
    </div>
</section>

<!-- =========================================================================
     SECTION 10: CUSTOMER REVIEWS & TESTIMONIALS
     ========================================================================= -->
<section class="bns-section bns-reviews-section" id="reviews">
    <div class="bns-container">
        <div class="bns-section-header bns-text-center">
            <span class="bns-section-subtitle"><?php esc_html_e('VERIFIED REVIEWS', 'shisharent'); ?></span>
            <h2 class="bns-section-title"><?php esc_html_e('WHAT CLIENTS SAY IN KOLKATA', 'shisharent'); ?></h2>
            <p class="bns-section-desc">
                <?php esc_html_e('Trusted by luxury enthusiasts, party hosts, and shisha connoisseurs across Kolkata.', 'shisharent'); ?>
            </p>
        </div>

        <div class="bns-reviews-grid">
            
            <div class="bns-review-card">
                <div class="bns-stars">★★★★★</div>
                <p class="bns-review-quote">"Rented the SR Regular Hookah with SR Chief Commissioner for a rooftop celebration in Salt Lake Sector V. Spotless German pipe, dense clouds with saffron and silver notes, and zero hassle pickup the next day!"</p>
                <div class="bns-reviewer">
                    <strong>Debayan Sen</strong>
                    <span>Verified Customer • Salt Lake, Sector V, Kolkata</span>
                </div>
            </div>

            <div class="bns-review-card">
                <div class="bns-stars">★★★★★</div>
                <p class="bns-review-quote">"The delivery from the Park Street hub was under 60 minutes in Ballygunge. The staff set up the hookah with SR Zafraan Paan and explained the heat regulator. Absolute five-star service in Kolkata."</p>
                <div class="bns-reviewer">
                    <strong>Priyanka Roy</strong>
                    <span>Verified Customer • South Kolkata, Ballygunge</span>
                </div>
            </div>

            <div class="bns-review-card">
                <div class="bns-stars">★★★★★</div>
                <p class="bns-review-quote">"Booked the SR Combo Hookah with SR Blueberry Blast and SR Teen Paan Rajni for a private party in New Town. Premium setup, long-lasting coconut coals, and pristine hygiene."</p>
                <div class="bns-reviewer">
                    <strong>Aniket Banerjee</strong>
                    <span>Event Host • New Town, Action Area II, Kolkata</span>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- =========================================================================
     SECTION 12: AGE VERIFICATION & HEALTH DISCLAIMER
     ========================================================================= -->
<section class="bns-section bns-disclaimer-section" id="disclaimer">
    <div class="bns-container">
        <div class="bns-disclaimer-inner">
            <div class="bns-disclaimer-badge">
                <span>21+</span>
            </div>
            <div class="bns-disclaimer-text">
                <h4><?php esc_html_e('Important Legal & Health Notice (Kolkata & West Bengal)', 'shisharent'); ?></h4>
                <p>
                    <?php esc_html_e('ShishaRent services and products are strictly intended for adults aged 21 years and above. Government-issued photo identification is verified upon delivery across Kolkata. Herbal and molasses products are for recreational use in compliance with local hospitality regulations.', 'shisharent'); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
