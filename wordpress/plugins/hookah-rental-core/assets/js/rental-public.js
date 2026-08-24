/**
 * Hookah Rental Core - Public JavaScript
 */
(function($) {
  'use strict';

  $(document).ready(function() {
    $('#bns-check-availability-btn').on('click', function(e) {
      e.preventDefault();
      var postalCode = $('#bns_postal_code').val().trim();
      var $resultContainer = $('#bns-availability-result');
      var $slotsWrapper = $('#bns-slots-wrapper');
      var $slotsSelect = $('#bns_selected_slot');

      if (!postalCode || postalCode.length !== 6) {
        $resultContainer
          .html('<div class="bns-alert bns-alert-error">⚠️ Please enter a valid 6-digit postal PIN code.</div>')
          .show();
        $slotsWrapper.hide();
        return;
      }

      $resultContainer
        .html('<div class="bns-alert bns-alert-info">⏳ Checking delivery network availability...</div>')
        .show();

      $.ajax({
        url: (typeof bnsRentalData !== 'undefined') ? bnsRentalData.ajaxUrl : '/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {
          action: 'bns_check_availability',
          nonce: (typeof bnsRentalData !== 'undefined') ? bnsRentalData.nonce : '',
          postal_code: postalCode
        },
        success: function(response) {
          if (response.success && response.data) {
            var data = response.data;
            if (data.serviceable || data.deliverable) {
              var districtName = data.district || 'Kolkata';
              $resultContainer.html(
                '<div class="bns-alert bns-alert-success" style="border-left:4px solid #10b981;background:rgba(16,185,129,0.1);padding:14px;border-radius:8px;">' +
                '<strong style="color:#10b981;font-size:0.95rem;">✓ DELIVERY AVAILABLE</strong><br>' +
                '<span style="font-weight:600;">' + districtName + ', West Bengal</span>' + (data.area ? ' (' + data.area + ')' : '') + '<br>' +
                '<span style="font-size:0.85rem;color:#64748b;">You can continue with your booking/order.</span>' +
                '</div>'
              ).show();

              // Populate slot options
              $slotsSelect.empty();
              $slotsSelect.append('<option value="">Select a delivery window...</option>');
              if (data.availableSlots && data.availableSlots.length > 0) {
                data.availableSlots.forEach(function(slot) {
                  $slotsSelect.append('<option value="' + slot.id + '">' + slot.timeWindow + '</option>');
                });
              }
              $slotsWrapper.slideDown();
            } else {
              $resultContainer.html(
                '<div class="bns-alert bns-alert-warning" style="border-left:4px solid #ef4444;background:rgba(239,68,68,0.1);padding:14px;border-radius:8px;">' +
                '<strong style="color:#ef4444;font-size:0.95rem;">✕ DELIVERY NOT AVAILABLE</strong><br>' +
                '<span style="font-size:0.85rem;color:#64748b;">Sorry, ShishaRent currently delivers only within Kolkata, North 24 Parganas and South 24 Parganas.</span>' +
                '</div>'
              ).show();
              $slotsWrapper.hide();
            }
          } else {
            $resultContainer.html(
              '<div class="bns-alert bns-alert-error" style="border-left:4px solid #ef4444;background:rgba(239,68,68,0.1);padding:14px;border-radius:8px;">' +
              '<strong style="color:#ef4444;">✕ DELIVERY NOT AVAILABLE</strong><br>' +
              '<span style="font-size:0.85rem;color:#64748b;">Sorry, ShishaRent currently delivers only within Kolkata, North 24 Parganas and South 24 Parganas.</span>' +
              '</div>'
            ).show();
            $slotsWrapper.hide();
          }
        },
        error: function() {
          $resultContainer.html(
            '<div class="bns-alert bns-alert-error">Network error communicating with the rental engine. Please try again.</div>'
          ).show();
          $slotsWrapper.hide();
        }
      });
    });
  });
})(jQuery);
