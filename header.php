<?php

// The header for our Pet Shop theme
// This is the template that displays all of the <head> section and everything up until <div id="content">
// 
// @package PetShop
// 
// ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <!-- Top header with logo, search and account functions -->
    <div class="top-header">
        <div class="container">
            <!-- Logo -->
         <div class="logo-container mobile-hidden">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo flex-center-column">
        <?php if (has_site_icon()) : ?>
            <img src="<?php echo esc_url(get_site_icon_url(512)); ?>" alt="<?php bloginfo('name'); ?>">
        <?php else : ?>
            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/petshop-icon.svg" alt="<?php bloginfo('name'); ?>">
        <?php endif; ?>
        <h1 class="logo-text"><?php bloginfo('name'); ?></h1>
    </a>
</div>
            
            <!-- Search Form -->
            <div class="search-container">
                <?php get_search_form(); ?>

                
                <!-- button for mobile -->
                 <div class=" category-menu-toggle mobile-only">
                 <button class="btn btn-primary btn-icon toggle-categories-btn" aria-label="Toggle Categories">
<span class="dashicons dashicons-menu"></span>
</button>
</div>

            </div>
            
            <!-- Secondary Navigation and User Actions -->
             <div class="fixed-on-mobile">
            <div class="header-actions">
          
                <!-- Secondary Navigation -->
                <nav class="secondary-navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'secondary',
                        'container'      => false,
                        'menu_class'     => 'secondary-menu',
                        'fallback_cb'    => false,
                        'depth'          => 1, 
                    ));
                    ?>
                </nav>
                
                <!-- Cart Button -->
                <!-- <a href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#'); ?>" class="btn btn-tertiary">
                    <span class="dashicons dashicons-cart"></span>
                    Cart 
                    <?php if (function_exists('WC') && WC()->cart && !WC()->cart->is_empty()): ?>
                        <span class="cart-count"><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>
                    <?php endif; ?>

                </a> -->

                <div class="cart-icon">
    <a href="<?php echo wc_get_cart_url(); ?>" class="cart-contents btn btn-tertiary">
        <span class="dashicons dashicons-cart"></span>
        Cart
        <span class="cart-contents-count "><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    </a>
</div>
                
                <!-- Login/Logout Button -->
                <?php if (is_user_logged_in()): ?>
                    <a href="<?php echo esc_url(wp_logout_url(get_permalink())); ?>" class="btn btn-primary"><span class="dashicons dashicons-admin-users"></span> Log out</a>
                <?php else: ?>
                    <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="btn btn-primary"> <span class="dashicons dashicons-admin-users"></span> Log in</a>
                <?php endif; ?>

                
            </div>
                  <!-- Logo for mobile only  -->
                     <a href="<?php echo esc_url(home_url('/')); ?>" class="mobile-logo flex-center-column mobile-only">
        <?php if (has_site_icon()) : ?>
            <img src="<?php echo esc_url(get_site_icon_url(512)); ?>" alt="<?php bloginfo('name'); ?>">
        <?php else : ?>
            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/petshop-icon.svg" alt="<?php bloginfo('name'); ?>">
        <?php endif; ?>
        <h1 class="logo-text"><?php bloginfo('name'); ?></h1>
    </a>
        </div>
        </div>
    </div>

    <!-- Main Navigation with Product Categories -->
    <nav class="main-navigation">
        <div class="container">
            <?php dynamic_sidebar('header-category-menu'); ?>
        </div>
    </nav>
</header>