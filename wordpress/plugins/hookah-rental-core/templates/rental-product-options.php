<?php
/**
 * Rental Product Single Page Options Template
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="bns-rental-product-options bns-card">
    <div class="bns-options-header">
        <h4><span class="bns-gold">SHISHA</span>RENT Configuration</h4>
        <p class="bns-options-desc"><?php esc_html_e('Customize your rental schedule, flavours, and delivery preferences.', 'hookah-rental-core'); ?></p>
    </div>

    <!-- Rental Start Date -->
    <div class="bns-rental-field">
        <label for="bns_rental_start_date">
            <strong><?php esc_html_e('1. Select Rental Start Date', 'hookah-rental-core'); ?></strong> <span class="bns-required">*</span>
        </label>
        <input type="date" id="bns_rental_start_date" name="bns_rental_date" class="bns-rental-input" min="<?php echo esc_attr(date('Y-m-d')); ?>" value="<?php echo esc_attr(date('Y-m-d')); ?>" required />
    </div>

    <!-- Rental Duration -->
    <div class="bns-rental-field">
        <label for="bns_selected_duration">
            <strong><?php esc_html_e('2. Rental Duration', 'hookah-rental-core'); ?></strong> <span class="bns-required">*</span>
        </label>
        <select id="bns_selected_duration" name="bns_duration" class="bns-rental-select" required>
            <option value="24" selected><?php esc_html_e('24 Hours (Standard Day Session)', 'hookah-rental-core'); ?></option>
            <option value="48"><?php esc_html_e('48 Hours (Weekend / Multi-Day)', 'hookah-rental-core'); ?></option>
            <option value="72"><?php esc_html_e('72 Hours (VIP Weekend & Party)', 'hookah-rental-core'); ?></option>
        </select>
    </div>

    <!-- Flavour Selection -->
    <div class="bns-rental-field">
        <label>
            <strong><?php esc_html_e('3. Choose Included Flavours', 'hookah-rental-core'); ?></strong> <span class="bns-required">*</span>
        </label>
        <div class="bns-flavour-options">
            <label class="bns-checkbox-label">
                <input type="checkbox" name="bns_flavours[]" value="blueberry-mint" checked /> Blueberry Mint Ice (Al Fakher)
            </label>
            <label class="bns-checkbox-label">
                <input type="checkbox" name="bns_flavours[]" value="love-66" checked /> Love 66 Passionfruit Melon (Adalya)
            </label>
            <label class="bns-checkbox-label">
                <input type="checkbox" name="bns_flavours[]" value="double-apple" /> Double Apple Classic (Al Fakher)
            </label>
            <label class="bns-checkbox-label">
                <input type="checkbox" name="bns_flavours[]" value="paan-raas" /> Paan Raas King (Afzal)
            </label>
            <label class="bns-checkbox-label">
                <input type="checkbox" name="bns_flavours[]" value="watermelon-freeze" /> Watermelon Freeze (Starbuzz)
            </label>
        </div>
        <small class="bns-hint"><?php esc_html_e('Select up to package limit. Additional heads available upon request.', 'hookah-rental-core'); ?></small>
    </div>

    <!-- Delivery PIN Code -->
    <div class="bns-rental-field">
        <label for="bns_product_postal_code">
            <strong><?php esc_html_e('4. Delivery PIN Code', 'hookah-rental-core'); ?></strong> <span class="bns-required">*</span>
        </label>
        <input type="text" id="bns_product_postal_code" name="bns_postal_code" placeholder="e.g. 700091" maxlength="6" class="bns-rental-input" required />
    </div>

    <!-- Delivery Slot -->
    <div class="bns-rental-field">
        <label for="bns_product_delivery_slot">
            <strong><?php esc_html_e('5. Preferred Delivery Slot', 'hookah-rental-core'); ?></strong> <span class="bns-required">*</span>
        </label>
        <select id="bns_product_delivery_slot" name="bns_delivery_slot" class="bns-rental-select" required>
            <option value=""><?php esc_html_e('Select a delivery window...', 'hookah-rental-core'); ?></option>
            <option value="14:00-16:00">2:00 PM - 4:00 PM (Afternoon Express)</option>
            <option value="16:00-18:00">4:00 PM - 6:00 PM (Evening Sunset)</option>
            <option value="18:00-20:00" selected>6:00 PM - 8:00 PM (Prime Evening)</option>
            <option value="20:00-22:00">8:00 PM - 10:00 PM (Late Night Session)</option>
        </select>
    </div>

    <!-- Security Deposit Info Badge -->
    <div class="bns-security-badge">
        <span class="bns-shield-icon">🛡️</span>
        <span class="bns-badge-text"><?php esc_html_e('Refundable security deposit is held and released upon quality return inspection.', 'hookah-rental-core'); ?></span>
    </div>
</div>

