<footer class="site-footer">
            <div class="faded-block-footer " ></div>
                   <svg class="blob-background" viewBox="0 0 1200 500" preserveAspectRatio="xMidYMid slice">
  </svg>
        <!-- <canvas id="footer-canvas" class="blob-canvas"></canvas> -->
    <!-- Top footer bar with logo and social links -->
    <div class="footer-top-block">
        <div class="container">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo flex-center">
                <?php if (has_site_icon()) : ?>
                    <img src="<?php echo esc_url(get_site_icon_url(512)); ?>" alt="<?php bloginfo('name'); ?>">
                <?php else : ?>
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/petshop-icon.svg" alt="<?php bloginfo('name'); ?>">
                <?php endif; ?>
                <h3 class="logo-text"><?php bloginfo('name'); ?></h3>
            </a>
            <div class="social-links">
                <a href="#" class="btn btn-icon btn-primary"><span class="dashicons dashicons-facebook-alt"></span></a>
                <a href="#" class="btn btn-icon btn-primary"><span class="dashicons dashicons-instagram"></span></a>
                <a href="#" class="btn btn-icon btn-primary"><span class="dashicons dashicons-twitter"></span></a>
                <a href="#" class="btn btn-icon btn-primary"><span class="dashicons dashicons-youtube"></span></a>
            </div>
        </div>
    </div>

    <!-- Main footer content -->
    <div class="footer-main">
        <div class="container">
            <div class="footer-widgets">
                <!-- About widget -->
                <div class="footer-widget footer-about">
                    <h4 class="widget-title">About Us
                        <div class="underline"></div>
                    </h4>
                    <p>We're dedicated to providing quality products and services for your beloved pets. From nutrition to toys, we have everything your furry friends need.</p>
                    <div class="contact-info">
                        <p><strong>Address:</strong> 123 Pet Avenue, Pawsville</p>
                        <p><strong>Phone:</strong> (555) 123-4567</p>
                        <p><strong>Email:</strong> info@petshop.com</p>
                    </div>
                </div>

                <!-- Quick Links widget -->
                <div class="footer-widget footer-links">
                    <h4 class="widget-title">Quick Links  <div class="underline"></div></h4>
      
                    <?php
                    wp_nav_menu(array(
                        'menu' => 'footer-menu',
                        'container' => false,
                        'menu_class' => 'footer-menu'
                    ));
                    ?>
              
                </div>

                <!-- Shop Categories widget -->
                <div class="footer-widget footer-categories">
                    <h4 class="widget-title">Shop Categories  <div class="underline"></div></h4>
                    <?php
                    wp_nav_menu(array(
                        'menu' => 'shop-categories',
                        'container' => false,
                        'menu_class' => 'footer-menu'
                    ));
                    ?>
                </div>

                <!-- Newsletter widget -->
                <div class="footer-widget footer-newsletter">
                    <h4 class="widget-title">Newsletter  <div class="underline"></div></h4>
                    <p>Subscribe to receive updates, access to exclusive deals, and more.</p>
                    <form class="footer-subscribe-form">
                        <input type="email" placeholder="Your email address" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                    <div class="payment-methods">
                        <span>We Accept:</span>
                        <div class="payment-icons">
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/payment-visa.png" alt="Visa">
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/payment-mastercard.png" alt="Mastercard">
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/payment-paypal.png" alt="PayPal">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer bottom with copyright and credits -->
    <div class="footer-bottom">
        <div class="container">
            <div class="copyright">
                <span>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All Rights Reserved.</span>
            </div>
            <div class="footer-links-secondary">
                <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a>
                <a href="<?php echo esc_url(home_url('/terms-conditions')); ?>">Terms & Conditions</a>
                <a href="<?php echo esc_url(home_url('/shipping-returns')); ?>">Shipping & Returns</a>
            </div>
            <div class="credits">
                <span>Built by <a href="https://www.dorplaut.com/" target="_blank">Dor Plaut</a></span>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>