<?php
/**
 * ShishaRent Theme functions and definitions
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('BNS_THEME_VERSION', '1.5.0');
define('BNS_THEME_DIR', get_template_directory());
define('BNS_THEME_URI', get_template_directory_uri());

// Require ShishaRent Gallery Core
if (file_exists(BNS_THEME_DIR . '/inc/class-shisharent-gallery.php')) {
    require_once BNS_THEME_DIR . '/inc/class-shisharent-gallery.php';
}

/**
 * Theme Setup
 */
function bns_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height'      => 80,
        'width'       => 240,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    // Register Navigation Menus
    register_nav_menus([
        'primary' => __('Primary Navigation Menu', 'shisharent'),
        'footer'  => __('Footer Navigation Menu', 'shisharent'),
    ]);
}
add_action('after_setup_theme', 'bns_theme_setup');

/**
 * Force Indian Rupee (INR / â‚¹) Currency Configuration in WooCommerce
 */
function bns_force_inr_currency($currency) {
    return 'INR';
}
add_filter('woocommerce_currency', 'bns_force_inr_currency', 999);

function bns_force_inr_currency_symbol($currency_symbol, $currency) {
    return 'â‚¹';
}
add_filter('woocommerce_currency_symbol', 'bns_force_inr_currency_symbol', 999, 2);

function bns_configure_woocommerce_defaults() {
    if (get_option('woocommerce_currency') !== 'INR') {
        update_option('woocommerce_currency', 'INR');
        update_option('woocommerce_currency_pos', 'left');
        update_option('woocommerce_price_thousand_sep', ',');
        update_option('woocommerce_price_decimal_sep', '.');
        update_option('woocommerce_price_num_decimals', 0);
    }
}
add_action('init', 'bns_configure_woocommerce_defaults');

/**
 * Auto-seed catalog and initial pages/posts
 */
function bns_maybe_seed_content() {
    if (file_exists(BNS_THEME_DIR . '/seed-gallery.php')) {
        require_once BNS_THEME_DIR . '/seed-gallery.php';
        if (function_exists('bns_maybe_seed_gallery')) {
            bns_maybe_seed_gallery();
        }
    }
    if (file_exists(BNS_THEME_DIR . '/seed-bms-flavours.php')) {
        require_once BNS_THEME_DIR . '/seed-bms-flavours.php';
        if (function_exists('bns_seed_bms_flavours_catalog') && !get_option('bms_flavours_catalog_seeded_v1', false)) {
            bns_seed_bms_flavours_catalog(false);
        }
    }

    if (file_exists(BNS_THEME_DIR . '/seed-pages-and-posts.php')) {
        require_once BNS_THEME_DIR . '/seed-pages-and-posts.php';
        if (function_exists('bns_seed_pages_and_blog_posts')) {
            bns_seed_pages_and_blog_posts();
        }
    }
}
add_action('init', 'bns_maybe_seed_content', 20);

/**
 * Enqueue scripts and styles
 */
function bns_enqueue_assets() {
    // Google Fonts - Modern Luxury (Playfair/Cormorant/Inter) & Gothic Luxury (Cinzel/Cinzel Decorative)
    wp_enqueue_style(
        'bns-google-fonts',
        'https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800;900&family=Cinzel+Decorative:wght@700;900&family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500;1,600&family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap',
        [],
        null
    );

    // Main Theme CSS
    wp_enqueue_style(
        'bns-theme-style',
        get_stylesheet_uri(),
        ['bns-google-fonts'],
        BNS_THEME_VERSION
    );

    if (file_exists(BNS_THEME_DIR . '/assets/css/main.css')) {
        wp_enqueue_style(
            'bns-theme-main',
            BNS_THEME_URI . '/assets/css/main.css',
            ['bns-theme-style'],
            BNS_THEME_VERSION
        );
    }

    // Main Theme JavaScript
    if (file_exists(BNS_THEME_DIR . '/assets/js/main.js')) {
        wp_enqueue_script(
            'bns-theme-script',
            BNS_THEME_URI . '/assets/js/main.js',
            ['jquery'],
            BNS_THEME_VERSION,
            true
        );

        wp_localize_script('bns-theme-script', 'bnsThemeData', [
            'ajaxUrl'    => admin_url('admin-ajax.php'),
            'cartUrl'    => function_exists('wc_get_cart_url') ? wc_get_cart_url() : '',
            'checkoutUrl'=> function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '',
            'currency'   => 'INR',
            'authNonce'  => wp_create_nonce('bns_auth_nonce'),
            'isLoggedIn' => is_user_logged_in() ? 1 : 0,
        ]);
    }
}
add_action('wp_enqueue_scripts', 'bns_enqueue_assets');

/**
 * High-definition imagery map for products & flavours
 */
function bns_get_product_image_url($product_id, $size = 'woocommerce_thumbnail') {
    $thumb_id = get_post_thumbnail_id($product_id);
    if ($thumb_id) {
        $img = wp_get_attachment_image_src($thumb_id, $size);
        if ($img) {
            return $img[0];
        }
    }

    $product = wc_get_product($product_id);
    $slug = $product ? $product->get_slug() : '';

    $product_images = [];

    if ($slug && isset($product_images[$slug])) {
        return $product_images[$slug];
    }

    // Check custom meta
    $meta_img = get_post_meta($product_id, '_bns_image_url', true);
    if ($meta_img) {
        return $meta_img;
    }

    // Default fallback image
    return get_template_directory_uri() . '/assets/images/logo.png';
}

/**
 * Image helper for Blog Posts
 */
function bns_get_post_image_url($post_id, $size = 'large') {
    if (has_post_thumbnail($post_id)) {
        $img = get_the_post_thumbnail_url($post_id, $size);
        if ($img) {
            return $img;
        }
    }

    $meta_img = get_post_meta($post_id, '_bns_image_url', true);
    if ($meta_img) {
        return $meta_img;
    }

    return get_template_directory_uri() . '/assets/images/logo.png';
}

/**
 * Calculate post reading time
 */
function bns_get_reading_time($post_id) {
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute
    return max(1, $reading_time) . ' min read';
}

/**
 * Filter WooCommerce Product HTML to replace empty thumbnail with high-res asset
 */
