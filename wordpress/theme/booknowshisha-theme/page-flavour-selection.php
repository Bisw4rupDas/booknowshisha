<?php
/**
 * Template Name: Flavour Selection Page
 *
 * Dedicated Two-Column Luxury Rental & Flavour Configurator for ShishaRent.
 * Features:
 * - Dedicated separate rental pricing (ascending from Basic to Combo)
 * - Selectable Chillum Material option (Classic Clay vs Gold Silicone)
 * - Dynamic Hookah Base upgrades (Standard ₹0, Ice +₹100, Milk +₹150, Combined +₹200)
 * - 23 verified SR artisanal flavour blends from shisharent-gallery
 * - Real-time dynamic total calculation & AJAX Add to Cart / Instant Checkout
 *
 * @package ShishaRent
 */

get_header();

// 1. Retrieve Central Rental Catalog
$rental_catalog = function_exists('bns_get_rental_packages') ? bns_get_rental_packages() : [];

// Flatten rental options for easy lookup
$all_rental_items = [];
if (!empty($rental_catalog)) {
    foreach ($rental_catalog as $cat_key => $cat_data) {
        foreach ($cat_data['items'] as $title => $item) {
            $all_rental_items[$title] = $item;
        }
    }
}

// 2. Retrieve Selected Rental Option from Query Parameter or Cookie
$selected_rental = isset($_GET['rental']) ? sanitize_text_field(wp_unslash($_GET['rental'])) : '';
if (empty($selected_rental) && isset($_COOKIE['bns_selected_rental'])) {
    $selected_rental = sanitize_text_field(wp_unslash($_COOKIE['bns_selected_rental']));
}
if (empty($selected_rental) || !isset($all_rental_items[$selected_rental])) {
    $selected_rental = 'SR SPECIAL HOOKAH';
}

$active_rental_pkg = $all_rental_items[$selected_rental] ?? [
    'tier'        => 'SPECIAL',
    'title'       => 'SR SPECIAL HOOKAH',
    'slug'        => 'sr-special-hookah',
    'price'       => 1499.00,
    'price_fmt'   => '₹1,499',
    'type'        => 'hookah',
    'image'       => 'sr-special-hookah.webp',
    'tagline'     => __('VIP Carbon Stealth Luxury', 'shisharent'),
    'description' => __('Tactical stealth matte-black luxury hookah with carbon fiber stem for VIP celebrations.', 'shisharent'),
    'specs'       => __('Carbon Matte Finish • HMD Heat Regulator • Priority Dispatch', 'shisharent'),
];

$is_hookah = ($active_rental_pkg['type'] === 'hookah');
$current_rental_thumb = $active_rental_pkg['image'];

// 3. Fetch all 23 SR Flavour Products from WooCommerce
$flavours_raw = wc_get_products([
    'limit'    => -1,
    'status'   => 'publish',
    'orderby'  => 'menu_order title',
    'order'    => 'ASC',
]);

$flavours_list = [];
foreach ($flavours_raw as $p) {
    $img_id = $p->get_image_id();
    $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'full') : '';
    $thumb_url = $img_id ? wp_get_attachment_image_url($img_id, 'thumbnail') : '';
    $slug = $p->get_slug();
    $price = (float) $p->get_price();
    
    // Categorize
    $filter_type = 'fruit';
    if (strpos($slug, 'paan') !== false || strpos($slug, 'supari') !== false || strpos($slug, 'zafraan') !== false || strpos($slug, 'commissioner') !== false) {
        $filter_type = 'paan';
    } elseif (strpos($slug, 'mint') !== false || strpos($slug, 'brainfreezer') !== false || strpos($slug, 'lemon') !== false) {
        $filter_type = 'mint';
    } elseif (strpos($slug, 'smokachinno') !== false || strpos($slug, 'vanilla') !== false || strpos($slug, 'marbella') !== false) {
        $filter_type = 'dessert';
    }

    $is_special = (strpos($slug, 'chief-commissioner') !== false || strpos($slug, 'special') !== false);
    $short_desc = wp_strip_all_tags($p->get_short_description());
    $desc = wp_strip_all_tags($p->get_description());

    // Generate sensory chips
    $tags = [];
    if (!empty($short_desc)) {
        $parts = explode('•', $short_desc);
        foreach ($parts as $part) {
            $cleaned = trim($part);
            if (!empty($cleaned)) {
                $tags[] = $cleaned;
            }
        }
    }
    if (empty($tags)) {
        $tags = ['Artisanal SR Blend', 'Dense Smoke', 'Kolkata Lounge Quality'];
    }

    $flavours_list[] = [
        'id'          => $p->get_id(),
        'name'        => $p->get_name(),
        'slug'        => $slug,
        'price'       => $price,
        'price_fmt'   => '₹' . number_format($price, 2),
        'image_url'   => $img_url,
        'thumb_url'   => $thumb_url ?: $img_url,
        'short_desc'  => $short_desc,
        'description' => $desc ?: $short_desc,
        'tags'        => $tags,
        'filter_type' => $filter_type,
        'is_special'  => $is_special,
    ];
}

