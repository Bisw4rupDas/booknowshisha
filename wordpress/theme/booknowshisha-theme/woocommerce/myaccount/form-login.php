<?php
/**
 * Customer Google Sign-In Form Template Override for WooCommerce
 * Exclusively Kolkata / India - Google Authentication (Mock / Placeholder Ready)
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_customer_login_form'); ?>

<div class="bns-myaccount-auth-container" id="bns-myaccount-auth-container">
    <div class="bns-google-modal-box bns-myaccount-google-card">
        
        <!-- Header & VIP Branding -->
        <div class="bns-auth-header">
            <div class="bns-auth-brand-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <span class="bns-auth-badge"><?php esc_html_e('BOOKMYSMOKE', 'shisharent'); ?></span>
            <h2 id="bns-inpage-auth-title" class="bns-auth-title"><?php esc_html_e('SIGN IN', 'shisharent'); ?></h2>
            <p id="bns-inpage-auth-subtitle" class="bns-auth-subtitle"><?php esc_html_e('Welcome to BookMySmoke. Sign in with Google to view active reservations, rental history, and manage your account.', 'shisharent'); ?></p>
        </div>

        <!-- Alert Notification Box -->
        <div id="bns-inpage-auth-alert" class="bns-auth-alert" style="display:none;"></div>

        <!-- Google Single Sign-On Action -->
        <div class="bns-google-action-wrap">
            <button type="button" id="bns-inpage-btn-google-auth" class="bns-btn-google-auth bns-inpage-btn-google" aria-label="<?php esc_attr_e('Continue with Google', 'shisharent'); ?>">
                <span class="bns-google-icon-wrap">
                    <svg width="20" height="20" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                </span>
                <span class="bns-google-text"><?php esc_html_e('Continue with Google', 'shisharent'); ?></span>
            </button>
        </div>

        <!-- Compliance & Security Notice -->
        <div class="bns-auth-footer-notice">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d4a95f" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span><?php esc_html_e('21+ Legal Age Verification • Secure Single Sign-On', 'shisharent'); ?></span>
        </div>

        <!-- Administrator Direct Access Note -->
        <div class="bns-admin-hint-row">
            <a href="<?php echo esc_url(wp_login_url()); ?>" class="bns-admin-hint-link">
                <?php esc_html_e('WordPress Administrator Login ?', 'shisharent'); ?>
            </a>
        </div>

    </div>
</div>

<?php do_action('woocommerce_after_customer_login_form'); ?>