function bns_woocommerce_product_image_fallback($image, $product, $size, $attr, $placeholder) {
    if (!$product) return $image;
    $thumb_id = $product->get_image_id();
    if (!$thumb_id) {
        $img_url = bns_get_product_image_url($product->get_id(), $size);
        $alt = esc_attr($product->get_name());
        return sprintf(
            '<img src="%s" alt="%s" class="attachment-%s size-%s wp-post-image bns-catalog-img" loading="lazy" />',
            esc_url($img_url),
            $alt,
            esc_attr($size),
            esc_attr($size)
        );
    }
    return $image;
}
add_filter('woocommerce_product_get_image', 'bns_woocommerce_product_image_fallback', 10, 5);

/**
 * Filter WooCommerce Placeholder image
 */
function bns_custom_woocommerce_placeholder_img_src($src) {
    return get_template_directory_uri() . '/assets/images/logo.png';
}
add_filter('woocommerce_placeholder_img_src', 'bns_custom_woocommerce_placeholder_img_src');

/**
 * Helper to get products by category slug
 */
function bns_get_products_by_category($category_slug = '', $limit = 8) {
    if (!class_exists('WooCommerce')) {
        return [];
    }

    $args = [
        'limit'   => $limit,
        'status'  => 'publish',
        'orderby' => 'menu_order title',
        'order'   => 'ASC',
    ];

    if (!empty($category_slug) && $category_slug !== 'all') {
        $args['category'] = [$category_slug];
    }

    return wc_get_products($args);
}

/**
 * AJAX Cart Fragments Refresh
 */
