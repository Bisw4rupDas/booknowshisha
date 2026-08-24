<?php
/**
 * Template Name: Bartending & Party Services
 * Template for Bartending & Party Services landing page
 *
 * @package ShishaRent
 */

get_header(); ?>

<!-- =========================================================================
     HERO SECTION: BARTENDING & PARTY SERVICES
     ========================================================================= -->
<section class="bns-service-hero">
    <div class="bns-service-hero-bg"></div>
    <div class="bns-container">
        <div class="bns-service-hero-content">
            <span class="bns-hero-badge">
                <span class="bns-pulse-dot"></span> <?php esc_html_e('FIVE-STAR EVENT EXPERIENCES • KOLKATA', 'shisharent'); ?>
            </span>
            <h1 class="bns-service-hero-title">
                BARTENDING &amp;<br>
                <span class="bns-text-gradient">PARTY SERVICES</span>
            </h1>
            <p class="bns-service-hero-desc">
                <?php esc_html_e('Bespoke mobile bar setups, master mixology, craft cocktail & mocktail menus, and curated hookah lounge valets for Kolkata’s most distinguished private and corporate events.', 'shisharent'); ?>
            </p>
            <div class="bns-hero-cta-group">
                <a href="https://wa.me/919903556825?text=Hi+ShishaRent,+I+would+like+to+request+a+quote+for+Bartending+%26+Party+Services+in+Kolkata" target="_blank" rel="noopener noreferrer" class="bns-btn-gold bns-btn-lg bns-glow-btn">
                    <?php esc_html_e('Request an Event Quote', 'shisharent'); ?> →
                </a>
                <a href="#event-services-grid" class="bns-btn-outline bns-btn-lg">
                    <?php esc_html_e('Explore Services', 'shisharent'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     CORE SERVICES GRID
     ========================================================================= -->
<section class="bns-section bns-service-details-section" id="event-services-grid">
    <div class="bns-container">
        <div class="bns-section-header bns-text-center">
            <span class="bns-section-subtitle"><?php esc_html_e('WHITE-GLOVE EVENT CATERING', 'shisharent'); ?></span>
            <h2 class="bns-section-title"><?php esc_html_e('COMPREHENSIVE PARTY SERVICES', 'shisharent'); ?></h2>
            <p class="bns-section-desc">
                <?php esc_html_e('Every detail is handled by hospitality professionals with flawless execution.', 'shisharent'); ?>
            </p>
        </div>

        <div class="bns-service-cards-grid">
            
            <div class="bns-service-feature-card">
                <div class="bns-service-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><path d="M8 22h8M12 11v11M4 2l8 9 8-9H4z"></path></svg>
                </div>
                <h3><?php esc_html_e('Professional Mixologists & Flair Bartenders', 'shisharent'); ?></h3>
                <p><?php esc_html_e('Certified bartenders specializing in craft mixology, high-volume speed service, and captivating flair entertainment for weddings and luxury galas.', 'shisharent'); ?></p>
                <ul class="bns-service-checklist">
                    <li>✓ Certified flair and craft mixology staff</li>
                    <li>✓ Impeccable grooming and professional attire</li>
                    <li>✓ Custom garnish preparation & ice management</li>
                </ul>
            </div>

            <div class="bns-service-feature-card">
                <div class="bns-service-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <h3><?php esc_html_e('Illuminated Mobile Bar Setup', 'shisharent'); ?></h3>
                <p><?php esc_html_e('Complete bar stations with atmospheric LED backlighting, stainless ice wells, speed rails, glassware racks, and custom wooden/acrylic finishes.', 'shisharent'); ?></p>
                <ul class="bns-service-checklist">
                    <li>✓ Portable luxury modular bar counters</li>
                    <li>✓ Premium crystal glassware for all drink types</li>
                    <li>✓ Integrated chillers and cocktail equipment</li>
                </ul>
            </div>

            <div class="bns-service-feature-card">
                <div class="bns-service-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                </div>
                <h3><?php esc_html_e('Curated Cocktail & Mocktail Menus', 'shisharent'); ?></h3>
                <p><?php esc_html_e('Customized drink recipes infused with fresh botanical purees, smoked spices, edible blossoms, and artisanal syrups tailored to your party theme.', 'shisharent'); ?></p>
                <ul class="bns-service-checklist">
                    <li>✓ Signature smoked cocktails & infused mocktails</li>
                    <li>✓ Customized bar menu cards with personalized names</li>
                    <li>✓ Zero-proof alcohol-free botanical drinks</li>
                </ul>
            </div>

            <div class="bns-service-feature-card">
                <div class="bns-service-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                </div>
                <h3><?php esc_html_e('Party Hookah Lounge & Shisha Masters', 'shisharent'); ?></h3>
                <p><?php esc_html_e('Dedicated on-site shisha valets providing continuous charcoal rotation, bowl repacking, and flavour management so hosts never lift a finger.', 'shisharent'); ?></p>
                <ul class="bns-service-checklist">
                    <li>✓ 2 to 10+ German & Egyptian hookah setups</li>
                    <li>✓ Live coconut coal lighting with zero mess</li>
                    <li>✓ Ultrasonic sanitization & sealed mouthpieces</li>
                </ul>
            </div>

            <div class="bns-service-feature-card">
                <div class="bns-service-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                </div>
                <h3><?php esc_html_e('Punctual Setup & White-Glove Teardown', 'shisharent'); ?></h3>
                <p><?php esc_html_e('Our team arrives 2 hours prior to start time for seamless bar and shisha staging, followed by complete post-event teardown and clean-up.', 'shisharent'); ?></p>
                <ul class="bns-service-checklist">
                    <li>✓ Timely delivery across Kolkata & Greater Kolkata</li>
                    <li>✓ Full teardown with zero cleaning required by host</li>
                    <li>✓ Safe disposal of coals and beverage waste</li>
                </ul>
            </div>

            <div class="bns-service-feature-card">
                <div class="bns-service-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                </div>
                <h3><?php esc_html_e('VIP Concierge Event Support', 'shisharent'); ?></h3>
                <p><?php esc_html_e('Dedicated event manager coordinating timelines, guest preferences, flavour formulas, and customized hospitality arrangements.', 'shisharent'); ?></p>
                <ul class="bns-service-checklist">
                    <li>✓ Direct VIP line with lead coordinator</li>
                    <li>✓ Tasting previews for large weddings & corporate galas</li>
                    <li>✓ 100% transparent pricing with no hidden charges</li>
                </ul>
            </div>

        </div>
    </div>
</section>

<!-- =========================================================================
     EVENT TYPES WE CATER
     ========================================================================= -->
<section class="bns-section bns-occasions-section">
    <div class="bns-container">
        <div class="bns-section-header bns-text-center">
            <span class="bns-section-subtitle"><?php esc_html_e('PERFECT FOR EVERY CELEBRATION', 'shisharent'); ?></span>
            <h2 class="bns-section-title"><?php esc_html_e('EVENT TYPES WE CATER IN KOLKATA', 'shisharent'); ?></h2>
        </div>

        <div class="bns-occasions-grid">
            <div class="bns-occasion-card">
                <div class="bns-occasion-img-box">
                    <img src="<?php echo esc_url(content_url('/uploads/services/service-bartending-sommelier.jpeg')); ?>" alt="<?php esc_attr_e('Professional Sommelier and Bartending Team in Kolkata', 'shisharent'); ?>" />
                </div>
                <div class="bns-occasion-info">
                    <h4><?php esc_html_e('Birthday Parties & Milestones', 'shisharent'); ?></h4>
                    <p><?php esc_html_e('Electrify 21st, 30th, 40th, or 50th birthdays with bespoke drinks, tuxedo bartenders, and social shisha lounges.', 'shisharent'); ?></p>
                </div>
            </div>

            <div class="bns-occasion-card">
                <div class="bns-occasion-img-box">
                    <img src="<?php echo esc_url(content_url('/uploads/services/service-bartending-gala.jpeg')); ?>" alt="<?php esc_attr_e('Luxury Wedding and Sangeet Cocktail Bar in Kolkata', 'shisharent'); ?>" />
                </div>
                <div class="bns-occasion-info">
                    <h4><?php esc_html_e('Weddings, Sangeet & Receptions', 'shisharent'); ?></h4>
                    <p><?php esc_html_e('Grand ballroom cocktail bars with crystal chandeliers, royal Desi Paan Raas hookah stations, and craft mixology.', 'shisharent'); ?></p>
                </div>
            </div>

            <div class="bns-occasion-card">
                <div class="bns-occasion-img-box">
                    <img src="<?php echo esc_url(content_url('/uploads/services/service-bartending-whiteglove.jpeg')); ?>" alt="<?php esc_attr_e('VIP White-Glove Corporate Gala Service in Kolkata', 'shisharent'); ?>" />
                </div>
                <div class="bns-occasion-info">
                    <h4><?php esc_html_e('Corporate Galas & Brand Mixers', 'shisharent'); ?></h4>
                    <p><?php esc_html_e('Sophisticated white-glove cocktail service for product launches, executive dinners, and annual brand galas.', 'shisharent'); ?></p>
                </div>
            </div>

            <div class="bns-occasion-card">
                <div class="bns-occasion-img-box">
                    <img src="<?php echo esc_url(content_url('/uploads/services/service-bartending-mixology.jpeg')); ?>" alt="<?php esc_attr_e('Master Craft Mixology for Rooftop Parties in Kolkata', 'shisharent'); ?>" />
                </div>
                <div class="bns-occasion-info">
                    <h4><?php esc_html_e('Rooftops & Private Penthouses', 'shisharent'); ?></h4>
                    <p><?php esc_html_e('Transform your terrace or living room into an upscale lounge across Salt Lake, New Town, and Ballygunge.', 'shisharent'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     HOW TO BOOK & REQUEST QUOTE CTA
     ========================================================================= -->
<section class="bns-section bns-quote-cta-section">
    <div class="bns-container">
        <div class="bns-catering-card">
            <div class="bns-catering-content bns-text-center">
                <span class="bns-tier-badge bns-badge-gold"><?php esc_html_e('START PLANNING YOUR EVENT', 'shisharent'); ?></span>
                <h2 class="bns-catering-title"><?php esc_html_e('Ready to Elevate Your Next Event in Kolkata?', 'shisharent'); ?></h2>
                <p class="bns-catering-desc" style="max-width: 700px; margin: 0 auto 28px;">
                    <?php esc_html_e('Contact our VIP event team with your date, venue location, and estimated guest count. We will craft a bespoke bar and shisha catering proposal within 2 hours.', 'shisharent'); ?>
                </p>
                <div class="bns-hero-cta-group" style="justify-content: center;">
                    <a href="https://wa.me/919903556825?text=Hello+ShishaRent,+I+would+like+to+plan+an+event+in+Kolkata" target="_blank" rel="noopener noreferrer" class="bns-btn-gold bns-btn-lg bns-glow-btn">
                        <?php esc_html_e('WhatsApp VIP Concierge (+91 99035 56825)', 'shisharent'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="bns-btn-outline bns-btn-lg">
                        <?php esc_html_e('Send Message via Contact Page', 'shisharent'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     HEALTH & SAFETY DISCLAIMER NOTICE
     ========================================================================= -->
<section class="bns-section-compact">
    <div class="bns-container">
        <div class="bns-health-disclaimer">
            <div class="bns-disclaimer-inner">
                <span class="bns-disclaimer-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </span>
                <div class="bns-disclaimer-text">
                    <strong class="bns-disclaimer-title"><?php esc_html_e('Health & Safety Notice', 'shisharent'); ?></strong>
                    <p><?php esc_html_e('Hookah, smoking, alcohol and other intoxicating substances can pose serious risks to health. We encourage responsible choices and do not promote or encourage the use of intoxicating substances. Please comply with all applicable laws and age restrictions.', 'shisharent'); ?></p>
                </div>
                <div class="bns-age-pill">21+ Strictly</div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
