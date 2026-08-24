<?php
/**
 * ShishaRent WooCommerce Catalog Seeder
 * Populates WooCommerce with initial rental packages, hookahs, flavours and accessories if not present.
 */

if (!defined('ABSPATH')) {
    require_once('/var/www/html/wp-load.php');
}

function bns_seed_woocommerce_catalog() {
    if (!class_exists('WooCommerce')) {
        return;
    }

    if (get_option('bns_catalog_seeded_v1', false)) {
        return;
    }

    // Check if products already exist
    $existing = wc_get_products(['limit' => 1]);
    if (!empty($existing)) {
        update_option('bns_catalog_seeded_v1', true);
        return;
    }

    // 1. Create Product Categories
    $categories = [
        'rental-packages' => ['name' => 'Rental Packages', 'desc' => 'All-inclusive hookah rental bundles with tobacco and coals'],
        'hookahs'         => ['name' => 'Premium Hookahs', 'desc' => 'Luxury handcrafted Egyptian, German, and Turkish hookahs'],
        'flavours'        => ['name' => 'Flavours & Blends', 'desc' => 'Curated premium molasses and tobacco-free herbal blends'],
        'accessories'     => ['name' => 'Coals & Accessories', 'desc' => 'Coconut charcoal, electric burners, bowls and mouthpieces'],
    ];

    $cat_ids = [];
    foreach ($categories as $slug => $cat_data) {
        $term = get_term_by('slug', $slug, 'product_cat');
        if (!$term) {
            $created = wp_insert_term($cat_data['name'], 'product_cat', [
                'slug' => $slug,
                'description' => $cat_data['desc'],
            ]);
            if (!is_wp_error($created)) {
                $cat_ids[$slug] = $created['term_id'];
            }
        } else {
            $cat_ids[$slug] = $term->term_id;
        }
    }

    // 2. Define Products
    $products_data = [
        // Packages
        [
            'name'        => 'Solo Standard 24H Package',
            'slug'        => 'solo-standard-24h',
            'description' => 'Perfect for an evening chill or solo session. Includes 1 Authentic Egyptian Hookah, 2 Curated Flavour Heads, Coconut Charcoal & 4 Hygienic Mouthpieces.',
            'short_desc'  => '1 Hookah • 2 Flavours • Coconut Coals • 24H Duration',
            'price'       => '1499',
            'regular_price' => '1799',
            'category'    => 'rental-packages',
            'image'       => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80',
            'featured'    => true,
        ],
        [
            'name'        => 'Duo Weekend 48H Package',
            'slug'        => 'duo-weekend-48h',
            'description' => 'Ideal for weekend getaways and small gatherings. Includes 1 Premium Borosilicate Hookah, 4 Flavour Heads, Extended Coal Pack & 8 Mouthpieces.',
            'short_desc'  => '1 Luxury Hookah • 4 Flavours • Extended Coals • 48H Duration',
            'price'       => '2499',
            'regular_price' => '2999',
            'category'    => 'rental-packages',
            'image'       => 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?auto=format&fit=crop&w=800&q=80',
            'featured'    => true,
        ],
        [
            'name'        => 'VIP Party Celebration 72H Package',
            'slug'        => 'vip-party-72h',
            'description' => 'The ultimate luxury experience for parties and celebrations. Includes Top-tier Stealth Hookah, 6 Curated Flavour Heads, Heavy-duty Coal Burner, XL Charcoal & 12 Mouthpieces.',
            'short_desc'  => '1 Stealth Hookah • 6 Flavours • Coal Burner + XL Coals • 72H Duration',
            'price'       => '3499',
            'regular_price' => '4299',
            'category'    => 'rental-packages',
            'image'       => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=800&q=80',
            'featured'    => true,
        ],

        // Hookahs
        [
            'name'        => 'Khalil Mamoon Gold Classic',
            'slug'        => 'km-gold-classic',
            'description' => 'Authentic Egyptian brass handcrafted hookah with ornate gold accents and deep rumbling draw.',
            'short_desc'  => '78cm Handcrafted Egyptian Brass & Bohemian Glass',
            'price'       => '999',
            'regular_price' => '1200',
            'category'    => 'hookahs',
            'image'       => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80',
            'featured'    => true,
        ],
        [
            'name'        => 'Amy Deluxe Stainless Steel Heavy',
            'slug'        => 'amy-deluxe-ss',
            'description' => 'German engineered click-system hookah delivering effortless airtight cloud density and modern purge valve system.',
            'short_desc'  => '65cm Heavy Brushed Stainless Steel • Click System',
            'price'       => '1199',
            'regular_price' => '1450',
            'category'    => 'hookahs',
            'image'       => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&w=800&q=80',
            'featured'    => true,
        ],
        [
            'name'        => 'Oduman Glass Modern N2 Travel',
            'slug'        => 'oduman-n2-travel',
            'description' => 'Compact ultra-pure Turkish borosilicate glass hookah with integrated LED light base and silicone hose.',
            'short_desc'  => '28cm Borosilicate Glass with Integrated LED Base',
            'price'       => '1299',
            'regular_price' => '1599',
            'category'    => 'hookahs',
            'image'       => 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?auto=format&fit=crop&w=800&q=80',
            'featured'    => false,
        ],
        [
            'name'        => 'Starbuzz Carbine Matte Stealth',
            'slug'        => 'starbuzz-carbine-matte',
            'description' => 'Tactical all-terrain hookah with 4-point all-terrain stabilizing legs, 360-degree rotating hose stem, and matte anodized finish.',
            'short_desc'  => '72cm Aerospace Anodized Aluminum & V2A Steel',
            'price'       => '1499',
            'regular_price' => '1899',
            'category'    => 'hookahs',
            'image'       => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=800&q=80',
            'featured'    => true,
        ],

        // Flavours
        [
            'name'        => 'Blueberry Mint Ice (Al Fakher)',
            'slug'        => 'blueberry-mint-ice',
            'description' => 'Sweet wild forest blueberries paired with refreshing icy peppermint chill.',
            'short_desc'  => 'Signature Al Fakher Blend • 50g Head',
            'price'       => '350',
            'regular_price' => '400',
            'category'    => 'flavours',
            'image'       => 'https://images.unsplash.com/photo-1510627489930-0c1b0bfb6785?auto=format&fit=crop&w=800&q=80',
            'featured'    => false,
        ],
        [
            'name'        => 'Love 66 Passionfruit Melon (Adalya)',
            'slug'        => 'love-66-adalya',
            'description' => 'The world-famous red berry, honeydew melon, passion fruit, and cool mint sensation.',
            'short_desc'  => 'World Famous Adalya Blend • 50g Head',
            'price'       => '400',
            'regular_price' => '450',
            'category'    => 'flavours',
            'image'       => 'https://images.unsplash.com/photo-1528825871115-3581a5387919?auto=format&fit=crop&w=800&q=80',
            'featured'    => false,
        ],
        [
            'name'        => 'Paan Raas Royal Fusion (Afzal)',
            'slug'        => 'paan-raas-afzal',
            'description' => 'Rich aromatic Indian betel leaf with sweet katha, gulkand, and cooling menthol.',
            'short_desc'  => 'Authentic Desi Paan Flavour • 50g Head',
            'price'       => '300',
            'regular_price' => '350',
            'category'    => 'flavours',
            'image'       => 'https://images.unsplash.com/photo-1546554137-f86b9593a222?auto=format&fit=crop&w=800&q=80',
            'featured'    => false,
        ],
        [
            'name'        => 'Double Apple Two Apples (Al Fakher)',
            'slug'        => 'double-apple-al-fakher',
            'description' => 'The timeless classic combination of sweet red apple, tart green apple, and rich anise spice.',
            'short_desc'  => 'Traditional Anise & Sweet Apple • 50g Head',
            'price'       => '350',
            'regular_price' => '400',
            'category'    => 'flavours',
            'image'       => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=800&q=80',
            'featured'    => false,
        ],

        // Accessories
        [
            'name'        => 'Natural Coconut Charcoal (1kg Cube Pack)',
            'slug'        => 'natural-coconut-charcoal-1kg',
            'description' => '100% natural compressed coconut shell cubes. Low ash, odorless, burning for up to 90 minutes per cube.',
            'short_desc'  => '1kg Box (72 Cubes) • Zero Chemicals & Odorless',
            'price'       => '450',
            'regular_price' => '550',
            'category'    => 'accessories',
            'image'       => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=800&q=80',
            'featured'    => false,
        ],
        [
            'name'        => 'Electric Fast Coal Burner (500W)',
            'slug'        => 'electric-coal-burner-500w',
            'description' => 'High efficiency electric coil burner specifically designed for glowing coconut coals in under 5 minutes.',
            'short_desc'  => '500W Fast Coil Heating • Heat Resistant Handle',
            'price'       => '899',
            'regular_price' => '1199',
            'category'    => 'accessories',
            'image'       => 'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?auto=format&fit=crop&w=800&q=80',
            'featured'    => false,
        ],
        [
            'name'        => 'Silicone Phunnel Bowl & Heat Management Device',
            'slug'        => 'silicone-phunnel-bowl-hmd',
            'description' => 'Unbreakable food-grade silicone bowl with aluminum Kaloud-style heat regulator for even molasses vaporization.',
            'short_desc'  => 'Drop-Proof Silicone + Anodized Aluminum HMD',
            'price'       => '650',
            'regular_price' => '850',
            'category'    => 'accessories',
            'image'       => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?auto=format&fit=crop&w=800&q=80',
            'featured'    => false,
        ],
    ];

    foreach ($products_data as $p) {
        $product = new WC_Product_Simple();
        $product->set_name($p['name']);
        $product->set_slug($p['slug']);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_description($p['description']);
        $product->set_short_description($p['short_desc']);
        $product->set_regular_price($p['regular_price']);
        $product->set_sale_price($p['price']);
        $product->set_price($p['price']);
        $product->set_manage_stock(false);
        $product->set_stock_status('instock');
        $product->set_featured($p['featured']);

        if (isset($cat_ids[$p['category']])) {
            $product->set_category_ids([$cat_ids[$p['category']]]);
        }

        // Store custom meta for image fallback
        $product->update_meta_data('_bns_image_url', $p['image']);

        $product->save();
    }

    update_option('bns_catalog_seeded_v1', true);
}
