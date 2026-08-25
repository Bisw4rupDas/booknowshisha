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
                        <strong style="color: var(--bns-accent-gold, #d4a95f);">📍 <?php esc_html_e('Ballygunge Hub', 'shisharent'); ?></strong><br>
                        Camac Street Area, 9/2A, Chamru Khansama Ln,<br>
                        Park Circus, Ballygunge,<br>
                        Kolkata, West Bengal 700019
                    </p>
                    <p class="bns-address-line" style="margin-bottom: 12px; border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 8px;">
                        <strong style="color: var(--bns-accent-gold, #d4a95f);">📍 <?php esc_html_e('Park Street Hub', 'shisharent'); ?></strong><br>
                        Park Street, Chaurangi More,<br>
                        Kolkata – 700071,<br>
                        West Bengal, India
                    </p>
                    <a href="https://maps.google.com/?q=Park+Street,+Chaurangi+More,+Kolkata+700071" target="_blank" rel="noopener noreferrer" class="bns-gold-link" style="display:inline-flex; align-items:center; gap:6px; margin-top: 4px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <span><?php esc_html_e('Get Directions on Google Maps', 'shisharent'); ?> →</span>
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
                    <li><span class="bns-area-tag">✓</span> <?php esc_html_e('Salt Lake & New Town (Sec I-V, AA I-III)', 'shisharent'); ?></li>
                    <li><span class="bns-area-tag">✓</span> <?php esc_html_e('South Kolkata (Ballygunge, Alipore, Gariahat)', 'shisharent'); ?></li>
                    <li><span class="bns-area-tag">✓</span> <?php esc_html_e('Central Kolkata (Park Street, Camac St, Esplanade)', 'shisharent'); ?></li>
                    <li><span class="bns-area-tag">✓</span> <?php esc_html_e('Rajarhat & North Hubs (Chinar Park, Lake Town)', 'shisharent'); ?></li>
                    <li><a href="<?php echo esc_url(home_url('/#checker')); ?>" class="bns-gold-link"><?php esc_html_e('→ Check Delivery PIN Code', 'shisharent'); ?></a></li>
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