// Initial active flavour
$initial_flavour = !empty($flavours_list) ? $flavours_list[0] : null;
?>

<!-- =========================================================================
     CONFIGURATOR HERO BANNER
     ========================================================================= -->
<section class="bns-cfg-hero">
    <div class="bns-container">
        <div class="bns-cfg-hero-inner">
            <div class="bns-cfg-step-indicator">
                <span class="bns-step-dot active">1</span>
                <span class="bns-step-line"></span>
                <span class="bns-step-dot current">2</span>
                <span class="bns-step-line"></span>
                <span class="bns-step-dot">3</span>
                <span class="bns-step-text"><?php esc_html_e('STEP 2 OF 3 • CONFIGURE RENTAL, CHILLUM, BASE & FLAVOUR', 'shisharent'); ?></span>
            </div>
            <h1 class="bns-cfg-main-title"><?php esc_html_e('LUXURY RENTAL CONFIGURATOR', 'shisharent'); ?></h1>
            <p class="bns-cfg-main-subtitle">
                <?php esc_html_e('Customize your hookah rental package, select your chillum material, add chill base enhancements, and pair with 23 authentic SR artisanal blends.', 'shisharent'); ?>
            </p>
        </div>
    </div>
</section>

<!-- =========================================================================
     TWO-COLUMN PRODUCT CONFIGURATOR
     ========================================================================= -->
