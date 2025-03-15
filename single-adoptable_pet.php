<?php
/**
 * The template for displaying single adoptable pet
 */

get_header();

while (have_posts()) : the_post();
    // Get pet details
    $gender = get_post_meta(get_the_ID(), '_pet_gender', true);
    $adoption_status = get_post_meta(get_the_ID(), '_pet_adoption_status', true) ?: 'available';
    
    // Get pet type (taxonomy)
    $pet_types = get_the_terms(get_the_ID(), 'pet_type');
    $pet_type = !empty($pet_types) ? $pet_types[0]->name : '';
?>

<div class="dynamic-page adoption-single">
    <div class="container">
        <nav class="breadcrumbs-nav">
            <a href="<?php echo home_url(); ?>">Home</a> &gt; 
            <a href="<?php echo get_post_type_archive_link('adoptable_pet'); ?>">Adoptable Pets</a> &gt; 
            <span><?php the_title(); ?></span>
        </nav>
        
        <div class="adoption-single-header">
            <h1 class="pet-name"><?php the_title(); ?></h1>
            
            <?php if ($adoption_status && $adoption_status != 'available') : ?>
                <div class="adoption-status adoption-status-<?php echo esc_attr($adoption_status); ?>">
                    <?php echo esc_html(ucfirst($adoption_status)); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="adoption-single-content">
            <div class="pet-gallery">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="pet-main-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="pet-details">
                <div class="pet-summary">
                    <ul class="pet-attributes">
                        <?php if ($pet_type) : ?>
                            <li>
                                <span class="attribute-label">Type:</span>
                                <span class="attribute-value"><?php echo esc_html($pet_type); ?></span>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($gender) : ?>
                            <li>
                                <span class="attribute-label">Gender:</span>
                                <span class="attribute-value"><?php echo esc_html(ucfirst($gender)); ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                    
                    <?php if ($adoption_status == 'available') : ?>
                        <div class="adoption-cta">
                            <a href="#adoption-inquiry" class="btn btn-primary">Adopt <?php the_title(); ?></a>
                            
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="pet-description">
                    <h2>About <?php the_title(); ?></h2>
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
        
        <?php if ($adoption_status == 'available') : ?>
            <div id="adoption-inquiry" class="adoption-inquiry">
                <h2>Adoption Inquiry</h2>
                <p>Interested in adopting <?php the_title(); ?>? Fill out this form and we'll get back to you soon.</p>
                
                <?php
                // If using Contact Form 7, you can add a shortcode like this:
                if (shortcode_exists('contact-form-7')) {
                    // Replace FORM_ID with your actual form ID
                    echo do_shortcode('[contact-form-7 id="FORM_ID" title="Adoption Inquiry"]');
                } else {
                    // Basic form if Contact Form 7 isn't available
                    ?>
                    <form class="adoption-form" action="#" method="post">
                        <input type="hidden" name="pet_id" value="<?php echo get_the_ID(); ?>">
                        <input type="hidden" name="pet_name" value="<?php echo esc_attr(get_the_title()); ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Your Name *</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="questions">Questions or Comments</label>
                            <textarea id="questions" name="questions" rows="3"></textarea>
                        </div>
                        
                        <div class="form-group form-submit">
                            <button type="submit" class="btn btn-primary">Submit Inquiry</button>
                        </div>
                    </form>
                    <?php
                }
                ?>
            </div>
        <?php endif; ?>
        
        <div class="more-pets">
            <h3>More Pets for Adoption</h3>
            
            <?php
            // Query for more pets
            $more_pets_args = array(
                'post_type' => 'adoptable_pet',
                'posts_per_page' => 3,
                'post__not_in' => array(get_the_ID()),
                'orderby' => 'rand',
                'meta_query' => array(
                    array(
                        'key' => '_pet_adoption_status',
                        'value' => 'available',
                        'compare' => '='
                    )
                )
            );
            
            $more_pets = new WP_Query($more_pets_args);
            
            if ($more_pets->have_posts()) :
            ?>
                <div class="more-pets-grid">
                    <?php while ($more_pets->have_posts()) : $more_pets->the_post(); ?>
                        <div class="more-pet-card">
                            <div class="more-pet-image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <div class="more-pet-content">
                                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                
                                <?php
                                $more_pet_gender = get_post_meta(get_the_ID(), '_pet_gender', true);
                                $more_pet_types = get_the_terms(get_the_ID(), 'pet_type');
                                $more_pet_type = !empty($more_pet_types) ? $more_pet_types[0]->name : '';
                                ?>
                                
                                <div class="more-pet-meta">
                                    <?php if ($more_pet_type) : ?>
                                        <span class="more-pet-type"><?php echo esc_html($more_pet_type); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if ($more_pet_gender) : ?>
                                        <span class="more-pet-gender"><?php echo esc_html(ucfirst($more_pet_gender)); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="view-all-pets">
                    <a href="<?php echo esc_url(get_post_type_archive_link('adoptable_pet')); ?>" class="btn btn-primary">View All Adoptable Pets</a>
                </div>
            <?php 
            else :
                echo '<p>No other pets currently available for adoption.</p>';
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</div>

<?php 
endwhile;
get_footer(); 
?>