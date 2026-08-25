<?php
/**
 * Customer Email Authentication Form Template Override for WooCommerce
 * Exclusively Kolkata / India - Email-Only Authentication
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_customer_login_form'); ?>

<div class="bns-myaccount-auth-container" id="bns-myaccount-auth-container">
    <div class="bns-auth-modal-box bns-myaccount-auth-card">
        
        <!-- Header & VIP Branding -->
        <div class="bns-auth-header">
            <div class="bns-auth-brand-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <span class="bns-auth-badge"><?php esc_html_e('BOOKMYSMOKE VIP', 'shisharent'); ?></span>
            <h2 id="bns-inpage-auth-title" class="bns-auth-title"><?php esc_html_e('SIGN IN', 'shisharent'); ?></h2>
            <p id="bns-inpage-auth-subtitle" class="bns-auth-subtitle"><?php esc_html_e('Welcome to BookMySmoke. Sign in to view active reservations, rental history, and manage your account.', 'shisharent'); ?></p>
        </div>

        <!-- Sleek Dual Pill Tab Switcher -->
        <div class="bns-auth-tabs-wrap" id="bns-inpage-tabs-wrap">
            <div class="bns-auth-tabs">
                <button type="button" class="bns-auth-tab active" data-target="signin" id="bns-inpage-tab-btn-signin">
                    <?php esc_html_e('SIGN IN', 'shisharent'); ?>
                </button>
                <button type="button" class="bns-auth-tab" data-target="signup" id="bns-inpage-tab-btn-signup">
                    <?php esc_html_e('CREATE ACCOUNT', 'shisharent'); ?>
                </button>
            </div>
        </div>

        <!-- Alert Notification Box -->
        <div id="bns-inpage-auth-alert" class="bns-auth-alert" style="display:none;"></div>

        <!-- 1. SIGN IN VIEW -->
        <div id="bns-inpage-view-signin" class="bns-auth-view">
            <form id="bns-inpage-form-signin" class="bns-auth-form" method="post" autocomplete="on">
                <input type="hidden" name="action" value="bns_email_login">
                <input type="hidden" name="security" value="<?php echo esc_attr(wp_create_nonce('bns_auth_nonce')); ?>">
                
                <div class="bns-form-group">
                    <label for="bns-inpage-login-email" class="bns-label"><?php esc_html_e('EMAIL ADDRESS', 'shisharent'); ?> <span class="bns-req">*</span></label>
                    <div class="bns-input-wrapper">
                        <input type="email" id="bns-inpage-login-email" name="email" class="bns-input" required autocomplete="email" placeholder="name@example.com">
                    </div>
                </div>

                <div class="bns-form-group">
                    <div class="bns-label-row">
                        <label for="bns-inpage-login-password" class="bns-label"><?php esc_html_e('PASSWORD', 'shisharent'); ?> <span class="bns-req">*</span></label>
                        <a href="#" id="bns-inpage-link-to-forgot" class="bns-forgot-link"><?php esc_html_e('Forgot Password?', 'shisharent'); ?></a>
                    </div>
                    <div class="bns-input-wrapper bns-password-wrap">
                        <input type="password" id="bns-inpage-login-password" name="password" class="bns-input" required autocomplete="current-password" placeholder="••••••••">
                        <button type="button" class="bns-pwd-toggle" aria-label="<?php esc_attr_e('Toggle password visibility', 'shisharent'); ?>" tabindex="-1">
                            <svg class="bns-eye-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="bns-eye-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div class="bns-form-group bns-remember-row">
                    <label class="bns-checkbox-label">
                        <input type="checkbox" id="bns-inpage-login-remember" name="remember" value="1" checked>
                        <span><?php esc_html_e('Remember me', 'shisharent'); ?></span>
                    </label>
                </div>

                <div class="bns-form-action">
                    <button type="submit" id="bns-inpage-btn-signin" class="bns-btn-gold bns-btn-block">
                        <span class="bns-btn-text"><?php esc_html_e('SIGN IN', 'shisharent'); ?></span>
                    </button>
                </div>

                <div class="bns-auth-divider">
                    <span><?php esc_html_e('OR', 'shisharent'); ?></span>
                </div>

                <div class="bns-switch-action">
                    <button type="button" id="bns-inpage-link-to-signup" class="bns-btn-outline bns-btn-block">
                        <?php esc_html_e('CREATE ACCOUNT', 'shisharent'); ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. CREATE ACCOUNT (SIGN UP) VIEW -->
        <div id="bns-inpage-view-signup" class="bns-auth-view" style="display:none;">
            <form id="bns-inpage-form-signup" class="bns-auth-form" method="post" autocomplete="on">
                <input type="hidden" name="action" value="bns_email_register">
                <input type="hidden" name="security" value="<?php echo esc_attr(wp_create_nonce('bns_auth_nonce')); ?>">

                <div class="bns-form-group">
                    <label for="bns-inpage-signup-name" class="bns-label"><?php esc_html_e('FULL NAME', 'shisharent'); ?> <span class="bns-req">*</span></label>
                    <div class="bns-input-wrapper">
                        <input type="text" id="bns-inpage-signup-name" name="name" class="bns-input" required autocomplete="name" placeholder="John Doe">
                    </div>
                </div>

                <div class="bns-form-group">
                    <label for="bns-inpage-signup-email" class="bns-label"><?php esc_html_e('EMAIL ADDRESS', 'shisharent'); ?> <span class="bns-req">*</span></label>
                    <div class="bns-input-wrapper">
                        <input type="email" id="bns-inpage-signup-email" name="email" class="bns-input" required autocomplete="email" placeholder="name@example.com">
                    </div>
                </div>

                <div class="bns-form-group">
                    <label for="bns-inpage-signup-password" class="bns-label"><?php esc_html_e('PASSWORD', 'shisharent'); ?> <span class="bns-req">*</span></label>
                    <div class="bns-input-wrapper bns-password-wrap">
                        <input type="password" id="bns-inpage-signup-password" name="password" class="bns-input" required autocomplete="new-password" placeholder="<?php esc_attr_e('Min. 8 characters', 'shisharent'); ?>" minlength="8">
                        <button type="button" class="bns-pwd-toggle" aria-label="<?php esc_attr_e('Toggle password visibility', 'shisharent'); ?>" tabindex="-1">
                            <svg class="bns-eye-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="bns-eye-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div class="bns-form-group">
                    <label for="bns-inpage-signup-confirm-password" class="bns-label"><?php esc_html_e('CONFIRM PASSWORD', 'shisharent'); ?> <span class="bns-req">*</span></label>
                    <div class="bns-input-wrapper bns-password-wrap">
                        <input type="password" id="bns-inpage-signup-confirm-password" name="confirm_password" class="bns-input" required autocomplete="new-password" placeholder="<?php esc_attr_e('Confirm password', 'shisharent'); ?>" minlength="8">
                        <button type="button" class="bns-pwd-toggle" aria-label="<?php esc_attr_e('Toggle password visibility', 'shisharent'); ?>" tabindex="-1">
                            <svg class="bns-eye-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="bns-eye-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div class="bns-form-action">
                    <button type="submit" id="bns-inpage-btn-signup" class="bns-btn-gold bns-btn-block">
                        <span class="bns-btn-text"><?php esc_html_e('CREATE ACCOUNT', 'shisharent'); ?></span>
                    </button>
                </div>

                <div class="bns-auth-divider">
                    <span><?php esc_html_e('OR', 'shisharent'); ?></span>
                </div>

                <div class="bns-switch-action">
                    <button type="button" id="bns-inpage-link-to-signin-from-signup" class="bns-btn-outline bns-btn-block">
                        <?php esc_html_e('ALREADY HAVE AN ACCOUNT? SIGN IN', 'shisharent'); ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. FORGOT PASSWORD VIEW -->
        <div id="bns-inpage-view-forgot" class="bns-auth-view" style="display:none;">
            <form id="bns-inpage-form-forgot" class="bns-auth-form" method="post" autocomplete="on">
                <input type="hidden" name="action" value="bns_forgot_password">
                <input type="hidden" name="security" value="<?php echo esc_attr(wp_create_nonce('bns_auth_nonce')); ?>">

                <div class="bns-form-group">
                    <label for="bns-inpage-forgot-email" class="bns-label"><?php esc_html_e('EMAIL ADDRESS', 'shisharent'); ?> <span class="bns-req">*</span></label>
                    <div class="bns-input-wrapper">
                        <input type="email" id="bns-inpage-forgot-email" name="email" class="bns-input" required autocomplete="email" placeholder="name@example.com">
                    </div>
                </div>

                <div class="bns-form-action">
                    <button type="submit" id="bns-inpage-btn-forgot" class="bns-btn-gold bns-btn-block">
                        <span class="bns-btn-text"><?php esc_html_e('SEND RESET LINK', 'shisharent'); ?></span>
                    </button>
                </div>

                <div class="bns-auth-divider">
                    <span><?php esc_html_e('OR', 'shisharent'); ?></span>
                </div>

                <div class="bns-switch-action">
                    <button type="button" id="bns-inpage-link-to-signin-from-forgot" class="bns-btn-outline bns-btn-block">
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
</div>

<?php do_action('woocommerce_after_customer_login_form'); ?>