function bns_cart_count_fragments($fragments) {
    ob_start();
    ?>
    <span class="bns-cart-count" id="bns-cart-counter">
        <?php echo (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0; ?>
    </span>
    <?php
    $fragments['#bns-cart-counter'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'bns_cart_count_fragments');

// =============================================================================
// NATIVE WORDPRESS BLOG ADMIN ENHANCEMENTS
// =============================================================================

/**
 * Ensure Native Post Type Supports All Blog Features
 */
function bns_enable_post_type_supports() {
    add_post_type_support('post', [
        'title',
        'editor',
        'author',
        'thumbnail',
        'excerpt',
        'trackbacks',
        'custom-fields',
        'comments',
        'revisions',
        'page-attributes',
        'post-formats',
    ]);
}
add_action('init', 'bns_enable_post_type_supports');

/**
 * Customize Admin Menu labels to make Blog / Posts clearly prominent
 */
function bns_customize_admin_blog_menu() {
    global $menu, $submenu;
    foreach ($menu as $key => $item) {
        if ($item[2] === 'edit.php') {
            $menu[$key][0] = __('Blog / Posts ðŸ“°', 'shisharent');
        }
    }
    if (isset($submenu['edit.php'])) {
        $submenu['edit.php'][5][0]  = __('All Blog Articles', 'shisharent');
        $submenu['edit.php'][10][0] = __('âœï¸ Add New Article', 'shisharent');
        $submenu['edit.php'][15][0] = __('ðŸ·ï¸ Blog Categories', 'shisharent');
        $submenu['edit.php'][16][0] = __('ðŸ”– Blog Tags', 'shisharent');
    }
}
add_action('admin_menu', 'bns_customize_admin_blog_menu');

/**
 * Customize Post Type Object Labels
 */
function bns_customize_post_object_labels() {
    global $wp_post_types;
    if (isset($wp_post_types['post'])) {
        $labels = &$wp_post_types['post']->labels;
        $labels->name               = __('Blog Articles', 'shisharent');
        $labels->singular_name      = __('Blog Article', 'shisharent');
        $labels->add_new            = __('Add New Article', 'shisharent');
        $labels->add_new_item       = __('Add New Blog Article', 'shisharent');
        $labels->edit_item          = __('Edit Blog Article', 'shisharent');
        $labels->new_item           = __('New Blog Article', 'shisharent');
        $labels->view_item          = __('View Blog Article', 'shisharent');
        $labels->search_items       = __('Search Blog Articles', 'shisharent');
        $labels->not_found          = __('No blog articles found', 'shisharent');
        $labels->not_found_in_trash = __('No blog articles found in Trash', 'shisharent');
        $labels->all_items          = __('All Blog Articles', 'shisharent');
        $labels->menu_name          = __('Blog / Posts', 'shisharent');
        $labels->name_admin_bar     = __('Blog Article', 'shisharent');
    }
}
add_action('init', 'bns_customize_post_object_labels');

/**
 * Add Admin Bar Quick Action for New Blog Post
 */
function bns_add_admin_bar_blog_node($wp_admin_bar) {
    if (!current_user_can('edit_posts')) return;
    $wp_admin_bar->add_node([
        'id'    => 'bns-add-blog-post',
        'title' => 'âœï¸ ' . __('Add Blog Article', 'shisharent'),
        'href'  => admin_url('post-new.php'),
    ]);
}
add_action('admin_bar_menu', 'bns_add_admin_bar_blog_node', 90);

/**
 * Add Admin Dashboard Quick Action Widget for Blog Management
 */
function bns_register_dashboard_blog_widget() {
    wp_add_dashboard_widget(
        'bns_dashboard_blog_widget',
        'âš¡ ' . __('ShishaRent Blog Management', 'shisharent'),
        'bns_render_dashboard_blog_widget'
    );
}
add_action('wp_dashboard_setup', 'bns_register_dashboard_blog_widget');

function bns_render_dashboard_blog_widget() {
    $published_count = wp_count_posts('post')->publish ?? 0;
    $draft_count     = wp_count_posts('post')->draft ?? 0;
    ?>
    <div style="padding: 10px 0; font-family: Inter, sans-serif;">
        <p style="font-size: 0.95rem; color: #475569; margin-bottom: 15px;">
            Manage and publish educational hookah guides, party advice, and flavour mixology articles natively.
        </p>
        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
            <div style="background: #f1f5f9; padding: 12px 18px; border-radius: 8px; flex: 1; border-left: 4px solid #10b981;">
                <span style="font-size: 1.3rem; font-weight: 700; color: #0f172a;"><?php echo esc_html($published_count); ?></span><br>
                <small style="color: #64748b; font-weight: 600;">Published Articles</small>
            </div>
            <div style="background: #f1f5f9; padding: 12px 18px; border-radius: 8px; flex: 1; border-left: 4px solid #f59e0b;">
                <span style="font-size: 1.3rem; font-weight: 700; color: #0f172a;"><?php echo esc_html($draft_count); ?></span><br>
                <small style="color: #64748b; font-weight: 600;">Drafts in Progress</small>
            </div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="button button-primary" style="text-align: center; padding: 6px 12px; font-weight: 600;">
                âœï¸ Add New Article
            </a>
            <a href="<?php echo esc_url(admin_url('edit.php')); ?>" class="button" style="text-align: center; padding: 6px 12px; font-weight: 600;">
                ðŸ“‹ Manage All Articles
            </a>
            <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=category')); ?>" class="button" style="text-align: center; padding: 6px 12px;">
                ðŸ·ï¸ Manage Categories
            </a>
            <a href="<?php echo esc_url(home_url('/blog/')); ?>" target="_blank" class="button" style="text-align: center; padding: 6px 12px;">
                ðŸŒ View Public Blog
            </a>
        </div>
    </div>
    <?php
}

/**
 * WooCommerce: Show 24 items per page on Shop (covers all 23 BMS flavours)
 */
add_filter('loop_shop_per_page', function($cols) {
    return 24;
}, 20);

/**
 * Central Rental Packages Catalog (Ascending Hierarchy & Separate Pricing)
 */
function bns_get_rental_packages() {
    return [
        'chilam' => [
            'category_id'    => 'chilam',
            'category_label' => __('CHILAM RENTALS', 'shisharent'),
            'category_badge' => __('CHILAM COLLECTION', 'shisharent'),
            'category_desc'  => __('Pre-packed artisanal phunnel bowls with heat management and 100% natural coconut coals in Kolkata.', 'shisharent'),
            'items' => [
                'SR BASIC CHILAM FLAVOUR' => [
                    'tier'        => 'BASIC',
                    'title'       => 'SR BASIC CHILAM FLAVOUR',
                    'slug'        => 'sr-basic-chilam',
                    'price'       => 499.00,
                    'price_fmt'   => 'â‚¹499',
                    'type'        => 'chilam',
                    'image'       => 'sr-basic-chilam.webp',
                    'tagline'     => __('Essential Single Flavour Pack', 'shisharent'),
                    'description' => __('Quick, reliable single-flavour clay phunnel pack with coconut coals for casual relaxation.', 'shisharent'),
                    'specs'       => __('1x Clay Bowl â€¢ Coconut Coals â€¢ 60-Min Session', 'shisharent'),
                ],
                'SR REGULAR CHILAM FLAVOUR' => [
                    'tier'        => 'REGULAR',
                    'title'       => 'SR REGULAR CHILAM FLAVOUR',
                    'slug'        => 'sr-regular-chilam',
                    'price'       => 599.00,
                    'price_fmt'   => 'â‚¹599',
                    'type'        => 'chilam',
                    'image'       => 'sr-regular-chilam.webp',
                    'tagline'     => __('Classic Dense Phunnel Pack', 'shisharent'),
                    'description' => __('Classic dense phunnel pack with authentic SR molasses, uniform airflow, and rich cloud density.', 'shisharent'),
                    'specs'       => __('Dense Phunnel Pack â€¢ Extended Coals â€¢ Standard Airflow', 'shisharent'),
                ],
                'SR PRIYAM CHILAM FLAVOUR' => [
                    'tier'        => 'PRIYAM',
                    'title'       => 'SR PRIYAM CHILAM FLAVOUR',
                    'slug'        => 'sr-priyam-chilam',
                    'price'       => 699.00,
                    'price_fmt'   => 'â‚¹699',
                    'type'        => 'chilam',
                    'image'       => 'sr-priyam-chilam.webp',
                    'tagline'     => __('Signature Dual-Layer Mixology', 'shisharent'),
                    'description' => __('Signature Priyam mixology pack featuring dual-layered herbal molasses and long-burning cube coals.', 'shisharent'),
                    'specs'       => __('Dual-Layered Mix â€¢ Long-Burning Cubes â€¢ 90-Min Duration', 'shisharent'),
                ],
                'SR SPECIAL CHILAM FLAVOUR' => [
                    'tier'        => 'SPECIAL',
                    'title'       => 'SR SPECIAL CHILAM FLAVOUR',
                    'slug'        => 'sr-special-chilam',
                    'price'       => 799.00,
                    'price_fmt'   => 'â‚¹799',
                    'type'        => 'chilam',
                    'image'       => 'sr-special-chilam.webp',
                    'tagline'     => __('Royal Reserve Master Blend', 'shisharent'),
                    'description' => __('Ultra-premium clay phunnel packed with reserve SR saffron molasses and master mixology finish.', 'shisharent'),
                    'specs'       => __('Royal Reserve Blend â€¢ Saffron Infusion â€¢ Maximum Clouds', 'shisharent'),
                ],
            ],
        ],
        'hookah' => [
            'category_id'    => 'hookah',
            'category_label' => __('HOOKAH RENTALS', 'shisharent'),
            'category_badge' => __('HOOKAH COLLECTION', 'shisharent'),
            'category_desc'  => __('Full complete hookah pipe rentals with pipes, hoses, coals, and doorstep setup in Kolkata.', 'shisharent'),
            'items' => [
                'SR BASIC HOOKAH' => [
                    'tier'        => 'BASIC',
                    'title'       => 'SR BASIC HOOKAH',
                    'slug'        => 'sr-basic-hookah',
                    'price'       => 899.00,
                    'price_fmt'   => 'â‚¹899',
                    'type'        => 'hookah',
                    'image'       => 'sr-basic-hookah.webp',
                    'tagline'     => __('Compact Starter Setup', 'shisharent'),
                    'description' => __('Compact, reliable hookah setup perfect for intimate chill sessions and casual gatherings.', 'shisharent'),
                    'specs'       => __('Ultrasonically Cleaned â€¢ Sealed Mouthpiece â€¢ Complete Kit', 'shisharent'),
                ],
                'SR REGULAR HOOKAH' => [
                    'tier'        => 'REGULAR',
                    'title'       => 'SR REGULAR HOOKAH',
                    'slug'        => 'sr-regular-hookah',
                    'price'       => 1099.00,
                    'price_fmt'   => 'â‚¹1,099',
                    'type'        => 'hookah',
                    'image'       => 'sr-regular-hookah.webp',
                    'tagline'     => __('Handcrafted Egyptian Brass', 'shisharent'),
                    'description' => __('Handcrafted Egyptian brass hookah with effortless wide-gauge draw and crystal glass base.', 'shisharent'),
                    'specs'       => __('Handcrafted Brass Stem â€¢ Medical Hose â€¢ 24H Rental Window', 'shisharent'),
                ],
                'SR PRIYAM HOOKAH' => [
                    'tier'        => 'PRIYAM',
                    'title'       => 'SR PRIYAM HOOKAH',
                    'slug'        => 'sr-priyam-hookah',
                    'price'       => 1299.00,
                    'price_fmt'   => 'â‚¹1,299',
                    'type'        => 'hookah',
                    'image'       => 'sr-priyam-hookah.webp',
                    'tagline'     => __('German Stainless Steel Precision', 'shisharent'),
                    'description' => __('Precision German stainless steel pipe with multi-port purge capability and quiet water diffuser.', 'shisharent'),
                    'specs'       => __('German Stainless Steel â€¢ Whisper Diffuser â€¢ White-Glove Setup', 'shisharent'),
                ],
                'SR SPECIAL HOOKAH' => [
                    'tier'        => 'SPECIAL',
                    'title'       => 'SR SPECIAL HOOKAH',
                    'slug'        => 'sr-special-hookah',
                    'price'       => 1499.00,
                    'price_fmt'   => 'â‚¹1,499',
                    'type'        => 'hookah',
                    'image'       => 'sr-special-hookah.webp',
                    'tagline'     => __('VIP Carbon Stealth Luxury', 'shisharent'),
                    'description' => __('Tactical stealth matte-black luxury hookah with carbon fiber stem for VIP celebrations.', 'shisharent'),
                    'specs'       => __('Carbon Matte Finish â€¢ HMD Heat Regulator â€¢ Priority Dispatch', 'shisharent'),
                ],
                'SR COMBO HOOKAH ALL FLAVOUR' => [
                    'tier'        => 'COMBO',
                    'title'       => 'SR COMBO HOOKAH ALL FLAVOUR',
                    'slug'        => 'sr-combo-hookah',
                    'price'       => 1999.00,
                    'price_fmt'   => 'â‚¹1,999',
                    'type'        => 'hookah',
                    'image'       => 'sr-combo-hookah.webp',
                    'tagline'     => __('The Ultimate All-Inclusive Party Package', 'shisharent'),
                    'description' => __('The ultimate all-inclusive party package. Luxury pipe setup with multi-flavour sampler and electric burner.', 'shisharent'),
                    'specs'       => __('Luxury Pipe Setup â€¢ Multi-Flavour Access â€¢ Electric Burner Included', 'shisharent'),
                ],
            ],
        ],
    ];
}

/**
 * Lookup a Rental Package by Title or Slug
 */
function bns_find_rental_package($query) {
    if (empty($query)) return null;
    $catalog = bns_get_rental_packages();
    $norm_q = strtoupper(trim($query));

    foreach ($catalog as $cat) {
        foreach ($cat['items'] as $title => $data) {
            if (strtoupper($title) === $norm_q || strtoupper($data['slug']) === $norm_q || str_replace('-', ' ', strtoupper($data['slug'])) === $norm_q) {
                return $data;
            }
        }
    }
    return null;
}

/**
 * Capture Rental, Chillum Material, and Hookah Base in Cart Item Data
 */
add_filter('woocommerce_add_cart_item_data', function($cart_item_data, $product_id, $variation_id) {
    if (isset($_REQUEST['rental_option']) && !empty($_REQUEST['rental_option'])) {
        $rental_name = sanitize_text_field(wp_unslash($_REQUEST['rental_option']));
        $pkg = bns_find_rental_package($rental_name);
        if ($pkg) {
            $cart_item_data['bns_rental_option'] = $pkg['title'];
            $cart_item_data['bns_rental_price']  = (float) $pkg['price'];
            $cart_item_data['bns_rental_type']   = $pkg['type'];
        } else {
            $cart_item_data['bns_rental_option'] = $rental_name;
        }
    }

    if (isset($_REQUEST['chillum_material']) && !empty($_REQUEST['chillum_material'])) {
        $mat = sanitize_text_field(wp_unslash($_REQUEST['chillum_material']));
        $is_gold_silicone = (strcasecmp($mat, 'Gold Silicone') === 0 || strcasecmp($mat, 'gold_silicone') === 0);
        $cart_item_data['bns_chillum_material'] = $is_gold_silicone ? 'Gold Silicone' : 'Classic Clay';
        $cart_item_data['bns_chillum_price']    = $is_gold_silicone ? 100.0 : 0.0;
    } elseif (!isset($cart_item_data['bns_chillum_material'])) {
        $cart_item_data['bns_chillum_material'] = 'Classic Clay';
        $cart_item_data['bns_chillum_price']    = 0.0;
    }

    $base_prices = [
        'none'     => 0,
        'standard' => 0,
        'ice'      => 100,
        'milk'     => 150,
        'both'     => 200,
        'ice_milk' => 200,
    ];
    $base_labels = [
        'none'     => __('No Base (Chilam Only)', 'shisharent'),
        'standard' => __('Standard Base (Included)', 'shisharent'),
        'ice'      => __('Ice Base (+â‚¹100)', 'shisharent'),
        'milk'     => __('Milk Base (+â‚¹150)', 'shisharent'),
        'both'     => __('Ice + Milk Base Combined (+â‚¹200)', 'shisharent'),
        'ice_milk' => __('Ice + Milk Base Combined (+â‚¹200)', 'shisharent'),
    ];

    if (isset($_REQUEST['hookah_base']) && !empty($_REQUEST['hookah_base'])) {
        $raw_base = sanitize_key($_REQUEST['hookah_base']);
    } elseif (isset($cart_item_data['bns_hookah_base'])) {
        $raw_base = sanitize_key($cart_item_data['bns_hookah_base']);
    } else {
        $raw_base = 'standard';
    }

    if (isset($base_prices[$raw_base])) {
        $cart_item_data['bns_hookah_base']       = $raw_base;
        $cart_item_data['bns_hookah_base_label'] = $base_labels[$raw_base];
        $cart_item_data['bns_hookah_base_price'] = $base_prices[$raw_base];
    }

    return $cart_item_data;
}, 10, 3);

/**
 * Dynamic Cart Price Calculation for Rental Packages & Hookah Base Upgrades
 */
add_action('woocommerce_before_calculate_totals', function($cart) {
    if (is_admin() && !defined('DOING_AJAX') && php_sapi_name() !== 'cli') {
        return;
    }

    $base_prices = [
        'none'     => 0,
        'standard' => 0,
        'ice'      => 100,
        'milk'     => 150,
        'both'     => 200,
        'ice_milk' => 200,
    ];

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        $base_fee = 0.0;
        $chillum_fee = (!empty($cart_item['bns_chillum_material']) && strcasecmp($cart_item['bns_chillum_material'], 'Gold Silicone') === 0) ? 100.0 : 0.0;

        if (!empty($cart_item['bns_hookah_base']) && isset($base_prices[$cart_item['bns_hookah_base']])) {
            $base_fee = (float) $base_prices[$cart_item['bns_hookah_base']];
        }

        if (!empty($cart_item['bns_rental_option'])) {
            $pkg = bns_find_rental_package($cart_item['bns_rental_option']);
            $rental_base_price = $pkg ? (float) $pkg['price'] : (isset($cart_item['bns_rental_price']) ? (float) $cart_item['bns_rental_price'] : 899.00);
            $product->set_price($rental_base_price + $base_fee + $chillum_fee);
        } elseif ($base_fee > 0 || $chillum_fee > 0) {
            $flavour_price = (float) get_post_meta($cart_item['product_id'], '_price', true);
            if (!$flavour_price) $flavour_price = 600.00;
            $product->set_price($flavour_price + $base_fee + $chillum_fee);
        }
    }
}, 20, 1);

/**
 * Display Rental Option, Chillum Material & Hookah Base in Cart and Checkout Summaries
 */
add_filter('woocommerce_get_item_data', function($item_data, $cart_item) {
    if (isset($cart_item['bns_rental_option']) && !empty($cart_item['bns_rental_option'])) {
        $item_data[] = [
            'name'  => __('Rental Setup', 'shisharent'),
            'value' => esc_html($cart_item['bns_rental_option']),
        ];
    }
    if (isset($cart_item['bns_chillum_material']) && !empty($cart_item['bns_chillum_material'])) {
        $is_gold = (strcasecmp($cart_item['bns_chillum_material'], 'Gold Silicone') === 0);
        $item_data[] = [
            'name'  => __('Chillum Material', 'shisharent'),
            'value' => $is_gold ? esc_html__('Gold Silicone (+â‚¹100)', 'shisharent') : esc_html__('Classic Clay (Included)', 'shisharent'),
        ];
    }
    if (isset($cart_item['bns_hookah_base']) && !empty($cart_item['bns_hookah_base'])) {
        $base_labels = [
            'none'     => __('No Base (Chilam Only)', 'shisharent'),
            'standard' => __('Standard Base (Included)', 'shisharent'),
            'ice'      => __('Ice Base (+â‚¹100)', 'shisharent'),
            'milk'     => __('Milk Base (+â‚¹150)', 'shisharent'),
            'both'     => __('Ice + Milk Base Combined (+â‚¹200)', 'shisharent'),
            'ice_milk' => __('Ice + Milk Base Combined (+â‚¹200)', 'shisharent'),
        ];
        $label = isset($base_labels[$cart_item['bns_hookah_base']]) ? $base_labels[$cart_item['bns_hookah_base']] : esc_html($cart_item['bns_hookah_base_label'] ?? $cart_item['bns_hookah_base']);
        $item_data[] = [
            'name'  => __('Hookah Base', 'shisharent'),
            'value' => esc_html($label),
        ];
    }
    return $item_data;
}, 10, 2);

/**
 * Persist Rental Option, Chillum Material & Hookah Base to WooCommerce Order Line Items
 */
add_action('woocommerce_checkout_create_order_line_item', function($item, $cart_item_key, $values, $order) {
    if (isset($values['bns_rental_option']) && !empty($values['bns_rental_option'])) {
        $item->add_meta_data(__('Selected Rental Setup', 'shisharent'), $values['bns_rental_option'], true);
        if (isset($values['bns_rental_price'])) {
            $item->add_meta_data('_bns_rental_price', $values['bns_rental_price'], true);
        }
    }
    if (isset($values['bns_chillum_material']) && !empty($values['bns_chillum_material'])) {
        $is_gold = (strcasecmp($values['bns_chillum_material'], 'Gold Silicone') === 0);
        $item->add_meta_data(__('Chillum Material', 'shisharent'), $is_gold ? __('Gold Silicone (+â‚¹100)', 'shisharent') : __('Classic Clay (Included)', 'shisharent'), true);
        $item->add_meta_data('_bns_chillum_material', $values['bns_chillum_material'], true);
        $item->add_meta_data('_bns_chillum_price', $is_gold ? 100 : 0, true);
    }
    if (isset($values['bns_hookah_base']) && !empty($values['bns_hookah_base'])) {
        $base_labels = [
            'none'     => __('No Base (Chilam Only)', 'shisharent'),
            'standard' => __('Standard Base (Included)', 'shisharent'),
            'ice'      => __('Ice Base (+â‚¹100)', 'shisharent'),
            'milk'     => __('Milk Base (+â‚¹150)', 'shisharent'),
            'both'     => __('Ice + Milk Base Combined (+â‚¹200)', 'shisharent'),
            'ice_milk' => __('Ice + Milk Base Combined (+â‚¹200)', 'shisharent'),
        ];
        $label = isset($base_labels[$values['bns_hookah_base']]) ? $base_labels[$values['bns_hookah_base']] : esc_html($values['bns_hookah_base_label'] ?? $values['bns_hookah_base']);
        $item->add_meta_data(__('Hookah Base', 'shisharent'), $label, true);
        $item->add_meta_data('_bns_hookah_base', $values['bns_hookah_base'], true);
        if (isset($values['bns_hookah_base_price'])) {
            $item->add_meta_data('_bns_hookah_base_price', $values['bns_hookah_base_price'], true);
        }
    }
}, 10, 4);

/**
 * AJAX Handler to add Flavour + Rental Option + Chillum Material + Hookah Base to Cart
 */
function bns_ajax_add_flavour_rental_to_cart() {
    if (defined('WC_ABSPATH')) {
        include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
        include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
    }
    if (null === WC()->session) {
        $session_class = apply_filters('woocommerce_session_handler', 'WC_Session_Handler');
        WC()->session = new $session_class();
        WC()->session->init();
    }
    if (null === WC()->customer) {
        WC()->customer = new WC_Customer(get_current_user_id(), true);
    }
    if (null === WC()->cart) {
        WC()->cart = new WC_Cart();
    }

    $product_id       = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $rental_option    = isset($_POST['rental_option']) ? sanitize_text_field(wp_unslash($_POST['rental_option'])) : '';
    $chillum_material = isset($_POST['chillum_material']) ? sanitize_text_field(wp_unslash($_POST['chillum_material'])) : 'Classic Clay';
    $hookah_base      = isset($_POST['hookah_base']) ? sanitize_key($_POST['hookah_base']) : 'standard';
    $quantity         = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if (!$product_id) {
        wp_send_json_error(['message' => 'Invalid product ID']);
    }

    $base_prices = [
        'none'     => 0,
        'standard' => 0,
        'ice'      => 100,
        'milk'     => 150,
        'both'     => 200,
        'ice_milk' => 200,
    ];
    $base_labels = [
        'none'     => __('No Base (Chilam Only)', 'shisharent'),
        'standard' => __('Standard Base (Included)', 'shisharent'),
        'ice'      => __('Ice Base (+â‚¹100)', 'shisharent'),
        'milk'     => __('Milk Base (+â‚¹150)', 'shisharent'),
        'both'     => __('Ice + Milk Base Combined (+â‚¹200)', 'shisharent'),
        'ice_milk' => __('Ice + Milk Base Combined (+â‚¹200)', 'shisharent'),
    ];

    if (!isset($base_prices[$hookah_base])) {
        $hookah_base = 'standard';
    }

    $cart_item_data = [];
    if (!empty($rental_option)) {
        $pkg = bns_find_rental_package($rental_option);
        $cart_item_data['bns_rental_option'] = $pkg ? $pkg['title'] : $rental_option;
        if ($pkg) {
            $cart_item_data['bns_rental_price'] = (float) $pkg['price'];
            $cart_item_data['bns_rental_type']  = $pkg['type'];
        }
    }
    $is_gold_silicone = (strcasecmp($chillum_material, 'Gold Silicone') === 0 || strcasecmp($chillum_material, 'gold_silicone') === 0);
    $cart_item_data['bns_chillum_material']  = $is_gold_silicone ? 'Gold Silicone' : 'Classic Clay';
    $cart_item_data['bns_chillum_price']     = $is_gold_silicone ? 100.0 : 0.0;
    $cart_item_data['bns_hookah_base']       = $hookah_base;
    $cart_item_data['bns_hookah_base_label'] = $base_labels[$hookah_base];
    $cart_item_data['bns_hookah_base_price'] = $base_prices[$hookah_base];

    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, 0, [], $cart_item_data);

    if ($cart_item_key) {
        wp_send_json_success([
            'message'          => 'Added to cart successfully',
            'cart_url'         => wc_get_cart_url(),
            'checkout_url'     => wc_get_checkout_url(),
            'cart_count'       => WC()->cart->get_cart_contents_count(),
            'rental_option'    => $cart_item_data['bns_rental_option'] ?? '',
            'chillum_material' => $cart_item_data['bns_chillum_material'],
            'hookah_base'      => $hookah_base,
            'base_label'       => $base_labels[$hookah_base],
            'base_price'       => $base_prices[$hookah_base],
        ]);
    } else {
        wp_send_json_error(['message' => 'Could not add to cart']);
    }
}
add_action('wp_ajax_bns_add_flavour_rental_to_cart', 'bns_ajax_add_flavour_rental_to_cart');
add_action('wp_ajax_nopriv_bns_add_flavour_rental_to_cart', 'bns_ajax_add_flavour_rental_to_cart');

