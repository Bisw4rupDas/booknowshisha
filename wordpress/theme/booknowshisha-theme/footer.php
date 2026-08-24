<?php
/**
 * Footer template for ShishaRent Theme (Kolkata Edition)
 * Includes official shop address, quick links, and health/safety notice.
 *
 * @package ShishaRent
 */
?>
</main><!-- #bns-main-content -->

<footer class="bns-footer" id="bns-footer-main">
    <div class="bns-footer-glow-accent"></div>
    <div class="bns-container">
        
        <!-- Main Footer Columns -->
        <div class="bns-footer-grid">
            
            <!-- Col 1: Brand & Bio -->
            <div class="bns-footer-col bns-footer-col-brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="bns-footer-logo" rel="home">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png?v=' . filemtime(get_template_directory() . '/assets/images/logo.png')); ?>" alt="<?php esc_attr_e('ShishaRent', 'shisharent'); ?>" class="bns-logo-img bns-footer-logo-img" />
                </a>
                <p class="bns-footer-tagline">
                    <?php esc_html_e('Premium on-demand hookah rentals & mobile party bar catering delivered directly to your doorstep across Kolkata & surrounding areas with medical-grade hygiene standards.', 'shisharent'); ?>
                </p>
                
                <div class="bns-social-links">
                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="bns-social-btn" title="Instagram" aria-label="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                    </a>
                    <a href="https://wa.me/919903556825" target="_blank" rel="noopener noreferrer" class="bns-social-btn" title="WhatsApp: +91 99035 56825" aria-label="WhatsApp">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    </a>
                    <a href="https://t.me" target="_blank" rel="noopener noreferrer" class="bns-social-btn" title="Telegram" aria-label="Telegram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </a>
                </div>
            </div>

            <!-- Col 2: Official Shop Locations & Contact -->
            <div class="bns-footer-col">
                <h5 class="bns-footer-heading"><?php esc_html_e('Official Shop Locations', 'shisharent'); ?></h5>
                <div class="bns-footer-address-box">
                    <p class="bns-address-line" style="margin-bottom: 12px;">
                        <strong style="color: var(--bns-accent-gold, #d4a95f);">ðŸ“ <?php esc_html_e('Ballygunge Hub', 'shisharent'); ?></strong><br>
                        Camac Street Area, 9/2A, Chamru Khansama Ln,<br>
                        Park Circus, Ballygunge,<br>
                        Kolkata, West Bengal 700019
                    </p>
                    <p class="bns-address-line" style="margin-bottom: 12px; border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 8px;">
                        <strong style="color: var(--bns-accent-gold, #d4a95f);">ðŸ“ <?php esc_html_e('Park Street Hub', 'shisharent'); ?></strong><br>
                        Park Street, Chaurangi More,<br>
                        Kolkata â€“ 700071,<br>
                        West Bengal, India
                    </p>
                    <a href="https://maps.google.com/?q=Park+Street,+Chaurangi+More,+Kolkata+700071" target="_blank" rel="noopener noreferrer" class="bns-gold-link" style="display:inline-flex; align-items:center; gap:6px; margin-top: 4px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <span><?php esc_html_e('Get Directions on Google Maps', 'shisharent'); ?> â†’</span>
                    </a>
                    <div class="bns-footer-phone-list" style="margin-top: 14px;">
                        <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <a href="tel:+919903556825" class="bns-phone-link">+91 99035 56825</a>
                        </div>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <a href="tel:+919051177720" class="bns-phone-link">+91 90511 77720</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Col 3: Quick Navigation Links -->
            <div class="bns-footer-col">
                <h5 class="bns-footer-heading"><?php esc_html_e('Quick Links', 'shisharent'); ?></h5>
                <ul class="bns-footer-links">
                    <li><a href="<?php echo esc_url(home_url('/#packages')); ?>"><?php esc_html_e('Rent a Hookah', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/gallery/')); ?>"><?php esc_html_e('ShishaRent Gallery', 'shisharent'); ?></a></li>
                    <?php if (class_exists('WooCommerce')): ?>
                        <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"><?php esc_html_e('Shop Catalog', 'shisharent'); ?></a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo esc_url(home_url('/#packages')); ?>"><?php esc_html_e('Rental Packages', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/#flavours')); ?>"><?php esc_html_e('Flavours & Blends', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/party-occasion-hookah/')); ?>"><?php esc_html_e('Party & Occasion Hookah', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/bartending-party-services/')); ?>"><?php esc_html_e('Bartending & Party Services', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog/')); ?>"><?php esc_html_e('Blog & Journal', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact Us', 'shisharent'); ?></a></li>
                </ul>
            </div>

            <!-- Col 4: Express Service Hubs & Hygiene Policy -->
            <div class="bns-footer-col">
                <h5 class="bns-footer-heading"><?php esc_html_e('Kolkata Service Zones', 'shisharent'); ?></h5>
                <ul class="bns-footer-links">
                    <li><span class="bns-area-tag">âœ“</span> <?php esc_html_e('Salt Lake & New Town (Sec I-V, AA I-III)', 'shisharent'); ?></li>
                    <li><span class="bns-area-tag">âœ“</span> <?php esc_html_e('South Kolkata (Ballygunge, Alipore, Gariahat)', 'shisharent'); ?></li>
                    <li><span class="bns-area-tag">âœ“</span> <?php esc_html_e('Central Kolkata (Park Street, Camac St, Esplanade)', 'shisharent'); ?></li>
                    <li><span class="bns-area-tag">âœ“</span> <?php esc_html_e('Rajarhat & North Hubs (Chinar Park, Lake Town)', 'shisharent'); ?></li>
                    <li><a href="<?php echo esc_url(home_url('/#checker')); ?>" class="bns-gold-link"><?php esc_html_e('â†’ Check Delivery PIN Code', 'shisharent'); ?></a></li>
                </ul>
                <div class="bns-age-badge" style="margin-top: 14px;">
                    <span class="bns-age-number">21+</span>
                    <span class="bns-age-text"><?php esc_html_e('STRICTLY ADULTS ONLY', 'shisharent'); ?></span>
                </div>
            </div>

        </div>

        <!-- Health & Safety Disclaimer Notice Box -->
        <div class="bns-footer-disclaimer-wrapper">
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

        <!-- Footer Bottom Bar -->
        <div class="bns-footer-bottom">
            <div class="bns-footer-bottom-inner">
                <p>&copy; <?php echo esc_html(date('Y')); ?> <span class="bns-gold">SHISHARENT</span>. <?php esc_html_e('All rights reserved. Dedicated to premium hookah rentals in Kolkata, North 24 Parganas, and South 24 Parganas.', 'shisharent'); ?></p>
                <div class="bns-payment-icons">
                    <span class="bns-pay-pill">UPI / GPay</span>
                    <span class="bns-pay-pill">Credit / Debit Card</span>
                    <span class="bns-pay-pill">Net Banking</span>
                    <span class="bns-pay-pill">Cash on Delivery (COD)</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Floating Mobile Navigation Dock -->
<div class="bns-mobile-dock">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="bns-dock-item <?php echo is_front_page() ? 'active' : ''; ?>">
        <span class="bns-dock-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        </span>
        <span class="bns-dock-label"><?php esc_html_e('Home', 'shisharent'); ?></span>
    </a>
    <a href="<?php echo esc_url(home_url('/#packages')); ?>" class="bns-dock-item">
        <span class="bns-dock-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
        </span>
        <span class="bns-dock-label"><?php esc_html_e('Packages', 'shisharent'); ?></span>
    </a>
    <a href="<?php echo esc_url(home_url('/bartending-party-services/')); ?>" class="bns-dock-item <?php echo (is_page('bartending-party-services') || is_page('party-occasion-hookah')) ? 'active' : ''; ?>">
        <span class="bns-dock-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        </span>
        <span class="bns-dock-label"><?php esc_html_e('Services', 'shisharent'); ?></span>
    </a>
    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog/')); ?>" class="bns-dock-item <?php echo (is_home() || is_singular('post')) ? 'active' : ''; ?>">
        <span class="bns-dock-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
        </span>
        <span class="bns-dock-label"><?php esc_html_e('Blog', 'shisharent'); ?></span>
    </a>
    <?php if (class_exists('WooCommerce')): ?>
        <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="bns-dock-item bns-dock-cart">
            <span class="bns-dock-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            </span>
            <span class="bns-dock-label"><?php esc_html_e('Cart', 'shisharent'); ?></span>
            <span class="bns-dock-badge" id="bns-dock-cart-count"><?php echo (WC()->cart) ? WC()->cart->get_cart_contents_count() : 0; ?></span>
        </a>
    <?php endif; ?>
    <a href="tel:+919903556825" class="bns-dock-item bns-dock-call">
        <span class="bns-dock-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
        </span>
        <span class="bns-dock-label"><?php esc_html_e('Call', 'shisharent'); ?></span>
    </a>
</div>

<!-- Customer Mobile OTP Authentication Modal -->
<div id="bns-auth-modal" class="bns-modal bns-otp-auth-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="bns-otp-modal-title">
    <div class="bns-modal-backdrop" id="bns-auth-backdrop"></div>
    <div class="bns-modal-content bns-otp-modal-box">
        <button type="button" class="bns-modal-close" id="bns-auth-close" aria-label="<?php esc_attr_e('Close Modal', 'shisharent'); ?>">âœ•</button>
        
        <div class="bns-otp-header">
            <div class="bns-otp-brand-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <span class="bns-otp-badge"><?php esc_html_e('KOLKATA VIP ACCESS', 'shisharent'); ?></span>
            <h3 id="bns-otp-modal-title" class="bns-otp-title"><?php esc_html_e('SIGN IN WITH MOBILE', 'shisharent'); ?></h3>
            <p class="bns-otp-subtitle"><?php esc_html_e('Enter your Indian mobile number to receive a secure 6-digit OTP.', 'shisharent'); ?></p>
        </div>

        <!-- Alert Notification Box -->
        <div id="bns-otp-alert" class="bns-otp-alert" style="display:none;"></div>

        <!-- STEP 1: Phone Number Input -->
        <form id="bns-otp-phone-form" class="bns-otp-form-step">
            <div class="bns-otp-input-group">
                <label for="bns-otp-phone-input" class="bns-otp-label"><?php esc_html_e('MOBILE NUMBER', 'shisharent'); ?> <span class="bns-req">*</span></label>
                <div class="bns-phone-prefix-wrap">
                    <span class="bns-phone-prefix">+91</span>
                    <input type="tel" id="bns-otp-phone-input" name="phone" required placeholder="98300 12345" maxlength="10" pattern="[6-9][0-9]{9}" inputmode="numeric" autocomplete="tel-national" class="bns-otp-input" />
                </div>
                <span class="bns-otp-hint"><?php esc_html_e('Enter your 10-digit mobile number (e.g. 9830012345)', 'shisharent'); ?></span>
            </div>

            <button type="submit" id="bns-btn-send-otp" class="button bns-btn-otp-gold">
                <span><?php esc_html_e('SEND OTP â†’', 'shisharent'); ?></span>
            </button>
        </form>

        <!-- STEP 2: OTP Verification -->
        <form id="bns-otp-verify-form" class="bns-otp-form-step" style="display:none;">
            <div class="bns-otp-phone-summary">
                <span class="bns-ops-label"><?php esc_html_e('Code sent to:', 'shisharent'); ?></span>
                <strong id="bns-otp-sent-number" class="bns-ops-number">+91 â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢</strong>
                <button type="button" id="bns-otp-edit-phone-btn" class="bns-btn-edit-phone" title="<?php esc_attr_e('Change mobile number', 'shisharent'); ?>">
                    <?php esc_html_e('Change', 'shisharent'); ?>
                </button>
            </div>

            <div class="bns-otp-input-group">
                <label for="bns-otp-code-input" class="bns-otp-label"><?php esc_html_e('6-DIGIT OTP CODE', 'shisharent'); ?> <span class="bns-req">*</span></label>
                <input type="text" id="bns-otp-code-input" name="otp" required placeholder="â€¢â€¢â€¢â€¢â€¢â€¢" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" class="bns-otp-input bns-otp-code-field" />
                <div class="bns-otp-resend-row">
                    <span id="bns-otp-timer-text" class="bns-otp-timer"><?php esc_html_e('Resend OTP in 30s', 'shisharent'); ?></span>
                    <button type="button" id="bns-btn-resend-otp" class="bns-btn-resend" style="display:none;">
                        <?php esc_html_e('Resend OTP', 'shisharent'); ?>
                    </button>
                </div>
            </div>

            <button type="submit" id="bns-btn-verify-otp" class="button bns-btn-otp-gold">
                <span><?php esc_html_e('VERIFY & SIGN IN â†’', 'shisharent'); ?></span>
            </button>
        </form>

        <!-- Compliance & Luxury Footer -->
        <div class="bns-otp-footer-notice">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d4a95f" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span><?php esc_html_e('21+ Legal Age Verification â€¢ Instant Access', 'shisharent'); ?></span>
        </div>
    </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
