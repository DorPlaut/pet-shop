<?php
/**
 * Template part for displaying product carousel
 *
 * @package PetShop
 */

// Parameters
$title = isset($args['title']) ? $args['title'] : 'Featured Products';
$description = isset($args['description']) ? $args['description'] : 'Check out our featured products for your pets.';
$products_count = isset($args['count']) ? intval($args['count']) : 8;
$product_type = isset($args['product_type']) ? $args['product_type'] : 'featured'; // featured, best_selling, sale, new
$category_ids = isset($args['category_ids']) ? $args['category_ids'] : '';

// Query args
$args = array(
    'post_type' => 'product',
    'posts_per_page' => $products_count,
    'post_status' => 'publish',
);

// Filter by product type
switch ($product_type) {
    case 'featured':
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_visibility',
                'field' => 'name',
                'terms' => 'featured',
                'operator' => 'IN',
            ),
        );
        break;
    
    case 'best_selling':
        $args['meta_key'] = 'total_sales';
        $args['orderby'] = 'meta_value_num';
        break;
        
    case 'sale':
        $args['meta_query'] = array(
            'relation' => 'OR',
            array( // Simple products
                'key' => '_sale_price',
                'value' => 0,
                'compare' => '>',
                'type' => 'numeric'
            ),
            array( // Variable products
                'key' => '_min_variation_sale_price',
                'value' => 0,
                'compare' => '>',
                'type' => 'numeric'
            )
        );
        break;
        
    case 'new':
        $args['orderby'] = 'date';
        break;
}

// Filter by category if specified
if (!empty($category_ids)) {
    $args['tax_query'][] = array(
        'taxonomy' => 'product_cat',
        'field' => 'term_id',
        'terms' => explode(',', $category_ids),
    );
}

// Run the query
$products = new WP_Query($args);

if ($products->have_posts()) :
?>

    <section class="product-carousel-section flex-center-column">
        <h2 class="section-title "><?php echo esc_html($title); ?> <div class="underline"></div></h2>
        <div class="container carousel-container">
        
        <div class="product-carousel">
            <?php while ($products->have_posts()) : $products->the_post(); 
                global $product;
            ?>
                <div class="product-slide">
                    <li <?php wc_product_class('', $product); ?>>
                        <?php
                        // Sale flash
                        woocommerce_show_product_loop_sale_flash();
                        
                        // Product image
                        woocommerce_template_loop_product_link_open();
                        woocommerce_template_loop_product_thumbnail();
                        woocommerce_template_loop_product_link_close();
                        
                        // Product title
                        ?> <a href="<?php the_permalink(); ?>" class="product-title-link">
                            <?php woocommerce_template_loop_product_title(); ?>
                        </a>
                        <?php
                        
                        // Product price
                        woocommerce_template_loop_price();
                        
                        // Add to cart button
                        woocommerce_template_loop_add_to_cart();
                        ?>
                    </li>
                </div>
            <?php endwhile; ?>
        </div>
        
        <!-- <div class="section-footer">
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn-secondary">View All Products</a>
        </div> -->
    </div>
</section>
<?php 
endif;
wp_reset_postdata();
?>