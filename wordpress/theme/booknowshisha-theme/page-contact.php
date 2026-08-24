<?php
/**
 * Template Name: Contact Us
 * Template for Contact Us page
 *
 * @package ShishaRent
 */

get_header(); ?>

<!-- =========================================================================
     HERO SECTION: CONTACT US
     ========================================================================= -->
<section class="bns-service-hero">
    <div class="bns-service-hero-bg"></div>
    <div class="bns-container">
        <div class="bns-service-hero-content">
            <span class="bns-hero-badge">
                <span class="bns-pulse-dot"></span> <?php esc_html_e('24/7 SUPPORT & DISPATCH • KOLKATA', 'shisharent'); ?>
            </span>
            <h1 class="bns-service-hero-title">
                GET IN <span class="bns-text-gradient">TOUCH</span>
            </h1>
            <p class="bns-service-hero-desc">
                <?php esc_html_e('We are here to assist with on-demand hookah rentals, doorstep delivery schedules, cocktail bar catering, and private VIP party consultations across Kolkata.', 'shisharent'); ?>
            </p>
        </div>
    </div>
</section>

<!-- =========================================================================
     CONTACT INFORMATION & ADDRESS MATRIX
     ========================================================================= -->
<section class="bns-section bns-contact-info-section">
    <div class="bns-container">
        <div class="bns-contact-cards-grid">
            
            <!-- Card 1: Official Shop Locations -->
            <div class="bns-contact-card">
                <div class="bns-contact-card-icon">📍</div>
                <h3 class="bns-contact-card-title"><?php esc_html_e('Official Kolkata Hubs', 'shisharent'); ?></h3>
                <div class="bns-contact-address-block">
                    <p style="margin-bottom: 10px;">
                        <strong style="color: var(--bns-accent-gold, #d4a95f);">📍 Hub 1 (Ballygunge):</strong><br>
                        Camac Street Area, 9/2A, Chamru Khansama Ln,<br>
                        Park Circus, Ballygunge, Kolkata 700019
                    </p>
                    <p style="border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 8px; margin-bottom: 0;">
                        <strong style="color: var(--bns-accent-gold, #d4a95f);">📍 Hub 2 (Park Street):</strong><br>
                        Park Street, Chaurangi More,<br>
                        Kolkata – 700071, West Bengal, India
                    </p>
                </div>
                <a href="https://maps.google.com/?q=Park+Street,+Chaurangi+More,+Kolkata+700071" target="_blank" rel="noopener noreferrer" class="bns-btn-outline bns-btn-block" style="margin-top: 18px;">
                    <?php esc_html_e('Open in Google Maps', 'shisharent'); ?> ↗
                </a>
            </div>

            <!-- Card 2: Hotlines & Direct WhatsApp -->
            <div class="bns-contact-card">
                <div class="bns-contact-card-icon">📞</div>
                <h3 class="bns-contact-card-title"><?php esc_html_e('Express Phone & WhatsApp', 'shisharent'); ?></h3>
                <div class="bns-contact-details">
                    <p>
                        <strong><?php esc_html_e('Primary Hotline & WhatsApp:', 'shisharent'); ?></strong><br>
                        <a href="tel:+919903556825" class="bns-gold-link">+91 99035 56825</a>
                    </p>
                    <p>
                        <strong><?php esc_html_e('Alternate Support Line:', 'shisharent'); ?></strong><br>
                        <a href="tel:+919051177720" class="bns-gold-link">+91 90511 77720</a>
                    </p>
                    <p class="bns-text-muted" style="font-size: 0.85rem;">
                        <?php esc_html_e('Available 7 days a week for active rentals and urgent dispatch inquiries.', 'shisharent'); ?>
                    </p>
                </div>
                <a href="https://wa.me/919903556825?text=Hello+ShishaRent,+I+have+an+inquiry+about+a+hookah+rental+in+Kolkata" target="_blank" rel="noopener noreferrer" class="bns-btn-gold bns-btn-block bns-glow-btn" style="margin-top: 18px;">
                    <?php esc_html_e('Chat on WhatsApp', 'shisharent'); ?> 💬
                </a>
            </div>

            <!-- Card 3: Express Delivery Zones -->
            <div class="bns-contact-card">
                <div class="bns-contact-card-icon">🚚</div>
                <h3 class="bns-contact-card-title"><?php esc_html_e('Service & Delivery Coverage', 'shisharent'); ?></h3>
                <ul class="bns-contact-coverage-list">
                    <li>✓ <strong>Salt Lake & New Town</strong> (60-90 min express)</li>
                    <li>✓ <strong>South Kolkata</strong> (Ballygunge, Alipore, Gariahat)</li>
                    <li>✓ <strong>Central Kolkata</strong> (Park Street, Camac St)</li>
                    <li>✓ <strong>Rajarhat & North Hubs</strong> (Chinar Park, Lake Town)</li>
                </ul>
                <a href="<?php echo esc_url(home_url('/#checker')); ?>" class="bns-btn-outline bns-btn-block" style="margin-top: 18px;">
                    <?php esc_html_e('Check Delivery PIN Code', 'shisharent'); ?> →
                </a>
            </div>

        </div>
    </div>
</section>

<!-- =========================================================================
     INTERACTIVE CONTACT FORM & MAP SECTION
     ========================================================================= -->
