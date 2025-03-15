<?php
/**
 * The template for displaying adoptable pets archive
 */

get_header();
?>

<div class="dynamic-page adoption-page">
    <div class="container">

        <h2 class="adoption-title">Pets for Adoption <div class="underline"></div></h2>
        <div class="adoption-intro">
            <p>Meet our wonderful pets looking for their forever homes. Each of these animals has been rescued and is now ready for adoption.</p>
        </div>
  

    
    <div class="adoption-grid">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <div class="adoption-card">
                    <div class="adoption-card-image">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?>
                            </a>
                        <?php else : ?>
                            <a href="<?php the_permalink(); ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pet-placeholder.jpg" alt="<?php the_title_attribute(); ?>">
                            </a>
                        <?php endif; ?>
                        
                        <?php 
                        $status = get_post_meta(get_the_ID(), '_pet_adoption_status', true);
                        if ($status && $status != 'available') : 
                        ?>
                            <div class="adoption-status adoption-status-<?php echo esc_attr($status); ?>">
                                <?php echo esc_html(ucfirst($status)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="adoption-card-content">
                        <h2 class="pet-name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        
                        <div class="pet-meta">
                            <?php
                            $gender = get_post_meta(get_the_ID(), '_pet_gender', true);
                            
                            // Get pet type (taxonomy)
                            $pet_types = get_the_terms(get_the_ID(), 'pet_type');
                            $pet_type = !empty($pet_types) ? $pet_types[0]->name : '';
                            ?>
                            
                            <?php if ($pet_type) : ?>
                                <span class="pet-type"><?php echo esc_html($pet_type); ?></span>
                            <?php endif; ?>
                            
                            <?php if ($gender) : ?>
                                <span class="pet-gender"><?php echo esc_html(ucfirst($gender)); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pet-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        
                        <div class="pet-actions">
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">Meet <?php the_title(); ?></a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            
            <div class="adoption-pagination">
                <?php the_posts_pagination(); ?>
            </div>
            
        <?php else : ?>
            <div class="no-pets-found">
                <h2>No pets currently available for adoption</h2>
                <p>Please check back soon or contact us to inquire about upcoming adoptable pets.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
</div>

<?php get_footer(); ?>



