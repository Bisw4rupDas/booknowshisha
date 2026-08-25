<?php
/**
 * ShishaRent Pages & Blog Seeder
 * Creates business pages, blog categories, and educational blog posts in native WordPress.
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    require_once('/var/www/html/wp-load.php');
}

function bns_seed_pages_and_blog_posts() {
    if (get_option('bns_pages_blog_seeded_v1', false)) {
        return;
    }

    // =========================================================================
    // 1. Create Core Business Pages
    // =========================================================================
    $pages_to_create = [
        'flavour-selection' => [
            'title'    => 'Flavour Selection',
            'content'  => '<!-- Flavour Selection Page handled by page-flavour-selection.php template -->',
            'template' => 'page-flavour-selection.php',
        ],
        'gallery' => [
            'title'    => 'ShishaRent Gallery',
            'content'  => '<!-- ShishaRent Gallery Page handled by page-gallery.php template -->',
            'template' => 'page-gallery.php',
        ],
        'bartending-party-services' => [
            'title'    => 'Bartending & Party Services',
            'content'  => '<!-- Bartending & Party Services Page Content handled by page-bartending-party-services.php template -->',
            'template' => 'page-bartending-party-services.php',
        ],
        'party-occasion-hookah' => [
            'title'    => 'Party & Occasion Hookah',
            'content'  => '<!-- Party & Occasion Hookah Page Content handled by page-party-occasion-hookah.php template -->',
            'template' => 'page-party-occasion-hookah.php',
        ],
        'contact' => [
            'title'    => 'Contact Us',
            'content'  => '<!-- Contact Page Content handled by page-contact.php template -->',
            'template' => 'page-contact.php',
        ],
        'blog' => [
            'title'    => 'Blog & Journal',
            'content'  => '<!-- Blog Archive Page handled by page-blog.php / home.php -->',
            'template' => 'page-blog.php',
        ],
    ];

    $created_page_ids = [];

    foreach ($pages_to_create as $slug => $pdata) {
        $existing = get_page_by_path($slug);
        if (!$existing) {
            $page_id = wp_insert_post([
                'post_title'   => $pdata['title'],
                'post_name'    => $slug,
                'post_content' => $pdata['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ]);

            if ($page_id && !is_wp_error($page_id)) {
                if (!empty($pdata['template'])) {
                    update_post_meta($page_id, '_wp_page_template', $pdata['template']);
                }
                $created_page_ids[$slug] = $page_id;
            }
        } else {
            $created_page_ids[$slug] = $existing->ID;
            if (!empty($pdata['template'])) {
                update_post_meta($existing->ID, '_wp_page_template', $pdata['template']);
            }
        }
    }

    // Set WordPress static front page & posts page
    if (isset($created_page_ids['blog'])) {
        update_option('page_for_posts', $created_page_ids['blog']);
    }

    // =========================================================================
    // 2. Create Blog Categories (Strictly Kolkata & Hookah Culture)
    // =========================================================================
    $blog_categories = [
        'hookah-guides'       => ['name' => 'Hookah Guides', 'desc' => 'Expert guides, hardware breakdowns, and bowl setup tutorials.'],
        'party-events'        => ['name' => 'Party & Events', 'desc' => 'VIP party packages, mobile bars, and celebration hosting tips in Kolkata.'],
        'flavours'            => ['name' => 'Flavours', 'desc' => 'Tasting notes, mixing formulas, and international molasses pairings.'],
        'rental-tips'         => ['name' => 'Rental Tips', 'desc' => 'Doorstep delivery, security deposits, and rental duration advice.'],
        'service-experiences' => ['name' => 'Service Experiences', 'desc' => 'White-glove delivery, live coal service, and corporate lounge setups.'],
        'kolkata'             => ['name' => 'Kolkata', 'desc' => 'Local events across Kolkata, North 24 Parganas, and South 24 Parganas.'],
        'news-updates'        => ['name' => 'News & Updates', 'desc' => 'Platform announcements, new fleet arrivals, and seasonal blends.'],
    ];

    $cat_term_ids = [];
    foreach ($blog_categories as $slug => $cat_info) {
        $term = get_term_by('slug', $slug, 'category');
        if (!$term) {
            $res = wp_insert_term($cat_info['name'], 'category', [
                'slug'        => $slug,
                'description' => $cat_info['desc'],
            ]);
            if (!is_wp_error($res)) {
                $cat_term_ids[$slug] = $res['term_id'];
            }
        } else {
            $cat_term_ids[$slug] = $term->term_id;
        }
    }

    // =========================================================================
    // 3. Create Sample Educational Blog Posts (Native WP Posts)
    // =========================================================================
    $sample_posts = [
        [
            'title'     => 'How to Choose the Right Hookah for a Party: Size, Airflow & Multi-Hose Setups',
            'slug'      => 'how-to-choose-the-right-hookah-for-a-party',
            'category'  => 'party-ideas',
            'image'     => get_template_directory_uri() . '/shisharent-gallery/WhatsApp%20Image%202026-08-22%20at%208.16.18%20PM.jpeg',
            'excerpt'   => 'Hosting a gathering in Kolkata? Discover how stem height, base stability, and purge valves impact your group session for effortless clouds all night.',
            'content'   => '
<p class="bns-lead-paragraph">When hosting a celebration, house party, or rooftop gathering in Kolkata, selecting the right hookah setup can make the difference between an effortless lounge experience and constant troubleshooting. From base stability to multi-hose airflow mechanics, here is everything you need to know before your next event.</p>

<h2>1. Base Stability: Wide vs Narrow Flasks</h2>
<p>In a social party environment where guests are mingling, accidental bumps can happen. Handcrafted Egyptian hookahs with wide bell-bottom bases (such as classic Khalil Mamoon models) or modern stainless German hookahs with heavy CNC bases provide the lowest center of gravity.</p>
<p>Avoid tall, top-heavy decorative pipes with narrow glass vases for large parties, as they are prone to tipping over when hoses are passed around.</p>

<h2>2. Single Hose vs Multi-Hose Airflow Dynamics</h2>
<p>While multi-hose hookahs sound convenient for group gatherings, single-hose setups with medical-grade silicone hoses and dedicated purge check-valves consistently deliver denser clouds and better heat management. If multiple guests want to smoke simultaneously, renting 2 to 3 separate single-hose setups creates dedicated lounge pods and prevents flavour overheating.</p>

<h2>3. Heat Management Devices (HMD) vs Traditional Foil</h2>
<p>For parties lasting several hours, Kaloud-style aluminum Heat Management Devices (HMDs) placed over silicone phunnel bowls provide superior safety and heat consistency compared to raw aluminum foil. HMDs enclose the glowing coals, eliminating flying ash and providing steady vaporization of molasses.</p>

<div class="bns-article-callout">
    <strong>💡 ShishaRent Host Tip:</strong> For rooftop barbecues or outdoor pool parties in Salt Lake and New Town, always opt for natural coconut charcoal cubes and an enclosed wind cover to protect your session from evening river breezes.
</div>

<h2>4. Professional On-Site Shisha Service</h2>
<p>If you are hosting a milestone birthday, pre-wedding cocktail night, or corporate dinner with 20+ guests, consider booking an all-inclusive party package with a dedicated Shisha Sommelier who manages live coals and flavour repacks seamlessly.</p>
',
        ],
        [
            'title'     => 'Best Hookah Flavours for Different Occasions: A Connoisseur’s Mixology Guide',
            'slug'      => 'best-hookah-flavours-for-different-occasions',
            'category'  => 'flavours',
            'image'     => get_template_directory_uri() . '/shisharent-gallery/WhatsApp%20Image%202026-08-23%20at%2010.59.18%20AM%20(1).jpeg',
            'excerpt'   => 'Explore the art of flavour pairing for intimate evenings, energetic celebrations, and relaxed summer sessions with world-renowned molasses blends.',
            'content'   => '
<p class="bns-lead-paragraph">Hookah molasses is an olfactory art. Just like pairing wine with a multi-course dinner or mixing craft cocktails, selecting the right flavour profile enhances the mood of any occasion. Here is our curated guide to flavour curation for every vibe.</p>

<h2>1. Intimate Solo & Duo Evenings: Deep & Aromatic Profiles</h2>
<p>For quiet evenings, reading sessions, or intimate conversations, complex double notes provide a soothing background. <strong>Double Apple with Anise</strong> remains the gold standard in Middle Eastern tradition, while blends of <strong>Earl Grey Bergamot with Vanilla</strong> provide subtle warmth without sensory fatigue.</p>

<h2>2. High-Energy Parties & Celebrations: Sweet Tropical Fruit Medleys</h2>
<p>When the party is in full swing, guests gravitate toward sweet, vibrant, and approachable flavor profiles. Top picks include:</p>
<ul>
    <li><strong>Love 66 (Adalya):</strong> Honeydew melon, wild passion fruit, ripe berries, and a refreshing mint finish.</li>
    <li><strong>Blueberry Mint Ice (Al Fakher):</strong> Sweet mountain blueberries with an arctic menthol blast.</li>
    <li><strong>Watermelon Freeze (Starbuzz):</strong> Juicy candied watermelon with an ice-cold exhale.</li>
</ul>

<h2>3. Traditional Indian Celebrations & Weddings: Royal Desi Blends</h2>
<p>Kolkata celebrations have a deep affinity for classic aromatic spices. <strong>Paan Raas</strong> mixed with sweet <strong>Gulkand Rose</strong> and a pinch of <strong>Kesar Mint</strong> creates a royal, opulent fragrance that guests instantly recognize and adore.</p>

<div class="bns-article-callout">
    <strong>🌿 Tobacco-Free Herbal Options:</strong> For health-conscious guests or those who prefer zero nicotine, ShishaRent offers 100% tobacco-free herbal molasses made from sugar cane fibers and pure food-grade essential oils.
</div>
',
        ],
        [
            'title'     => 'How to Set Up a Hookah at Home for Maximum Cloud Density & Smooth Draw',
            'slug'      => 'how-to-set-up-a-hookah-at-home-smooth-draw',
            'category'  => 'tips-guides',
            'image'     => get_template_directory_uri() . '/shisharent-gallery/WhatsApp%20Image%202026-08-22%20at%207.16.12%20PM%20(1).jpeg',
            'excerpt'   => 'Master water levels, fluff packing techniques, and heat management for five-star lounge cloud density in the comfort of your living room.',
            'content'   => '
<p class="bns-lead-paragraph">Ever wondered why hookah in a luxury lounge tastes so crisp and produces massive, velvet clouds while a home setup sometimes feels harsh? The secret lies in three fundamental variables: water depth, bowl pack density, and coal heat equilibrium.</p>

<h2>Step 1: The Golden Water Level Rule</h2>
<p>Fill your glass base with cold filtered water (or ice water) so that the submerged downstem is immersed precisely <strong>1.5 cm to 2.5 cm (about 0.75 to 1 inch)</strong> below the water surface. Too little water results in warm, uncooled smoke; too much water restricts airflow and causes water to bubble up into the hose.</p>

<h2>Step 2: The Fluff Pack Technique for Phunnel Bowls</h2>
<p>Never compress or tightly pack your molasses. Use a fork or poker to gently drop the molasses into the bowl rim until it is fluffy and level, approximately 1-2 mm below the upper rim. This ensures unobstructed airflow channels around every tobacco leaf.</p>

<h2>Step 3: 100% Fully Red Coals Only</h2>
<p>Only place coconut charcoal cubes onto your bowl once they are glowing bright red on all six sides. Black unlit patches emit unpleasant carbon odor and harsh smoke. Use an electric 500W coal burner for 5-7 minutes, turning the coals halfway through.</p>

<h2>Step 4: The 5-Minute Warm-Up Period</h2>
<p>After placing 2 to 3 coals on your HMD or foil, resist the urge to immediately start pulling hard. Allow the bowl to heat soak evenly for 4 to 5 minutes. Take slow, steady initial puffs to gently activate the molasses.</p>
',
        ],
        [
            'title'     => 'Planning an Unforgettable Hookah & Mobile Bar Party in Kolkata',
            'slug'      => 'planning-hookah-mobile-bar-party-kolkata',
            'category'  => 'kolkata-events',
            'image'     => get_template_directory_uri() . '/shisharent-gallery/WhatsApp%20Image%202026-08-22%20at%208.16.20%20PM%20(1).jpeg',
            'excerpt'   => 'From rooftop venues in Salt Lake to penthouses in Ballygunge, here is how to curate a five-star beverage and shisha experience for your guests.',
            'content'   => '
<p class="bns-lead-paragraph">Kolkata has always been a city of warmth, hospitality, and sophisticated social gatherings. Whether you are hosting an intimate terrace get-together in South Kolkata or a lavish celebration in New Town, pairing mobile craft bartending with premium shisha rental creates an unforgettable hospitality standard.</p>

<h2>1. Creating Dedicated Social Zones</h2>
<p>Designing your venue layout is essential. Set up the illuminated mobile bar counter as the energetic focal point near the entrance, and create cozy, low-seating lounge nooks with floor cushions and ambient lighting for the shisha pods.</p>

<h2>2. Pairing Cocktails & Mocktails with Hookah Blends</h2>
<p>Coordinate your beverage menu with your flavour offerings. Citrus-forward drinks (like Passionfruit Mojitos or Smoked Rosemary Lemonade) pair exceptionally well with cooling berry molasses, while espresso-infused cocktails complement spiced dessert flavours.</p>

<h2>3. Punctual Delivery & Hassle-Free Teardown</h2>
<p>With ShishaRent’s doorstep rental and event catering, all equipment arrives sanitized and pre-assembled 60-90 minutes before your event. When the evening wraps up, our team handles collection and cleaning—leaving your residence spotless.</p>
',
        ],
        [
            'title'     => 'Hookah Care & Cleaning: Why Medical-Grade Hygiene Standards Matter',
            'slug'      => 'hookah-care-cleaning-hygiene-standards',
            'category'  => 'hookah-care',
            'image'     => get_template_directory_uri() . '/shisharent-gallery/WhatsApp%20Image%202026-08-22%20at%207.16.17%20PM%20(1).jpeg',
            'excerpt'   => 'Learn how ultrasonic sanitization, food-grade silicone, and individually sealed mouthpieces guarantee pure taste and uncompromising safety.',
            'content'   => '
<p class="bns-lead-paragraph">When renting or owning a hookah, hygiene is not just about cleanliness—it directly dictates the quality of the flavor. Ghosting (residual flavor lingering from previous sessions) and buildup inside traditional leather hoses can spoil even the finest molasses.</p>

<h2>1. Why Traditional Leather Hoses Are Outdated</h2>
<p>Old-style wire-bound leather hoses cannot be washed with water because the internal wire rusts. Modern setups exclusively utilize food-grade, medical-standard silicone hoses that can be flushed with boiling water and food-safe sanitizers between every single session.</p>

<h2>2. Ultrasonic Base & Downstem Sanitization</h2>
<p>At ShishaRent Kolkata, every returned hookah undergoes high-temperature ultrasonic washing, removing microscopic residue, mineral scaling, and essential oil films. Glass vases are sterilized and air-dried in a dust-free environment.</p>

<h2>3. Single-Use Sealed Mouthpieces</h2>
<p>Personal hygiene is non-negotiable. Every rental delivery package comes sealed with individually wrapped, single-use plastic mouthpieces for each guest to ensure zero cross-contamination.</p>
',
        ],
        [
            'title'     => 'Top Trends in Luxury Event Catering & Shisha Lounges for 2026',
            'slug'      => 'top-trends-luxury-event-catering-shisha-lounges-2026',
            'category'  => 'event-services',
            'image'     => get_template_directory_uri() . '/shisharent-gallery/WhatsApp%20Image%202026-08-22%20at%208.25.41%20PM%20(1).jpeg',
            'excerpt'   => 'From LED glow bases and ice-infused chillers to artisanal botanical mocktail pairings, discover what luxury hosts in Kolkata are requesting.',
            'content'   => '
<p class="bns-lead-paragraph">The event hospitality industry in India is experiencing a sophisticated evolution. Private hosts and corporate event planners in Kolkata are moving away from generic setups toward curated, interactive sensory lounges. Here are the top trends shaping premium celebrations this year.</p>

<h2>1. Integrated LED Glow & Minimalist Glass Aesthetics</h2>
<p>Modern luxury hosts favor transparent borosilicate glass hookahs with subterranean waterproof LED lighting that can be synchronized to the event’s ambient lighting scheme or DJ console.</p>

<h2>2. Live Flavour Sommelier & Customized Smoking Menus</h2>
<p>Guests love personalization. Having a knowledgeable Shisha Master at the party who mixes custom molasses recipes on the spot—tailored to each guest’s sweetness and cooling preferences—adds a five-star hospitality touch.</p>

<h2>3. Alcohol-Free Craft Mocktail Lounges</h2>
<p>Zero-proof mixology using botanical distillates, cold-pressed fruit syrups, and smoking cloches has become a major trend for health-conscious celebrations, weddings, and family galas.</p>
',
        ],
    ];

    foreach ($sample_posts as $post_data) {
        $existing_post = get_page_by_path($post_data['slug'], OBJECT, 'post');
        if (!$existing_post) {
            $post_id = wp_insert_post([
                'post_title'    => $post_data['title'],
                'post_name'     => $post_data['slug'],
                'post_content'  => trim($post_data['content']),
                'post_excerpt'  => $post_data['excerpt'],
                'post_status'   => 'publish',
                'post_type'     => 'post',
                'post_author'   => 1,
            ]);

            if ($post_id && !is_wp_error($post_id)) {
                // Attach Category
                if (isset($cat_term_ids[$post_data['category']])) {
                    wp_set_post_categories($post_id, [$cat_term_ids[$post_data['category']]]);
                }

                // Attach custom meta for image fallback
                update_post_meta($post_id, '_bns_image_url', $post_data['image']);
            }
        }
    }

    update_option('bns_pages_blog_seeded_v1', true);
}

/**
 * Synchronize all blog articles with authentic shisharent-gallery photos
 */
