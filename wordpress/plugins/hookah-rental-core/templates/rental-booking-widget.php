<?php
/**
 * Rental Booking & PIN Availability Widget Template
 * Exclusively Kolkata / India - ShishaRent
 *
 * @package Hookah_Rental_Core
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="bns-rental-widget" id="bns-rental-booking-widget">
    <div class="bns-widget-header">
        <div class="bns-widget-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d4af37" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span><?php esc_html_e('EXPRESS KOLKATA NETWORK', 'shisharent'); ?></span>
        </div>
        <h3 class="bns-widget-title"><span class="bns-gold">SHISHA</span>RENT Availability Checker</h3>
        <p class="bns-widget-subtitle"><?php esc_html_e('Enter your Kolkata delivery PIN code to check 60-90 min express dispatch & reserved time slots.', 'shisharent'); ?></p>
    </div>

    <form class="bns-rental-form" id="bns-rental-form" onsubmit="return false;">
        <!-- Popular Kolkata Quick-Select Chips -->
        <div class="bns-quick-pins-wrap">
            <span class="bns-quick-pins-label"><?php esc_html_e('Popular Hubs:', 'shisharent'); ?></span>
            <div class="bns-quick-pins">
                <button type="button" class="bns-pin-chip" data-pin="700091">📍 Salt Lake (700091)</button>
                <button type="button" class="bns-pin-chip" data-pin="700016">📍 Park St (700016)</button>
                <button type="button" class="bns-pin-chip" data-pin="700019">📍 Ballygunge (700019)</button>
                <button type="button" class="bns-pin-chip" data-pin="700156">📍 New Town (700156)</button>
                <button type="button" class="bns-pin-chip" data-pin="700027">📍 Alipore (700027)</button>
            </div>
        </div>

        <div class="bns-form-row">
            <div class="bns-rental-step">
                <label for="bns_postal_code" class="bns-widget-input-label">
                    <?php esc_html_e('DELIVERY PIN CODE', 'shisharent'); ?>
                    <span class="bns-req">*</span>
                </label>
                <div class="bns-input-group">
                    <div class="bns-input-icon-wrap">
                        <svg class="bns-pin-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d4af37" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <input type="text" id="bns_postal_code" name="postal_code" placeholder="<?php esc_attr_e('Enter 6-digit PIN code (e.g. 700091)', 'shisharent'); ?>" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" class="bns-rental-input" autocomplete="postal-code" required />
                        <button type="button" id="bns-pin-clear-btn" class="bns-pin-clear" style="display:none;" aria-label="Clear PIN">✕</button>
                    </div>
                    <button type="button" id="bns-check-availability-btn" class="bns-btn-gold bns-btn-check">
                        <span class="bns-check-btn-text"><?php esc_html_e('Check Slots', 'shisharent'); ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Dynamic Live Availability Result Card -->
        <div id="bns-availability-result" class="bns-result-container" style="display: none;"></div>

        <!-- Delivery Slots & Action Section -->
        <div id="bns-slots-wrapper" class="bns-slots-wrapper" style="display: none;">
            <div class="bns-rental-slots-grid">
                <div class="bns-rental-step">
                    <label for="bns_rental_start_date"><?php esc_html_e('Select Rental Date', 'shisharent'); ?></label>
                    <input type="date" id="bns_rental_start_date" name="rental_date" class="bns-rental-input" min="<?php echo esc_attr(date('Y-m-d')); ?>" value="<?php echo esc_attr(date('Y-m-d')); ?>" />
                </div>

                <div class="bns-rental-step">
                    <label for="bns_selected_slot"><?php esc_html_e('Choose Delivery Window', 'shisharent'); ?></label>
                    <select id="bns_selected_slot" name="delivery_slot" class="bns-rental-select">
                        <option value=""><?php esc_html_e('Select a delivery window...', 'shisharent'); ?></option>
                        <option value="slot_12_15">12:00 PM – 03:00 PM (Afternoon Express)</option>
                        <option value="slot_15_18" selected>03:00 PM – 06:00 PM (Evening Prime)</option>
                        <option value="slot_18_21">06:00 PM – 09:00 PM (Night Party Express)</option>
                        <option value="slot_21_00">09:00 PM – 12:00 AM (Late Night VIP)</option>
                    </select>
                </div>
            </div>

            <div class="bns-action-row">
                <a href="#rentals" class="bns-btn-gold bns-btn-block bns-btn-browse-rentals">
                    <span><?php esc_html_e('Browse Hookahs & Packages →', 'shisharent'); ?></span>
                </a>
            </div>
        </div>
    </form>
</div>
