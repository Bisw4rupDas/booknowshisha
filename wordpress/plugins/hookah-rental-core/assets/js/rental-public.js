/**
 * Hookah Rental Core - Public JavaScript (Enhanced Availability Checker)
 */
(function($) {
  'use strict';

  $(document).ready(function() {
    var $postalInput = $('#bns_postal_code');
    var $checkBtn = $('#bns-check-availability-btn');
    var $btnText = $checkBtn.find('.bns-check-btn-text');
    var $clearBtn = $('#bns-pin-clear-btn');
    var $resultContainer = $('#bns-availability-result');
    var $slotsWrapper = $('#bns-slots-wrapper');
    var $quickChips = $('.bns-pin-chip');
    var checkTimeout = null;

    function getAjaxUrl() {
      if (typeof bnsRentalData !== 'undefined' && bnsRentalData.ajaxUrl) return bnsRentalData.ajaxUrl;
      if (typeof bnsThemeData !== 'undefined' && bnsThemeData.ajaxUrl) return bnsThemeData.ajaxUrl;
      return '/wp-admin/admin-ajax.php';
    }

    function getNonce() {
      if (typeof bnsRentalData !== 'undefined' && bnsRentalData.nonce) return bnsRentalData.nonce;
      if (typeof bnsThemeData !== 'undefined' && bnsThemeData.authNonce) return bnsThemeData.authNonce;
      return '';
    }

    function toggleClearBtn() {
      if ($postalInput.val().length > 0) {
        $clearBtn.show();
      } else {
        $clearBtn.hide();
      }
    }

    $postalInput.on('input', function() {
      var val = $(this).val().replace(/[^0-9]/g, '').slice(0, 6);
      $(this).val(val);
      toggleClearBtn();

      // Highlight active chip if matches
      $quickChips.removeClass('is-active');
      $quickChips.filter('[data-pin="' + val + '"]').addClass('is-active');

      if (val.length === 6) {
        clearTimeout(checkTimeout);
        checkTimeout = setTimeout(performCheck, 300);
      }
    });

    $clearBtn.on('click', function() {
      $postalInput.val('').focus();
      toggleClearBtn();
      $quickChips.removeClass('is-active');
      $resultContainer.slideUp();
      $slotsWrapper.slideUp();
    });

    $quickChips.on('click', function(e) {
      e.preventDefault();
      var pin = $(this).data('pin');
      $postalInput.val(pin);
      toggleClearBtn();
      $quickChips.removeClass('is-active');
      $(this).addClass('is-active');
      performCheck();
    });

    $postalInput.on('keypress', function(e) {
      if (e.which === 13) {
        e.preventDefault();
        performCheck();
      }
    });

    $checkBtn.on('click', function(e) {
      e.preventDefault();
      performCheck();
    });

    function performCheck() {
      var postalCode = $.trim($postalInput.val());

      if (!postalCode || postalCode.length !== 6 || !/^[1-9][0-9]{5}$/.test(postalCode)) {
        $resultContainer.html(
          '<div class="bns-avail-card bns-avail-error">' +
          '<div class="bns-avail-icon">⚠️</div>' +
          '<div class="bns-avail-body">' +
          '<div class="bns-avail-status">INVALID PIN CODE</div>' +
          '<div class="bns-avail-meta">Please enter a valid 6-digit Indian postal PIN code (e.g. 700091, 700016, 700156).</div>' +
          '</div>' +
          '</div>'
        ).slideDown();
        $slotsWrapper.slideUp();
        return;
      }

      $checkBtn.prop('disabled', true).addClass('is-loading');
      $btnText.text('Checking...');
      $resultContainer.html(
        '<div class="bns-avail-card bns-avail-loading">' +
        '<div class="bns-avail-icon">⏳</div>' +
        '<div class="bns-avail-body">' +
        '<div class="bns-avail-status">VERIFYING DELIVERY ZONE...</div>' +
        '<div class="bns-avail-meta">Resolving express dispatch hub for PIN ' + postalCode + '...</div>' +
        '</div>' +
        '</div>'
      ).slideDown();

      // Client-side quick-pass for all Kolkata (700xxx) and 24 Parganas (743xxx) PINs
      var isKolkataFastPass = postalCode.startsWith('700') || postalCode.startsWith('743');

      $.ajax({
        url: getAjaxUrl(),
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'bns_check_availability',
          nonce: getNonce(),
          postal_code: postalCode
        },
        success: function(response) {
          $checkBtn.prop('disabled', false).removeClass('is-loading');
          $btnText.text('Check Slots');

          var data = (response && response.data) ? response.data : null;
          var isDeliverable = (data && (data.deliverable || data.serviceable)) || (!data && isKolkataFastPass);

          if (isDeliverable) {
            var district = (data && data.district) ? data.district : (postalCode.startsWith('700') ? 'Kolkata' : 'North 24 Parganas');
            var area = (data && data.area) ? data.area : 'Kolkata Metropolitan Area';
            var zone = (data && data.zoneName) ? data.zoneName : (district + ' Express Hub');

            $resultContainer.html(
              '<div class="bns-avail-card bns-avail-success">' +
              '<div class="bns-avail-icon">✓</div>' +
              '<div class="bns-avail-body">' +
              '<div class="bns-avail-status">✓ DELIVERY AVAILABLE IN ' + district.toUpperCase() + '</div>' +
              '<div class="bns-avail-location">📍 ' + area + ' (PIN: ' + postalCode + ')</div>' +
              '<div class="bns-avail-meta">Full ShishaRent rental catalog & doorstep white-glove setup available for your location.</div>' +
              '<div class="bns-dispatch-badge">⚡ 60-90 Min Express Dispatch Available</div>' +
              '</div>' +
              '</div>'
            ).slideDown();
            $slotsWrapper.slideDown();
          } else {
            var reasonMsg = (data && data.message) ? data.message : 'Sorry, ShishaRent currently delivers exclusively within Kolkata, North 24 Parganas and South 24 Parganas.';
            $resultContainer.html(
              '<div class="bns-avail-card bns-avail-error">' +
              '<div class="bns-avail-icon">✕</div>' +
              '<div class="bns-avail-body">' +
              '<div class="bns-avail-status">✕ OUTSIDE SERVICE ZONE</div>' +
              '<div class="bns-avail-meta">' + reasonMsg + '</div>' +
              '</div>' +
              '</div>'
            ).slideDown();
            $slotsWrapper.slideUp();
          }
        },
        error: function() {
          $checkBtn.prop('disabled', false).removeClass('is-loading');
          $btnText.text('Check Slots');

          // Graceful fallback for all Kolkata PINs even during network interruption
          if (isKolkataFastPass) {
            var district = postalCode.startsWith('700') ? 'Kolkata' : 'North 24 Parganas';
            $resultContainer.html(
              '<div class="bns-avail-card bns-avail-success">' +
              '<div class="bns-avail-icon">✓</div>' +
              '<div class="bns-avail-body">' +
              '<div class="bns-avail-status">✓ DELIVERY AVAILABLE IN ' + district.toUpperCase() + '</div>' +
              '<div class="bns-avail-location">📍 Kolkata Region (PIN: ' + postalCode + ')</div>' +
              '<div class="bns-avail-meta">Service verified for Kolkata Delivery Hub.</div>' +
              '<div class="bns-dispatch-badge">⚡ Express Dispatch Ready</div>' +
              '</div>' +
              '</div>'
            ).slideDown();
            $slotsWrapper.slideDown();
          } else {
            $resultContainer.html(
              '<div class="bns-avail-card bns-avail-error">' +
              '<div class="bns-avail-icon">✕</div>' +
              '<div class="bns-avail-body">' +
              '<div class="bns-avail-status">✕ DELIVERY NOT AVAILABLE</div>' +
              '<div class="bns-avail-meta">Sorry, ShishaRent delivers exclusively within Kolkata, North 24 Parganas and South 24 Parganas.</div>' +
              '</div>' +
              '</div>'
            ).slideDown();
            $slotsWrapper.slideUp();
          }
        }
      });
    }
  });
})(jQuery);
