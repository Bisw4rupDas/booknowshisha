<?php
/**
 * Rental Booking Widget Template
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="bns-rental-widget" id="bns-rental-booking-widget">
    <div class="bns-widget-header">
        <h3><span class="bns-gold">SHISHA</span>RENT Availability Checker</h3>
        <p class="bns-widget-subtitle"><?php esc_html_e('Enter your delivery PIN code to check express delivery availability & slots.', 'hookah-rental-core'); ?></p>
    </div>

    <form class="bns-rental-form" id="bns-rental-form">
        <div class="bns-form-row">
            <div class="bns-rental-step">
                <label for="bns_postal_code"><?php esc_html_e('Delivery PIN Code (e.g. 700091, 700016, 700156)', 'hookah-rental-core'); ?></label>
                <div class="bns-input-group">
                    <input type="text" id="bns_postal_code" name="postal_code" placeholder="Enter 6-digit PIN" maxlength="6" class="bns-rental-input" required />
                    <button type="button" id="bns-check-availability-btn" class="bns-btn-gold">
                        <?php esc_html_e('Check Slots', 'hookah-rental-core'); ?>
                    </button>
                </div>
            </div>
        </div>

        <div id="bns-availability-result" class="bns-result-container" style="display: none;"></div>

        <div id="bns-slots-wrapper" class="bns-slots-wrapper" style="display: none;">
            <div class="bns-rental-step">
                <label for="bns_rental_start_date"><?php esc_html_e('Select Rental Date', 'hookah-rental-core'); ?></label>
                <input type="date" id="bns_rental_start_date" name="rental_date" class="bns-rental-input" min="<?php echo esc_attr(date('Y-m-d')); ?>" />
            </div>

            <div class="bns-rental-step">
                <label for="bns_selected_slot"><?php esc_html_e('Choose Delivery Window', 'hookah-rental-core'); ?></label>
                <select id="bns_selected_slot" name="delivery_slot" class="bns-rental-select">
                    <option value=""><?php esc_html_e('Select a slot...', 'hookah-rental-core'); ?></option>
                </select>
            </div>

            <div class="bns-action-row">
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="bns-btn-gold bns-btn-block">
                    <?php esc_html_e('Browse Hookahs & Packages →', 'hookah-rental-core'); ?>
                </a>
            </div>
        </div>
    </form>
</div>
