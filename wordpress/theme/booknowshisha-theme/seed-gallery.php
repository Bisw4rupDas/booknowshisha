<?php
/**
 * ShishaRent Gallery Idempotent Importer & Seeder
 *
 * Imports genuine ShishaRent photography into the WordPress Media Library,
 * creates proper attachment records, generates thumbnails/srcset, and assigns
 * categories and SEO metadata without duplicate creation.
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    require_once('/var/www/html/wp-load.php');
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

function bns_import_gallery_images($force_reimport = false) {
    if (!$force_reimport && get_option('bns_gallery_seeded_v1', false)) {
        return [
            'status'  => 'already_seeded',
            'message' => 'Gallery images already imported into WordPress Media Library.',
            'count'   => count(ShishaRent_Gallery::get_gallery_images('all')),
        ];
    }

    // Source staging directory inside container or local
    $source_dirs = [
        '/tmp/shisharent-gallery-src',
        WP_CONTENT_DIR . '/uploads/gallery-staging',
        ABSPATH . '../shisharent-gallery',
    ];

    $source_dir = '';
    foreach ($source_dirs as $dir) {
        if (is_dir($dir)) {
            $source_dir = $dir;
            break;
        }
    }

    if (empty($source_dir)) {
        return [
            'status'  => 'error',
            'message' => 'Source gallery directory not found.',
        ];
    }

    $upload_dir = wp_upload_dir();
    $target_base = $upload_dir['basedir'] . '/gallery';
    if (!file_exists($target_base)) {
        wp_mkdir_p($target_base);
    }

    $files = scandir($source_dir);
    $seen_hashes = [];
    $imported = 0;
    $skipped_duplicates = 0;
    $already_existed = 0;
    $errors = [];

    $file_index = 0;

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $source_path = $source_dir . '/' . $file;
        if (!is_file($source_path)) {
            continue;
        }

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            continue; // Skip videos or unsupported files
        }

        $hash = hash_file('sha256', $source_path);

        // Check for in-batch duplicate
        if (isset($seen_hashes[$hash])) {
            $skipped_duplicates++;
            continue;
        }
        $seen_hashes[$hash] = $file;

        $file_index++;

        // Determine Category, Title, and Alt Text based on photograph content
        $category = 'hookahs';
        $title    = 'ShishaRent Premium Hookah Setup';
        $alt      = 'Handcrafted premium hookah setup for rental in Kolkata';

        if (strpos($file, '6.53.') !== false) {
            $category = 'hookahs';
            $title    = 'Classic Handcrafted Shisha ' . $file_index;
            $alt      = 'Artisanal tabletop hookah with ornamental glass base and silicone hose in Kolkata';
        } elseif (strpos($file, '7.16.') !== false) {
            $category = 'hookahs';
            $title    = 'Luxury Precision Hookah ' . $file_index;
            $alt      = 'Luxury stainless steel and handcrafted glass shisha pipe available for doorstep rental in Kolkata';
        } elseif (strpos($file, '7.56.') !== false || strpos($file, '7.58.') !== false) {
            $category = 'events';
            $title    = 'VIP Shisha Concierge & Event Valet ' . $file_index;
            $alt      = 'Professional ShishaRent event valet and hospitality staff catering a luxury gathering in Kolkata';
        } elseif (
            strpos($file, '8.01.') !== false ||
            strpos($file, '8.08.') !== false ||
            strpos($file, '8.10.') !== false ||
            strpos($file, '8.16.') !== false ||
            strpos($file, '8.21.') !== false ||
            strpos($file, '8.22.') !== false ||
            strpos($file, '8.25.') !== false
        ) {
            $category = 'events';
            $title    = 'Luxury Mobile Bar & Event Catering ' . $file_index;
            $alt      = 'Bespoke mobile craft cocktail bar and VIP hookah lounge catering for events across Kolkata';
        } elseif (strpos($file, '8.41.') !== false) {
            $category = 'accessories';
            $title    = 'Artisanal Hookah Clay Bowl & Hardware ' . $file_index;
            $alt      = 'Terracotta clay hookah bowl and heat management accessory for optimal cloud density';
        } elseif (strpos($file, '10.59.') !== false) {
            if (
                strpos($file, '10.59.18') !== false ||
                strpos($file, '10.59.19') !== false ||
                strpos($file, '10.59.20') !== false ||
                strpos($file, '10.59.21') !== false ||
                strpos($file, '10.59.22') !== false ||
                strpos($file, '10.59.23') !== false ||
                strpos($file, '10.59.24') !== false ||
                strpos($file, '10.59.25') !== false ||
                strpos($file, '10.59.26') !== false ||
                strpos($file, '10.59.27') !== false ||
                strpos($file, '10.59.28') !== false ||
                strpos($file, '10.59.29') !== false ||
                strpos($file, '10.59.30') !== false
            ) {
                $category = 'flavours';
                $title    = 'Artisanal Molasses & Fruit Head ' . $file_index;
                $alt      = 'Freshly packed hookah phunnel bowl with premium molasses, mint leaves, and ice cubes';
            } else {
                $category = 'hookahs';
                $title    = 'Premium Hookah Experience ' . $file_index;
                $alt      = 'Handcrafted luxury hookah rental setup in Kolkata';
            }
        } elseif (strpos($file, '11.12.') !== false || strpos($file, '11.44.') !== false) {
            $category = 'flavours';
            $title    = 'Botanical Flavour Infusion & Ice Bowl ' . $file_index;
            $alt      = 'Curated hookah bowl infused with aromatic rose petals, crushed ice, and gourmet molasses';
        }

        // Check if already in WordPress database by hash
        $existing = get_posts([
            'post_type'   => 'attachment',
            'post_status' => 'inherit',
            'meta_key'    => '_bns_gallery_hash',
            'meta_value'  => $hash,
            'numberposts' => 1,
        ]);

        if (!empty($existing)) {
            $att_id = $existing[0]->ID;
            update_post_meta($att_id, '_bns_is_gallery_item', '1');
            update_post_meta($att_id, '_bns_gallery_category', $category);
            wp_set_post_terms($att_id, [$category], 'bns_gallery_category');
            $already_existed++;
            continue;
        }

        // Clean filename for WordPress uploads
        $clean_filename = 'shisharent-gallery-' . sprintf('%03d', $file_index) . '.' . $ext;
        $target_file = $target_base . '/' . $clean_filename;

        // Copy file into WordPress uploads
        if (!copy($source_path, $target_file)) {
            $errors[] = "Failed to copy {$file} to {$target_file}";
            continue;
        }

        $wp_filetype = wp_check_filetype($target_file, null);

        $attachment = [
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_text_field($title),
            'post_content'   => '',
            'post_excerpt'   => sanitize_text_field($title),
            'post_status'    => 'inherit',
            'guid'           => $upload_dir['baseurl'] . '/gallery/' . $clean_filename,
        ];

        $attach_id = wp_insert_attachment($attachment, $target_file);

        if (!is_wp_error($attach_id)) {
            $attach_data = wp_generate_attachment_metadata($attach_id, $target_file);
            wp_update_attachment_metadata($attach_id, $attach_data);

            update_post_meta($attach_id, '_wp_attachment_image_alt', sanitize_text_field($alt));
            update_post_meta($attach_id, '_bns_gallery_hash', $hash);
            update_post_meta($attach_id, '_bns_is_gallery_item', '1');
            update_post_meta($attach_id, '_bns_gallery_category', $category);
            update_post_meta($attach_id, '_bns_original_filename', $file);

            wp_set_post_terms($attach_id, [$category], 'bns_gallery_category');

            $imported++;
        } else {
            $errors[] = "Failed to insert attachment for {$file}: " . $attach_id->get_error_message();
        }
    }

    update_option('bns_gallery_seeded_v1', true);

    return [
        'status'             => 'success',
        'total_unique'       => count($seen_hashes),
        'imported'           => $imported,
        'skipped_duplicates' => $skipped_duplicates,
        'already_existed'    => $already_existed,
        'errors'             => $errors,
    ];
}

function bns_maybe_seed_gallery() {
    if (!get_option('bns_gallery_seeded_v1', false)) {
        bns_import_gallery_images(false);
    }
}
add_action('init', 'bns_maybe_seed_gallery', 30);