function bns_sync_blog_posts_with_gallery() {
    $gallery_map = [
        'how-to-choose-the-right-hookah-for-a-party' => 'WhatsApp Image 2026-08-22 at 8.16.18 PM.jpeg',
        'best-hookah-flavours-for-different-occasions' => 'WhatsApp Image 2026-08-23 at 10.59.18 AM (1).jpeg',
        'how-to-set-up-a-hookah-at-home-smooth-draw' => 'WhatsApp Image 2026-08-22 at 7.16.12 PM (1).jpeg',
        'planning-hookah-mobile-bar-party-kolkata' => 'WhatsApp Image 2026-08-22 at 8.16.20 PM (1).jpeg',
        'hookah-care-cleaning-hygiene-standards' => 'WhatsApp Image 2026-08-22 at 7.16.17 PM (1).jpeg',
        'top-trends-luxury-event-catering-shisha-lounges-2026' => 'WhatsApp Image 2026-08-22 at 8.25.41 PM (1).jpeg',
    ];

    foreach ($gallery_map as $slug => $filename) {
        $post = get_page_by_path($slug, OBJECT, 'post');
        if ($post) {
            $img_url = get_template_directory_uri() . '/shisharent-gallery/' . rawurlencode($filename);
            update_post_meta($post->ID, '_bns_image_url', $img_url);
        }
    }
}
add_action('init', 'bns_sync_blog_posts_with_gallery', 30);
