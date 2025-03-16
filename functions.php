<?php
/**
 * Pet Shop Theme Functions
 *
 * This file contains the core functionality for the Pet Shop theme.
 */

/**
 * Theme Setup
 * 
 * Register theme features and supports
 */
function pet_shop_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary'   => __('Primary Menu', 'pet-shop'),
        'secondary' => __('Secondary Menu', 'pet-shop'),
        'footer'    => __('Footer Menu', 'pet-shop')
    ));
    
    // WooCommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'pet_shop_setup');

/**
 * Enqueue Styles and Scripts
 * 
 * Load CSS and JS files for the theme
 */
function pet_shop_scripts() {
    // Main stylesheet
    wp_enqueue_style('pet-shop-style', get_stylesheet_uri(), array(), '1.0.0');
    
    // Theme CSS files
    wp_enqueue_style('pet-shop-main', get_template_directory_uri() . '/assets/css/main.css', array(), '1.0.0');
    wp_enqueue_style('pet-shop-shop', get_template_directory_uri() . '/assets/css/shop.css', array(), '1.0.0');
    wp_enqueue_style('pet-shop-posts', get_template_directory_uri() . '/assets/css/posts.css', array(), '1.0.0');
    
    // Fonts
    wp_enqueue_style('pet-shop-fonts', 'https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nunito:wght@300;400;600;700&display=swap', array(), null);
    
    // WordPress dashicons
    wp_enqueue_style('dashicons');
    
    // Theme JS files
    wp_enqueue_script('pet-shop-custom', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('pet-shop-cart-handler', get_template_directory_uri() . '/assets/js/cart-handler.js', array('jquery', 'wc-add-to-cart'), '1.0.0', true);
    
    // Third-party libraries
    wp_enqueue_script('three-js', 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js', array(), '128', true);


    // Enqueue GSAP from CDN
    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', array(), '3.12.5', true);


    
    // Theme-specific scripts

    wp_enqueue_script('blob-background', get_template_directory_uri() . '/assets/js/blob-background.js', array('gsap'), '1.0.0', true);


    // wp_enqueue_script('blob-shader', get_template_directory_uri() . '/assets/js/blob-shader.js', array('three-js'), '1.0.0', true);

    wp_enqueue_script('pet-shop-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), '1.0.0', true);
    
    // Pass PHP variables to JavaScript
    wp_localize_script('pet-shop-navigation', 'petShopData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'homeUrl' => home_url(),
    ));
}
add_action('wp_enqueue_scripts', 'pet_shop_scripts');

/**
 * Slider Scripts
 * 
 * Enqueue slider scripts only on pages that need them
 */
function pet_shop_enqueue_slider_scripts() {
    // Only enqueue on pages that need sliders
    if (is_front_page() || is_home() || is_shop() || is_product_category() || is_singular('adoptable_pet') || is_post_type_archive('adoptable_pet')) {
        // Slick Slider CSS
        wp_enqueue_style('slick-slider', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
        wp_enqueue_style('slick-slider-theme', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
        
        // Slick Slider JS
        wp_enqueue_script('slick-slider', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), '1.8.1', true);
        
        // Custom initialization script
        wp_enqueue_script('pet-shop-sliders', get_template_directory_uri() . '/assets/js/pet-shop-sliders.js', array('slick-slider'), '1.0.0', true);
    }
}
add_action('wp_enqueue_scripts', 'pet_shop_enqueue_slider_scripts');

/**
 * WooCommerce Cart Fragments
 * 
 * Ensure cart count updates dynamically via AJAX
 */
function pet_shop_cart_count_fragments($fragments) {
    $cart_count = WC()->cart->get_cart_contents_count();
    
    ob_start();
    ?>
    <span class="cart-contents-count"><?php echo esc_html($cart_count); ?></span>
    <?php
    $fragments['.cart-contents-count'] = ob_get_clean();
    
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'pet_shop_cart_count_fragments');

/**
 * Widget Areas
 * 
 * Register widget areas for the theme
 */
function pet_shop_widgets_init() {
    register_sidebar(array(
        'name'          => __('Header Category Menu', 'pet-shop'),
        'id'            => 'header-category-menu',
        'description'   => __('Add widgets here to appear in your header category menu.', 'pet-shop'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'pet_shop_widgets_init');

/**
 * Theme Customizer
 * 
 * Add theme options to the WordPress Customizer
 */
function pet_shop_customizer_settings($wp_customize) {
    // Hero Section
    $wp_customize->add_section('hero_section', array(
        'title'    => __('Hero Section', 'pet-shop'),
        'priority' => 30,
    ));
    
    // Hero Title
    $wp_customize->add_setting('hero_title', array(
        'default'           => 'Welcome to Our Pet Shop',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_title', array(
        'label'    => __('Hero Title', 'pet-shop'),
        'section'  => 'hero_section',
        'type'     => 'text',
    ));
    
    // Hero Subtitle
    $wp_customize->add_setting('hero_subtitle', array(
        'default'           => 'Find the perfect companion for your family',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_subtitle', array(
        'label'    => __('Hero Subtitle', 'pet-shop'),
        'section'  => 'hero_section',
        'type'     => 'text',
    ));

    // Hero Text
    $wp_customize->add_setting('hero_text', array(
        'default'           => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ut purus eget sapien.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_text', array(
        'label'    => __('Hero Text', 'pet-shop'),
        'section'  => 'hero_section',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'pet_shop_customizer_settings');

/**
 * Plugin Compatibility
 * 
 * Adjustments for plugin compatibility
 */
function disable_jetpack_dns_prefetch($urls) {
    return array();
}
add_filter('wp_resource_hints', 'disable_jetpack_dns_prefetch', 10, 2);

function fix_jetpack_local_dns_issue() {
    add_filter('jetpack_is_managed_platform', '__return_false', 999);
    add_filter('jetpack_is_staging_site', '__return_false', 999);
}
add_action('plugins_loaded', 'fix_jetpack_local_dns_issue');

/**
 * Helper Functions
 * 
 * Utility functions for template parts
 */
function pet_shop_display_product_carousel($args = array()) {
    get_template_part('template-parts/product-carousel', null, $args);
}

function pet_shop_display_adoption_carousel($args = array()) {
    get_template_part('template-parts/adoption-carousel', null, $args);
}

/**
 * Custom Post Types
 * 
 * Register custom post types for the theme
 */
function pet_shop_register_adoptable_pets() {
    $labels = array(
        'name'               => 'Adoptable Pets',
        'singular_name'      => 'Adoptable Pet',
        'menu_name'          => 'Adoptable Pets',
        'add_new'            => 'Add New Pet',
        'add_new_item'       => 'Add New Adoptable Pet',
        'edit_item'          => 'Edit Adoptable Pet',
        'new_item'           => 'New Adoptable Pet',
        'view_item'          => 'View Adoptable Pet',
        'search_items'       => 'Search Adoptable Pets',
        'not_found'          => 'No adoptable pets found',
        'not_found_in_trash' => 'No adoptable pets found in trash',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'adoptable-pets'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-heart',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
    );

    register_post_type('adoptable_pet', $args);

    // Register Pet Type Taxonomy
    $taxonomy_labels = array(
        'name'              => 'Pet Types',
        'singular_name'     => 'Pet Type',
        'search_items'      => 'Search Pet Types',
        'all_items'         => 'All Pet Types',
        'parent_item'       => 'Parent Pet Type',
        'parent_item_colon' => 'Parent Pet Type:',
        'edit_item'         => 'Edit Pet Type',
        'update_item'       => 'Update Pet Type',
        'add_new_item'      => 'Add New Pet Type',
        'new_item_name'     => 'New Pet Type Name',
        'menu_name'         => 'Pet Types',
    );

    $taxonomy_args = array(
        'hierarchical'      => true,
        'labels'            => $taxonomy_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'pet-type'),
    );

    register_taxonomy('pet_type', array('adoptable_pet'), $taxonomy_args);
}
add_action('init', 'pet_shop_register_adoptable_pets');

/**
 * Custom Meta Boxes
 * 
 * Add custom meta boxes for adoptable pets
 */
function pet_shop_add_gender_meta_box() {
    add_meta_box(
        'pet_gender_meta_box',
        'Pet Gender',
        'pet_shop_gender_meta_box_html',
        'adoptable_pet',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'pet_shop_add_gender_meta_box');

function pet_shop_gender_meta_box_html($post) {
    wp_nonce_field('pet_shop_gender_meta_box', 'pet_shop_gender_meta_box_nonce');
    $gender = get_post_meta($post->ID, '_pet_gender', true);
    ?>
    <select name="pet_gender" id="pet_gender" style="width: 100%;">
        <option value="">Select Gender</option>
        <option value="male" <?php selected($gender, 'male'); ?>>Male</option>
        <option value="female" <?php selected($gender, 'female'); ?>>Female</option>
    </select>
    <?php
}

function pet_shop_save_gender_meta_box($post_id) {
    if (!isset($_POST['pet_shop_gender_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['pet_shop_gender_meta_box_nonce'], 'pet_shop_gender_meta_box') ||
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
        !current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['pet_gender'])) {
        update_post_meta($post_id, '_pet_gender', sanitize_text_field($_POST['pet_gender']));
    }
}
add_action('save_post', 'pet_shop_save_gender_meta_box');

function pet_shop_add_adoption_status_meta_box() {
    add_meta_box(
        'pet_adoption_status_meta_box',
        'Adoption Status',
        'pet_shop_adoption_status_meta_box_html',
        'adoptable_pet',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'pet_shop_add_adoption_status_meta_box');

function pet_shop_adoption_status_meta_box_html($post) {
    wp_nonce_field('pet_shop_adoption_status_meta_box', 'pet_shop_adoption_status_meta_box_nonce');
    $status = get_post_meta($post->ID, '_pet_adoption_status', true) ?: 'available';
    ?>
    <select name="pet_adoption_status" id="pet_adoption_status" style="width: 100%;">
        <option value="available" <?php selected($status, 'available'); ?>>Available</option>
        <option value="pending" <?php selected($status, 'pending'); ?>>Pending</option>
        <option value="adopted" <?php selected($status, 'adopted'); ?>>Adopted</option>
    </select>
    <?php
}

function pet_shop_save_adoption_status_meta_box($post_id) {
    if (!isset($_POST['pet_shop_adoption_status_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['pet_shop_adoption_status_meta_box_nonce'], 'pet_shop_adoption_status_meta_box') ||
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
        !current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['pet_adoption_status'])) {
        update_post_meta($post_id, '_pet_adoption_status', sanitize_text_field($_POST['pet_adoption_status']));
    }
}
add_action('save_post', 'pet_shop_save_adoption_status_meta_box');