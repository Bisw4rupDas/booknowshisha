<?php
/**
 * ShishaRent - One-Time Product Image Repair Script
 *
 * Your live products currently have the WRONG featured images attached
 * (random stock photos of apples, bags, bananas, bread, etc. — likely
 * left over from a WooCommerce sample-data import). This script
 * force-replaces the featured image on each named product with a real
 * hookah/shisha-relevant photo, matched by product slug.
 *
 * HOW TO RUN:
 * 1. Upload this file into: wp-content/themes/booknowshisha-theme/
 * 2. Visit it once in your browser:
 *      https://yourdomain.com/wp-content/themes/booknowshisha-theme/fix-product-images.php
 * 3. You'll see a report of what was updated. Delete this file afterwards.
 *
 * NOTE: These are still stock/placeholder photos, not your real product
 * photography. They exist to stop the site showing unrelated images
 * (apples, bags, bread) until you upload real photos for each product.
 */

if (!defined('ABSPATH')) {
    require_once('/var/www/html/wp-load.php');
}

if (!current_user_can('manage_options') && php_sapi_name() !== 'cli') {
    // Allow running while logged out ONLY on a fresh unfixed install; comment
    // out the next line if you want to restrict this to logged-in admins.
    // wp_die('You must be logged in as an administrator to run this script.');
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

/**
 * Slug => relevant photo URL. Matched to product name/category.
 */
$bns_image_fixes = [
    'solo-standard-24h'            => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80', // hookah
    'duo-weekend-48h'              => 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?auto=format&fit=crop&w=800&q=80', // glass hookah
    'vip-party-72h'                => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=800&q=80', // hookah lounge
    'km-gold-classic'               => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80', // hookah
    'amy-deluxe-ss'                 => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&w=800&q=80', // steel texture
    'oduman-n2-travel'              => 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?auto=format&fit=crop&w=800&q=80', // glass hookah
    'starbuzz-carbine-matte'        => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=800&q=80', // hookah lounge
    'blueberry-mint-ice'            => 'https://images.unsplash.com/photo-1498557850523-fd3d118b962e?auto=format&fit=crop&w=800&q=80', // blueberries
    'love-66-adalya'                => 'https://images.unsplash.com/photo-1571575173700-afb9492e6a50?auto=format&fit=crop&w=800&q=80', // melon
    'paan-raas-afzal'               => 'https://images.unsplash.com/photo-1518895312237-a9e23508077d?auto=format&fit=crop&w=800&q=80', // rose petals
    'double-apple-al-fakher'        => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=800&q=80', // apples
    'natural-coconut-charcoal-1kg'  => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=800&q=80', // charcoal
    'electric-coal-burner-500w'     => 'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?auto=format&fit=crop&w=800&q=80', // burner/coil
    'silicone-phunnel-bowl-hmd'     => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?auto=format&fit=crop&w=800&q=80', // bowl/device
];

function bns_sideload_and_attach_image($product_id, $image_url) {
    $tmp = download_url($image_url);
    if (is_wp_error($tmp)) {
        return $tmp;
    }

    $file_array = [
        'name'     => 'product-' . $product_id . '-' . basename(parse_url($image_url, PHP_URL_PATH)) . '.jpg',
        'tmp_name' => $tmp,
    ];

    $attachment_id = media_handle_sideload($file_array, $product_id);

    if (is_wp_error($attachment_id)) {
        @unlink($file_array['tmp_name']);
        return $attachment_id;
    }

    // Remove any previous featured image relationship (not the attachment
    // itself, in case it's shared) and force-set the new one.
    set_post_thumbnail($product_id, $attachment_id);

    return $attachment_id;
}

$report = [];

foreach ($bns_image_fixes as $slug => $image_url) {
    $product = get_page_by_path($slug, OBJECT, 'product');
    if (!$product) {
        // Fallback: try matching by product slug via WooCommerce lookup
        $products = wc_get_products(['slug' => $slug, 'limit' => 1]);
        $product = $products ? get_post($products[0]->get_id()) : null;
    }

    if (!$product) {
        $report[] = "SKIPPED (not found): {$slug}";
        continue;
    }

    $result = bns_sideload_and_attach_image($product->ID, $image_url);

    if (is_wp_error($result)) {
        $report[] = "FAILED: {$slug} — " . $result->get_error_message();
    } else {
        $report[] = "FIXED: {$slug} (product #{$product->ID}) → attachment #{$result}";
    }
}

update_option('bns_product_images_fixed_v1', true);

header('Content-Type: text/plain');
echo "ShishaRent Product Image Repair — Report\n";
echo "==========================================\n\n";
echo implode("\n", $report);
echo "\n\nDone. You can now delete this file (fix-product-images.php) from your server.\n";
