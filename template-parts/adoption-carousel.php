<?php
/**
 * Template part for displaying adoptable pets carousel
 *
 * @package PetShop
 */

// Parameters
$title = isset($args['title']) ? $args['title'] : 'Pets for Adoption';
$description = isset($args['description']) ? $args['description'] : 'Meet our wonderful pets who are looking for their forever homes.';
$pets_count = isset($args['count']) ? intval($args['count']) : 6;
$pet_types = isset($args['pet_types']) ? $args['pet_types'] : '';

// Query args
$args = array(
    'post_type' => 'adoptable_pet',
    'posts_per_page' => $pets_count,
    'orderby' => 'date',
    'order' => 'DESC',
    'meta_query' => array(
        array(
            'key' => '_pet_adoption_status',
            'value' => 'available',
            'compare' => '='
        )
    )
);

// Filter by pet types if specified
if (!empty($pet_types)) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'pet_type',
            'field' => 'slug',
            'terms' => explode(',', $pet_types),
        )
    );
}

// Run the query
$adoptable_pets = new WP_Query($args);

if ($adoptable_pets->have_posts()) :
?>

        <section class="adoption-carousel-section flex-center-column">
            <h2 class="section-title "><?php echo esc_html($title); ?> <div class="underline"></div></h2>
            <div class="container">

        
        <div class="adoption-carousel">
            <?php while ($adoptable_pets->have_posts()) : $adoptable_pets->the_post(); ?>
                <div class="adoption-slide">
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
                        </div>
                        
                        <div class="adoption-card-content">
                            <h3 class="pet-name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            
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
                </div>
            <?php endwhile; ?>
        </div>
        
        <div class="section-buttons">
            <a href="<?php echo esc_url(get_post_type_archive_link('adoptable_pet')); ?>" class="btn btn-secondary">View All Adoptable Pets</a>
        </div>
    </div>
</section>
<?php 
endif;
wp_reset_postdata();
?>