/**
 * ==========================================================================
 * KOLKATA & INDIA EXCLUSIVE WOOCOMMERCE & CHECKOUT CONFIGURATION
 * ==========================================================================
 */

// 1. Enqueue Cart & Checkout CSS
function bns_enqueue_checkout_styles() {
    if (is_cart() || is_checkout() || is_account_page()) {
        if (file_exists(BNS_THEME_DIR . '/assets/css/cart.css') && (is_cart() || is_checkout())) {
            wp_enqueue_style(
                'bns-cart-style',
                BNS_THEME_URI . '/assets/css/cart.css',
                ['bns-theme-main'],
                BNS_THEME_VERSION
            );
        }
        if (file_exists(BNS_THEME_DIR . '/assets/css/checkout.css') && is_checkout()) {
            wp_enqueue_style(
                'bns-checkout-style',
                BNS_THEME_URI . '/assets/css/checkout.css',
                ['bns-theme-main', 'bns-cart-style'],
                BNS_THEME_VERSION
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'bns_enqueue_checkout_styles', 25);

// 2. Strict India & West Bengal WooCommerce Settings Enforcement
add_action('init', function() {
    update_option('woocommerce_default_country', 'IN:WB');
    update_option('woocommerce_allowed_countries', 'specific');
    update_option('woocommerce_specific_allowed_countries', ['IN']);
    update_option('woocommerce_ship_to_countries', 'specific');
    update_option('woocommerce_specific_ship_to_countries', ['IN']);
    update_option('woocommerce_shipping_cost_requires_address', 'no');
    
    update_option('woocommerce_currency', 'INR');
    update_option('woocommerce_currency_pos', 'left');
    update_option('woocommerce_price_thousand_sep', ',');
    update_option('woocommerce_price_decimal_sep', '.');
    update_option('woocommerce_price_num_decimals', 0);
}, 10);

// Restrict countries filter
add_filter('woocommerce_countries_allowed_countries', function($countries) {
    return ['IN' => 'India'];
}, 999);

add_filter('woocommerce_countries_shipping_countries', function($countries) {
    return ['IN' => 'India'];
}, 999);

// Default country and state
add_filter('default_checkout_billing_country', function() { return 'IN'; }, 999);
add_filter('default_checkout_billing_state', function() { return 'WB'; }, 999);
add_filter('default_checkout_billing_city', function() { return 'Kolkata'; }, 999);
add_filter('default_checkout_shipping_country', function() { return 'IN'; }, 999);
add_filter('default_checkout_shipping_state', function() { return 'WB'; }, 999);
add_filter('default_checkout_shipping_city', function() { return 'Kolkata'; }, 999);

// Disable shipping to different address by default
add_filter('woocommerce_ship_to_different_address_checked', '__return_false', 999);

// 3. Define and Redesign Kolkata Mandatory & Clear Checkout Fields
add_filter('woocommerce_checkout_fields', function($fields) {
    // -------------------------------------------------------------
    // SECTION 1: CONTACT INFORMATION
    // -------------------------------------------------------------
    $fields['billing']['billing_first_name'] = [
        'label'       => __('FULL NAME', 'shisharent'),
        'placeholder' => __('Enter your full name', 'shisharent'),
        'required'    => true,
        'class'       => ['form-row-wide', 'bns-form-field'],
        'priority'    => 10,
        'autocomplete'=> 'name',
    ];

    // Hide redundant last name
    $fields['billing']['billing_last_name'] = [
        'label'       => false,
        'placeholder' => '',
        'required'    => false,
        'class'       => ['bns-hidden-field'],
        'priority'    => 15,
    ];

    // Mobile Number (Mandatory, 10-digit Indian)
    $fields['billing']['billing_phone'] = [
        'type'        => 'tel',
        'label'       => __('MOBILE NUMBER', 'shisharent'),
        'placeholder' => __('Enter your 10-digit mobile number', 'shisharent'),
        'required'    => true,
        'class'       => ['form-row-wide', 'bns-form-field'],
        'priority'    => 20,
        'custom_attributes' => [
            'maxlength' => '15',
            'pattern'   => '[0-9+ -]*',
            'inputmode' => 'tel',
        ],
        'autocomplete'=> 'tel',
    ];

    // Email Address (Mandatory)
    $fields['billing']['billing_email'] = [
        'type'        => 'email',
        'label'       => __('EMAIL ADDRESS', 'shisharent'),
        'placeholder' => __('Enter your email address', 'shisharent'),
        'required'    => true,
        'class'       => ['form-row-wide', 'bns-form-field'],
        'priority'    => 30,
        'autocomplete'=> 'email',
    ];

    // -------------------------------------------------------------
    // SECTION 2: DELIVERY ADDRESS (Kolkata Specific)
    // -------------------------------------------------------------
    // Complete Delivery Address
    $fields['billing']['billing_address_1'] = [
        'label'       => __('DELIVERY ADDRESS', 'shisharent'),
        'placeholder' => __('House/Flat No., Building, Street', 'shisharent'),
        'description' => __('Enter the complete address where you want your order delivered.', 'shisharent'),
        'required'    => true,
        'class'       => ['form-row-wide', 'bns-form-field'],
        'priority'    => 40,
        'autocomplete'=> 'address-line1',
    ];

    // Apartment / Flat / Floor / Building (Optional) - no US "suite"
    $fields['billing']['billing_address_2'] = [
        'label'       => __('APARTMENT / FLAT / FLOOR', 'shisharent') . ' <span class="bns-optional-tag">(Optional)</span>',
        'placeholder' => __('Apartment / Flat / Floor / Building (optional)', 'shisharent'),
        'required'    => false,
        'class'       => ['form-row-wide', 'bns-form-field'],
        'priority'    => 50,
        'autocomplete'=> 'address-line2',
    ];

    // Area / Locality (Mandatory)
    $fields['billing']['billing_area'] = [
        'type'        => 'text',
        'label'       => __('AREA / LOCALITY', 'shisharent'),
        'placeholder' => __('e.g. Ballygunge, Salt Lake, New Town', 'shisharent'),
        'required'    => true,
        'class'       => ['form-row-wide', 'bns-form-field'],
        'priority'    => 60,
    ];

    // City (Default Kolkata)
    $fields['billing']['billing_city'] = [
        'type'        => 'text',
        'label'       => __('CITY', 'shisharent'),
        'placeholder' => __('Kolkata', 'shisharent'),
        'default'     => 'Kolkata',
        'required'    => true,
        'class'       => ['form-row-first', 'bns-form-field'],
        'priority'    => 70,
        'autocomplete'=> 'address-level2',
    ];

    // State (Default West Bengal)
    $fields['billing']['billing_state'] = [
        'type'        => 'state',
        'label'       => __('STATE', 'shisharent'),
        'default'     => 'WB',
        'required'    => true,
        'class'       => ['form-row-last', 'bns-form-field'],
        'priority'    => 80,
        'autocomplete'=> 'address-level1',
    ];

    // Country (Default India)
    $fields['billing']['billing_country'] = [
        'type'        => 'country',
        'label'       => __('COUNTRY / REGION', 'shisharent'),
        'default'     => 'IN',
        'required'    => true,
        'class'       => ['form-row-first', 'bns-form-field'],
        'priority'    => 90,
        'autocomplete'=> 'country',
    ];

    // PIN Code (Mandatory 6-digit)
    $fields['billing']['billing_postcode'] = [
        'type'        => 'text',
        'label'       => __('PIN CODE', 'shisharent'),
        'placeholder' => __('Enter 6-digit PIN code', 'shisharent'),
        'required'    => true,
        'class'       => ['form-row-last', 'bns-form-field'],
        'priority'    => 100,
        'custom_attributes' => [
            'maxlength' => '6',
            'pattern'   => '[0-9]{6}',
            'inputmode' => 'numeric',
        ],
        'autocomplete'=> 'postal-code',
    ];

    // -------------------------------------------------------------
    // SECTION 3: ORDER NOTES (Optional)
    // -------------------------------------------------------------
    if (isset($fields['order']['order_comments'])) {
        $fields['order']['order_comments']['label'] = __('ADD A NOTE TO YOUR ORDER', 'shisharent') . ' <span class="bns-optional-tag">(Optional)</span>';
        $fields['order']['order_comments']['placeholder'] = __('Optional â€” add delivery instructions, landmark details or other information for our team.', 'shisharent');
        $fields['order']['order_comments']['required'] = false;
        $fields['order']['order_comments']['class'] = ['form-row-wide', 'bns-form-field'];
    }

    // Remove unwanted fields
    unset($fields['billing']['billing_company']);

    return $fields;
}, 999);

// 4. Strict Validation on Checkout Submission
add_action('woocommerce_checkout_process', function() {
    // 1. Full Name Validation
    $full_name = isset($_POST['billing_first_name']) ? trim(sanitize_text_field($_POST['billing_first_name'])) : '';
    if (empty($full_name)) {
        wc_add_notice(__('Please enter your full name.', 'shisharent'), 'error');
    }

    // 2. Mobile Number Validation (Indian 10-digit)
    $raw_phone = isset($_POST['billing_phone']) ? trim(sanitize_text_field($_POST['billing_phone'])) : '';
    $clean_phone = preg_replace('/[^0-9]/', '', $raw_phone);
    if (strlen($clean_phone) === 12 && substr($clean_phone, 0, 2) === '91') {
        $clean_phone = substr($clean_phone, 2);
    } elseif (strlen($clean_phone) === 11 && substr($clean_phone, 0, 1) === '0') {
        $clean_phone = substr($clean_phone, 1);
    }

    if (empty($clean_phone) || strlen($clean_phone) !== 10 || !in_array($clean_phone[0], ['6', '7', '8', '9'])) {
        wc_add_notice(__('Please enter a valid 10-digit Indian mobile number.', 'shisharent'), 'error');
    }

    // 3. Email Validation
    $email = isset($_POST['billing_email']) ? trim(sanitize_email($_POST['billing_email'])) : '';
    if (empty($email) || !is_email($email)) {
        wc_add_notice(__('Please enter a valid email address.', 'shisharent'), 'error');
    }

    // 4. Complete Delivery Address Validation
    $address_1 = isset($_POST['billing_address_1']) ? trim(sanitize_text_field($_POST['billing_address_1'])) : '';
    if (empty($address_1)) {
        wc_add_notice(__('Please enter your complete delivery address (House/Flat No., Building, Street).', 'shisharent'), 'error');
    }

    // 5. Area / Locality Validation
    $area = isset($_POST['billing_area']) ? trim(sanitize_text_field($_POST['billing_area'])) : '';
    if (empty($area)) {
        wc_add_notice(__('Please enter your area / locality (e.g. Ballygunge, Salt Lake, New Town).', 'shisharent'), 'error');
    }

    // 6. City / State Defaults
    if (empty($_POST['billing_city'])) {
        $_POST['billing_city'] = 'Kolkata';
    }
    if (empty($_POST['billing_state'])) {
        $_POST['billing_state'] = 'WB';
    }
    if (empty($_POST['billing_country'])) {
        $_POST['billing_country'] = 'IN';
    }

    // 7. PIN Code & Strict 3-District Serviceability Validation
    $postcode = isset($_POST['billing_postcode']) ? trim(sanitize_text_field($_POST['billing_postcode'])) : '';
    if (empty($postcode)) {
        wc_add_notice(__('Please enter your 6-digit delivery PIN code.', 'shisharent'), 'error');
    } elseif (!preg_match('/^[1-9][0-9]{5}$/', $postcode)) {
        wc_add_notice(__('Please enter a valid 6-digit Indian PIN code.', 'shisharent'), 'error');
    } else {
        if (class_exists('Hookah_Serviceability')) {
            $serviceability = Hookah_Serviceability::check_pin_serviceability($postcode);
            if (!$serviceability['deliverable']) {
                wc_add_notice(
                    sprintf(
                        __('âœ• DELIVERY NOT AVAILABLE: Currently serving Kolkata, North 24 Parganas and South 24 Parganas only. (Entered PIN: %s)', 'shisharent'),
                        esc_html($postcode)
                    ),
                    'error'
                );
            }
        }
    }

    // 8. Age Verification
    if (empty($_POST['bns_age_verification'])) {
        wc_add_notice(__('You must verify that you are at least 21 years of age to rent a hookah.', 'shisharent'), 'error');
    }
}, 1);

// 5. Persist Order Metadata Cleanly
add_action('woocommerce_checkout_update_order_meta', function($order_id) {
    // Process full name into first & last name
    if (!empty($_POST['billing_first_name'])) {
        $full_name = trim(sanitize_text_field($_POST['billing_first_name']));
        $parts = explode(' ', $full_name, 2);
        $first_name = $parts[0];
        $last_name  = isset($parts[1]) ? $parts[1] : '';

        update_post_meta($order_id, '_billing_full_name', $full_name);
        update_post_meta($order_id, '_billing_first_name', $first_name);
        update_post_meta($order_id, '_billing_last_name', $last_name);
        update_post_meta($order_id, '_shipping_first_name', $first_name);
        update_post_meta($order_id, '_shipping_last_name', $last_name);
    }

    // Format phone with +91
    if (!empty($_POST['billing_phone'])) {
        $raw_phone = trim(sanitize_text_field($_POST['billing_phone']));
        $clean_phone = preg_replace('/[^0-9]/', '', $raw_phone);
        if (strlen($clean_phone) === 12 && substr($clean_phone, 0, 2) === '91') {
            $clean_phone = substr($clean_phone, 2);
        } elseif (strlen($clean_phone) === 11 && substr($clean_phone, 0, 1) === '0') {
            $clean_phone = substr($clean_phone, 1);
        }
        $formatted_phone = '+91 ' . $clean_phone;
        update_post_meta($order_id, '_billing_phone', $formatted_phone);
        update_post_meta($order_id, '_shipping_phone', $formatted_phone);
    }

    // Area & Delivery Address
    if (!empty($_POST['billing_area'])) {
        $area = sanitize_text_field($_POST['billing_area']);
        update_post_meta($order_id, '_billing_area', $area);
        update_post_meta($order_id, '_shipping_area', $area);
        update_post_meta($order_id, '_bns_area', $area);
    }

    // Mirror billing address to shipping address
    if (!empty($_POST['billing_address_1'])) {
        update_post_meta($order_id, '_shipping_address_1', sanitize_text_field($_POST['billing_address_1']));
    }
    if (!empty($_POST['billing_address_2'])) {
        update_post_meta($order_id, '_shipping_address_2', sanitize_text_field($_POST['billing_address_2']));
    }
    if (!empty($_POST['billing_city'])) {
        update_post_meta($order_id, '_shipping_city', sanitize_text_field($_POST['billing_city']));
    }
    if (!empty($_POST['billing_state'])) {
        update_post_meta($order_id, '_shipping_state', sanitize_text_field($_POST['billing_state']));
    }
    if (!empty($_POST['billing_country'])) {
        update_post_meta($order_id, '_shipping_country', sanitize_text_field($_POST['billing_country']));
    }
    if (!empty($_POST['billing_postcode'])) {
        update_post_meta($order_id, '_shipping_postcode', sanitize_text_field($_POST['billing_postcode']));
    }

    // Rental Schedule Meta
    if (!empty($_POST['bns_rental_date'])) {
        update_post_meta($order_id, '_bns_rental_date', sanitize_text_field($_POST['bns_rental_date']));
    }
    if (!empty($_POST['bns_delivery_slot'])) {
        update_post_meta($order_id, '_bns_delivery_slot', sanitize_text_field($_POST['bns_delivery_slot']));
    }
    if (!empty($_POST['bns_age_verification'])) {
        update_post_meta($order_id, '_bns_age_verified', 'yes');
    }
}, 10, 1);






// Auto-bootstrap Email Authentication if plugin is inactive
add_action('after_setup_theme', function() {
    if (!class_exists('Hookah_Email_Auth')) {
        $plugin_auth = WP_PLUGIN_DIR . '/hookah-rental-core/includes/class-hookah-email-auth.php';
        if (file_exists($plugin_auth)) {
            require_once $plugin_auth;
            new Hookah_Email_Auth();
        }
    }
}, 5);
