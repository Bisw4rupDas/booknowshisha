<?php
/**
 * ShishaRent Gallery Core & Admin Management
 *
 * Handles gallery taxonomy, attachment queries, admin management interface,
 * and media helper methods for the ShishaRent luxury gallery.
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ShishaRent_Gallery {

    /**
     * Singleton instance
     */
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'register_gallery_taxonomy']);
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_bns_save_gallery_item', [$this, 'ajax_save_gallery_item']);
        add_action('wp_ajax_bns_remove_gallery_item', [$this, 'ajax_remove_gallery_item']);
        add_filter('document_title_parts', [$this, 'customize_gallery_page_title']);
    }

    /**
     * Register Custom Taxonomy for Gallery Categories on Attachments
     */
    public function register_gallery_taxonomy() {
        $labels = [
            'name'              => __('Gallery Categories', 'shisharent'),
            'singular_name'     => __('Gallery Category', 'shisharent'),
            'search_items'      => __('Search Gallery Categories', 'shisharent'),
            'all_items'         => __('All Gallery Categories', 'shisharent'),
            'edit_item'         => __('Edit Gallery Category', 'shisharent'),
            'update_item'       => __('Update Gallery Category', 'shisharent'),
            'add_new_item'      => __('Add New Gallery Category', 'shisharent'),
            'new_item_name'     => __('New Gallery Category Name', 'shisharent'),
            'menu_name'         => __('Gallery Categories', 'shisharent'),
        ];

        register_taxonomy('bns_gallery_category', ['attachment'], [
            'hierarchical'          => true,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'rewrite'               => ['slug' => 'gallery-category'],
            'show_in_rest'          => true,
            'update_count_callback' => '_update_generic_term_count',
        ]);

        // Pre-populate core categories if not existing
        $default_cats = [
            'hookahs'     => __('Hookahs', 'shisharent'),
            'events'      => __('Events & Catering', 'shisharent'),
            'flavours'    => __('Flavours & Mixology', 'shisharent'),
            'accessories' => __('Accessories', 'shisharent'),
        ];

        foreach ($default_cats as $slug => $name) {
            if (!term_exists($slug, 'bns_gallery_category')) {
                wp_insert_term($name, 'bns_gallery_category', ['slug' => $slug]);
            }
        }
    }

    /**
     * Register Admin Menu for Gallery Management
     */
    public function register_admin_menu() {
        add_menu_page(
            __('ShishaRent Gallery', 'shisharent'),
            __('📸 Gallery', 'shisharent'),
            'manage_options',
            'shisharent-gallery',
            [$this, 'render_admin_gallery_page'],
            'dashicons-format-gallery',
            25
        );
    }

    /**
     * Enqueue Admin Assets for Media Uploader & AJAX
     */
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_shisharent-gallery' !== $hook) {
            return;
        }
        wp_enqueue_media();
    }

    /**
     * SEO Title customization for /gallery/
     */
    public function customize_gallery_page_title($title_parts) {
        if (is_page('gallery') || is_page_template('page-gallery.php')) {
            $title_parts['title'] = 'ShishaRent Gallery | Premium Hookah Rental in Kolkata';
            $title_parts['site']  = '';
        }
        return $title_parts;
    }

    /**
     * Fetch Gallery Images
     *
     * @param string $category 'all', 'hookahs', 'events', 'flavours', 'accessories'
     * @param int    $limit    Number of items to retrieve (-1 for all)
     * @return array
     */
    public static function get_gallery_images($category = 'all', $limit = -1) {
        $args = [
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => $limit,
            'orderby'        => 'menu_order date ID',
            'order'          => 'DESC',
            'meta_query'     => [
                [
                    'key'     => '_bns_is_gallery_item',
                    'value'   => '1',
                    'compare' => '=',
                ],
            ],
        ];

        if (!empty($category) && 'all' !== $category) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'bns_gallery_category',
                    'field'    => 'slug',
                    'terms'    => $category,
                ],
            ];
        }

        $query = new WP_Query($args);
        $images = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $id = get_the_ID();

                $thumb_src = wp_get_attachment_image_src($id, 'medium');
                $large_src = wp_get_attachment_image_src($id, 'large');
                $full_src  = wp_get_attachment_image_src($id, 'full');
                $alt       = get_post_meta($id, '_wp_attachment_image_alt', true);
                if (empty($alt)) {
                    $alt = get_the_title($id);
                }

                $meta_cat = get_post_meta($id, '_bns_gallery_category_slug', true);
                if (!empty($meta_cat)) {
                    $cat_slug = $meta_cat;
                    $cat_names = [
                        'hookahs'     => __('Hookahs', 'shisharent'),
                        'events'      => __('Events & Catering', 'shisharent'),
                        'flavours'    => __('Flavours & Mixology', 'shisharent'),
                        'accessories' => __('Accessories', 'shisharent'),
                    ];
                    $cat_name = $cat_names[$cat_slug] ?? __('Hookahs', 'shisharent');
                } else {
                    $terms = wp_get_post_terms($id, 'bns_gallery_category');
                    $cat_slug = !empty($terms) && !is_wp_error($terms) ? $terms[0]->slug : 'hookahs';
                    $cat_name = !empty($terms) && !is_wp_error($terms) ? $terms[0]->name : __('Hookahs', 'shisharent');
                }

                $meta = wp_get_attachment_metadata($id);
                $width  = $full_src ? $full_src[1] : ($meta['width'] ?? 1200);
                $height = $full_src ? $full_src[2] : ($meta['height'] ?? 1200);
                
                $orientation = 'square';
                if ($width > $height * 1.05) {
                    $orientation = 'landscape';
                } elseif ($height > $width * 1.05) {
                    $orientation = 'portrait';
                }

                $images[] = [
                    'id'          => $id,
                    'title'       => get_the_title($id),
                    'caption'     => wp_get_attachment_caption($id),
                    'alt'         => $alt,
                    'category'    => $cat_slug,
                    'cat_name'    => $cat_name,
                    'orientation' => $orientation,
                    'width'       => $width,
                    'height'      => $height,
                    'thumb_url'   => $thumb_src ? $thumb_src[0] : '',
                    'large_url'   => $large_src ? $large_src[0] : ($full_src ? $full_src[0] : ''),
                    'full_url'    => $full_src ? $full_src[0] : '',
                ];
            }
            wp_reset_postdata();
        }

        return $images;
    }

    /**
     * Get Category Summary with item counts
     */
    public static function get_gallery_categories() {
        $all_images = self::get_gallery_images('all', -1);
        $counts = [
            'all'         => count($all_images),
            'hookahs'     => 0,
            'events'      => 0,
            'flavours'    => 0,
            'accessories' => 0,
        ];

        foreach ($all_images as $img) {
            $cat = $img['category'] ?? 'hookahs';
            if (!isset($counts[$cat])) {
                $counts[$cat] = 0;
            }
            $counts[$cat]++;
        }

        $categories = [
            'all' => [
                'slug'  => 'all',
                'name'  => __('All', 'shisharent'),
                'count' => $counts['all'],
            ],
            'hookahs' => [
                'slug'  => 'hookahs',
                'name'  => __('Hookahs', 'shisharent'),
                'count' => $counts['hookahs'],
            ],
            'events' => [
                'slug'  => 'events',
                'name'  => __('Events & Catering', 'shisharent'),
                'count' => $counts['events'],
            ],
            'flavours' => [
                'slug'  => 'flavours',
                'name'  => __('Flavours & Mixology', 'shisharent'),
                'count' => $counts['flavours'],
            ],
            'accessories' => [
                'slug'  => 'accessories',
                'name'  => __('Accessories', 'shisharent'),
                'count' => $counts['accessories'],
            ],
        ];

        return $categories;
    }

    /**
     * Get Homepage Preview Images (6 curated standout photos)
     */
    public static function get_homepage_preview_images($limit = 6) {
        $all = self::get_gallery_images('all', -1);
        if (empty($all)) {
            return [];
        }

        // Group by category to pick a balanced representation
        $grouped = [];
        foreach ($all as $img) {
            $cat = $img['category'];
            if (!isset($grouped[$cat])) {
                $grouped[$cat] = [];
            }
            $grouped[$cat][] = $img;
        }

        $selected = [];
        // Pick from events, hookahs, flavours, accessories
        $picks = [
            'events'      => 2,
            'hookahs'     => 2,
            'flavours'    => 1,
            'accessories' => 1,
        ];

        foreach ($picks as $cat => $count) {
            if (!empty($grouped[$cat])) {
                $slice = array_slice($grouped[$cat], 0, $count);
                $selected = array_merge($selected, $slice);
            }
        }

        // If not enough, fill from remaining
        if (count($selected) < $limit) {
            foreach ($all as $img) {
                if (!in_array($img['id'], wp_list_pluck($selected, 'id'), true)) {
                    $selected[] = $img;
                    if (count($selected) >= $limit) {
                        break;
                    }
                }
            }
        }

        return array_slice($selected, 0, $limit);
    }

    /**
     * Render Admin Gallery Page
     */
    public function render_admin_gallery_page() {
        $items = self::get_gallery_images('all', -1);
        $cats  = self::get_gallery_categories();
        ?>
        <div class="wrap bns-gallery-admin-wrap" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px; padding: 20px; background: #0f172a; border-radius: 12px; border: 1px solid #1e293b; color: #fff;">
                <div>
                    <h1 style="color: #b8863b; font-size: 1.8rem; margin: 0 0 6px 0; font-weight: 700;">📸 ShishaRent Gallery Command Center</h1>
                    <p style="margin: 0; color: #94a3b8; font-size: 0.95rem;">Manage, organize, and curate live photographs for the ShishaRent customer-facing gallery in Kolkata.</p>
                </div>
                <div style="display:flex; gap:10px;">
                    <button type="button" id="bns-add-media-to-gallery-btn" class="button button-primary" style="background: #b8863b; border-color: #92692c; font-weight: 600; padding: 6px 16px; height: auto;">
                        ➕ Add Images from Media Library
                    </button>
                    <a href="<?php echo esc_url(home_url('/gallery/')); ?>" target="_blank" class="button" style="background: #1e293b; border-color: #334155; color: #f8fafc; font-weight: 600; padding: 6px 16px; height: auto;">
                        🌐 View Live Gallery
                    </a>
                </div>
            </div>

            <!-- Stats Bar -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
                <div style="background: #fff; padding: 16px 20px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="font-size: 0.8rem; text-transform: uppercase; color: #64748b; font-weight: 600;">Total Active Photos</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #0f172a; margin-top: 4px;"><?php echo count($items); ?></div>
                </div>
                <?php foreach ($cats as $slug => $cdata): if ($slug === 'all') continue; ?>
                <div style="background: #fff; padding: 16px 20px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="font-size: 0.8rem; text-transform: uppercase; color: #64748b; font-weight: 600;"><?php echo esc_html($cdata['name']); ?></div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #b8863b; margin-top: 4px;"><?php echo esc_html($cdata['count']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Gallery Items Grid -->
            <div style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: #0f172a;">All Gallery Images (<?php echo count($items); ?>)</h2>
                    <div>
                        <select id="bns-admin-category-filter" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
                            <option value="all">Show All Categories</option>
                            <option value="hookahs">Hookahs</option>
                            <option value="events">Events & Catering</option>
                            <option value="flavours">Flavours & Mixology</option>
                            <option value="accessories">Accessories</option>
                        </select>
                    </div>
                </div>

                <div class="bns-admin-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
                    <?php if (!empty($items)) : foreach ($items as $img) : ?>
                        <div class="bns-admin-card" data-category="<?php echo esc_attr($img['category']); ?>" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; display: flex; flex-direction: column; transition: all 0.2s ease;">
                            <div style="position: relative; height: 180px; background: #000; overflow: hidden;">
                                <img src="<?php echo esc_url($img['thumb_url'] ?: $img['large_url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                                <span style="position: absolute; top: 8px; right: 8px; background: rgba(15,23,42,0.85); color: #b8863b; font-size: 0.72rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">
                                    <?php echo esc_html($img['cat_name']); ?>
                                </span>
                            </div>
                            <div style="padding: 12px; flex: 1; display: flex; flex-direction: column;">
                                <strong style="font-size: 0.88rem; color: #0f172a; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 4px;">
                                    <?php echo esc_html($img['title']); ?>
                                </strong>
                                <span style="font-size: 0.76rem; color: #64748b; margin-bottom: 8px;">
                                    <?php echo esc_html($img['width']); ?> &times; <?php echo esc_html($img['height']); ?> px (<?php echo esc_html($img['orientation']); ?>)
                                </span>
                                <div style="margin-top: auto; display: flex; gap: 6px; padding-top: 8px; border-top: 1px solid #e2e8f0;">
                                    <a href="<?php echo esc_url(admin_url('post.php?post=' . $img['id'] . '&action=edit')); ?>" target="_blank" class="button button-small" style="flex: 1; text-align: center;">
                                        ✏️ Edit
                                    </a>
                                    <button type="button" class="button button-small bns-remove-btn" data-id="<?php echo esc_attr($img['id']); ?>" style="color: #ef4444; border-color: #fca5a5;">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <p style="color: #64748b; grid-column: 1 / -1;">No gallery images found. Run the setup import below or click "Add Images from Media Library".</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Category filter in admin
            $('#bns-admin-category-filter').on('change', function() {
                var cat = $(this).val();
                if (cat === 'all') {
                    $('.bns-admin-card').show();
                } else {
                    $('.bns-admin-card').hide();
                    $('.bns-admin-card[data-category="' + cat + '"]').show();
                }
            });

            // Media uploader trigger
            $('#bns-add-media-to-gallery-btn').on('click', function(e) {
                e.preventDefault();
                var customUploader = wp.media({
                    title: 'Select or Upload Images for ShishaRent Gallery',
                    button: { text: 'Add to Gallery' },
                    multiple: true,
                    library: { type: 'image' }
                });

                customUploader.on('select', function() {
                    var selection = customUploader.state().get('selection');
                    var attachmentIds = [];
                    selection.map(function(attachment) {
                        attachment = attachment.toJSON();
                        attachmentIds.push(attachment.id);
                    });

                    if (attachmentIds.length > 0) {
                        $.post(ajaxurl, {
                            action: 'bns_save_gallery_item',
                            ids: attachmentIds,
                            nonce: '<?php echo wp_create_nonce('bns_gallery_admin_nonce'); ?>'
                        }, function(res) {
                            window.location.reload();
                        });
                    }
                });

                customUploader.open();
            });

            // Remove from gallery
            $('.bns-remove-btn').on('click', function() {
                if (!confirm('Remove this image from the customer gallery?')) return;
                var btn = $(this);
                var id = btn.data('id');
                $.post(ajaxurl, {
                    action: 'bns_remove_gallery_item',
                    id: id,
                    nonce: '<?php echo wp_create_nonce('bns_gallery_admin_nonce'); ?>'
                }, function(res) {
                    btn.closest('.bns-admin-card').fadeOut(300, function() { $(this).remove(); });
                });
            });
        });
        </script>
        <?php
    }

    /**
     * AJAX Save Items to Gallery
     */
    public function ajax_save_gallery_item() {
        check_ajax_referer('bns_gallery_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $ids = isset($_POST['ids']) ? array_map('intval', (array)$_POST['ids']) : [];
        foreach ($ids as $id) {
            update_post_meta($id, '_bns_is_gallery_item', '1');
            // If no category assigned, default to hookahs
            $terms = wp_get_post_terms($id, 'bns_gallery_category');
            if (empty($terms)) {
                wp_set_post_terms($id, ['hookahs'], 'bns_gallery_category');
            }
        }

        wp_send_json_success(['count' => count($ids)]);
    }

    /**
     * AJAX Remove Item from Gallery
     */
    public function ajax_remove_gallery_item() {
        check_ajax_referer('bns_gallery_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id) {
            delete_post_meta($id, '_bns_is_gallery_item');
            wp_send_json_success();
        }
        wp_send_json_error('Invalid ID');
    }
}

// Initialize Gallery Core
ShishaRent_Gallery::get_instance();
