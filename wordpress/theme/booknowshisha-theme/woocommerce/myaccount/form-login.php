<?php
/**
 * Customer Mobile OTP Login Form Template Override for WooCommerce
 * Exclusively Kolkata / India - Mobile Number + OTP Only (No Google Login)
 *
 * @package ShishaRent
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_customer_login_form'); ?>

<div class="bns-myaccount-auth-container" id="bns-myaccount-auth-container">
    <div class="bns-otp-modal-box bns-myaccount-otp-card">
        
        <!-- Header & VIP Branding -->
        <div class="bns-otp-header">
            <div class="bns-otp-brand-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <span class="bns-otp-badge"><?php esc_html_e('KOLKATA VIP ACCESS', 'shisharent'); ?></span>
            <h2 id="bns-inpage-otp-title" class="bns-otp-title"><?php esc_html_e('SIGN IN WITH MOBILE', 'shisharent'); ?></h2>
            <p id="bns-inpage-otp-subtitle" class="bns-otp-subtitle"><?php esc_html_e('Enter your 10-digit Indian mobile number to receive a secure 6-digit verification code.', 'shisharent'); ?></p>
        </div>

        <!-- Alert Notification Box -->
        <div id="bns-inpage-otp-alert" class="bns-otp-alert" style="display:none;"></div>

        <!-- STEP 1: Phone Number Input -->
        <form id="bns-inpage-otp-phone-form" class="bns-otp-form-step bns-otp-phone-form">
            <div class="bns-otp-input-group">
                <label for="bns-inpage-otp-phone-input" class="bns-otp-label">
                    <?php esc_html_e('MOBILE NUMBER', 'shisharent'); ?> <span class="bns-req">*</span>
                </label>
                <div class="bns-phone-prefix-wrap">
                    <span class="bns-phone-prefix">+91</span>
                    <input type="tel" 
                           id="bns-inpage-otp-phone-input" 
                           name="phone" 
                           required 
                           placeholder="98300 12345" 
                           maxlength="10" 
                           pattern="[6-9][0-9]{9}" 
                           inputmode="numeric" 
                           autocomplete="tel-national" 
                           class="bns-otp-input bns-otp-phone-input" />
                </div>
                <span class="bns-otp-hint"><?php esc_html_e('Enter your 10-digit mobile number (e.g. 9830012345)', 'shisharent'); ?></span>
            </div>

            <button type="submit" id="bns-inpage-btn-send-otp" class="button bns-btn-otp-gold bns-btn-send-otp">
                <span><?php esc_html_e('SEND OTP ?', 'shisharent'); ?></span>
            </button>
        </form>

        <!-- STEP 2: OTP Verification -->
        <form id="bns-inpage-otp-verify-form" class="bns-otp-form-step bns-otp-verify-form" style="display:none;">
            <div class="bns-otp-phone-summary">
                <span class="bns-ops-label"><?php esc_html_e('OTP sent to:', 'shisharent'); ?></span>
                <strong id="bns-inpage-otp-sent-number" class="bns-ops-number bns-otp-sent-number">+91 ••••• •••••</strong>
                <button type="button" id="bns-inpage-otp-edit-phone-btn" class="bns-btn-edit-phone" title="<?php esc_attr_e('Change mobile number', 'shisharent'); ?>">
                    <?php esc_html_e('Change Number', 'shisharent'); ?>
                </button>
            </div>

            <div class="bns-otp-input-group">
                <label for="bns-inpage-otp-code-input" class="bns-otp-label">
                    <?php esc_html_e('6-DIGIT OTP CODE', 'shisharent'); ?> <span class="bns-req">*</span>
                </label>
                <input type="text" 
                       id="bns-inpage-otp-code-input" 
                       name="otp" 
                       required 
                       placeholder="••••••" 
                       maxlength="6" 
                       pattern="[0-9]{6}" 
                       inputmode="numeric" 
                       autocomplete="one-time-code" 
                       class="bns-otp-input bns-otp-code-field bns-otp-code-input" />
                <div class="bns-otp-resend-row">
                    <span id="bns-inpage-otp-timer-text" class="bns-otp-timer bns-otp-timer-text"><?php esc_html_e('Resend OTP in 30s', 'shisharent'); ?></span>
                    <button type="button" id="bns-inpage-btn-resend-otp" class="bns-btn-resend bns-btn-resend-otp" style="display:none;">
                        <?php esc_html_e('Resend OTP', 'shisharent'); ?>
                    </button>
                </div>
            </div>

            <button type="submit" id="bns-inpage-btn-verify-otp" class="button bns-btn-otp-gold bns-btn-verify-otp">
                <span><?php esc_html_e('VERIFY & CONTINUE ?', 'shisharent'); ?></span>
            </button>
        </form>

        <!-- Compliance Notice -->
        <div class="bns-otp-footer-notice">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d4a95f" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span><?php esc_html_e('21+ Legal Age Verification • Instant Access', 'shisharent'); ?></span>
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
