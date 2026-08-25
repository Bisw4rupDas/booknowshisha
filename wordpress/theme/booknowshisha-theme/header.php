<?php
/**
 * Header template for ShishaRent Theme (Kolkata Edition)
 * Includes Light/Dark theme initialization and accessible theme toggle controls.
 *
 * @package ShishaRent
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="dark">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <script>
        (function() {
            try {
                var savedTheme = localStorage.getItem('shisharent_theme');
                if (savedTheme === 'light') {
                    document.documentElement.setAttribute('data-theme', 'light');
                } else {
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (e) {}
        })();
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="bns-header" id="bns-site-header">
    <div class="bns-container bns-header-container">
        <div class="bns-nav-wrapper">
            <!-- Brand Logo -->
            <div class="bns-brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="bns-logo-link" rel="home" title="<?php esc_attr_e('ShishaRent Home', 'shisharent'); ?>">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png?v=' . filemtime(get_template_directory() . '/assets/images/logo.png')); ?>" alt="<?php esc_attr_e('ShishaRent', 'shisharent'); ?>" class="bns-logo-img" />
                </a>
            </div>

            <!-- Desktop Navigation Menu -->
            <nav class="bns-nav-menu" id="bns-desktop-nav" aria-label="<?php esc_attr_e('Main Navigation', 'shisharent'); ?>">
                <?php
                if (has_nav_menu('primary')) {
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'bns-menu-items',
                        'fallback_cb'    => false,
                    ]);
                } else {
                    // Default structured navigation links
                    ?>
                    <ul class="bns-menu-items">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>" class="<?php echo is_front_page() ? 'active' : ''; ?>"><?php esc_html_e('HOME', 'shisharent'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/#packages')); ?>"><?php esc_html_e('RENT A HOOKAH', 'shisharent'); ?></a></li>
                        
                        <!-- Services Dropdown -->
                        <li class="bns-nav-dropdown-parent">
                            <a href="<?php echo esc_url(home_url('/bartending-party-services/')); ?>" class="bns-dropdown-trigger <?php echo (is_page('bartending-party-services') || is_page('party-occasion-hookah')) ? 'active' : ''; ?>">
                                <?php esc_html_e('SERVICES', 'shisharent'); ?> <span class="bns-dropdown-arrow">Ã¢â€“Â¾</span>
                            </a>
                            <ul class="bns-dropdown-menu">
                                <li>
                                    <a href="<?php echo esc_url(home_url('/bartending-party-services/')); ?>" class="<?php echo is_page('bartending-party-services') ? 'active' : ''; ?>">
                                        <div>
                                            <strong><?php esc_html_e('Bartending & Party Services', 'shisharent'); ?></strong>
                                            <small><?php esc_html_e('Mobile bar catering & craft mixologists', 'shisharent'); ?></small>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo esc_url(home_url('/party-occasion-hookah/')); ?>" class="<?php echo is_page('party-occasion-hookah') ? 'active' : ''; ?>">
                                        <div>
                                            <strong><?php esc_html_e('Party & Occasion Hookah', 'shisharent'); ?></strong>
                                            <small><?php esc_html_e('Multi-hookah event setups & shisha valets', 'shisharent'); ?></small>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li><a href="<?php echo esc_url(home_url('/#rentals')); ?>"><?php esc_html_e('RENTALS', 'shisharent'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/flavour-selection/')); ?>" class="<?php echo is_page('flavour-selection') ? 'active' : ''; ?>"><?php esc_html_e('FLAVOURS', 'shisharent'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/gallery/')); ?>" class="<?php echo is_page('gallery') ? 'active' : ''; ?>"><?php esc_html_e('GALLERY', 'shisharent'); ?></a></li>
                        <?php if (class_exists('WooCommerce')): ?>
                            <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="<?php echo (function_exists('is_shop') && is_shop()) ? 'active' : ''; ?>"><?php esc_html_e('SHOP', 'shisharent'); ?></a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog/')); ?>" class="<?php echo (is_home() || is_singular('post') || is_category()) ? 'active' : ''; ?>"><?php esc_html_e('BLOG', 'shisharent'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="<?php echo is_page('contact') ? 'active' : ''; ?>"><?php esc_html_e('CONTACT', 'shisharent'); ?></a></li>
                    </ul>
                    <?php
                }
                ?>
            </nav>

            <!-- Header Action Controls -->
            <div class="bns-header-actions">
                
                <!-- Light / Dark Mode Toggle Switch (Desktop) -->
                <button type="button" class="bns-theme-toggle" id="bns-theme-toggle" aria-label="<?php esc_attr_e('Switch between light and dark theme', 'shisharent'); ?>" title="<?php esc_attr_e('Switch Theme Mode', 'shisharent'); ?>" role="switch" aria-checked="false">
                    <span class="bns-theme-toggle-track">
                        <span class="bns-theme-toggle-icon bns-icon-sun-wrap">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                        </span>
                        <span class="bns-theme-toggle-icon bns-icon-moon-wrap">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                        <span class="bns-theme-toggle-thumb"></span>
                    </span>
                </button>

                <!-- Phone / Express Hotline -->
                <a href="tel:+919903556825" class="bns-hotline-btn" title="<?php esc_attr_e('Express Hookah Hotline: +91 99035 56825', 'shisharent'); ?>">
                    <span class="bns-pulse-dot"></span>
                    <span class="bns-phone-number">+91 99035 56825</span>
                </a>

                <?php if (class_exists('WooCommerce')): ?>
                    <?php
                    $is_logged_in = is_user_logged_in();
                    $current_user = wp_get_current_user();
                    $account_url  = wc_get_page_permalink('myaccount');
                    $orders_url   = wc_get_endpoint_url('orders', '', $account_url);
                    $address_url  = wc_get_endpoint_url('edit-address', '', $account_url);
                    $details_url  = wc_get_endpoint_url('edit-account', '', $account_url);
                    $logout_url   = wc_logout_url(home_url());
                    $user_name    = $is_logged_in ? ($current_user->first_name ?: $current_user->display_name) : '';
                    ?>
                    <!-- Account Dropdown Component -->
                    <div class="bns-account-dropdown-wrapper">
                        <a href="<?php echo esc_url($account_url); ?>" class="bns-action-icon bns-account-trigger <?php echo $is_logged_in ? 'is-logged-in' : 'bns-open-auth-btn'; ?>" data-logged-in="<?php echo $is_logged_in ? '1' : '0'; ?>" title="<?php echo $is_logged_in ? esc_attr(sprintf(__('My Account (%s)', 'shisharent'), $user_name)) : esc_attr__('Sign In', 'shisharent'); ?>" aria-label="<?php esc_attr_e('My Account', 'shisharent'); ?>" aria-haspopup="true" aria-expanded="false" id="bns-account-trigger">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <?php if ($is_logged_in): ?>
                                <span class="bns-account-status-dot" title="<?php esc_attr_e('Active Account', 'shisharent'); ?>"></span>
                            <?php endif; ?>
                        </a>

                        <!-- Desktop Account Dropdown Menu -->
                        <div class="bns-account-dropdown-menu" id="bns-account-dropdown" role="menu">
                            <div class="bns-account-menu-inner">
                                <?php if ($is_logged_in): ?>
                                    <div class="bns-account-user-header">
                                        <div class="bns-user-avatar-badge">
                                            <?php echo esc_html(strtoupper(substr($user_name, 0, 1) ?: 'U')); ?>
                                        </div>
                                        <div class="bns-user-meta">
                                            <span class="bns-user-welcome"><?php esc_html_e('Welcome back,', 'shisharent'); ?></span>
                                            <h4 class="bns-user-name"><?php echo esc_html($user_name); ?></h4>
                                            <span class="bns-user-email"><?php echo esc_html($current_user->user_email); ?></span>
                                        </div>
                                    </div>
                                    <div class="bns-account-menu-links">
                                        <a href="<?php echo esc_url($orders_url); ?>" class="bns-account-link-item" role="menuitem">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                            <div>
                                                <strong><?php esc_html_e('My Orders & Rentals', 'shisharent'); ?></strong>
                                                <small><?php esc_html_e('Track active bookings & rental history', 'shisharent'); ?></small>
                                            </div>
                                        </a>
                                        <a href="<?php echo esc_url($address_url); ?>" class="bns-account-link-item" role="menuitem">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            <div>
                                                <strong><?php esc_html_e('Saved Delivery Addresses', 'shisharent'); ?></strong>
                                                <small><?php esc_html_e('Kolkata & 24 Parganas locations', 'shisharent'); ?></small>
                                            </div>
                                        </a>
                                        <a href="<?php echo esc_url($details_url); ?>" class="bns-account-link-item" role="menuitem">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                            <div>
                                                <strong><?php esc_html_e('Account Details & Security', 'shisharent'); ?></strong>
                                                <small><?php esc_html_e('Profile details & password', 'shisharent'); ?></small>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="bns-account-footer-action">
                                        <a href="<?php echo esc_url($logout_url); ?>" class="bns-logout-btn" role="menuitem">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                            <span><?php esc_html_e('Sign Out', 'shisharent'); ?></span>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="bns-account-guest-header">
                                        <span class="bns-guest-badge"><?php esc_html_e('BOOKMYSMOKE ACCOUNT', 'shisharent'); ?></span>
                                        <h4 class="bns-guest-title"><?php esc_html_e('Sign In', 'shisharent'); ?></h4>
                                        <p class="bns-guest-desc"><?php esc_html_e('Access your active hookah reservations, rental history & saved Kolkata delivery addresses.', 'shisharent'); ?></p>
                                        <a href="#" class="button bns-btn-account-login bns-open-auth-btn">
                                            <?php esc_html_e('SIGN IN →', 'shisharent'); ?>
                                        </a>
                                    </div>
                                    <div class="bns-account-guest-links">
                                        <a href="<?php echo esc_url($orders_url); ?>" class="bns-account-link-item" role="menuitem">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                            <div>
                                                <strong><?php esc_html_e('Track Order / Rental Status', 'shisharent'); ?></strong>
                                                <small><?php esc_html_e('View delivery status with Order ID', 'shisharent'); ?></small>
                                            </div>
                                        </a>
                                        <a href="tel:+919903556825" class="bns-account-link-item" role="menuitem">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                            <div>
                                                <strong><?php esc_html_e('VIP Hotline Concierge', 'shisharent'); ?></strong>
                                                <small><?php esc_html_e('+91 99035 56825 / +91 90511 77720', 'shisharent'); ?></small>
                                            </div>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Cart -->
                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="bns-action-icon bns-cart-btn" title="<?php esc_attr_e('View Shopping Cart', 'shisharent'); ?>" aria-label="Shopping Cart">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span class="bns-cart-count" id="bns-cart-counter">
                            <?php echo (WC()->cart) ? WC()->cart->get_cart_contents_count() : 0; ?>
                        </span>
                    </a>
                <?php endif; ?>

                <!-- Mobile Hamburger Toggle -->
                <button type="button" class="bns-hamburger-btn" id="bns-mobile-toggle" aria-label="<?php esc_attr_e('Toggle Menu', 'shisharent'); ?>">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div class="bns-mobile-drawer" id="bns-mobile-drawer">
        <div class="bns-drawer-backdrop" id="bns-drawer-backdrop"></div>
        <div class="bns-drawer-content">
            <div class="bns-drawer-header">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="bns-drawer-logo" rel="home">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png?v=' . filemtime(get_template_directory() . '/assets/images/logo.png')); ?>" alt="<?php esc_attr_e('ShishaRent', 'shisharent'); ?>" class="bns-logo-img bns-drawer-logo-img" />
                </a>
                <button type="button" class="bns-drawer-close" id="bns-drawer-close" aria-label="<?php esc_attr_e('Close Menu', 'shisharent'); ?>">Ã¢Å“â€¢</button>
            </div>

            <!-- Mobile Theme Toggle Bar -->
            <div class="bns-mobile-theme-bar">
                <span class="bns-mobile-theme-label"><?php esc_html_e('Theme Mode:', 'shisharent'); ?></span>
                <button type="button" class="bns-theme-toggle bns-theme-toggle-mobile" id="bns-theme-toggle-mobile" aria-label="<?php esc_attr_e('Switch Theme Mode', 'shisharent'); ?>" role="switch" aria-checked="false">
                    <span class="bns-theme-toggle-track">
                        <span class="bns-theme-toggle-icon bns-icon-sun-wrap">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                        </span>
                        <span class="bns-theme-toggle-icon bns-icon-moon-wrap">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                        <span class="bns-theme-toggle-thumb"></span>
                    </span>
                </button>
            </div>

            <?php if (class_exists('WooCommerce')): ?>
                <!-- Mobile User Account Box -->
                <div class="bns-mobile-account-card">
                    <?php if ($is_logged_in): ?>
                        <div class="bns-mobile-user-row">
                            <div class="bns-mobile-avatar">
                                <?php echo esc_html(strtoupper(substr($user_name, 0, 1) ?: 'U')); ?>
                            </div>
                            <div class="bns-mobile-user-info">
                                <strong><?php echo esc_html($user_name); ?></strong>
                                <span><?php echo esc_html($current_user->user_email); ?></span>
                            </div>
                        </div>
                        <div class="bns-mobile-account-btn-group">
                            <a href="<?php echo esc_url($orders_url); ?>" class="bns-btn-mobile-act"><?php esc_html_e('My Orders', 'shisharent'); ?></a>
                            <a href="<?php echo esc_url($account_url); ?>" class="bns-btn-mobile-act"><?php esc_html_e('Dashboard', 'shisharent'); ?></a>
                            <a href="<?php echo esc_url($logout_url); ?>" class="bns-btn-mobile-act bns-act-logout"><?php esc_html_e('Logout', 'shisharent'); ?></a>
                        </div>
                    <?php else: ?>
                        <div class="bns-mobile-guest-box">
                            <div class="bns-mobile-guest-text">
                                <span class="bns-mobile-guest-tag"><?php esc_html_e('BOOKMYSMOKE VIP', 'shisharent'); ?></span>
                                <strong><?php esc_html_e('Account & Reservations', 'shisharent'); ?></strong>
                                <p><?php esc_html_e('Sign in to track active rentals & saved delivery addresses.', 'shisharent'); ?></p>
                            </div>
                            <a href="#" class="bns-btn-mobile-login bns-open-auth-btn">
                                <?php esc_html_e('SIGN IN →', 'shisharent'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <nav class="bns-mobile-menu">
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/')); ?>" class="bns-mobile-link"><?php esc_html_e('Home', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/#rentals')); ?>" class="bns-mobile-link"><?php esc_html_e('Hookah & Chilam Rentals', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/flavour-selection/')); ?>" class="bns-mobile-link <?php echo is_page('flavour-selection') ? 'active' : ''; ?>"><?php esc_html_e('23 SR Flavours', 'shisharent'); ?></a></li>
                    
                    <?php if (class_exists('WooCommerce')): ?>
                        <li class="bns-mobile-menu-section-heading"><?php esc_html_e('My Account & Bookings', 'shisharent'); ?></li>
                        <li><a href="<?php echo esc_url($orders_url); ?>" class="bns-mobile-link bns-sub-link"><?php esc_html_e('My Orders & Rental Bookings', 'shisharent'); ?></a></li>
                        <li><a href="<?php echo esc_url($address_url); ?>" class="bns-mobile-link bns-sub-link"><?php esc_html_e('Saved Delivery Addresses', 'shisharent'); ?></a></li>
                        <li><a href="<?php echo esc_url($details_url); ?>" class="bns-mobile-link bns-sub-link"><?php esc_html_e('Account Details & Security', 'shisharent'); ?></a></li>
                    <?php endif; ?>

                    <li class="bns-mobile-menu-section-heading"><?php esc_html_e('Services & Catering', 'shisharent'); ?></li>
                    <li><a href="<?php echo esc_url(home_url('/bartending-party-services/')); ?>" class="bns-mobile-link bns-sub-link"><?php esc_html_e('Bartending & Party Services', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/party-occasion-hookah/')); ?>" class="bns-mobile-link bns-sub-link"><?php esc_html_e('Party & Occasion Hookah', 'shisharent'); ?></a></li>
                    
                    <li class="bns-mobile-menu-section-heading"><?php esc_html_e('Products & Showcase', 'shisharent'); ?></li>
                    <li><a href="<?php echo esc_url(home_url('/gallery/')); ?>" class="bns-mobile-link <?php echo is_page('gallery') ? 'active' : ''; ?>"><?php esc_html_e('Gallery Showcase', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/#rentals')); ?>" class="bns-mobile-link"><?php esc_html_e('Rental Options', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/flavour-selection/')); ?>" class="bns-mobile-link"><?php esc_html_e('Flavour Selection', 'shisharent'); ?></a></li>
                    <?php if (class_exists('WooCommerce')): ?>
                        <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="bns-mobile-link"><?php esc_html_e('Shop Catalog (23 Flavours)', 'shisharent'); ?></a></li>
                    <?php endif; ?>
                    
                    <li class="bns-mobile-menu-section-heading"><?php esc_html_e('Information & Journal', 'shisharent'); ?></li>
                    <li><a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog/')); ?>" class="bns-mobile-link"><?php esc_html_e('Blog & Journal', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/#how-it-works')); ?>" class="bns-mobile-link"><?php esc_html_e('How It Works', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/#about')); ?>" class="bns-mobile-link"><?php esc_html_e('About Us', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/#checker')); ?>" class="bns-mobile-link"><?php esc_html_e('Delivery PIN Checker', 'shisharent'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="bns-mobile-link"><?php esc_html_e('Contact Us', 'shisharent'); ?></a></li>
                </ul>
            </nav>
            <div class="bns-drawer-footer">
                <a href="tel:+919903556825" class="bns-btn-gold bns-btn-block"><?php esc_html_e('Call Hotline: +91 99035 56825', 'shisharent'); ?></a>
                <a href="tel:+919051177720" class="bns-btn-outline bns-btn-block" style="margin-top: 8px;"><?php esc_html_e('Alt Hotline: +91 90511 77720', 'shisharent'); ?></a>
            </div>
        </div>
    </div>
</header>

<main id="bns-main-content" class="bns-site-main">


