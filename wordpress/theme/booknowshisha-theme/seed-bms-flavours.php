<?php
/**
 * ShishaRent BMS Flavours Catalog Seeder
 *
 * Populates WooCommerce with the exact 23 BMS flavour products,
 * assigns authentic studio photographs from shisharent-gallery,
 * sets correct ₹ INR prices, and ensures the Shop page contains ONLY these 23 flavours.
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    require_once('/var/www/html/wp-load.php');
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

function bns_seed_bms_flavours_catalog($force_reseed = true) {
    if (!class_exists('WooCommerce')) {
        return ['status' => 'error', 'message' => 'WooCommerce is not active'];
    }

    // Set WooCommerce Currency to INR
    update_option('woocommerce_currency', 'INR');
    update_option('woocommerce_currency_pos', 'left'); // ₹600.00
    update_option('woocommerce_price_thousand_sep', ',');
    update_option('woocommerce_price_decimal_sep', '.');
    update_option('woocommerce_price_num_decimals', 2);

    // Source staging directory for gallery images inside container
    $upload_dir = wp_upload_dir();
    $target_dir = $upload_dir['basedir'] . '/flavours';
    if (!file_exists($target_dir)) {
        wp_mkdir_p($target_dir);
    }

    // 1. Ensure "Flavours & Blends" category exists
    $cat_slug = 'flavours';
    $cat_name = 'Flavours & Blends';
    $term = get_term_by('slug', $cat_slug, 'product_cat');
    if (!$term) {
        $created_cat = wp_insert_term($cat_name, 'product_cat', [
            'slug' => $cat_slug,
            'description' => 'Genuine SR artisanal molasses and premium international shisha flavour blends.',
        ]);
        $cat_id = is_array($created_cat) ? $created_cat['term_id'] : 0;
    } else {
        $cat_id = $term->term_id;
    }

    // 2. Exact 23 SR Flavours Definitions & Mapped Gallery Images
    $bms_flavours = [
        [
            'name'        => 'SR Blueberry Blast',
            'slug'        => 'bms-blueberry-blast',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 10.59.25 AM (2).jpeg',
            'gallery_id'  => 'shisharent-gallery-071.jpeg',
            'description' => 'Succulent wild alpine blueberries infused with icy coolness for dense, sweet, and refreshing clouds.',
            'short_desc'  => 'Wild Blueberry • Crystal Ice • Signature SR Blend',
            'sku'         => 'BMS-FLV-001',
        ],
        [
            'name'        => 'SR Brainfreezer Gum Rose',
            'slug'        => 'bms-brainfreezer-gum-rose',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 11.44.19 AM (1).jpeg',
            'gallery_id'  => 'shisharent-gallery-093.jpeg',
            'description' => 'A chilling sub-zero menthol rush combined with fragrant pink Damask rose petals and sweet aromatic spearmint gum.',
            'short_desc'  => 'Sub-Zero Menthol • Damask Rose • Sweet Spearmint Gum',
            'sku'         => 'BMS-FLV-002',
        ],
        [
            'name'        => 'SR Candy Crush',
            'slug'        => 'bms-candy-crush',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 11.44.18 AM (1).jpeg',
            'gallery_id'  => 'shisharent-gallery-092.jpeg',
            'description' => 'A playful confectionary blast of sweet candied berries, rainbow fruit drops, and velvet bubblegum.',
            'short_desc'  => 'Candied Fruit Drops • Sweet Berries • Rainbow Confection',
            'sku'         => 'BMS-FLV-003',
        ],
        [
            'name'        => 'SR Chief Commissioner',
            'slug'        => 'bms-chief-commissioner',
            'price'       => '650.00', // ONLY flavour at 650.00
            'image_file'  => 'WhatsApp Image 2026-08-23 at 11.44.17 AM (2).jpeg',
            'gallery_id'  => 'shisharent-gallery-090.jpeg',
            'description' => 'The crown jewel of royal shisha. Infused with pure edible silver vark, Kashmiri saffron, and aged betel essence for a truly regal smoke.',
            'short_desc'  => 'Royal Silver Vark • Kashmiri Saffron • Aged Royal Paan',
            'sku'         => 'BMS-FLV-004',
        ],
        [
            'name'        => 'SR Cola Blast',
            'slug'        => 'bms-cola-blast',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 11.44.18 AM.jpeg',
            'gallery_id'  => 'shisharent-gallery-089.jpeg',
            'description' => 'Crisp effervescent vintage cola syrup spiked with crushed ice cubes and warming botanical notes.',
            'short_desc'  => 'Vintage Sparkling Cola • Crushed Ice • Botanical Spices',
            'sku'         => 'BMS-FLV-005',
        ],
        [
            'name'        => 'SR Dark Rose',
            'slug'        => 'bms-dark-rose',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 11.44.20 AM (1).jpeg',
            'gallery_id'  => 'shisharent-gallery-096.jpeg',
            'description' => 'An alluring, velvety floral blend featuring dark black rose petals, night-blooming jasmine, and rich dark molasses.',
            'short_desc'  => 'Velvet Black Rose • Night Jasmine • Dark Molasses',
            'sku'         => 'BMS-FLV-006',
        ],
        [
            'name'        => 'SR Double Apple Mint',
            'slug'        => 'bms-double-apple-mint',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 10.59.18 AM (1).jpeg',
            'gallery_id'  => 'shisharent-gallery-053.jpeg',
            'description' => 'The timeless Middle Eastern balance of sweet red apples, crisp green apples, mild star anise, and refreshing garden mint.',
            'short_desc'  => 'Red & Green Apples • Sweet Anise • Garden Mint',
            'sku'         => 'BMS-FLV-007',
        ],
        [
            'name'        => 'SR Elaichi Mint',
            'slug'        => 'bms-elaichi-mint',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 10.59.18 AM.jpeg',
            'gallery_id'  => 'shisharent-gallery-054.jpeg',
            'description' => 'Fragrant crushed green cardamom pods infused with fresh mint leaves for an authentic royal Indian lounge experience.',
            'short_desc'  => 'Green Cardamom • Fresh Mint • Royal Herbal Blend',
            'sku'         => 'BMS-FLV-008',
        ],
        [
            'name'        => 'SR Electric Mint',
            'slug'        => 'bms-electric-mint',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 10.59.19 AM.jpeg',
            'gallery_id'  => 'shisharent-gallery-056.jpeg',
            'description' => 'High-voltage arctic peppermint and spearmint leaves delivering an electrifying frost on every draw.',
            'short_desc'  => 'High-Voltage Mint • Pure Peppermint • Arctic Frost',
            'sku'         => 'BMS-FLV-009',
        ],
        [
            'name'        => 'SR Grape Blast',
            'slug'        => 'bms-grape-blast',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 11.44.16 AM (1).jpeg',
            'gallery_id'  => 'shisharent-gallery-086.jpeg',
            'description' => 'Plump black Concord grapes and juicy white grapes bursting with mouthwatering sweetness and a touch of cooling ice.',
            'short_desc'  => 'Black Concord Grapes • White Grapes • Icy Finish',
            'sku'         => 'BMS-FLV-010',
        ],
        [
            'name'        => 'SR Gum Supari',
            'slug'        => 'bms-gum-supari',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 10.59.26 AM.jpeg',
            'gallery_id'  => 'shisharent-gallery-075.jpeg',
            'description' => 'Traditional roasted betel nut supari shavings perfectly married with refreshing minty chewing gum.',
            'short_desc'  => 'Roasted Betel Supari • Refreshing Gum • Classic Paan Note',
            'sku'         => 'BMS-FLV-011',
        ],
        [
            'name'        => 'SR KIWI BLAST',
            'slug'        => 'bms-kiwi-blast',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 11.44.19 AM.jpeg',
            'gallery_id'  => 'shisharent-gallery-095.jpeg',
            'description' => 'Zesty, tangy New Zealand green kiwi slices crushed with sweet tropical undertones and chilled smoke.',
            'short_desc'  => 'Zesty Green Kiwi • Tropical Fusion • Chilled Exhale',
            'sku'         => 'BMS-FLV-012',
        ],
        [
            'name'        => 'SR Marbella',
            'slug'        => 'bms-marbella',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 11.44.16 AM (2).jpeg',
            'gallery_id'  => 'shisharent-gallery-087.jpeg',
            'description' => 'Inspired by Mediterranean coastal luxury. Sweet sun-dried dates, wild berries, and cooling ocean breeze menthol.',
            'short_desc'  => 'Mediterranean Dates • Sun-Dried Berries • Coastal Chill',
            'sku'         => 'BMS-FLV-013',
        ],
        [
            'name'        => 'SR Meetha Pan',
            'slug'        => 'bms-meetha-pan',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 10.59.27 AM.jpeg',
            'gallery_id'  => 'shisharent-gallery-077.jpeg',
            'description' => 'Calcutta’s iconic Meetha Paan experience. Fresh Maghai betel leaves, rose gulkand, sweet saunf, and crushed almonds.',
            'short_desc'  => 'Calcutta Maghai Paan • Rose Gulkand • Sweet Saunf',
            'sku'         => 'BMS-FLV-014',
        ],
        [
            'name'        => 'SR Orange Blast',
            'slug'        => 'bms-orange-blast',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 11.44.19 AM (2).jpeg',
            'gallery_id'  => 'shisharent-gallery-094.jpeg',
            'description' => 'Sun-drenched Valencia oranges paired with fresh mint sprigs, aromatic cinnamon bark, and ice.',
            'short_desc'  => 'Valencia Orange • Cinnamon Bark • Citrus Mint',
            'sku'         => 'BMS-FLV-015',
        ],
        [
            'name'        => 'SR Peachy Punch',
            'slug'        => 'bms-peachy-punch',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 11.44.17 AM (1).jpeg',
            'gallery_id'  => 'shisharent-gallery-089.jpeg',
            'description' => 'Luscious Georgia peach nectar blended with ripe summer berries and velvet vanilla undertones.',
            'short_desc'  => 'Georgia Peach • Summer Berries • Velvet Cream',
            'sku'         => 'BMS-FLV-016',
        ],
        [
            'name'        => 'SR Smokachinno',
            'slug'        => 'bms-smokachinno',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 10.59.26 AM (1).jpeg',
            'gallery_id'  => 'shisharent-gallery-073.jpeg',
            'description' => 'Dark roasted espresso beans, creamy mochaccino foam, and bittersweet Belgian chocolate fudge in a dense, aromatic draw.',
            'short_desc'  => 'Dark Espresso • Mochaccino Foam • Belgian Chocolate',
            'sku'         => 'BMS-FLV-017',
        ],
        [
            'name'        => 'SR Super Lemon Shots',
            'slug'        => 'bms-super-lemon-shots',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 10.59.28 AM.jpeg',
            'gallery_id'  => 'shisharent-gallery-079.jpeg',
            'description' => 'Electric sour Meyer lemons and key limes packed with frosty ice crystal shots to wake up your palate.',
            'short_desc'  => 'Meyer Lemon • Sour Key Lime • Icy Citrus Shots',
            'sku'         => 'BMS-FLV-018',
        ],
        [
            'name'        => 'SR Teen Paan Rajni',
            'slug'        => 'bms-teen-paan-rajni',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 11.44.21 AM (1).jpeg',
            'gallery_id'  => 'shisharent-gallery-099.jpeg',
            'description' => 'A trio of heritage betel leaves combined with aromatic Rajnigandha pearls, cooling churna, and silver foil.',
            'short_desc'  => 'Triple Betel Leaf • Rajnigandha Pearls • Heritage Blend',
            'sku'         => 'BMS-FLV-019',
        ],
        [
            'name'        => 'SR TEEN PAN ROSE (SR SPECIAL)',
            'slug'        => 'bms-teen-pan-rose-special',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 10.59.29 AM.jpeg',
            'gallery_id'  => 'shisharent-gallery-082.jpeg',
            'description' => 'The SR House Special. Three varieties of fresh paan leaves infused with organic Kashmiri rose petal gulkand and icy menthol.',
            'short_desc'  => 'SR House Signature • Triple Paan • Kashmiri Rose Gulkand',
            'sku'         => 'BMS-FLV-020',
        ],
        [
            'name'        => 'SR Thanda Paan',
            'slug'        => 'bms-thanda-paan',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 10.59.23 AM (2).jpeg',
            'gallery_id'  => 'shisharent-gallery-066.jpeg',
            'description' => 'An intensely chilled betel leaf sensation that delivers sub-zero icy coolness paired with rich, traditional paan flavor.',
            'short_desc'  => 'Sub-Zero Icy Paan • Cooling Menthol • Aromatic Betel Leaf',
            'sku'         => 'BMS-FLV-021',
        ],
        [
            'name'        => 'SR Vanilla Paan Rasna',
            'slug'        => 'bms-vanilla-paan-rasna',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 11.44.16 AM.jpeg',
            'gallery_id'  => 'shisharent-gallery-088.jpeg',
            'description' => 'Creamy Madagascar vanilla bean puree fused with sweet Calcutta paan and fruity Rasna essence for a smooth dessert smoke.',
            'short_desc'  => 'Madagascar Vanilla • Sweet Paan • Fruity Rasna Essence',
            'sku'         => 'BMS-FLV-022',
        ],
        [
            'name'        => 'SR Zafraan Paan',
            'slug'        => 'bms-zafraan-paan',
            'price'       => '600.00',
            'image_file'  => 'WhatsApp Image 2026-08-23 at 11.44.17 AM.jpeg',
            'gallery_id'  => 'shisharent-gallery-091.jpeg',
            'description' => 'Hand-harvested Kashmiri saffron stigma, purple saffron blossoms, royal betel leaves, and delicate sweet silver masala.',
            'short_desc'  => 'Kashmiri Saffron Stigma • Purple Saffron Blossom • Royal Paan',
            'sku'         => 'BMS-FLV-023',
        ],
    ];

    // 3. Remove/Delete old sample products so Shop contains ONLY the 23 BMS flavours
    $all_existing_posts = get_posts([
        'post_type'   => 'product',
        'numberposts' => -1,
        'post_status' => 'any',
    ]);
    $bms_slugs = wp_list_pluck($bms_flavours, 'slug');

    foreach ($all_existing_posts as $epost) {
        if (!in_array($epost->post_name, $bms_slugs)) {
            wp_delete_post($epost->ID, true);
        }
    }

    $created_count = 0;
    $updated_count = 0;
    $assigned_images = [];

    // 4. Seed / Update the 23 BMS Flavour Products
    foreach ($bms_flavours as $flv) {
        $att_id = 0;

        // Locate image in uploads/gallery
        $gallery_file = $upload_dir['basedir'] . '/gallery/' . $flv['gallery_id'];
        $flv_clean_filename = 'bms-' . sanitize_title($flv['slug']) . '.jpeg';
        $flv_target_file = $target_dir . '/' . $flv_clean_filename;

        if (file_exists($gallery_file)) {
            if (!file_exists($flv_target_file)) {
                copy($gallery_file, $flv_target_file);
            }
        }

        // Check if attachment already created for this flavour
        $existing_att = get_posts([
            'post_type'   => 'attachment',
            'post_status' => 'inherit',
            'title'       => $flv['name'],
            'numberposts' => 1,
        ]);

        if (!empty($existing_att)) {
            $att_id = $existing_att[0]->ID;
        } elseif (file_exists($flv_target_file)) {
            $wp_filetype = wp_check_filetype($flv_target_file, null);
            $attachment = [
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_text_field($flv['name']),
                'post_content'   => '',
                'post_excerpt'   => sanitize_text_field($flv['name'] . ' Hookah Flavour Kolkata'),
                'post_status'    => 'inherit',
                'guid'           => $upload_dir['baseurl'] . '/flavours/' . $flv_clean_filename,
            ];

            $att_id = wp_insert_attachment($attachment, $flv_target_file);
            if (!is_wp_error($att_id)) {
                $attach_data = wp_generate_attachment_metadata($att_id, $flv_target_file);
                wp_update_attachment_metadata($att_id, $attach_data);
                update_post_meta($att_id, '_wp_attachment_image_alt', sanitize_text_field($flv['name'] . ' Premium Hookah Flavour Kolkata'));
            }
        }

        // Check if product already exists by slug
        $matched_post = get_posts([
            'post_type'   => 'product',
            'name'        => $flv['slug'],
            'post_status' => 'any',
            'numberposts' => 1,
        ]);

        if (!empty($matched_post)) {
            $product = wc_get_product($matched_post[0]->ID);
            if (!$product) {
                $product = new WC_Product_Simple($matched_post[0]->ID);
            }
            $updated_count++;
        } else {
            $product = new WC_Product_Simple();
            $created_count++;
        }

        $product->set_name($flv['name']);
        $product->set_slug($flv['slug']);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_description($flv['description']);
        $product->set_short_description($flv['short_desc']);
        $product->set_sku($flv['sku']);
        $product->set_price($flv['price']);
        $product->set_regular_price($flv['price']);
        $product->set_manage_stock(false);
        $product->set_stock_status('instock');

        if ($cat_id) {
            $product->set_category_ids([$cat_id]);
        }

        if ($att_id) {
            $product->set_image_id($att_id);
        }

        $product_id = $product->save();

        $assigned_images[] = [
            'id'       => $product_id,
            'name'     => $flv['name'],
            'price'    => '₹' . $flv['price'],
            'image_id' => $att_id,
            'img_url'  => $att_id ? wp_get_attachment_url($att_id) : 'NONE',
            'src_file' => $flv['image_file'],
        ];
    }

    update_option('bms_flavours_catalog_seeded_v1', true);

    return [
        'status'          => 'success',
        'total_flavours'  => count($bms_flavours),
        'created_count'   => $created_count,
        'updated_count'   => $updated_count,
        'assigned_images' => $assigned_images,
    ];
}