<section class="bns-section bns-contact-form-section">
    <div class="bns-container">
        <div class="bns-contact-form-grid">
            
            <!-- Left: Contact Form -->
            <div class="bns-contact-form-col">
                <div class="bns-form-card">
                    <span class="bns-section-subtitle"><?php esc_html_e('SEND A MESSAGE', 'shisharent'); ?></span>
                    <h2 class="bns-section-title" style="margin-bottom: 20px; font-size: 1.8rem;"><?php esc_html_e('ENQUIRY & BOOKING FORM', 'shisharent'); ?></h2>
                    
                    <form id="bns-contact-form" class="bns-custom-form">
                        <div class="bns-form-group">
                            <label for="contact-name"><?php esc_html_e('Your Full Name', 'shisharent'); ?> *</label>
                            <input type="text" id="contact-name" name="name" class="bns-form-input" placeholder="e.g. Rahul Mukherjee" required />
                        </div>

                        <div class="bns-form-row">
                            <div class="bns-form-group">
                                <label for="contact-phone"><?php esc_html_e('Phone Number (WhatsApp)', 'shisharent'); ?> *</label>
                                <input type="tel" id="contact-phone" name="phone" class="bns-form-input" placeholder="+91 98765 43210" required />
                            </div>
                            <div class="bns-form-group">
                                <label for="contact-area"><?php esc_html_e('Delivery Area / PIN', 'shisharent'); ?></label>
                                <input type="text" id="contact-area" name="area" class="bns-form-input" placeholder="e.g. Salt Lake / 700091" />
                            </div>
                        </div>

                        <div class="bns-form-group">
                            <label for="contact-service"><?php esc_html_e('Service of Interest', 'shisharent'); ?></label>
                            <select id="contact-service" name="service" class="bns-form-input">
                                <option value="Hookah Rental (24h/48h/72h)"><?php esc_html_e('Hookah Rental (24h/48h/72h)', 'shisharent'); ?></option>
                                <option value="Party & Occasion Hookah Package"><?php esc_html_e('Party & Occasion Hookah Package', 'shisharent'); ?></option>
                                <option value="Bartending & Mobile Bar Catering"><?php esc_html_e('Bartending & Mobile Bar Catering', 'shisharent'); ?></option>
                                <option value="VIP Full Lounge with Sommelier"><?php esc_html_e('VIP Full Lounge with Sommelier', 'shisharent'); ?></option>
                                <option value="Flavour Purchase / Supplies"><?php esc_html_e('Flavour Purchase / Supplies', 'shisharent'); ?></option>
                                <option value="General Inquiry"><?php esc_html_e('General Inquiry', 'shisharent'); ?></option>
                            </select>
                        </div>

                        <div class="bns-form-group">
                            <label for="contact-message"><?php esc_html_e('Message / Event Details', 'shisharent'); ?> *</label>
                            <textarea id="contact-message" name="message" class="bns-form-input bns-form-textarea" rows="4" placeholder="Tell us your date, preferred flavours, or event details..." required></textarea>
                        </div>

                        <button type="submit" class="bns-btn-gold bns-btn-block bns-glow-btn" id="bns-contact-submit-btn">
                            <?php esc_html_e('Send Message via WhatsApp / Online', 'shisharent'); ?> →
                        </button>
                        <div id="bns-contact-feedback" style="display:none; margin-top: 14px;"></div>
                    </form>
                </div>
            </div>

            <!-- Right: Map Preview & Address Pin -->
            <div class="bns-contact-map-col">
                <div class="bns-map-container-card">
                    <div class="bns-map-header">
                        <h4>📍 <?php esc_html_e('ShishaRent Kolkata Hub', 'shisharent'); ?></h4>
                        <p class="bns-text-muted">Camac Street Area, Park Circus, Ballygunge, Kolkata</p>
                    </div>
                    
                    <div class="bns-map-embed-wrapper">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14739.736021617267!2d88.35824558715822!3d22.544158400000003!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a02771c5082f483%3A0xb3b45a6c3dd2be96!2sPark%20Circus%2C%20Ballygunge%2C%20Kolkata%2C%20West%20Bengal%20700019!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" 
                            width="100%" 
                            height="340" 
                            style="border:0; border-radius: 12px;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            title="ShishaRent Official Shop Location Map">
                        </iframe>
                    </div>

                    <div class="bns-map-footer-links">
                        <a href="https://maps.google.com/?q=Camac+Street+Area,+9/2A,+Chamru+Khansama+Ln,+Park+Circus,+Ballygunge,+Kolkata,+West+Bengal+700019" target="_blank" rel="noopener noreferrer" class="bns-gold-link">
                            <?php esc_html_e('→ Get Driving / Metro Directions', 'shisharent'); ?>
                        </a>
                    </div>
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
                <span class="bns-disclaimer-icon">⚠️</span>
                <div class="bns-disclaimer-text">
                    <strong class="bns-disclaimer-title"><?php esc_html_e('Health & Safety Notice', 'shisharent'); ?></strong>
                    <p><?php esc_html_e('The consumption of intoxicating substances and exposure to smoke can be harmful to your health. Hookah/shisha smoke, tobacco and nicotine products, alcohol, and other intoxicating substances carry health risks. Please consume responsibly and in accordance with applicable laws and regulations.', 'shisharent'); ?></p>
                </div>
                <div class="bns-age-pill">21+ Strictly</div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