<section class="bns-section bns-cfg-section">
    <div class="bns-container">

        <div class="bns-cfg-layout">

            <!-- =============================================================
                 LEFT COLUMN: LARGE PRIMARY VISUAL STAGE (~52%)
                 ============================================================= -->
            <div class="bns-cfg-visual-col">
                <div class="bns-cfg-visual-sticky">
                    
                    <div class="bns-cfg-image-card">
                        <!-- Visual View Switcher (Rental Setup vs Flavour Bowl) -->
                        <div class="bns-cfg-view-tabs">
                            <button type="button" class="bns-cfg-view-tab active" id="bns-view-tab-rental">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v6m0 0l4-4m-4 4L8 4"></path><path d="M8 8h8l3 12H5L8 8z"></path><path d="M6 17h12"></path></svg>
                                <span><?php esc_html_e('Rental Setup', 'shisharent'); ?></span>
                            </button>
                            <button type="button" class="bns-cfg-view-tab" id="bns-view-tab-flavour">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v8M8 12h8"></path></svg>
                                <span><?php esc_html_e('Prepared Bowl', 'shisharent'); ?></span>
                            </button>
                        </div>

                        <!-- Badges -->
                        <div class="bns-cfg-badge-wrap">
                            <span id="bns-cfg-tier-badge" class="bns-cfg-badge bns-badge-gold">
                                [ <?php echo esc_html($active_rental_pkg['tier']); ?> TIER ]
                            </span>
                            <span id="bns-cfg-flv-badge" class="bns-cfg-bowl-badge">
                                <?php echo ($initial_flavour && $initial_flavour['is_special']) ? esc_html__('ROYAL RESERVE BLEND', 'shisharent') : esc_html__('AUTHENTIC SR BLEND', 'shisharent'); ?>
                            </span>
                        </div>

                        <!-- Main High-Res Image Stage -->
                        <div class="bns-cfg-image-stage">
                            <!-- Rental Setup Image -->
                            <img id="bns-cfg-rental-main-img" 
                                 src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/rentals/' . $current_rental_thumb); ?>" 
                                 alt="<?php echo esc_attr($active_rental_pkg['title']); ?>" 
                                 class="bns-cfg-image-active" />
                            
                            <!-- Flavour Bowl Image (Switchable) -->
                            <img id="bns-cfg-main-img" 
                                 src="<?php echo esc_url($initial_flavour ? $initial_flavour['image_url'] : ''); ?>" 
                                 alt="<?php echo esc_attr($initial_flavour ? $initial_flavour['name'] : 'SR Flavour'); ?>" 
                                 class="bns-cfg-image-secondary" 
                                 style="display: none;" />
                            
                            <div class="bns-cfg-image-glow"></div>
                        </div>

                        <!-- Image Footer Info -->
                        <div class="bns-cfg-image-footer">
                            <div class="bns-cfg-feature-item">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <span><?php esc_html_e('90+ Min Extended Session', 'shisharent'); ?></span>
                            </div>
                            <div class="bns-cfg-feature-item">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path></svg>
                                <span><?php esc_html_e('All Coals & Setup Included', 'shisharent'); ?></span>
                            </div>
                            <div class="bns-cfg-feature-item">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"></path></svg>
                                <span><?php esc_html_e('45-Min Kolkata Delivery', 'shisharent'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Flavour Visual Scroller (Thumbnails) -->
                    <div class="bns-cfg-thumbs-strip">
                        <span class="bns-cfg-thumbs-label"><?php esc_html_e('CHOOSE ARTISANAL FLAVOUR BOWL:', 'shisharent'); ?></span>
                        <div class="bns-cfg-thumbs-track" id="bns-cfg-thumbs-track">
                            <?php foreach ($flavours_list as $index => $flv) : ?>
                                <button type="button" 
                                        class="bns-cfg-thumb-btn <?php echo ($index === 0) ? 'active' : ''; ?>" 
                                        data-id="<?php echo esc_attr($flv['id']); ?>" 
                                        title="<?php echo esc_attr($flv['name']); ?>">
                                    <img src="<?php echo esc_url($flv['thumb_url']); ?>" alt="<?php echo esc_attr($flv['name']); ?>" />
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </div>

            <!-- =============================================================
                 RIGHT COLUMN: EDITORIAL CONFIGURATION CONTROLS (~48%)
                 ============================================================= -->
            <div class="bns-cfg-details-col">

                <!-- 1. Active Rental Status Pill & Setup Switcher -->
                <div class="bns-cfg-rental-bar">
                    <div class="bns-cfg-rental-left">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/rentals/' . $current_rental_thumb); ?>" 
                             alt="<?php echo esc_attr($active_rental_pkg['title']); ?>" 
                             class="bns-cfg-rental-icon" 
                             id="bns-cfg-rental-img" />
                        <div class="bns-cfg-rental-meta">
                            <span class="bns-cfg-meta-sub"><?php esc_html_e('SELECTED RENTAL SETUP', 'shisharent'); ?></span>
                            <strong class="bns-cfg-rental-title" id="bns-cfg-rental-title-text"><?php echo esc_html($active_rental_pkg['title']); ?></strong>
                        </div>
                    </div>
                    <div class="bns-cfg-rental-right">
                        <button type="button" class="bns-cfg-switch-rental-btn" id="bns-open-rental-modal-btn">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                            <span><?php esc_html_e('Change Setup', 'shisharent'); ?></span>
                        </button>
                    </div>
                </div>

                <!-- 2. Product Brand, Title, Rating, Dedicated Rental Price -->
                <div class="bns-cfg-header-block">
                    <span class="bns-cfg-brand-label" id="bns-cfg-rental-tagline-text"><?php echo esc_html($active_rental_pkg['tagline']); ?></span>
                    <h2 class="bns-cfg-flv-title" id="bns-cfg-rental-name-heading"><?php echo esc_html($active_rental_pkg['title']); ?></h2>
                    
                    <div class="bns-cfg-rating-row">
                        <div class="bns-cfg-stars">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#d4af37" stroke="#d4af37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#d4af37" stroke="#d4af37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#d4af37" stroke="#d4af37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#d4af37" stroke="#d4af37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#d4af37" stroke="#d4af37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        </div>
                        <span class="bns-cfg-review-count"><?php esc_html_e('4.9/5 • Authentic Kolkata Lounge Quality', 'shisharent'); ?></span>
                    </div>

                    <!-- Dedicated Rental Base Price Display -->
                    <div class="bns-cfg-price-wrap">
                        <span class="bns-cfg-cur">₹</span>
                        <span class="bns-cfg-price-num" id="bns-cfg-rental-price-num"><?php echo esc_html(number_format($active_rental_pkg['price'], 2)); ?></span>
                        <span class="bns-cfg-tax-badge"><?php esc_html_e('Rental Setup Package', 'shisharent'); ?></span>
                    </div>
                </div>

                <!-- 3. Short Description & Specs -->
                <div class="bns-cfg-desc-card">
                    <p class="bns-cfg-desc-text" id="bns-cfg-rental-desc-text">
                        <?php echo esc_html($active_rental_pkg['description']); ?>
                    </p>
                    <div class="bns-cfg-specs-pill" id="bns-cfg-rental-specs-pill">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span id="bns-cfg-specs-span"><?php echo esc_html($active_rental_pkg['specs']); ?></span>
                    </div>
                </div>

                <!-- 4. CHILAM MATERIAL SELECTION (Selectable Interactive Option) -->
                <div class="bns-cfg-block bns-cfg-chillum-block">
                    <div class="bns-cfg-block-header">
                        <span class="bns-cfg-step-tag"><?php esc_html_e('OPTION 1', 'shisharent'); ?></span>
                        <h3 class="bns-cfg-block-title"><?php esc_html_e('CHILAM MATERIAL', 'shisharent'); ?></h3>
                    </div>

                    <div class="bns-cfg-chillum-grid">
                        
                        <!-- Classic Clay -->
                        <div class="bns-cfg-chillum-card active" data-material="Classic Clay" data-price="0" tabindex="0" role="button">
                            <div class="bns-cfg-chillum-radio">
                                <span class="bns-radio-circle"></span>
                            </div>
                            <div class="bns-cfg-chillum-info">
                                <div class="bns-cfg-chillum-title-row">
                                    <strong class="bns-cfg-chillum-name"><?php esc_html_e('Classic Clay', 'shisharent'); ?></strong>
                                    <span class="bns-badge-included"><?php esc_html_e('Default (₹0)', 'shisharent'); ?></span>
                                </div>
                                <p class="bns-cfg-chillum-desc"><?php esc_html_e('Traditional porous terracotta clay phunnel for authentic flavour and uniform heat distribution.', 'shisharent'); ?></p>
                            </div>
                        </div>

                        <!-- Gold Silicone -->
                        <div class="bns-cfg-chillum-card" data-material="Gold Silicone" data-price="100" tabindex="0" role="button">
                            <div class="bns-cfg-chillum-radio">
                                <span class="bns-radio-circle"></span>
                            </div>
                            <div class="bns-cfg-chillum-info">
                                <div class="bns-cfg-chillum-title-row">
                                    <strong class="bns-cfg-chillum-name"><?php esc_html_e('Gold Silicone', 'shisharent'); ?></strong>
                                    <span class="bns-cfg-base-badge bns-badge-price">+₹100</span>
                                </div>
                                <p class="bns-cfg-chillum-desc"><?php esc_html_e('Medical-grade heat-resistant silicone phunnel with premium gold alloy rim for high durability.', 'shisharent'); ?></p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 5. Hookah Base Selection (Hookah Upgrades) -->
                <div class="bns-cfg-block bns-cfg-base-block" id="bns-cfg-base-section" style="<?php echo $is_hookah ? '' : 'display: none;'; ?>">
                    <div class="bns-cfg-block-header">
                        <span class="bns-cfg-step-tag"><?php esc_html_e('OPTION 2', 'shisharent'); ?></span>
                        <h3 class="bns-cfg-block-title"><?php esc_html_e('HOOKAH BASE CUSTOMIZATION', 'shisharent'); ?></h3>
                    </div>

                    <!-- Do You Want a Hookah Base Toggle (YES / NO) -->
                    <div class="bns-cfg-base-toggle-wrap">
                        <div class="bns-cfg-toggle-label">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v6m0 0l4-4m-4 4L8 4"></path><path d="M8 8h8l3 12H5L8 8z"></path><path d="M6 17h12"></path></svg>
                            <span><?php esc_html_e('CUSTOMIZE HOOKAH BASE?', 'shisharent'); ?></span>
                        </div>
                        <div class="bns-cfg-toggle-switch">
                            <button type="button" class="bns-cfg-toggle-btn active" id="bns-base-toggle-yes" data-val="yes">
                                <?php esc_html_e('YES', 'shisharent'); ?>
                            </button>
                            <button type="button" class="bns-cfg-toggle-btn" id="bns-base-toggle-no" data-val="no">
                                <?php esc_html_e('NO', 'shisharent'); ?>
                            </button>
                        </div>
                    </div>

                    <!-- Hookah Base Selectable Cards -->
                    <div class="bns-cfg-base-cards-wrap" id="bns-cfg-base-cards-container">
                        
                        <!-- Standard Base -->
                        <div class="bns-cfg-base-card active" data-base="standard" data-price="0" data-label="Standard Base (Included)" tabindex="0" role="button">
                            <div class="bns-cfg-base-card-radio">
                                <span class="bns-radio-circle"></span>
                            </div>
                            <div class="bns-cfg-base-card-content">
                                <div class="bns-cfg-base-card-title-row">
                                    <strong class="bns-cfg-base-name"><?php esc_html_e('Standard Base', 'shisharent'); ?></strong>
                                    <span class="bns-cfg-base-badge bns-badge-included"><?php esc_html_e('Included (₹0)', 'shisharent'); ?></span>
                                </div>
                                <p class="bns-cfg-base-desc"><?php esc_html_e('Standard crystal glass base with filtered fresh water.', 'shisharent'); ?></p>
                            </div>
                        </div>

                        <!-- Ice Base -->
                        <div class="bns-cfg-base-card" data-base="ice" data-price="100" data-label="Ice Base (+₹100)" tabindex="0" role="button">
                            <div class="bns-cfg-base-card-radio">
                                <span class="bns-radio-circle"></span>
                            </div>
                            <div class="bns-cfg-base-card-content">
                                <div class="bns-cfg-base-card-title-row">
                                    <strong class="bns-cfg-base-name"><?php esc_html_e('Ice Base', 'shisharent'); ?></strong>
                                    <span class="bns-cfg-base-badge bns-badge-price">+₹100</span>
                                </div>
                                <p class="bns-cfg-base-desc"><?php esc_html_e('Chilled ice base option for a cooler smoking experience.', 'shisharent'); ?></p>
                            </div>
                        </div>

                        <!-- Milk Base -->
                        <div class="bns-cfg-base-card" data-base="milk" data-price="150" data-label="Milk Base (+₹150)" tabindex="0" role="button">
                            <div class="bns-cfg-base-card-radio">
                                <span class="bns-radio-circle"></span>
                            </div>
                            <div class="bns-cfg-base-card-content">
                                <div class="bns-cfg-base-card-title-row">
                                    <strong class="bns-cfg-base-name"><?php esc_html_e('Milk Base', 'shisharent'); ?></strong>
                                    <span class="bns-cfg-base-badge bns-badge-price">+₹150</span>
                                </div>
                                <p class="bns-cfg-base-desc"><?php esc_html_e('Milk base option for richer, denser clouds.', 'shisharent'); ?></p>
                            </div>
                        </div>

                        <!-- Both Combined -->
                        <div class="bns-cfg-base-card" data-base="both" data-price="200" data-label="Ice + Milk Combined (+₹200)" tabindex="0" role="button">
                            <div class="bns-cfg-base-card-radio">
                                <span class="bns-radio-circle"></span>
                            </div>
                            <div class="bns-cfg-base-card-content">
                                <div class="bns-cfg-base-card-title-row">
                                    <strong class="bns-cfg-base-name"><?php esc_html_e('Ice + Milk Combined', 'shisharent'); ?></strong>
                                    <span class="bns-cfg-base-badge bns-badge-price">+₹200</span>
                                </div>
                                <p class="bns-cfg-base-desc"><?php esc_html_e('Ice base and Milk base both combined together for maximum chill and density.', 'shisharent'); ?></p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 6. Flavour Selection (23 Choices Cards / Chips) -->
                <div class="bns-cfg-block bns-cfg-flavour-block">
                    <div class="bns-cfg-block-header">
                        <span class="bns-cfg-step-tag"><?php esc_html_e('OPTION 3', 'shisharent'); ?></span>
                        <h3 class="bns-cfg-block-title"><?php esc_html_e('CHOOSE SR FLAVOUR BOWL', 'shisharent'); ?></h3>
                        <span class="bns-cfg-count-pill"><?php echo count($flavours_list); ?> <?php esc_html_e('Blends Available', 'shisharent'); ?></span>
                    </div>

                    <!-- Category Filter Tabs -->
                    <div class="bns-cfg-filter-tabs">
                        <button type="button" class="bns-cfg-tab active" data-filter="all"><?php esc_html_e('All (23)', 'shisharent'); ?></button>
                        <button type="button" class="bns-cfg-tab" data-filter="paan"><?php esc_html_e('Paan & Royal', 'shisharent'); ?></button>
                        <button type="button" class="bns-cfg-tab" data-filter="fruit"><?php esc_html_e('Fruity & Sweet', 'shisharent'); ?></button>
                        <button type="button" class="bns-cfg-tab" data-filter="mint"><?php esc_html_e('Mint & Chill', 'shisharent'); ?></button>
                        <button type="button" class="bns-cfg-tab" data-filter="dessert"><?php esc_html_e('Dessert & Exotic', 'shisharent'); ?></button>
                    </div>

                    <!-- Flavour Selection Grid -->
                    <div class="bns-cfg-chips-grid" id="bns-cfg-chips-grid">
                        <?php foreach ($flavours_list as $index => $flv) : ?>
                            <div class="bns-cfg-chip <?php echo ($index === 0) ? 'selected' : ''; ?> <?php echo $flv['is_special'] ? 'is-royal' : ''; ?>"
                                 data-id="<?php echo esc_attr($flv['id']); ?>"
                                 data-filter="<?php echo esc_attr($flv['filter_type']); ?>"
                                 tabindex="0"
                                 role="button"
                                 aria-pressed="<?php echo ($index === 0) ? 'true' : 'false'; ?>">
                                
                                <div class="bns-cfg-chip-thumb">
                                    <img src="<?php echo esc_url($flv['thumb_url']); ?>" alt="<?php echo esc_attr($flv['name']); ?>" loading="lazy" />
                                </div>
                                <div class="bns-cfg-chip-info">
                                    <strong class="bns-cfg-chip-name"><?php echo esc_html($flv['name']); ?></strong>
                                    <span class="bns-cfg-chip-price"><?php esc_html_e('Included', 'shisharent'); ?></span>
                                </div>
                                <div class="bns-cfg-chip-check">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 7. Real-Time Price Breakdown Summary Box -->
                <div class="bns-cfg-summary-box">
                    <div class="bns-cfg-summary-row bns-cfg-sum-rental-row">
                        <span class="bns-cfg-sum-label"><?php esc_html_e('Rental Setup Base:', 'shisharent'); ?> <strong id="bns-sum-rental-title"><?php echo esc_html($active_rental_pkg['title']); ?></strong></span>
                        <span class="bns-cfg-sum-val" id="bns-sum-rental-price">₹<?php echo esc_html(number_format($active_rental_pkg['price'], 2)); ?></span>
                    </div>
                    <div class="bns-cfg-summary-row" id="bns-sum-chillum-row">
                        <span class="bns-cfg-sum-label"><?php esc_html_e('Chillum Material:', 'shisharent'); ?> <strong id="bns-sum-chillum-title"><?php esc_html_e('Classic Clay', 'shisharent'); ?></strong></span>
                        <span class="bns-cfg-sum-val" id="bns-sum-chillum-price"><?php esc_html_e('Included (₹0.00)', 'shisharent'); ?></span>
                    </div>
                    <div class="bns-cfg-summary-row" id="bns-sum-base-row" style="<?php echo $is_hookah ? '' : 'display: none;'; ?>">
                        <span class="bns-cfg-sum-label"><?php esc_html_e('Hookah Base Option:', 'shisharent'); ?> <strong id="bns-sum-base-title"><?php esc_html_e('Standard Base', 'shisharent'); ?></strong></span>
                        <span class="bns-cfg-sum-val" id="bns-sum-base-price">₹0.00</span>
                    </div>
                    <div class="bns-cfg-summary-row">
                        <span class="bns-cfg-sum-label"><?php esc_html_e('Included Flavour Bowl:', 'shisharent'); ?> <strong id="bns-sum-flv-title"><?php echo esc_html($initial_flavour ? $initial_flavour['name'] : 'SR Blueberry Blast'); ?></strong></span>
                        <span class="bns-cfg-sum-val bns-text-gold"><?php esc_html_e('Included', 'shisharent'); ?></span>
                    </div>
                    <div class="bns-cfg-summary-divider"></div>
                    <div class="bns-cfg-summary-total-row">
                        <span class="bns-cfg-sum-total-label"><?php esc_html_e('Total Amount:', 'shisharent'); ?></span>
                        <span class="bns-cfg-sum-total-price" id="bns-sum-total-price">₹<?php echo esc_html(number_format($active_rental_pkg['price'], 2)); ?></span>
                    </div>
                </div>

                <!-- 8. Quantity Stepper & Action Buttons (Add to Cart + Buy Now) -->
                <div class="bns-cfg-actions-wrap">
                    
                    <div class="bns-cfg-qty-control">
                        <button type="button" class="bns-cfg-qty-btn" id="bns-qty-minus" aria-label="<?php esc_attr_e('Decrease quantity', 'shisharent'); ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                        <input type="number" id="bns-cfg-qty-input" class="bns-cfg-qty-val" value="1" min="1" max="10" readonly />
                        <button type="button" class="bns-cfg-qty-btn" id="bns-qty-plus" aria-label="<?php esc_attr_e('Increase quantity', 'shisharent'); ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                    </div>

                    <div class="bns-cfg-btn-group">
                        <button type="button" class="bns-btn-secondary bns-cfg-add-cart-btn" id="bns-cfg-add-cart-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            <span><?php esc_html_e('ADD TO CART', 'shisharent'); ?></span>
                        </button>

                        <button type="button" class="bns-btn-gold bns-cfg-buy-now-btn" id="bns-cfg-buy-now-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                            <span><?php esc_html_e('BUY NOW', 'shisharent'); ?></span>
                        </button>
                    </div>

                </div>

                <!-- 9. Supporting Information (HOW IT WORKS) -->
                <div class="bns-cfg-how-it-works">
                    <h4 class="bns-cfg-how-title"><?php esc_html_e('HOW IT WORKS', 'shisharent'); ?></h4>
                    
                    <div class="bns-cfg-steps-grid">
                        <div class="bns-cfg-step-card">
                            <div class="bns-cfg-step-num">01</div>
                            <div class="bns-cfg-step-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                            </div>
                            <div class="bns-cfg-step-meta">
                                <strong><?php esc_html_e('Choose Setup', 'shisharent'); ?></strong>
                                <p><?php esc_html_e('Select between German/Egyptian hookahs or individual pre-packed chilams.', 'shisharent'); ?></p>
                            </div>
                        </div>

                        <div class="bns-cfg-step-card">
                            <div class="bns-cfg-step-num">02</div>
                            <div class="bns-cfg-step-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v8M8 12h8"></path></svg>
                            </div>
                            <div class="bns-cfg-step-meta">
                                <strong><?php esc_html_e('Chillum & Flavour', 'shisharent'); ?></strong>
                                <p><?php esc_html_e('Select Classic Clay or Gold Silicone, paired with 23 SR blends.', 'shisharent'); ?></p>
                            </div>
                        </div>

                        <div class="bns-cfg-step-card">
                            <div class="bns-cfg-step-num">03</div>
                            <div class="bns-cfg-step-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M2 12h20M4.93 4.93l14.14 14.14M4.93 19.07l14.14-14.14"></path></svg>
                            </div>
                            <div class="bns-cfg-step-meta">
                                <strong><?php esc_html_e('Customize Base', 'shisharent'); ?></strong>
                                <p><?php esc_html_e('Add Ice Base (+₹100) or Milk Base (+₹150) for cooler dense clouds.', 'shisharent'); ?></p>
                            </div>
                        </div>

                        <div class="bns-cfg-step-card">
                            <div class="bns-cfg-step-num">04</div>
                            <div class="bns-cfg-step-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                            </div>
                            <div class="bns-cfg-step-meta">
                                <strong><?php esc_html_e('Doorstep Setup', 'shisharent'); ?></strong>
                                <p><?php esc_html_e('45-minute Kolkata delivery with coal ignition and white-glove setup.', 'shisharent'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>

<!-- =========================================================================
     RENTAL OPTION MODAL (Quick Switcher with Ascending Catalog)
     ========================================================================= -->
<div class="bns-modal-backdrop" id="bns-rental-modal" aria-hidden="true">
    <div class="bns-modal-dialog">
        <div class="bns-modal-header">
            <h3 class="bns-modal-title"><?php esc_html_e('Select Your Rental Setup', 'shisharent'); ?></h3>
            <button type="button" class="bns-modal-close" id="bns-close-rental-modal" aria-label="<?php esc_attr_e('Close Modal', 'shisharent'); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="bns-modal-body">
            <p class="bns-modal-desc"><?php esc_html_e('Select a hookah or individual chilam package to pair with your chosen flavour in Kolkata.', 'shisharent'); ?></p>
            
            <?php if (!empty($rental_catalog)) : ?>
                <?php foreach ($rental_catalog as $cat_key => $cat_data) : ?>
                    <div class="bns-modal-cat-section">
                        <span class="bns-modal-cat-title"><?php echo esc_html($cat_data['category_label']); ?></span>
                        <div class="bns-modal-rentals-grid">
                            <?php foreach ($cat_data['items'] as $r_title => $r_data) : 
                                $is_current = ($r_title === $selected_rental);
                            ?>
                                <div class="bns-modal-rental-card <?php echo $is_current ? 'selected' : ''; ?>" 
                                     data-rental="<?php echo esc_attr($r_title); ?>"
                                     data-type="<?php echo esc_attr($r_data['type']); ?>"
                                     data-price="<?php echo esc_attr($r_data['price']); ?>"
                                     data-price-fmt="<?php echo esc_attr($r_data['price_fmt']); ?>"
                                     data-tier="<?php echo esc_attr($r_data['tier']); ?>"
                                     data-tagline="<?php echo esc_attr($r_data['tagline']); ?>"
                                     data-desc="<?php echo esc_attr($r_data['description']); ?>"
                                     data-specs="<?php echo esc_attr($r_data['specs']); ?>"
                                     data-img="<?php echo esc_attr($r_data['image']); ?>"
                                     tabindex="0"
                                     role="button">
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/rentals/' . $r_data['image']); ?>" 
                                         alt="<?php echo esc_attr($r_title); ?>" 
                                         class="bns-modal-rental-thumb" />
                                    <div class="bns-modal-rental-meta">
                                        <span class="bns-modal-tier-pill">[ <?php echo esc_html($r_data['tier']); ?> ]</span>
                                        <strong><?php echo esc_html($r_title); ?></strong>
                                        <span class="bns-modal-rental-price"><?php echo esc_html($r_data['price_fmt']); ?></span>
                                    </div>
                                    <div class="bns-modal-check">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- =========================================================================
     EMBEDDED CLIENT DATA
     ========================================================================= -->
<script>
window.bnsFlavoursData = <?php echo json_encode($flavours_list); ?>;
window.bnsInitialRental = <?php echo json_encode($active_rental_pkg['title']); ?>;
window.bnsRentalPackage = <?php echo json_encode($active_rental_pkg); ?>;
window.bnsRentalCatalog = <?php echo json_encode($all_rental_items); ?>;
window.bnsIsHookah = <?php echo json_encode($is_hookah); ?>;
</script>

<?php get_footer(); ?>
