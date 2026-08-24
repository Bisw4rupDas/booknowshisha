/**
 * ShishaRent Theme Main JavaScript
 * Full interactive controls: Theme switcher (Light/Dark mode), live filter, carousel, PIN checker, contact form, modal, and accordion.
 *
 * @package ShishaRent
 */
(function($) {
  'use strict';

  // --------------------------------------------------------------------------
  // 0. Immediate Theme Mode Sync (Before & After DOM Ready)
  // --------------------------------------------------------------------------
  function getSavedTheme() {
    try {
      var saved = localStorage.getItem('shisharent_theme');
      if (saved === 'dark' || saved === 'light') {
        return saved;
      }
    } catch (e) {}
    return 'dark';
  }

  function applyTheme(theme, showToastNotification) {
    var isDark = (theme === 'dark');
    document.documentElement.setAttribute('data-theme', theme);
    try {
      localStorage.setItem('shisharent_theme', theme);
    } catch (e) {}

    // Synchronize all toggle switches on page
    $('.bns-theme-toggle, #bns-theme-toggle, #bns-theme-toggle-mobile').each(function() {
      $(this).attr('aria-checked', isDark ? 'true' : 'false');
    });

    if (showToastNotification && typeof window.showBnsToast === 'function') {
      if (isDark) {
        window.showBnsToast('Gothic Luxury Mode', 'Switched to dramatic dark lounge aesthetic.', 'theme');
      } else {
        window.showBnsToast('Modern Luxury Mode', 'Switched to refined daytime aesthetic.', 'theme');
      }
    }
  }

  // Apply immediately
  applyTheme(getSavedTheme(), false);

  // Delegated theme toggle click handler (works anywhere, anytime)
  $(document).on('click', '.bns-theme-toggle, #bns-theme-toggle, #bns-theme-toggle-mobile', function(e) {
    e.preventDefault();
    var current = document.documentElement.getAttribute('data-theme') || 'light';
    var nextTheme = (current === 'dark') ? 'light' : 'dark';
    applyTheme(nextTheme, true);
  });

  $(document).on('keydown', '.bns-theme-toggle, #bns-theme-toggle, #bns-theme-toggle-mobile', function(e) {
    if (e.which === 13 || e.which === 32) { // Enter or Space
      e.preventDefault();
      var current = document.documentElement.getAttribute('data-theme') || 'light';
      var nextTheme = (current === 'dark') ? 'light' : 'dark';
      applyTheme(nextTheme, true);
    }
  });

  $(document).ready(function() {

    // Ensure toggle UI is synchronized once DOM is ready
    applyTheme(getSavedTheme(), false);

    // ------------------------------------------------------------------------
    // 1. Refined Toast Notification Utility (Clean & Professional)
    // ------------------------------------------------------------------------
    function showToast(title, message, type) {
      type = type || 'info';
      var $toastContainer = $('#bns-toast-container');
      if (!$toastContainer.length) {
        $toastContainer = $('<div id="bns-toast-container" style="position:fixed;top:24px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:12px;pointer-events:none;"></div>');
        $('body').append($toastContainer);
      }

      var iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
      var borderColor = 'rgba(184,134,59,0.4)';
      var iconColor = '#b8863b';

      if (type === 'success') {
        iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        borderColor = 'rgba(16,185,129,0.5)';
        iconColor = '#10b981';
      } else if (type === 'warning') {
        iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
        borderColor = 'rgba(245,158,11,0.5)';
        iconColor = '#f59e0b';
      } else if (type === 'theme') {
        iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>';
        borderColor = 'rgba(212,169,95,0.6)';
        iconColor = '#d4a95f';
      }

      var isDark = $('html').attr('data-theme') === 'dark';
      var bgStyle = isDark ? 'background:rgba(12,16,27,0.96);color:#f8fafc;' : 'background:rgba(255,255,255,0.98);color:#111827;';
      var textMuted = isDark ? '#94a3b8' : '#4b5563';

      var $toast = $(
        '<div style="pointer-events:auto;min-width:300px;max-width:380px;' + bgStyle + 'border:1px solid ' + borderColor + ';box-shadow:0 12px 36px rgba(0,0,0,0.22);border-radius:10px;padding:14px 18px;font-family:Inter,sans-serif;font-size:0.86rem;display:flex;gap:14px;align-items:center;backdrop-filter:blur(12px);transform:translateX(100%);transition:all 0.3s cubic-bezier(0.16,1,0.3,1);">' +
          '<div style="color:' + iconColor + ';display:flex;align-items:center;">' + iconSvg + '</div>' +
          '<div style="flex:1;">' +
            '<div style="font-weight:600;letter-spacing:0.3px;margin-bottom:2px;">' + title + '</div>' +
            '<div style="color:' + textMuted + ';font-size:0.8rem;line-height:1.4;">' + message + '</div>' +
          '</div>' +
        '</div>'
      );

      $toastContainer.append($toast);
      setTimeout(function() {
        $toast.css('transform', 'translateX(0)');
      }, 10);

      setTimeout(function() {
        $toast.css({ 'transform': 'translateX(120%)', 'opacity': '0' });
        setTimeout(function() { $toast.remove(); }, 350);
      }, 3500);
    }
    window.showBnsToast = showToast;

    // ------------------------------------------------------------------------
    // 2. Header Scroll Effect
    // ------------------------------------------------------------------------
    var $header = $('#bns-site-header');
    $(window).on('scroll', function() {
      if ($(window).scrollTop() > 30) {
        $header.addClass('scrolled');
      } else {
        $header.removeClass('scrolled');
      }
    });

    // ------------------------------------------------------------------------
    // 3. Mobile Drawer Navigation
    // ------------------------------------------------------------------------
    var $drawer = $('#bns-mobile-drawer');
    var $drawerToggle = $('#bns-mobile-toggle');
    var $drawerClose = $('#bns-drawer-close');
    var $drawerBackdrop = $('#bns-drawer-backdrop');

    if ($drawerToggle.length) {
      $drawerToggle.on('click', function(e) {
        e.preventDefault();
        $drawer.addClass('open');
        $('body').css('overflow', 'hidden');
      });
    }

    function closeDrawer() {
      $drawer.removeClass('open');
      $('body').css('overflow', '');
    }

    if ($drawerClose.length) $drawerClose.on('click', closeDrawer);
    if ($drawerBackdrop.length) $drawerBackdrop.on('click', closeDrawer);
    $('.bns-mobile-link').on('click', closeDrawer);

    // ------------------------------------------------------------------------
    // 4. Hero Hookah Centerpiece (Brand Logo is static and persistent)
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // 5. Featured Catalog Category Tabs
    // ------------------------------------------------------------------------
    var $tabBtns = $('.bns-tab-btn');
    var $productCards = $('.bns-product-card');

    $tabBtns.on('click', function(e) {
      e.preventDefault();
      var selectedCategory = $(this).data('tab');

      $tabBtns.removeClass('active');
      $(this).addClass('active');

      if (selectedCategory === 'all') {
        $productCards.stop().fadeIn(250);
      } else {
        $productCards.each(function() {
          var cardCats = $(this).data('category') || '';
          if (cardCats.indexOf(selectedCategory) !== -1) {
            $(this).stop().fadeIn(250);
          } else {
            $(this).stop().fadeOut(150);
          }
        });
      }
    });

    // ------------------------------------------------------------------------
    // 6. Featured Catalog Pagination Controls
    // ------------------------------------------------------------------------
    $('.bns-page-num').on('click', function() {
      $('.bns-page-num').removeClass('active');
      $(this).addClass('active');
      var pageNumber = $(this).text();

      $('html, body').animate({
        scrollTop: $('#catalog').offset().top - 70
      }, 400);
    });

    $('#bns-prev-page').on('click', function() {
      var $current = $('.bns-page-num.active');
      var $prev = $current.prev('.bns-page-num');
      if ($prev.length) {
        $prev.trigger('click');
      }
    });

    $('#bns-next-page').on('click', function() {
      var $current = $('.bns-page-num.active');
      var $next = $current.next('.bns-page-num');
      if ($next.length) {
        $next.trigger('click');
      }
    });

    // ------------------------------------------------------------------------
    // 7. Interactive Flavour Selection Pill Cards
    // ------------------------------------------------------------------------
    $('.bns-flavour-pill-card').on('click', function() {
      $(this).toggleClass('selected');
      var flavourName = $(this).find('h4').text() || 'Flavour';
      if ($(this).hasClass('selected')) {
        $(this).css('border-color', 'var(--bns-accent-gold)');
        showToast('Flavour Selected', flavourName + ' added to your session mix.', 'success');
      } else {
        $(this).css('border-color', '');
        showToast('Flavour Removed', flavourName + ' unselected.', 'info');
      }
    });

    // ------------------------------------------------------------------------
    // 8. Delivery PIN Availability Checker (Strict 3-District Authority)
    // ------------------------------------------------------------------------
    function executePinCheck(pin) {
      pin = (pin || '').toString().trim();
      if (!pin || pin.length !== 6 || !/^\d{6}$/.test(pin)) {
        showToast('Invalid PIN', 'Please enter a valid 6-digit PIN code (e.g. 700019, 700091, 700027).', 'warning');
        $('#bns-pin-result').html(
          '<div style="margin-top:14px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:12px;color:#ef4444;font-size:0.85rem;display:flex;align-items:center;gap:8px;">' +
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>' +
            '<span>Please enter a valid 6-digit numeric PIN code.</span>' +
          '</div>'
        ).slideDown(200);
        return;
      }

      var $btn = $('#bns-pin-submit-btn, .bns-btn-check-pin');
      var origText = $btn.text();
      $btn.text('Checking...').prop('disabled', true);

      // Perform request to NestJS backend check-zone
      $.ajax({
        url: 'http://localhost:3000/api/delivery/check-zone',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ postalCode: pin }),
        success: function(response) {
          $btn.text(origText).prop('disabled', false);
          if (response && response.deliverable) {
            var district = response.district || 'Kolkata';
            showToast(
              'Delivery Available',
              district + ', West Bengal. You can proceed with your order.',
              'success'
            );
            $('#bns-pin-result').html(
              '<div style="margin-top:14px;background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:14px;color:#10b981;font-size:0.88rem;display:flex;align-items:flex-start;gap:12px;">' +
                '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>' +
                '<div>' +
                  '<strong style="font-size:0.92rem;color:#10b981;letter-spacing:0.5px;">DELIVERY AVAILABLE</strong><br>' +
                  '<span style="color:var(--bns-text-primary);font-weight:600;">' + district + ', West Bengal</span>' + (response.area ? ' (' + response.area + ')' : '') + '<br>' +
                  '<span style="color:var(--bns-text-muted);font-size:0.8rem;">Ready for immediate white-glove dispatch.</span>' +
                '</div>' +
              '</div>'
            ).slideDown(200);
          } else {
            var errorMsg = (response && response.message) ? response.message : 'ShishaRent delivers strictly within Kolkata, North 24 Parganas and South 24 Parganas.';
            showToast('Non-Serviceable Area', errorMsg, 'warning');
            $('#bns-pin-result').html(
              '<div style="margin-top:14px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:14px;color:#ef4444;font-size:0.88rem;display:flex;align-items:flex-start;gap:12px;">' +
                '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                '<div>' +
                  '<strong style="font-size:0.92rem;color:#ef4444;letter-spacing:0.5px;">DELIVERY NOT AVAILABLE</strong><br>' +
                  '<span style="color:var(--bns-text-muted);font-size:0.82rem;">ShishaRent delivers strictly within Kolkata, North 24 Parganas, and South 24 Parganas.</span>' +
                '</div>' +
              '</div>'
            ).slideDown(200);
          }
        },
        error: function() {
          $btn.text(origText).prop('disabled', false);
          // Strict offline fallback adhering to 3-district rule
          var kolkataPins = ['700001','700002','700003','700004','700005','700006','700007','700008','700009','700010','700011','700012','700013','700014','700015','700016','700017','700018','700019','700020','700021','700022','700023','700024','700025','700026','700029','700031','700033','700037','700039','700040','700045','700046','700047','700054','700062','700067','700068','700069','700071','700072','700073','700076','700077','700082','700085','700087','700092','700095','700105'];
          var north24Pins = ['700028','700030','700035','700036','700048','700049','700050','700051','700052','700055','700056','700057','700058','700059','700064','700065','700074','700079','700080','700081','700083','700089','700090','700091','700097','700098','700101','700102','700106','700108','700109','700110','700111','700112','700113','700114','700115','700116','700117','700118','700119','700120','700121','700122','700123','700124','700125','700126','700127','700128','700129','700130','700131','700132','700133','700134','700135','700136','700156','700157','700158','700159','700160','700161','700162','743122','743125','743126','743144','743145','743165','743166','743221','743232','743234','743235','743245','743248','743263','743401','743411','743412','743422','743424','743427','743456'];
          var south24Pins = ['700027','700032','700034','700038','700041','700042','700043','700044','700053','700060','700061','700063','700070','700075','700078','700084','700086','700088','700093','700094','700096','700099','700100','700103','700104','700107','700137','700138','700139','700140','700141','700142','700143','700144','700145','700146','700147','700148','700149','700150','700151','700152','700153','700154','700155','743302','743312','743318','743329','743331','743337','743347','743355','743372','743387'];

          var district = null;
          if (kolkataPins.indexOf(pin) !== -1) district = 'Kolkata';
          else if (north24Pins.indexOf(pin) !== -1) district = 'North 24 Parganas';
          else if (south24Pins.indexOf(pin) !== -1) district = 'South 24 Parganas';

          if (district) {
            showToast('Delivery Available', district + ', West Bengal. You can proceed with your order.', 'success');
            $('#bns-pin-result').html(
              '<div style="margin-top:14px;background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:14px;color:#10b981;font-size:0.88rem;display:flex;align-items:flex-start;gap:12px;">' +
                '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>' +
                '<div>' +
                  '<strong style="font-size:0.92rem;color:#10b981;letter-spacing:0.5px;">DELIVERY AVAILABLE</strong><br>' +
                  '<span style="color:var(--bns-text-primary);font-weight:600;">' + district + ', West Bengal</span><br>' +
                  '<span style="color:var(--bns-text-muted);font-size:0.8rem;">Ready for immediate white-glove dispatch.</span>' +
                '</div>' +
              '</div>'
            ).slideDown(200);
          } else {
            showToast('Non-Serviceable Area', 'ShishaRent delivers strictly within Kolkata, North 24 Parganas and South 24 Parganas.', 'warning');
            $('#bns-pin-result').html(
              '<div style="margin-top:14px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:14px;color:#ef4444;font-size:0.88rem;display:flex;align-items:flex-start;gap:12px;">' +
                '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                '<div>' +
                  '<strong style="font-size:0.92rem;color:#ef4444;letter-spacing:0.5px;">DELIVERY NOT AVAILABLE</strong><br>' +
                  '<span style="color:var(--bns-text-muted);font-size:0.82rem;">ShishaRent delivers strictly within Kolkata, North 24 Parganas, and South 24 Parganas.</span>' +
                '</div>' +
              '</div>'
            ).slideDown(200);
          }
        }
      });
    }

    $('#bns-pin-submit-btn, .bns-btn-check-pin').on('click', function(e) {
      e.preventDefault();
      var pin = $('#bns-pin-input').val() || $('.bns-pin-input').val();
      executePinCheck(pin);
    });

    $('#bns-pin-input, .bns-pin-input').on('keypress', function(e) {
      if (e.which === 13) {
        e.preventDefault();
        executePinCheck($(this).val());
      }
    });

    // Quick PIN Pill Buttons
    $('.bns-zone-tag').on('click', function() {
      var pinText = $(this).text();
      var match = pinText.match(/\b\d{6}\b/);
      if (match && match[0]) {
        $('#bns-pin-input, .bns-pin-input').val(match[0]);
        executePinCheck(match[0]);
      }
    });

    // ------------------------------------------------------------------------
    // 9. Interactive Contact Form Submission Handler
    // ------------------------------------------------------------------------
    $('#bns-contact-form').on('submit', function(e) {
      e.preventDefault();
      var name = $('#contact-name').val();
      var phone = $('#contact-phone').val();
      var area = $('#contact-area').val() || 'Kolkata';
      var service = $('#contact-service').val();
      var message = $('#contact-message').val();

      var formattedText = 'Hi ShishaRent Kolkata,\n\n' +
        'Name: ' + name + '\n' +
        'Phone: ' + phone + '\n' +
        'Location: ' + area + '\n' +
        'Service: ' + service + '\n' +
        'Message: ' + message;

      var waUrl = 'https://wa.me/919903556825?text=' + encodeURIComponent(formattedText);

      // Open WhatsApp chat in new window
      window.open(waUrl, '_blank');

      $('#bns-contact-feedback').html(
        '<div style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:12px;color:#10b981;font-size:0.88rem;display:flex;align-items:center;gap:10px;">' +
          '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>' +
          '<span>Thank you ' + name + '! Your inquiry has been forwarded to our WhatsApp concierge. Our team will reply shortly.</span>' +
        '</div>'
      ).slideDown(250);

      showToast('Inquiry Sent', 'WhatsApp conversation initiated with ShishaRent concierge.', 'success');
    });

    // ------------------------------------------------------------------------
    // 10. FAQ Accordion
    // ------------------------------------------------------------------------
    $('.bns-faq-question').on('click', function() {
      var $parentItem = $(this).closest('.bns-faq-item');
      var isActive = $parentItem.hasClass('active');

      $('.bns-faq-item').removeClass('active');
      if (!isActive) {
        $parentItem.addClass('active');
      }
    });

    // ------------------------------------------------------------------------
    // 11. Smooth Scrolling for Internal Anchors
    // ------------------------------------------------------------------------
    $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').on('click', function(e) {
      if (
        location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') &&
        location.hostname === this.hostname
      ) {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if (target.length) {
          e.preventDefault();
          $('html, body').animate({
            scrollTop: target.offset().top - 70
          }, 600);
        }
      }
    });

    // ------------------------------------------------------------------------
    // 12. Customer Mobile OTP Authentication System (Kolkata & India Only)
    // ------------------------------------------------------------------------
    var $authModal = $('#bns-auth-modal');
    var otpTimerInterval = null;
    var inpageTimerInterval = null;
    var currentOtpPhone = '';
    var inpageOtpPhone = '';

    // Open Auth Modal from ANY customer login trigger when logged out
    $(document).on('click', '#bns-account-trigger:not(.is-logged-in), .bns-account-trigger:not(.is-logged-in), .bns-btn-account-login, .bns-btn-mobile-login, .bns-open-auth-btn, .bns-trigger-otp-login, .woocommerce-info a.showlogin, a.showlogin', function(e) {
      // If on the /my-account/ in-page form itself, do not open modal, focus the in-page input
      if ($('#bns-inpage-otp-phone-input').length && $('#bns-inpage-otp-phone-input').is(':visible')) {
        $('#bns-inpage-otp-phone-input').focus();
        return;
      }

      e.preventDefault();
      if ($drawer.length) {
        $drawer.removeClass('open');
        $('body').css('overflow', '');
      }
      $('.bns-account-dropdown-menu').removeClass('is-open');
      resetOtpModal();
      $authModal.css('display', 'flex').hide().fadeIn(200);
      setTimeout(function() {
        $('#bns-otp-phone-input').focus();
      }, 50);
    });

    // Close Auth Modal
    $(document).on('click', '#bns-auth-close, #bns-auth-backdrop', function() {
      $authModal.fadeOut(150);
      clearInterval(otpTimerInterval);
    });

    // Close on Escape key
    $(document).on('keydown', function(e) {
      if ((e.key === 'Escape' || e.keyCode === 27) && $authModal.is(':visible')) {
        $authModal.fadeOut(150);
        clearInterval(otpTimerInterval);
      }
    });

    function resetOtpModal() {
      $('#bns-otp-alert').hide().text('').removeClass('bns-alert-error bns-alert-success');
      $('#bns-otp-phone-form').show();
      $('#bns-otp-verify-form').hide();
      $('#bns-btn-send-otp').prop('disabled', false).find('span').text('SEND OTP →');
      $('#bns-btn-verify-otp').prop('disabled', false).find('span').text('VERIFY & SIGN IN →');
      $('#bns-otp-code-input').val('');
      clearInterval(otpTimerInterval);
    }

    function showOtpAlert(msg, type, isPage) {
      var $alert = isPage ? $('#bns-inpage-otp-alert') : $('#bns-otp-alert');
      $alert.removeClass('bns-alert-error bns-alert-success')
        .addClass(type === 'success' ? 'bns-alert-success' : 'bns-alert-error')
        .html(msg).fadeIn(200);
    }

    function sanitizeIndianPhone(raw) {
      var clean = (raw || '').replace(/[^0-9]/g, '');
      if (clean.length === 12 && clean.indexOf('91') === 0) {
        clean = clean.substring(2);
      } else if (clean.length === 11 && clean.indexOf('0') === 0) {
        clean = clean.substring(1);
      }
      return clean;
    }

    // =========================================================================
    // MODAL OTP LOGIC
    // =========================================================================

    // Modal Step 1: Send OTP
    $('#bns-otp-phone-form').on('submit', function(e) {
      e.preventDefault();
      var cleanPhone = sanitizeIndianPhone($('#bns-otp-phone-input').val());

      if (cleanPhone.length !== 10 || !['6', '7', '8', '9'].includes(cleanPhone.charAt(0))) {
        showOtpAlert('Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.', 'error', false);
        $('#bns-otp-phone-input').focus();
        return;
      }

      var $btn = $('#bns-btn-send-otp');
      $btn.prop('disabled', true).find('span').text('SENDING OTP...');
      $('#bns-otp-alert').hide();

      $.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {
          action: 'bns_send_otp',
          phone: cleanPhone,
        },
        success: function(res) {
          $btn.prop('disabled', false).find('span').text('SEND OTP →');
          if (res && res.success) {
            currentOtpPhone = cleanPhone;
            $('#bns-otp-sent-number').text(res.data.masked_phone || ('+91 ' + cleanPhone));
            $('#bns-otp-phone-form').hide();
            $('#bns-otp-verify-form').fadeIn(200);
            $('#bns-otp-code-input').focus();
            startResendTimer(res.data.cooldown_seconds || 30, false);
            showToast('OTP Dispatched', res.data.message || 'Verification code sent to your mobile.', 'success');
          } else {
            var errMsg = (res && res.data && res.data.message) ? res.data.message : 'Failed to send OTP. Please try again.';
            showOtpAlert(errMsg, 'error', false);
          }
        },
        error: function() {
          $btn.prop('disabled', false).find('span').text('SEND OTP →');
          showOtpAlert('Network error while requesting OTP. Please check your connection.', 'error', false);
        }
      });
    });

    // Modal Step 2: Verify OTP
    $('#bns-otp-verify-form').on('submit', function(e) {
      e.preventDefault();
      var code = $('#bns-otp-code-input').val().trim().replace(/[^0-9]/g, '');

      if (code.length !== 6) {
        showOtpAlert('Please enter the full 6-digit verification code.', 'error', false);
        $('#bns-otp-code-input').focus();
        return;
      }

      var $btn = $('#bns-btn-verify-otp');
      $btn.prop('disabled', true).find('span').text('VERIFYING...');
      $('#bns-otp-alert').hide();

      $.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {
          action: 'bns_verify_otp',
          phone: currentOtpPhone,
          otp: code,
          redirect: window.location.href,
        },
        success: function(res) {
          if (res && res.success) {
            $btn.find('span').text('SIGNING IN...');
            showToast('Authentication Successful', res.data.message || 'Welcome to BookMySmoke!', 'success');
            setTimeout(function() {
              if (res.data.redirect) {
                window.location.href = res.data.redirect;
              } else {
                location.reload();
              }
            }, 600);
          } else {
            $btn.prop('disabled', false).find('span').text('VERIFY & SIGN IN →');
            var errMsg = (res && res.data && res.data.message) ? res.data.message : 'Invalid OTP code. Please try again.';
            showOtpAlert(errMsg, 'error', false);
            $('#bns-otp-code-input').focus();
          }
        },
        error: function() {
          $btn.prop('disabled', false).find('span').text('VERIFY & SIGN IN →');
          showOtpAlert('Network error during verification. Please try again.', 'error', false);
        }
      });
    });

    // Modal Resend OTP Action
    $('#bns-btn-resend-otp').on('click', function(e) {
      e.preventDefault();
      var $btn = $(this);
      $btn.prop('disabled', true).text('Sending...');
      $('#bns-otp-alert').hide();

      $.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {
          action: 'bns_send_otp',
          phone: currentOtpPhone,
        },
        success: function(res) {
          $btn.prop('disabled', false).text('Resend OTP');
          if (res && res.success) {
            startResendTimer(res.data.cooldown_seconds || 30, false);
            showToast('OTP Resent', res.data.message || 'New OTP sent to your mobile.', 'info');
          } else {
            var errMsg = (res && res.data && res.data.message) ? res.data.message : 'Could not resend OTP. Please wait before retrying.';
            showOtpAlert(errMsg, 'error', false);
          }
        },
        error: function() {
          $btn.prop('disabled', false).text('Resend OTP');
          showOtpAlert('Network error while resending OTP.', 'error', false);
        }
      });
    });

    // Modal Switch back to edit phone
    $('#bns-otp-edit-phone-btn').on('click', function(e) {
      e.preventDefault();
      clearInterval(otpTimerInterval);
      $('#bns-otp-verify-form').hide();
      $('#bns-otp-phone-form').fadeIn(200);
      $('#bns-otp-phone-input').focus();
    });

    // =========================================================================
    // IN-PAGE / MY-ACCOUNT OTP LOGIC
    // =========================================================================

    // In-Page Step 1: Send OTP
    $('#bns-inpage-otp-phone-form').on('submit', function(e) {
      e.preventDefault();
      var cleanPhone = sanitizeIndianPhone($('#bns-inpage-otp-phone-input').val());

      if (cleanPhone.length !== 10 || !['6', '7', '8', '9'].includes(cleanPhone.charAt(0))) {
        showOtpAlert('Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.', 'error', true);
        $('#bns-inpage-otp-phone-input').focus();
        return;
      }

      var $btn = $('#bns-inpage-btn-send-otp');
      $btn.prop('disabled', true).find('span').text('SENDING OTP...');
      $('#bns-inpage-otp-alert').hide();

      $.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {
          action: 'bns_send_otp',
          phone: cleanPhone,
        },
        success: function(res) {
          $btn.prop('disabled', false).find('span').text('SEND OTP →');
          if (res && res.success) {
            inpageOtpPhone = cleanPhone;
            $('#bns-inpage-otp-sent-number').text(res.data.masked_phone || ('+91 ' + cleanPhone));
            $('#bns-inpage-otp-phone-form').hide();
            $('#bns-inpage-otp-verify-form').fadeIn(200);
            $('#bns-inpage-otp-code-input').focus();
            startResendTimer(res.data.cooldown_seconds || 30, true);
            showToast('OTP Dispatched', res.data.message || 'Verification code sent to your mobile.', 'success');
          } else {
            var errMsg = (res && res.data && res.data.message) ? res.data.message : 'Failed to send OTP. Please try again.';
            showOtpAlert(errMsg, 'error', true);
          }
        },
        error: function() {
          $btn.prop('disabled', false).find('span').text('SEND OTP →');
          showOtpAlert('Network error while requesting OTP. Please check your connection.', 'error', true);
        }
      });
    });

    // In-Page Step 2: Verify OTP
    $('#bns-inpage-otp-verify-form').on('submit', function(e) {
      e.preventDefault();
      var code = $('#bns-inpage-otp-code-input').val().trim().replace(/[^0-9]/g, '');

      if (code.length !== 6) {
        showOtpAlert('Please enter the full 6-digit verification code.', 'error', true);
        $('#bns-inpage-otp-code-input').focus();
        return;
      }

      var $btn = $('#bns-inpage-btn-verify-otp');
      $btn.prop('disabled', true).find('span').text('VERIFYING...');
      $('#bns-inpage-otp-alert').hide();

      $.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {
          action: 'bns_verify_otp',
          phone: inpageOtpPhone,
          otp: code,
          redirect: window.location.href,
        },
        success: function(res) {
          if (res && res.success) {
            $btn.find('span').text('SIGNING IN...');
            showToast('Authentication Successful', res.data.message || 'Welcome to BookMySmoke!', 'success');
            setTimeout(function() {
              if (res.data.redirect) {
                window.location.href = res.data.redirect;
              } else {
                location.reload();
              }
            }, 600);
          } else {
            $btn.prop('disabled', false).find('span').text('VERIFY & SIGN IN →');
            var errMsg = (res && res.data && res.data.message) ? res.data.message : 'Invalid OTP code. Please try again.';
            showOtpAlert(errMsg, 'error', true);
            $('#bns-inpage-otp-code-input').focus();
          }
        },
        error: function() {
          $btn.prop('disabled', false).find('span').text('VERIFY & SIGN IN →');
          showOtpAlert('Network error during verification. Please try again.', 'error', true);
        }
      });
    });

    // In-Page Resend OTP Action
    $('#bns-inpage-btn-resend-otp').on('click', function(e) {
      e.preventDefault();
      var $btn = $(this);
      $btn.prop('disabled', true).text('Sending...');
      $('#bns-inpage-otp-alert').hide();

      $.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {
          action: 'bns_send_otp',
          phone: inpageOtpPhone,
        },
        success: function(res) {
          $btn.prop('disabled', false).text('Resend OTP');
          if (res && res.success) {
            startResendTimer(res.data.cooldown_seconds || 30, true);
            showToast('OTP Resent', res.data.message || 'New OTP sent to your mobile.', 'info');
          } else {
            var errMsg = (res && res.data && res.data.message) ? res.data.message : 'Could not resend OTP. Please wait before retrying.';
            showOtpAlert(errMsg, 'error', true);
          }
        },
        error: function() {
          $btn.prop('disabled', false).text('Resend OTP');
          showOtpAlert('Network error while resending OTP.', 'error', true);
        }
      });
    });

    // In-Page Switch back to edit phone
    $('#bns-inpage-otp-edit-phone-btn').on('click', function(e) {
      e.preventDefault();
      clearInterval(inpageTimerInterval);
      $('#bns-inpage-otp-verify-form').hide();
      $('#bns-inpage-otp-phone-form').fadeIn(200);
      $('#bns-inpage-otp-phone-input').focus();
    });

    // Common Resend Timer Function
    function startResendTimer(seconds, isPage) {
      if (isPage) {
        clearInterval(inpageTimerInterval);
        var pTimeLeft = seconds;
        var $pTimer = $('#bns-inpage-otp-timer-text');
        var $pResend = $('#bns-inpage-btn-resend-otp');
        $pTimer.show().text('Resend OTP in ' + pTimeLeft + 's');
        $pResend.hide();
        inpageTimerInterval = setInterval(function() {
          pTimeLeft--;
          if (pTimeLeft <= 0) {
            clearInterval(inpageTimerInterval);
            $pTimer.hide();
            $pResend.fadeIn(150);
          } else {
            $pTimer.text('Resend OTP in ' + pTimeLeft + 's');
          }
        }, 1000);
      } else {
        clearInterval(otpTimerInterval);
        var timeLeft = seconds;
        var $timerText = $('#bns-otp-timer-text');
        var $resendBtn = $('#bns-btn-resend-otp');
        $timerText.show().text('Resend OTP in ' + timeLeft + 's');
        $resendBtn.hide();
        otpTimerInterval = setInterval(function() {
          timeLeft--;
          if (timeLeft <= 0) {
            clearInterval(otpTimerInterval);
            $timerText.hide();
            $resendBtn.fadeIn(150);
          } else {
            $timerText.text('Resend OTP in ' + timeLeft + 's');
          }
        }, 1000);
      }
    }



    // ------------------------------------------------------------------------
    // 10. Customer-Facing Gallery: Interactive Filter Tabs & Full-Screen Lightbox
    // ------------------------------------------------------------------------
    var $galleryGrid = $('#bns-main-gallery-grid');
    var $lightbox = $('#bns-gallery-lightbox');

    if ($galleryGrid.length && $lightbox.length) {
      var $items = $galleryGrid.find('.bns-gallery-item');
      var $lbImg = $('#bns-lightbox-img');
      var $lbCurrent = $('#bns-lb-current');
      var $lbTotal = $('#bns-lb-total');
      var $lbTitle = $('#bns-lightbox-title');
      var $lbCategory = $('#bns-lightbox-category');
      var $lbContainer = $('#bns-lightbox-img-container');

      var currentFilter = 'all';
      var activeItems = [];
      var currentIndex = 0;

      function updateActiveItems() {
        activeItems = [];
        $items.each(function() {
          var $item = $(this);
          var itemCat = $item.data('category');
          if (currentFilter === 'all' || itemCat === currentFilter) {
            $item.removeClass('bns-item-hidden').css('opacity', '1').css('transform', 'scale(1)');
            activeItems.push($item);
          } else {
            $item.addClass('bns-item-hidden').css('opacity', '0').css('transform', 'scale(0.95)');
          }
        });
        $lbTotal.text(activeItems.length);
      }

      updateActiveItems();

      // Category tab click
      $('.bns-filter-tab').on('click', function(e) {
        e.preventDefault();
        var $tab = $(this);
        $('.bns-filter-tab').removeClass('active').attr('aria-selected', 'false');
        $tab.addClass('active').attr('aria-selected', 'true');
        currentFilter = $tab.data('category');
        updateActiveItems();
      });

      function showLightboxImage(idx) {
        if (!activeItems.length) return;
        if (idx < 0) idx = activeItems.length - 1;
        if (idx >= activeItems.length) idx = 0;
        currentIndex = idx;

        var $activeItem = activeItems[currentIndex];
        var fullSrc = $activeItem.data('full-src') || $activeItem.data('large-src');
        var title = $activeItem.data('title') || '';
        var alt = $activeItem.data('alt') || '';
        var catName = $activeItem.data('cat-name') || '';

        $lbCurrent.text(currentIndex + 1);
        $lbTotal.text(activeItems.length);
        $lbTitle.text(title);
        $lbCategory.text(catName);

        $lbContainer.addClass('is-loading');
        $lbImg.addClass('loading');

        var preloader = new Image();
        preloader.onload = function() {
          $lbImg.attr('src', fullSrc).attr('alt', alt);
          $lbImg.removeClass('loading');
          $lbContainer.removeClass('is-loading');
        };
        preloader.onerror = function() {
          $lbImg.attr('src', $activeItem.data('large-src')).attr('alt', alt);
          $lbImg.removeClass('loading');
          $lbContainer.removeClass('is-loading');
        };
        preloader.src = fullSrc;
      }

      function openLightbox(itemElement) {
        var clickedItem = $(itemElement).closest('.bns-gallery-item');
        var foundIdx = activeItems.findIndex(function($it) {
          return $it[0] === clickedItem[0];
        });

        if (foundIdx === -1) foundIdx = 0;
        showLightboxImage(foundIdx);

        $('body').addClass('bns-lightbox-open');
        $lightbox.css('display', 'flex').hide().fadeIn(250, function() {
          $lightbox.addClass('active');
        });
      }

      function closeLightbox() {
        $lightbox.removeClass('active').fadeOut(200, function() {
          $(this).css('display', 'none');
          $('body').removeClass('bns-lightbox-open');
        });
      }

      function prevLightboxImage() {
        showLightboxImage(currentIndex - 1);
      }

      function nextLightboxImage() {
        showLightboxImage(currentIndex + 1);
      }

      // Card click opens lightbox
      $galleryGrid.on('click', '.bns-gallery-item', function(e) {
        e.preventDefault();
        openLightbox(this);
      });

      // Controls
      $('#bns-lightbox-close, #bns-lightbox-backdrop').on('click', function(e) {
        if (e.target === this || $(this).is('#bns-lightbox-close')) {
          closeLightbox();
        }
      });

      $('#bns-lightbox-prev').on('click', function(e) {
        e.stopPropagation();
        prevLightboxImage();
      });

      $('#bns-lightbox-next').on('click', function(e) {
        e.stopPropagation();
        nextLightboxImage();
      });

      // Keyboard Navigation
      $(document).on('keydown', function(e) {
        if (!$lightbox.is(':visible')) return;
        if (e.key === 'Escape' || e.keyCode === 27) {
          closeLightbox();
        } else if (e.key === 'ArrowLeft' || e.keyCode === 37) {
          prevLightboxImage();
        } else if (e.key === 'ArrowRight' || e.keyCode === 39) {
          nextLightboxImage();
        }
      });

      // Touch Swipe Gestures for Mobile
      var touchStartX = 0;
      var touchEndX = 0;
      var touchStartY = 0;
      var touchEndY = 0;

      $lightbox.on('touchstart', function(e) {
        var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
        touchStartX = touch.pageX;
        touchStartY = touch.pageY;
      });

      $lightbox.on('touchend', function(e) {
        var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
        touchEndX = touch.pageX;
        touchEndY = touch.pageY;
        handleSwipeGesture();
      });

      function handleSwipeGesture() {
        var deltaX = touchEndX - touchStartX;
        var deltaY = touchEndY - touchStartY;
        // Check if horizontal swipe exceeds vertical swipe and exceeds threshold
        if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 45) {
          if (deltaX > 0) {
            prevLightboxImage(); // Swipe right -> previous
          } else {
            nextLightboxImage(); // Swipe left -> next
          }
        }
      }
    }

        // ------------------------------------------------------------------------
    // 11. Two-Column Luxury Rental & Flavour Configurator Logic
    // ------------------------------------------------------------------------
    var flavoursData = window.bnsFlavoursData || [];
    var rentalCatalog = window.bnsRentalCatalog || {};
    var $chipsGrid = $('#bns-cfg-chips-grid');
    
    if ($chipsGrid.length && flavoursData.length) {
      var activeFlv = flavoursData[0];
      var currentRental = window.bnsInitialRental || 'SR SPECIAL HOOKAH';
      var currentRentalPkg = window.bnsRentalPackage || (rentalCatalog[currentRental] || null);
      var currentRentalPrice = parseFloat(currentRentalPkg ? currentRentalPkg.price : 1499);
      var isHookah = (typeof window.bnsIsHookah !== 'undefined') ? window.bnsIsHookah : true;
      var selectedChillum = 'Classic Clay';
      var selectedChillumPrice = 0;
      var baseEnabled = isHookah;
      var selectedBase = isHookah ? 'standard' : 'none';
      var selectedBasePrice = 0;
      var selectedBaseLabel = isHookah ? 'Standard Base' : 'No Base (Chilam Only)';
      var currentQty = 1;
      var activeView = 'rental'; // 'rental' or 'flavour'

      function updateActiveFlavourDisplay(flv) {
        if (!flv) return;
        activeFlv = flv;

        // 1. Update flavour image with smooth fade
        var $mainImg = $('#bns-cfg-main-img');
        $mainImg.addClass('fade-out');
        setTimeout(function() {
          $mainImg.attr('src', flv.image_url).attr('alt', flv.name).removeClass('fade-out');
        }, 150);

        // 2. Update Flavour Badge
        var $flvBadge = $('#bns-cfg-flv-badge');
        if (flv.is_special) {
          $flvBadge.text('ROYAL RESERVE BLEND');
        } else {
          $flvBadge.text('AUTHENTIC SR BLEND');
        }

        // 3. Update Active Chip & Thumbnail
        $('.bns-cfg-chip').removeClass('selected').attr('aria-pressed', 'false');
        $('.bns-cfg-chip[data-id="' + flv.id + '"]').addClass('selected').attr('aria-pressed', 'true');

        $('.bns-cfg-thumb-btn').removeClass('active');
        $('.bns-cfg-thumb-btn[data-id="' + flv.id + '"]').addClass('active');

        // 4. Update Summary Box & Total
        $('#bns-sum-flv-title').text(flv.name);
        recalcConfiguratorTotal();
      }

      function updateActiveRentalPackage(pkgName) {
        var pkg = rentalCatalog[pkgName];
        if (!pkg) return;

        currentRental = pkg.title;
        currentRentalPkg = pkg;
        currentRentalPrice = parseFloat(pkg.price);
        isHookah = (pkg.type === 'hookah');

        // Update titles, badges, and pricing
        $('#bns-cfg-rental-title-text').text(pkg.title);
        $('#bns-cfg-rental-name-heading').text(pkg.title);
        $('#bns-cfg-rental-tagline-text').text(pkg.tagline);
        $('#bns-cfg-rental-price-num').text(parseFloat(pkg.price).toFixed(2));
        $('#bns-cfg-rental-desc-text').text(pkg.description);
        $('#bns-cfg-specs-span').text(pkg.specs);
        $('#bns-cfg-tier-badge').text('[ ' + pkg.tier + ' TIER ]');

        // Update summary box
        $('#bns-sum-rental-title').text(pkg.title);
        $('#bns-sum-rental-price').text('â‚¹' + parseFloat(pkg.price).toFixed(2));

        // Update images
        var imgUrl = '/wp-content/themes/booknowshisha-theme/assets/images/rentals/' + pkg.image;
        $('#bns-cfg-rental-img').attr('src', imgUrl).attr('alt', pkg.title);
        $('#bns-cfg-rental-main-img').attr('src', imgUrl).attr('alt', pkg.title);

        // Toggle Hookah Base visibility
        if (isHookah) {
          $('#bns-cfg-base-section').show();
          $('#bns-sum-base-row').show();
          if (selectedBase === 'none') {
            $('#bns-base-toggle-yes').trigger('click');
          }
        } else {
          $('#bns-cfg-base-section').hide();
          $('#bns-sum-base-row').hide();
          selectedBase = 'none';
          selectedBasePrice = 0;
        }

        // Set Cookie
        document.cookie = 'bns_selected_rental=' + encodeURIComponent(pkg.title) + '; path=/; max-age=' + (60 * 60 * 24 * 7);

        recalcConfiguratorTotal();
      }

      function recalcConfiguratorTotal() {
        var rentalPrice = parseFloat(currentRentalPrice || 1499);
        var basePrice = (isHookah && baseEnabled) ? parseFloat(selectedBasePrice) : 0;
        var chillumPrice = parseFloat(selectedChillumPrice || 0);
        var unitTotal = rentalPrice + basePrice + chillumPrice;
        var grandTotal = unitTotal * currentQty;

        $('#bns-sum-rental-price').text('â‚¹' + rentalPrice.toFixed(2));
        $('#bns-sum-base-price').text(basePrice > 0 ? '+â‚¹' + basePrice.toFixed(2) : 'â‚¹0.00');
        $('#bns-sum-chillum-price').text(chillumPrice > 0 ? '+â‚¹' + chillumPrice.toFixed(2) : 'Included (â‚¹0.00)');
        $('#bns-sum-total-price').text('â‚¹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
      }

      // Visual View Switcher (Rental Setup vs Prepared Bowl)
      $('#bns-view-tab-rental').on('click', function() {
        activeView = 'rental';
        $('#bns-view-tab-rental').addClass('active');
        $('#bns-view-tab-flavour').removeClass('active');
        $('#bns-cfg-rental-main-img').show();
        $('#bns-cfg-main-img').hide();
      });

      $('#bns-view-tab-flavour').on('click', function() {
        activeView = 'flavour';
        $('#bns-view-tab-flavour').addClass('active');
        $('#bns-view-tab-rental').removeClass('active');
        $('#bns-cfg-main-img').show();
        $('#bns-cfg-rental-main-img').hide();
      });

      // Chillum Material Option Click
      $('.bns-cfg-chillum-card').on('click', function() {
        var $card = $(this);
        $('.bns-cfg-chillum-card').removeClass('active');
        $card.addClass('active');
        selectedChillum = $card.data('material') || 'Classic Clay';
        selectedChillumPrice = parseFloat($card.data('price') || 0);

        $('#bns-sum-chillum-title').text(selectedChillum);
        $('#bns-sum-chillum-price').text(selectedChillumPrice > 0 ? '+â‚¹' + selectedChillumPrice.toFixed(2) : 'Included (â‚¹0.00)');
        
        recalcConfiguratorTotal();
        showToast('Chilam Selected', selectedChillum + (selectedChillumPrice > 0 ? ' (+â‚¹100)' : '') + ' configured for your setup.', 'info');
      });

      // Chip Click (Flavour Selection)
      $chipsGrid.on('click', '.bns-cfg-chip', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var found = flavoursData.find(function(item) { return item.id == id; });
        if (found) {
          updateActiveFlavourDisplay(found);
          // If viewing rental, switch view to flavour to give immediate feedback
          if (activeView !== 'flavour') {
            $('#bns-view-tab-flavour').trigger('click');
          }
        }
      });

      // Thumbnail Strip Click
      $('#bns-cfg-thumbs-track').on('click', '.bns-cfg-thumb-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var found = flavoursData.find(function(item) { return item.id == id; });
        if (found) {
          updateActiveFlavourDisplay(found);
          if (activeView !== 'flavour') {
            $('#bns-view-tab-flavour').trigger('click');
          }
        }
      });

      // Filter Tabs (All / Paan / Fruit / Mint / Dessert)
      $('.bns-cfg-tab').on('click', function(e) {
        e.preventDefault();
        var filter = $(this).data('filter');
        $('.bns-cfg-tab').removeClass('active');
        $(this).addClass('active');

        $chipsGrid.find('.bns-cfg-chip').each(function() {
          var itemFilter = $(this).data('filter');
          if (filter === 'all' || itemFilter === filter) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      });

      // Hookah Base Toggle (YES / NO)
      $('#bns-base-toggle-yes').on('click', function() {
        baseEnabled = true;
        $('#bns-base-toggle-yes').addClass('active');
        $('#bns-base-toggle-no').removeClass('active');
        $('#bns-cfg-base-cards-container').removeClass('disabled');

        var $activeCard = $('#bns-cfg-base-cards-container .bns-cfg-base-card.active');
        if (!$activeCard.length) {
          $activeCard = $('#bns-cfg-base-cards-container .bns-cfg-base-card').first().addClass('active');
        }
        selectedBase = $activeCard.data('base') || 'standard';
        selectedBasePrice = parseFloat($activeCard.data('price') || 0);
        selectedBaseLabel = $activeCard.data('label') || 'Standard Base';

        $('#bns-sum-base-title').text(selectedBaseLabel);
        $('#bns-sum-base-price').text(selectedBasePrice > 0 ? '+â‚¹' + selectedBasePrice.toFixed(2) : 'â‚¹0.00');
        recalcConfiguratorTotal();
      });

      $('#bns-base-toggle-no').on('click', function() {
        baseEnabled = false;
        $('#bns-base-toggle-no').addClass('active');
        $('#bns-base-toggle-yes').removeClass('active');
        $('#bns-cfg-base-cards-container').addClass('disabled');

        selectedBase = 'none';
        selectedBasePrice = 0;
        selectedBaseLabel = 'No Base (Chilam Only)';

        $('#bns-sum-base-title').text('No Base (Chilam Only)');
        $('#bns-sum-base-price').text('â‚¹0.00');
        recalcConfiguratorTotal();
      });

      // Hookah Base Card Click
      $('#bns-cfg-base-cards-container').on('click', '.bns-cfg-base-card', function() {
        if (!baseEnabled) {
          $('#bns-base-toggle-yes').trigger('click');
        }
        var $card = $(this);
        $('#bns-cfg-base-cards-container .bns-cfg-base-card').removeClass('active');
        $card.addClass('active');

        selectedBase = $card.data('base') || 'standard';
        selectedBasePrice = parseFloat($card.data('price') || 0);
        selectedBaseLabel = $card.data('label') || 'Standard Base';

        $('#bns-sum-base-title').text(selectedBaseLabel);
        $('#bns-sum-base-price').text(selectedBasePrice > 0 ? '+â‚¹' + selectedBasePrice.toFixed(2) : 'â‚¹0.00');
        recalcConfiguratorTotal();
      });

      // Quantity Controls
      $('#bns-qty-minus').on('click', function() {
        if (currentQty > 1) {
          currentQty--;
          $('#bns-cfg-qty-input').val(currentQty);
          recalcConfiguratorTotal();
        }
      });

      $('#bns-qty-plus').on('click', function() {
        if (currentQty < 10) {
          currentQty++;
          $('#bns-cfg-qty-input').val(currentQty);
          recalcConfiguratorTotal();
        }
      });

      // Add to Cart Action (AJAX)
      $('#bns-cfg-add-cart-btn').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        $btn.prop('disabled', true).text('Adding to Cart...');

        var ajaxUrl = (typeof bnsThemeData !== 'undefined' && bnsThemeData.ajaxUrl) ? bnsThemeData.ajaxUrl : '/wp-admin/admin-ajax.php';
        var cartUrl = (typeof bnsThemeData !== 'undefined' && bnsThemeData.cartUrl) ? bnsThemeData.cartUrl : '/cart/';

        $.ajax({
          url: ajaxUrl,
          type: 'POST',
          data: {
            action: 'bns_add_flavour_rental_to_cart',
            product_id: activeFlv.id,
            rental_option: currentRental,
            chillum_material: selectedChillum,
            hookah_base: selectedBase,
            quantity: currentQty,
          },
          success: function(res) {
            $btn.prop('disabled', false).html('<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg> <span>ADD TO CART</span>');
            if (res && res.success) {
              var baseMsg = (selectedBase && selectedBase !== 'standard' && selectedBase !== 'none') ? ' + ' + selectedBase.toUpperCase() + ' BASE' : '';
              showToast('Added to Cart', activeFlv.name + ' with ' + currentRental + ' (' + selectedChillum + baseMsg + ') added to booking cart.', 'success');
              if (res.data && res.data.cart_count) {
                $('.bns-cart-count').text(res.data.cart_count);
              }
            } else {
              window.location.href = '/cart/?add-to-cart=' + activeFlv.id + '&quantity=' + currentQty + '&rental_option=' + encodeURIComponent(currentRental) + '&chillum_material=' + encodeURIComponent(selectedChillum) + '&hookah_base=' + encodeURIComponent(selectedBase);
            }
          },
          error: function() {
            $btn.prop('disabled', false).html('<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg> <span>ADD TO CART</span>');
            window.location.href = '/cart/?add-to-cart=' + activeFlv.id + '&quantity=' + currentQty + '&rental_option=' + encodeURIComponent(currentRental) + '&chillum_material=' + encodeURIComponent(selectedChillum) + '&hookah_base=' + encodeURIComponent(selectedBase);
          }
        });
      });

      // Buy Now Action (AJAX + Immediate Checkout Redirect)
      $('#bns-cfg-buy-now-btn').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        $btn.prop('disabled', true).text('Proceeding to Checkout...');

        var ajaxUrl = (typeof bnsThemeData !== 'undefined' && bnsThemeData.ajaxUrl) ? bnsThemeData.ajaxUrl : '/wp-admin/admin-ajax.php';
        var checkoutUrl = (typeof bnsThemeData !== 'undefined' && bnsThemeData.checkoutUrl) ? bnsThemeData.checkoutUrl : '/checkout/';

        $.ajax({
          url: ajaxUrl,
          type: 'POST',
          data: {
            action: 'bns_add_flavour_rental_to_cart',
            product_id: activeFlv.id,
            rental_option: currentRental,
            chillum_material: selectedChillum,
            hookah_base: selectedBase,
            quantity: currentQty,
          },
          success: function(res) {
            if (res && res.success && res.data && res.data.checkout_url) {
              window.location.href = res.data.checkout_url;
            } else {
              window.location.href = checkoutUrl;
            }
          },
          error: function() {
            window.location.href = checkoutUrl;
          }
        });
      });

      // Rental Switcher Modal Controls
      $('#bns-open-rental-modal-btn').on('click', function() {
        $('#bns-rental-modal').addClass('show').attr('aria-hidden', 'false');
      });

      $('#bns-close-rental-modal').on('click', function() {
        $('#bns-rental-modal').removeClass('show').attr('aria-hidden', 'true');
      });

      $('#bns-rental-modal').on('click', function(e) {
        if ($(e.target).is('#bns-rental-modal')) {
          $('#bns-rental-modal').removeClass('show').attr('aria-hidden', 'true');
        }
      });

      // Rental Modal Card Selection
      $('.bns-modal-rental-card').on('click', function() {
        var $card = $(this);
        var rName = $card.data('rental');

        $('.bns-modal-rental-card').removeClass('selected');
        $card.addClass('selected');

        updateActiveRentalPackage(rName);

        $('#bns-rental-modal').removeClass('show').attr('aria-hidden', 'true');
        showToast('Rental Setup Updated', 'Active setup switched to ' + rName + '.', 'info');
      });

      // Initialize
      if (activeFlv) {
        updateActiveFlavourDisplay(activeFlv);
      }
      if (currentRentalPkg) {
        updateActiveRentalPackage(currentRentalPkg.title);
      }
    }

    /* --------------------------------------------------------------------------
     * 10. LUXURY CART PAGE INTERACTIONS (QUANTITY STEPPERS & AUTO-UPDATE)
     * -------------------------------------------------------------------------- */
    var cartUpdateTimer = null;

    // Quantity Stepper Decrement
    $(document).on('click', '.bns-stepper-minus', function(e) {
      e.preventDefault();
      var $stepper = $(this).closest('.bns-qty-stepper-box');
      var $input = $stepper.find('input.qty');
      var currentVal = parseInt($input.val(), 10) || 1;
      var minVal = parseInt($input.attr('min'), 10) || 1;

      if (currentVal > minVal) {
        $input.val(currentVal - 1).trigger('change');
      }
    });

    // Quantity Stepper Increment
    $(document).on('click', '.bns-stepper-plus', function(e) {
      e.preventDefault();
      var $stepper = $(this).closest('.bns-qty-stepper-box');
      var $input = $stepper.find('input.qty');
      var currentVal = parseInt($input.val(), 10) || 1;
      var maxVal = parseInt($input.attr('max'), 10) || 999;

      if (currentVal < maxVal) {
        $input.val(currentVal + 1).trigger('change');
      }
    });

    /* --------------------------------------------------------------------------
     * 11. ACCOUNT DROPDOWN TOGGLE & ACCESSIBILITY (LOGGED-IN CUSTOMERS)
     * -------------------------------------------------------------------------- */
    $(document).on('click', '#bns-account-trigger.is-logged-in', function(e) {
      // On mobile or touch, prevent instant jump and toggle dropdown
      if (window.innerWidth <= 1024 || ('ontouchstart' in window)) {
        var $wrapper = $(this).closest('.bns-account-dropdown-wrapper');
        var $menu = $wrapper.find('.bns-account-dropdown-menu');
        if ($menu.length) {
          e.preventDefault();
          var isOpen = $menu.hasClass('is-open');
          $('.bns-account-dropdown-menu').removeClass('is-open');
          $(this).attr('aria-expanded', !isOpen);
          if (!isOpen) {
            $menu.addClass('is-open');
          }
        }
      }
    });

    // Close account dropdown on click outside
    $(document).on('click', function(e) {
      if (!$(e.target).closest('.bns-account-dropdown-wrapper').length) {
        $('.bns-account-dropdown-menu').removeClass('is-open');
        $('#bns-account-trigger').attr('aria-expanded', 'false');
      }
    });

    // Close on Escape key
    $(document).on('keydown', function(e) {
      if (e.key === 'Escape' || e.keyCode === 27) {
        $('.bns-account-dropdown-menu').removeClass('is-open');
        $('#bns-account-trigger').attr('aria-expanded', 'false');
      }
    });

  });
})(jQuery);


