<?php
/**
 * Template Name: Party & Occasion Hookah
 * Template for Party & Occasion Hookah landing page
 *
 * @package ShishaRent
 */

get_header(); ?>

<!-- =========================================================================
     HERO SECTION: PARTY & OCCASION HOOKAH
     ========================================================================= -->
<section class="bns-service-hero">
    <div class="bns-service-hero-bg"></div>
    <div class="bns-container">
        <div class="bns-service-hero-content">
            <span class="bns-hero-badge">
                <span class="bns-pulse-dot"></span> <?php esc_html_e('EVENT HOOKAH CATERING • KOLKATA', 'shisharent'); ?>
            </span>
            <h1 class="bns-service-hero-title">
                MAKE YOUR OCCASION<br>
                <span class="bns-text-gradient">UNFORGETTABLE</span>
            </h1>
            <p class="bns-service-hero-desc">
                <?php esc_html_e('Curated multi-hookah setups, exotic international flavour bars, live charcoal management, and medical-grade sanitized equipment delivered directly to your celebration across Kolkata.', 'shisharent'); ?>
            </p>
            <div class="bns-hero-cta-group">
                <a href="#party-packages-matrix" class="bns-btn-gold bns-btn-lg bns-glow-btn">
                    <?php esc_html_e('Plan Your Party', 'shisharent'); ?> →
                </a>
                <a href="#party-occasions-grid" class="bns-btn-outline bns-btn-lg">
                    <?php esc_html_e('View Occasion Types', 'shisharent'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     OCCASIONS GRID
     ========================================================================= -->
<section class="bns-section bns-occasions-section" id="party-occasions-grid">
    <div class="bns-container">
        <div class="bns-section-header bns-text-center">
            <span class="bns-section-subtitle"><?php esc_html_e('TAILORED FOR EVERY MOMENT', 'shisharent'); ?></span>
            <h2 class="bns-section-title"><?php esc_html_e('CELEBRATIONS & OCCASIONS WE SERVE', 'shisharent'); ?></h2>
            <p class="bns-section-desc">
                <?php esc_html_e('From intimate living room gatherings to grand celebratory banquets across Kolkata, Salt Lake, and New Town.', 'shisharent'); ?>
            </p>
        </div>

        <div class="bns-occasions-grid">
            <div class="bns-occasion-card">
                <div class="bns-occasion-img-box">
                    <img src="<?php echo esc_url(content_url('/uploads/services/service-party-rooftop-dj.jpeg')); ?>" alt="<?php esc_attr_e('Rooftop Birthday Party Hookah in Kolkata', 'shisharent'); ?>" />
                </div>
                <div class="bns-occasion-info">
                    <h4><?php esc_html_e('Birthday Parties & Rooftops', 'shisharent'); ?></h4>
                    <p><?php esc_html_e('Make milestone birthdays pop with illuminated shisha bars, rooftop DJ lounges, and fresh berry blends.', 'shisharent'); ?></p>
                </div>
            </div>

            <div class="bns-occasion-card">
                <div class="bns-occasion-img-box">
                    <img src="<?php echo esc_url(content_url('/uploads/services/service-party-wedding-lawn.jpeg')); ?>" alt="<?php esc_attr_e('Grand Wedding Lawn Hookah Catering in Kolkata', 'shisharent'); ?>" />
                </div>
                <div class="bns-occasion-info">
                    <h4><?php esc_html_e('Weddings & Grand Lawn Sangeet', 'shisharent'); ?></h4>
                    <p><?php esc_html_e('Opulent gold Egyptian hookahs, illuminated bars, and traditional Paan Raas & Saffron blends for royal celebrations.', 'shisharent'); ?></p>
                </div>
            </div>

            <div class="bns-occasion-card">
                <div class="bns-occasion-img-box">
                    <img src="<?php echo esc_url(content_url('/uploads/services/service-party-palace-ballroom.jpeg')); ?>" alt="<?php esc_attr_e('Corporate Galas and Palace Banquets in Kolkata', 'shisharent'); ?>" />
                </div>
                <div class="bns-occasion-info">
                    <h4><?php esc_html_e('Corporate Galas & Banquets', 'shisharent'); ?></h4>
                    <p><?php esc_html_e('Sophisticated indoor palace lounges for networking mixers, team achievements, and executive retreats.', 'shisharent'); ?></p>
                </div>
            </div>

            <div class="bns-occasion-card">
                <div class="bns-occasion-img-box">
                    <img src="<?php echo esc_url(content_url('/uploads/services/service-party-houseparty-hookahs.jpeg')); ?>" alt="<?php esc_attr_e('House Party Hookah Rental in Kolkata', 'shisharent'); ?>" />
                </div>
                <div class="bns-occasion-info">
                    <h4><?php esc_html_e('House Parties & Weekend Chill', 'shisharent'); ?></h4>
                    <p><?php esc_html_e('48-hour weekend packages with electric burners and plenty of coals for effortless home sessions.', 'shisharent'); ?></p>
                </div>
            </div>

            <div class="bns-occasion-card">
                <div class="bns-occasion-img-box">
                    <img src="<?php echo esc_url(content_url('/uploads/services/service-hookah-german-gold-trio.jpeg')); ?>" alt="<?php esc_attr_e('Private VIP Hookah Celebrations in Kolkata', 'shisharent'); ?>" />
                </div>
                <div class="bns-occasion-info">
                    <h4><?php esc_html_e('Private VIP Celebrations', 'shisharent'); ?></h4>
                    <p><?php esc_html_e('Intimate German stainless steel and gold hookah setups with mood LED lighting and reserve SR molasses.', 'shisharent'); ?></p>
                </div>
            </div>

            <div class="bns-occasion-card">
                <div class="bns-occasion-img-box">
                    <img src="<?php echo esc_url(content_url('/uploads/services/service-party-hooghly-riverfront.jpeg')); ?>" alt="<?php esc_attr_e('Riverside Sunset Lounge & Festivals in Kolkata', 'shisharent'); ?>" />
                </div>
                <div class="bns-occasion-info">
                    <h4><?php esc_html_e('Riverside Sunset & Festivals', 'shisharent'); ?></h4>
                    <p><?php esc_html_e('Outdoor Hooghly riverfront lounges, Diwali, New Year’s Eve, and festive celebrations across Kolkata.', 'shisharent'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     SERVICE & SETUP OPTIONS
     ========================================================================= -->
<section class="bns-section bns-service-details-section">
    <div class="bns-container">
        <div class="bns-section-header bns-text-center">
            <span class="bns-section-subtitle"><?php esc_html_e('WHAT’S INCLUDED IN OUR SERVICE', 'shisharent'); ?></span>
            <h2 class="bns-section-title"><?php esc_html_e('END-TO-END PARTY HOOKAH SOLUTIONS', 'shisharent'); ?></h2>
        </div>

        <div class="bns-service-cards-grid">
            <div class="bns-service-feature-card">
                <div class="bns-service-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                </div>
                <h3><?php esc_html_e('Multi-Hookah Rental', 'shisharent'); ?></h3>
                <p><?php esc_html_e('Book 1 to 10+ German precision stainless steel and authentic Egyptian brass pipes ready for simultaneous guest sessions.', 'shisharent'); ?></p>
            </div>

            <div class="bns-service-feature-card">
                <div class="bns-service-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                </div>
                <h3><?php esc_html_e('Exotic Flavour Selection', 'shisharent'); ?></h3>
                <p><?php esc_html_e('Choose from over 20+ world-class flavours: Adalya Love 66, Al Fakher Blueberry Mint, Afzal Paan Raas, and 0% Nicotine Herbal.', 'shisharent'); ?></p>
            </div>

            <div class="bns-service-feature-card">
                <div class="bns-service-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                </div>
                <h3><?php esc_html_e('Express Delivery & Setup', 'shisharent'); ?></h3>
                <p><?php esc_html_e('Our delivery specialist brings all equipment, tests airtight airflow seals, and sets up coals so your party is ready to smoke in minutes.', 'shisharent'); ?></p>
            </div>

            <div class="bns-service-feature-card">
                <div class="bns-service-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><circle cx="12" cy="12" r="8"></circle><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                </div>
                <h3><?php esc_html_e('Coals, Burners & Supplies', 'shisharent'); ?></h3>
                <p><?php esc_html_e('Every occasion package includes natural coconut shell coals, 500W electric fast coal burners, tongs, and wind covers.', 'shisharent'); ?></p>
            </div>

            <div class="bns-service-feature-card">
                <div class="bns-service-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
                <h3><?php esc_html_e('Medical-Grade Hygiene', 'shisharent'); ?></h3>
                <p><?php esc_html_e('Ultrasonically sterilized hardware and individually sealed, single-use mouthpieces for total health confidence.', 'shisharent'); ?></p>
            </div>

            <div class="bns-service-feature-card">
                <div class="bns-service-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8863b" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                </div>
                <h3><?php esc_html_e('Zero Washing or Cleanup', 'shisharent'); ?></h3>
                <p><?php esc_html_e('When the party ends, simply leave the equipment in place. Our crew collects everything and handles all sanitization.', 'shisharent'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     PARTY PACKAGES PRICING MATRIX
     ========================================================================= -->
<section class="bns-section bns-packages-section" id="party-packages-matrix">
    <div class="bns-container">
        <div class="bns-section-header bns-text-center">
            <span class="bns-section-subtitle"><?php esc_html_e('TRANSPARENT ALL-INCLUSIVE RATES', 'shisharent'); ?></span>
            <h2 class="bns-section-title"><?php esc_html_e('CURATED OCCASION PACKAGES', 'shisharent'); ?></h2>
        </div>

        <div class="bns-packages-grid">
            
            <!-- Tier 1: 2-Hookah Duo Bash -->
            <div class="bns-tier-card">
                <div class="bns-tier-header">
                    <span class="bns-tier-badge"><?php esc_html_e('INTIMATE GATHERING', 'shisharent'); ?></span>
                    <h3 class="bns-tier-title"><?php esc_html_e('2-Hookah Party (48H)', 'shisharent'); ?></h3>
                    <div class="bns-tier-pricing">
                        <span class="bns-tier-currency">₹</span>
                        <span class="bns-tier-amount">4,499</span>
                        <span class="bns-tier-period">/ 48h</span>
                    </div>
                    <p class="bns-tier-desc"><?php esc_html_e('Perfect for house parties, birthday dinners, and terrace barbecues up to 15 guests.', 'shisharent'); ?></p>
                </div>
                <div class="bns-tier-features">
                    <ul>
                        <li><span class="bns-check">✓</span> <strong>2x</strong> German/Egyptian Premium Hookahs</li>
                        <li><span class="bns-check">✓</span> <strong>8x</strong> Curated Flavour Heads (50g ea)</li>
                        <li><span class="bns-check">✓</span> <strong>1kg</strong> Coconut Charcoal Box</li>
                        <li><span class="bns-check">✓</span> <strong>1x</strong> 500W Electric Coal Burner</li>
                        <li><span class="bns-check">✓</span> <strong>16x</strong> Sealed Hygienic Mouthpieces</li>
                        <li><span class="bns-check">✓</span> Free Doorstep Setup & Collection</li>
                    </ul>
                </div>
                <div class="bns-tier-footer">
                    <a href="https://wa.me/919903556825?text=Hi+ShishaRent,+I+would+like+to+book+the+2-Hookah+Party+Package+(48H)" target="_blank" rel="noopener noreferrer" class="bns-btn-outline bns-btn-block">
                        <?php esc_html_e('Book 2-Hookah Package', 'shisharent'); ?> →
                    </a>
                </div>
            </div>

            <!-- Tier 2: 4-Hookah Quad Celebration (POPULAR) -->
            <div class="bns-tier-card bns-tier-popular">
                <div class="bns-popular-ribbon"><?php esc_html_e('POPULAR FOR EVENTS', 'shisharent'); ?></div>
                <div class="bns-tier-header">
                    <span class="bns-tier-badge bns-badge-gold"><?php esc_html_e('GRAND CELEBRATION', 'shisharent'); ?></span>
                    <h3 class="bns-tier-title"><?php esc_html_e('4-Hookah Quad (48H)', 'shisharent'); ?></h3>
                    <div class="bns-tier-pricing">
                        <span class="bns-tier-currency">₹</span>
                        <span class="bns-tier-amount">7,999</span>
                        <span class="bns-tier-period">/ 48h</span>
                    </div>
                    <p class="bns-tier-desc"><?php esc_html_e('Designed for large celebrations, sangeet nights, pool parties, and milestone birthdays.', 'shisharent'); ?></p>
                </div>
                <div class="bns-tier-features">
                    <ul>
                        <li><span class="bns-check">✓</span> <strong>4x</strong> Stainless Steel Click Hookahs</li>
                        <li><span class="bns-check">✓</span> <strong>16x</strong> Premium International Flavour Heads</li>
                        <li><span class="bns-check">✓</span> <strong>2kg</strong> Extended Coconut Charcoal Supply</li>
                        <li><span class="bns-check">✓</span> <strong>2x</strong> 500W Fast Electric Coal Burners</li>
                        <li><span class="bns-check">✓</span> <strong>32x</strong> Sealed Hygienic Mouthpieces</li>
                        <li><span class="bns-check">✓</span> Priority Setup & On-Call WhatsApp Concierge</li>
                    </ul>
                </div>
                <div class="bns-tier-footer">
                    <a href="https://wa.me/919903556825?text=Hi+ShishaRent,+I+would+like+to+book+the+4-Hookah+Quad+Celebration+Package" target="_blank" rel="noopener noreferrer" class="bns-btn-gold bns-btn-block bns-glow-btn">
                        <?php esc_html_e('Book 4-Hookah Quad Package', 'shisharent'); ?> →
                    </a>
                </div>
            </div>

            <!-- Tier 3: Full-Service VIP Lounge -->
            <div class="bns-tier-card">
                <div class="bns-tier-header">
                    <span class="bns-tier-badge"><?php esc_html_e('VIP CONCIERGE & SOMMELIER', 'shisharent'); ?></span>
                    <h3 class="bns-tier-title"><?php esc_html_e('VIP Lounge with Sommelier', 'shisharent'); ?></h3>
                    <div class="bns-tier-pricing">
                        <span class="bns-tier-currency">₹</span>
                        <span class="bns-tier-amount">Custom</span>
                        <span class="bns-tier-period">/ Event</span>
                    </div>
                    <p class="bns-tier-desc"><?php esc_html_e('Dedicated on-site Shisha Master managing continuous coal lighting, repacks, and flavour mixes.', 'shisharent'); ?></p>
                </div>
                <div class="bns-tier-features">
                    <ul>
                        <li><span class="bns-check">✓</span> Custom count of luxury hookahs (5 to 15+)</li>
                        <li><span class="bns-check">✓</span> Dedicated certified on-site Shisha Master</li>
                        <li><span class="bns-check">✓</span> Unlimited live coal lighting & continuous repacks</li>
                        <li><span class="bns-check">✓</span> Complete flavour bar with custom mixing</li>
                        <li><span class="bns-check">✓</span> Full pre-event setup and post-event teardown</li>
                        <li><span class="bns-check">✓</span> Ideal for Weddings, Galas & Corporate Events</li>
                    </ul>
                </div>
                <div class="bns-tier-footer">
                    <a href="https://wa.me/919903556825?text=Hi+ShishaRent,+I+would+like+a+quote+for+the+VIP+Lounge+with+Dedicated+Sommelier" target="_blank" rel="noopener noreferrer" class="bns-btn-outline bns-btn-block">
                        <?php esc_html_e('Request Custom Quote', 'shisharent'); ?> →
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