<!-- Customer Email Authentication Modal -->
<div id="bns-auth-modal" class="bns-modal bns-auth-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="bns-modal-title">
    <div class="bns-modal-backdrop" id="bns-auth-backdrop"></div>
    <div class="bns-modal-content bns-auth-modal-box">
        <button type="button" class="bns-modal-close" id="bns-auth-close" aria-label="<?php esc_attr_e('Close Modal', 'shisharent'); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
        
        <!-- Header & VIP Branding -->
        <div class="bns-auth-header">
            <div class="bns-auth-brand-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <span class="bns-auth-badge"><?php esc_html_e('SHISHARENT VIP', 'shisharent'); ?></span>
            <h3 id="bns-modal-title" class="bns-auth-title"><?php esc_html_e('SIGN IN', 'shisharent'); ?></h3>
            <p id="bns-modal-subtitle" class="bns-auth-subtitle"><?php esc_html_e('Welcome to ShishaRent. Sign in to access your Kolkata reservations, track active rentals, and manage your account.', 'shisharent'); ?></p>
        </div>

        <!-- Sleek Dual Pill Tab Switcher -->
        <div class="bns-auth-tabs-wrap" id="bns-modal-tabs-wrap">
            <div class="bns-auth-tabs">
                <button type="button" class="bns-auth-tab active" data-target="signin" id="bns-tab-btn-signin">
                    <?php esc_html_e('SIGN IN', 'shisharent'); ?>
                </button>
                <button type="button" class="bns-auth-tab" data-target="signup" id="bns-tab-btn-signup">
                    <?php esc_html_e('CREATE ACCOUNT', 'shisharent'); ?>
                </button>
            </div>
        </div>

        <!-- Alert Notification Box -->
        <div id="bns-auth-alert" class="bns-auth-alert" style="display:none;"></div>

        <!-- 1. SIGN IN VIEW -->
        <div id="bns-view-signin" class="bns-auth-view">
            <form id="bns-form-signin" class="bns-auth-form" method="post" autocomplete="on">
                <input type="hidden" name="action" value="bns_email_login">
                <input type="hidden" name="security" value="<?php echo esc_attr(wp_create_nonce('bns_auth_nonce')); ?>">
                
                <div class="bns-form-group">
                    <label for="bns-login-email" class="bns-label"><?php esc_html_e('EMAIL ADDRESS', 'shisharent'); ?> <span class="bns-req">*</span></label>
                    <div class="bns-input-wrapper">
                        <input type="email" id="bns-login-email" name="email" class="bns-input" required autocomplete="email" placeholder="name@example.com">
                    </div>
                </div>

                <div class="bns-form-group">
                    <div class="bns-label-row">
                        <label for="bns-login-password" class="bns-label"><?php esc_html_e('PASSWORD', 'shisharent'); ?> <span class="bns-req">*</span></label>
                        <a href="#" id="bns-link-to-forgot" class="bns-forgot-link"><?php esc_html_e('Forgot Password?', 'shisharent'); ?></a>
                    </div>
                    <div class="bns-input-wrapper bns-password-wrap">
                        <input type="password" id="bns-login-password" name="password" class="bns-input" required autocomplete="current-password" placeholder="••••••••">
                        <button type="button" class="bns-pwd-toggle" aria-label="<?php esc_attr_e('Toggle password visibility', 'shisharent'); ?>" tabindex="-1">
                            <svg class="bns-eye-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="bns-eye-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div class="bns-form-group bns-remember-row">
                    <label class="bns-checkbox-label">
                        <input type="checkbox" id="bns-login-remember" name="remember" value="1" checked>
                        <span><?php esc_html_e('Remember me', 'shisharent'); ?></span>
                    </label>
                </div>

                <div class="bns-form-action">
                    <button type="submit" id="bns-btn-signin" class="bns-btn-gold bns-btn-block">
                        <span class="bns-btn-text"><?php esc_html_e('SIGN IN', 'shisharent'); ?></span>
                    </button>
                </div>

                <div class="bns-auth-divider">
                    <span><?php esc_html_e('OR', 'shisharent'); ?></span>
                </div>

                <div class="bns-switch-action">
                    <button type="button" id="bns-link-to-signup" class="bns-btn-outline bns-btn-block">
                        <?php esc_html_e('CREATE ACCOUNT', 'shisharent'); ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. CREATE ACCOUNT (SIGN UP) VIEW -->
        <div id="bns-view-signup" class="bns-auth-view" style="display:none;">
            <form id="bns-form-signup" class="bns-auth-form" method="post" autocomplete="on">
                <input type="hidden" name="action" value="bns_email_register">
                <input type="hidden" name="security" value="<?php echo esc_attr(wp_create_nonce('bns_auth_nonce')); ?>">

                <div class="bns-form-group">
                    <label for="bns-signup-name" class="bns-label"><?php esc_html_e('FULL NAME', 'shisharent'); ?> <span class="bns-req">*</span></label>
                    <div class="bns-input-wrapper">
                        <input type="text" id="bns-signup-name" name="name" class="bns-input" required autocomplete="name" placeholder="John Doe">
                    </div>
                </div>

                <div class="bns-form-group">
                    <label for="bns-signup-email" class="bns-label"><?php esc_html_e('EMAIL ADDRESS', 'shisharent'); ?> <span class="bns-req">*</span></label>
                    <div class="bns-input-wrapper">
                        <input type="email" id="bns-signup-email" name="email" class="bns-input" required autocomplete="email" placeholder="name@example.com">
                    </div>
                </div>

                <div class="bns-form-group">
                    <label for="bns-signup-password" class="bns-label"><?php esc_html_e('PASSWORD', 'shisharent'); ?> <span class="bns-req">*</span></label>
                    <div class="bns-input-wrapper bns-password-wrap">
                        <input type="password" id="bns-signup-password" name="password" class="bns-input" required autocomplete="new-password" placeholder="<?php esc_attr_e('Min. 8 characters', 'shisharent'); ?>" minlength="8">
                        <button type="button" class="bns-pwd-toggle" aria-label="<?php esc_attr_e('Toggle password visibility', 'shisharent'); ?>" tabindex="-1">
                            <svg class="bns-eye-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="bns-eye-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div class="bns-form-group">
                    <label for="bns-signup-confirm-password" class="bns-label"><?php esc_html_e('CONFIRM PASSWORD', 'shisharent'); ?> <span class="bns-req">*</span></label>
                    <div class="bns-input-wrapper bns-password-wrap">
                        <input type="password" id="bns-signup-confirm-password" name="confirm_password" class="bns-input" required autocomplete="new-password" placeholder="<?php esc_attr_e('Confirm password', 'shisharent'); ?>" minlength="8">
                        <button type="button" class="bns-pwd-toggle" aria-label="<?php esc_attr_e('Toggle password visibility', 'shisharent'); ?>" tabindex="-1">
                            <svg class="bns-eye-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="bns-eye-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div class="bns-form-action">
                    <button type="submit" id="bns-btn-signup" class="bns-btn-gold bns-btn-block">
                        <span class="bns-btn-text"><?php esc_html_e('CREATE ACCOUNT', 'shisharent'); ?></span>
                    </button>
                </div>

                <div class="bns-auth-divider">
                    <span><?php esc_html_e('OR', 'shisharent'); ?></span>
                </div>

                <div class="bns-switch-action">
                    <button type="button" id="bns-link-to-signin-from-signup" class="bns-btn-outline bns-btn-block">
                        <?php esc_html_e('ALREADY HAVE AN ACCOUNT? SIGN IN', 'shisharent'); ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. FORGOT PASSWORD VIEW -->
        <div id="bns-view-forgot" class="bns-auth-view" style="display:none;">
            <form id="bns-form-forgot" class="bns-auth-form" method="post" autocomplete="on">
                <input type="hidden" name="action" value="bns_forgot_password">
                <input type="hidden" name="security" value="<?php echo esc_attr(wp_create_nonce('bns_auth_nonce')); ?>">

                <div class="bns-form-group">
                    <label for="bns-forgot-email" class="bns-label"><?php esc_html_e('EMAIL ADDRESS', 'shisharent'); ?> <span class="bns-req">*</span></label>
                    <div class="bns-input-wrapper">
                        <input type="email" id="bns-forgot-email" name="email" class="bns-input" required autocomplete="email" placeholder="name@example.com">
                    </div>
                </div>

                <div class="bns-form-action">
                    <button type="submit" id="bns-btn-forgot" class="bns-btn-gold bns-btn-block">
                        <span class="bns-btn-text"><?php esc_html_e('SEND RESET LINK', 'shisharent'); ?></span>
                    </button>
                </div>

                <div class="bns-auth-divider">
                    <span><?php esc_html_e('OR', 'shisharent'); ?></span>
                </div>

                <div class="bns-switch-action">
                    <button type="button" id="bns-link-to-signin-from-forgot" class="bns-btn-outline bns-btn-block">
                        <?php esc_html_e('← BACK TO SIGN IN', 'shisharent'); ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Compliance & Security Notice -->
        <div class="bns-auth-footer-notice">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dfb76c" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span><?php esc_html_e('21+ Legal Age Verification • Secure 256-Bit SSL Encryption', 'shisharent'); ?></span>
        </div>

        <!-- Administrator Direct Access Note -->
        <div class="bns-admin-hint-row">
            <a href="<?php echo esc_url(wp_login_url()); ?>" class="bns-admin-hint-link">
                <?php esc_html_e('WordPress Administrator Login →', 'shisharent'); ?>
            </a>
        </div>
    </div>
</div><?php wp_footer(); ?>
</body>
</html>


