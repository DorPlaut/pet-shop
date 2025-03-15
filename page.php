<?php get_header(); ?>

<main id="primary" class="site-main">

    <?php
    // Start the Loop
    while (have_posts()) :
        the_post();
        ?>
        
        <section class="dynamic-page">
            <div class="container">
                <h2 class="page-headline"><?php the_title(); ?>
            <div class="underline"></div></h2>
                
                <div class="page-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </section>
        
    <?php
    endwhile;
    ?>

    
</main>

<?php get_footer(); ?>