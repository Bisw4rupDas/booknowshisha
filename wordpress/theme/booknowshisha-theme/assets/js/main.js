/**
 * ShishaRent Theme Main JavaScript
 * Full interactive controls: Theme switcher (Light/Dark mode), live filter, carousel, PIN checker, contact form, modal, and accordion.
 *
 * @package ShishaRent
 */
(function($) {
  'use strict';

  $(document).ready(function() {

      // ------------------------------------------------------------------------
    // 12. Customer Email Authentication System (Email Login, Sign-Up & Password Reset)
    // ------------------------------------------------------------------------
    var $authModal = $('#bns-auth-modal');
    var ajaxEndpoint = (typeof bnsThemeData !== 'undefined' && bnsThemeData.ajaxUrl) ? bnsThemeData.ajaxUrl : '/wp-admin/admin-ajax.php';
    var defaultNonce = (typeof bnsThemeData !== 'undefined' && bnsThemeData.authNonce) ? bnsThemeData.authNonce : '';

    function showAuthAlert(msg, type, isPage) {
      var $alert = isPage ? $('#bns-inpage-auth-alert') : $('#bns-auth-alert');
      $alert.removeClass('bns-alert-error bns-alert-success')
        .addClass(type === 'success' ? 'bns-alert-success' : 'bns-alert-error')
        .html(msg).stop(true, true).fadeIn(200);
    }

    function hideAuthAlert(isPage) {
      var $alert = isPage ? $('#bns-inpage-auth-alert') : $('#bns-auth-alert');
      $alert.hide().html('');
    }

    function switchModalView(view) {
      hideAuthAlert(false);
      $('#bns-view-signin, #bns-view-signup, #bns-view-forgot').hide();
      var $title = $('#bns-modal-title');
      var $subtitle = $('#bns-modal-subtitle');
      var $tabs = $('#bns-modal-tabs-wrap');
      var $tabSignIn = $('#bns-tab-btn-signin');
      var $tabSignUp = $('#bns-tab-btn-signup');

      if (view === 'signup') {
        $tabs.show();
        $tabSignIn.removeClass('active');
        $tabSignUp.addClass('active');
        $('#bns-view-signup').stop(true, true).fadeIn(150);
        $title.text('CREATE ACCOUNT');
        $subtitle.text('Create your ShishaRent account to manage hookah rentals and checkout faster.');
      } else if (view === 'forgot') {
        $tabs.hide();
        $('#bns-view-forgot').stop(true, true).fadeIn(150);
        $title.text('RESET PASSWORD');
        $subtitle.text("Enter your email address and we'll send you a secure link to reset your password.");
      } else {
        $tabs.show();
        $tabSignIn.addClass('active');
        $tabSignUp.removeClass('active');
        $('#bns-view-signin').stop(true, true).fadeIn(150);
        $title.text('SIGN IN');
        $subtitle.text('Welcome to ShishaRent. Sign in to access your Kolkata reservations, track active rentals, and manage your account.');
      }
    }

    function switchInpageView(view) {
      hideAuthAlert(true);
      $('#bns-inpage-view-signin, #bns-inpage-view-signup, #bns-inpage-view-forgot').hide();
      var $title = $('#bns-inpage-auth-title');
      var $subtitle = $('#bns-inpage-auth-subtitle');
      var $tabs = $('#bns-inpage-tabs-wrap');
      var $tabSignIn = $('#bns-inpage-tab-btn-signin');
      var $tabSignUp = $('#bns-inpage-tab-btn-signup');

      if (view === 'signup') {
        $tabs.show();
        $tabSignIn.removeClass('active');
        $tabSignUp.addClass('active');
        $('#bns-inpage-view-signup').stop(true, true).fadeIn(150);
        $title.text('CREATE ACCOUNT');
        $subtitle.text('Create your ShishaRent account to manage hookah rentals and checkout faster.');
      } else if (view === 'forgot') {
        $tabs.hide();
        $('#bns-inpage-view-forgot').stop(true, true).fadeIn(150);
        $title.text('RESET PASSWORD');
        $subtitle.text("Enter your email address and we'll send you a secure link to reset your password.");
      } else {
        $tabs.show();
        $tabSignIn.addClass('active');
        $tabSignUp.removeClass('active');
        $('#bns-inpage-view-signin').stop(true, true).fadeIn(150);
        $title.text('SIGN IN');
        $subtitle.text('Welcome to ShishaRent. Sign in to view active reservations, rental history, and manage your account.');
      }
    }

    // Tab buttons in modal
    $(document).on('click', '#bns-tab-btn-signin', function(e) {
      e.preventDefault();
      switchModalView('signin');
    });

    $(document).on('click', '#bns-tab-btn-signup', function(e) {
      e.preventDefault();
      switchModalView('signup');
    });

    // Tab buttons in in-page form
    $(document).on('click', '#bns-inpage-tab-btn-signin', function(e) {
      e.preventDefault();
      switchInpageView('signin');
    });

    $(document).on('click', '#bns-inpage-tab-btn-signup', function(e) {
      e.preventDefault();
      switchInpageView('signup');
    });

    // Modal view switch links
    $(document).on('click', '#bns-link-to-signup', function(e) {
      e.preventDefault();
      switchModalView('signup');
    });

    $(document).on('click', '#bns-link-to-forgot', function(e) {
      e.preventDefault();
      switchModalView('forgot');
    });

    $(document).on('click', '#bns-link-to-signin-from-signup, #bns-link-to-signin-from-forgot', function(e) {
      e.preventDefault();
      switchModalView('signin');
    });

    // In-page view switch links
    $(document).on('click', '#bns-inpage-link-to-signup', function(e) {
      e.preventDefault();
      switchInpageView('signup');
    });

    $(document).on('click', '#bns-inpage-link-to-forgot', function(e) {
      e.preventDefault();
      switchInpageView('forgot');
    });

    $(document).on('click', '#bns-inpage-link-to-signin-from-signup, #bns-inpage-link-to-signin-from-forgot', function(e) {
      e.preventDefault();
      switchInpageView('signin');
    });

    // Open Email Auth Modal from customer login triggers when logged out
    $(document).on('click', '#bns-account-trigger:not(.is-logged-in), .bns-account-trigger:not(.is-logged-in), .bns-btn-account-login, .bns-btn-mobile-login, .bns-open-auth-btn, .woocommerce-info a.showlogin, a.showlogin', function(e) {
      if ($('#bns-inpage-form-signin').length && $('#bns-inpage-form-signin').is(':visible')) {
        $('html, body').animate({
          scrollTop: $('#bns-myaccount-auth-container').offset().top - 80
        }, 400);
        return;
      }

      e.preventDefault();
      if (typeof $drawer !== 'undefined' && $drawer.length) {
        $drawer.removeClass('open');
        $('body').css('overflow', '');
      }
      $('.bns-account-dropdown-menu').removeClass('is-open');
      switchModalView('signin');
      $authModal.addClass('is-open').css('display', 'flex').hide().fadeIn(200);
    });

    // Close Auth Modal
    $(document).on('click', '#bns-auth-close, #bns-auth-backdrop', function() {
      $authModal.removeClass('is-open').fadeOut(150);
    });

    // Close on Escape key
    $(document).on('keydown', function(e) {
      if ((e.key === 'Escape' || e.keyCode === 27) && $authModal.is(':visible')) {
        $authModal.removeClass('is-open').fadeOut(150);
      }
    });

    // Password Visibility Toggle
    $(document).on('click', '.bns-pwd-toggle', function(e) {
      e.preventDefault();
      var $btn = $(this);
      var $wrap = $btn.closest('.bns-password-wrap');
      var $input = $wrap.find('input');
      var isPassword = $input.attr('type') === 'password';

      $input.attr('type', isPassword ? 'text' : 'password');
      if (isPassword) {
        $btn.find('.bns-eye-show').hide();
        $btn.find('.bns-eye-hide').show();
      } else {
        $btn.find('.bns-eye-show').show();
        $btn.find('.bns-eye-hide').hide();
      }
    });

    // Sign In Submission Handler (Modal & In-Page)
    $(document).on('submit', '#bns-form-signin, #bns-inpage-form-signin', function(e) {
      e.preventDefault();
      var $form = $(this);
      var isPage = $form.attr('id') === 'bns-inpage-form-signin';
      var $btn = $form.find('button[type="submit"]');
      var $btnText = $btn.find('.bns-btn-text');
      var origText = $btnText.text();

      var emailVal = $.trim($form.find('input[name="email"]').val());
      var passVal = $.trim($form.find('input[name="password"]').val());

      if (!emailVal) {
        showAuthAlert('Please enter your email address.', 'error', isPage);
        return;
      }
      if (!passVal) {
        showAuthAlert('Please enter your password.', 'error', isPage);
        return;
      }

      hideAuthAlert(isPage);
      $btn.prop('disabled', true).addClass('is-loading');
      $btnText.text('Signing in...');

      var formData = $form.serializeArray();
      var dataObj = {};
      $.each(formData, function(_, field) {
        dataObj[field.name] = field.value;
      });
      dataObj.action = 'bns_email_login';
      if (!dataObj.security && defaultNonce) {
        dataObj.security = defaultNonce;
      }
      dataObj.redirect = window.location.href;

      $.ajax({
        url: ajaxEndpoint,
        type: 'POST',
        data: dataObj,
        success: function(res) {
          if (res && res.success) {
            $btnText.text('Signed In!');
            showAuthAlert(res.data.message || 'Signed in successfully!', 'success', isPage);
            if (typeof showToast === 'function') {
              showToast('Authentication', res.data.message || 'Welcome back to ShishaRent!', 'success');
            }
            setTimeout(function() {
              if (res.data && res.data.redirect) {
                window.location.href = res.data.redirect;
              } else {
                location.reload();
              }
            }, 500);
          } else {
            $btn.prop('disabled', false).removeClass('is-loading');
            $btnText.text(origText);
            var errMsg = (res && res.data && res.data.message) ? res.data.message : 'Invalid email address or password.';
            showAuthAlert(errMsg, 'error', isPage);
          }
        },
        error: function() {
          $btn.prop('disabled', false).removeClass('is-loading');
          $btnText.text(origText);
          showAuthAlert('Network error during sign-in. Please try again.', 'error', isPage);
        }
      });
    });

    // Sign Up / Registration Submission Handler (Modal & In-Page)
    $(document).on('submit', '#bns-form-signup, #bns-inpage-form-signup', function(e) {
      e.preventDefault();
      var $form = $(this);
      var isPage = $form.attr('id') === 'bns-inpage-form-signup';
      var $btn = $form.find('button[type="submit"]');
      var $btnText = $btn.find('.bns-btn-text');
      var origText = $btnText.text();

      var nameVal = $.trim($form.find('input[name="name"]').val());
      var emailVal = $.trim($form.find('input[name="email"]').val());
      var pwd = $form.find('input[name="password"]').val();
      var confirmPwd = $form.find('input[name="confirm_password"]').val();

      if (!nameVal) {
        showAuthAlert('Please enter your full name.', 'error', isPage);
        return;
      }

      if (!emailVal || emailVal.indexOf('@') === -1) {
        showAuthAlert('Please enter a valid email address.', 'error', isPage);
        return;
      }

      if (!pwd || pwd.length < 8) {
        showAuthAlert('Password must be at least 8 characters long.', 'error', isPage);
        return;
      }

      if (pwd !== confirmPwd) {
        showAuthAlert('Passwords do not match. Please re-enter your confirm password.', 'error', isPage);
        return;
      }

      hideAuthAlert(isPage);
      $btn.prop('disabled', true).addClass('is-loading');
      $btnText.text('Creating Account...');

      var formData = $form.serializeArray();
      var dataObj = {};
      $.each(formData, function(_, field) {
        dataObj[field.name] = field.value;
      });
      dataObj.action = 'bns_email_register';
      if (!dataObj.security && defaultNonce) {
        dataObj.security = defaultNonce;
      }
      dataObj.redirect = window.location.href;

      $.ajax({
        url: ajaxEndpoint,
        type: 'POST',
        data: dataObj,
        success: function(res) {
          if (res && res.success) {
            $btnText.text('Account Created!');
            showAuthAlert(res.data.message || 'Account created successfully!', 'success', isPage);
            if (typeof showToast === 'function') {
              showToast('Account Created', res.data.message || 'Welcome to ShishaRent!', 'success');
            }
            setTimeout(function() {
              if (res.data && res.data.redirect) {
                window.location.href = res.data.redirect;
              } else {
                location.reload();
              }
            }, 500);
          } else {
            $btn.prop('disabled', false).removeClass('is-loading');
            $btnText.text(origText);
            var errMsg = (res && res.data && res.data.message) ? res.data.message : 'Could not create account. Please try again.';
            showAuthAlert(errMsg, 'error', isPage);
          }
        },
        error: function() {
          $btn.prop('disabled', false).removeClass('is-loading');
          $btnText.text(origText);
          showAuthAlert('Network error during registration. Please try again.', 'error', isPage);
        }
      });
    });

    // Forgot Password Submission Handler (Modal & In-Page)
    $(document).on('submit', '#bns-form-forgot, #bns-inpage-form-forgot', function(e) {
      e.preventDefault();
      var $form = $(this);
      var isPage = $form.attr('id') === 'bns-inpage-form-forgot';
      var $btn = $form.find('button[type="submit"]');
      var $btnText = $btn.find('.bns-btn-text');
      var origText = $btnText.text();

      var emailVal = $.trim($form.find('input[name="email"]').val());
      if (!emailVal || emailVal.indexOf('@') === -1) {
        showAuthAlert('Please enter a valid email address.', 'error', isPage);
        return;
      }

      hideAuthAlert(isPage);
      $btn.prop('disabled', true).addClass('is-loading');
      $btnText.text('Sending Link...');

      var formData = $form.serializeArray();
      var dataObj = {};
      $.each(formData, function(_, field) {
        dataObj[field.name] = field.value;
      });
      dataObj.action = 'bns_forgot_password';
      if (!dataObj.security && defaultNonce) {
        dataObj.security = defaultNonce;
      }

      $.ajax({
        url: ajaxEndpoint,
        type: 'POST',
        data: dataObj,
        success: function(res) {
          $btn.prop('disabled', false).removeClass('is-loading');
          $btnText.text(origText);
          if (res && res.success) {
            showAuthAlert(res.data.message || 'If that email is registered, a password reset link has been sent.', 'success', isPage);
            $form.find('input[name="email"]').val('');
          } else {
            var errMsg = (res && res.data && res.data.message) ? res.data.message : 'Failed to send password reset email. Please try again.';
            showAuthAlert(errMsg, 'error', isPage);
          }
        },
        error: function() {
          $btn.prop('disabled', false).removeClass('is-loading');
          $btnText.text(origText);
          showAuthAlert('Network error while processing request. Please try again.', 'error', isPage);
        }
      });
    });

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
        $('#bns-sum-rental-price').text('₹' + parseFloat(pkg.price).toFixed(2));

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

        $('#bns-sum-rental-price').text('₹' + rentalPrice.toFixed(2));
        $('#bns-sum-base-price').text(basePrice > 0 ? '+₹' + basePrice.toFixed(2) : '₹0.00');
        $('#bns-sum-chillum-price').text(chillumPrice > 0 ? '+₹' + chillumPrice.toFixed(2) : 'Included (₹0.00)');
        $('#bns-sum-total-price').text('₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
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
        $('#bns-sum-chillum-price').text(selectedChillumPrice > 0 ? '+₹' + selectedChillumPrice.toFixed(2) : 'Included (₹0.00)');
        
        recalcConfiguratorTotal();
        showToast('Chilam Selected', selectedChillum + (selectedChillumPrice > 0 ? ' (+₹100)' : '') + ' configured for your setup.', 'info');
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
        $('#bns-sum-base-price').text(selectedBasePrice > 0 ? '+₹' + selectedBasePrice.toFixed(2) : '₹0.00');
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
        $('#bns-sum-base-price').text('₹0.00');
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
        $('#bns-sum-base-price').text(selectedBasePrice > 0 ? '+₹' + selectedBasePrice.toFixed(2) : '₹0.00');
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




