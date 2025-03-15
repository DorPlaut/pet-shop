<?php get_header(); ?>

<main id="primary" class="site-main">
    <!-- Hero Section -->
     <?php get_template_part('template-parts/hero'); ?>
     <!-- second section - carousel and newsletter -->
      <div class="container carousel-plus-newsletter">  
          <!-- newsletter -->
          <?php get_template_part('template-parts/newsletter'); ?>
         <!-- product carusel -->

     <?php pet_shop_display_product_carousel(array(
    'title' => 'Featured Products',
    'description' => 'Check out our top products for your furry friends',
    'count' => 8,
    'product_type' => 'featured'
)); ?>            
            
        </div>
  

<!--  -->

<?php pet_shop_display_adoption_carousel(array(
    'title' => 'Pets Looking for a Home',
    'description' => 'Meet our wonderful pets who need loving families',
    'count' => 6
));
?>


<!-- <?php
    // Start the Loop
    while (have_posts()) :
        the_post();
        ?>
        
        <section class="home-page">
            <div class="container">
                <h1><?php the_title(); ?></h1>
                
                <div class="page-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </section>
        
        <?php
    endwhile; // End of the loop.
    ?> -->

<!-- Additional static sections can go here -->

</main>

<?php get_footer(); ?>