<?php
/**
 * footer.php — Magnet People v2
 * Takariwa Studio
 */
?>

<footer class="site-footer">
    <div class="container">

        <div class="site-footer__top">

            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo" aria-label="<?php bloginfo('name'); ?>">
                <?php if (has_custom_logo()) : the_custom_logo();
                else : ?>magnet<?php endif; ?>
            </a>

            <!-- Nav footer -->
            <nav class="footer-nav" aria-label="Menú footer">
                <?php wp_nav_menu([
                    'theme_location' => 'footer',
                    'menu_class'     => 'footer-nav__links',
                    'container'      => false,
                    'depth'          => 1,
                    'fallback_cb'    => function() { ?>
                        <ul class="footer-nav__links">
                            <li><a href="<?php echo home_url('/about/'); ?>">About</a></li>
                            <li><a href="<?php echo home_url('/servicios/'); ?>">Services</a></li>
                            <li><a href="<?php echo home_url('/blog/'); ?>">Blog</a></li>
                            <li><a href="<?php echo home_url('/membresia/'); ?>">Get Access</a></li>
                            <li><a href="<?php echo home_url('/contacto/'); ?>">Let's Talk</a></li>
                        </ul>
                    <?php },
                ]); ?>
            </nav>

            <!-- Social -->
            <div class="footer-social">
                <div class="footer-social__links">
                    <a href="https://facebook.com/magnetpeople" target="_blank" rel="noopener">
                        <?php echo magnet_icon('facebook'); ?>
                    </a>
                    <a href="https://instagram.com/magnetpeople" target="_blank" rel="noopener">
                        <?php echo magnet_icon('instagram'); ?>
                    </a>
                    <a href="https://linkedin.com/company/magnetpeople" target="_blank" rel="noopener">
                        <?php echo magnet_icon('linkedin'); ?>
                    </a>
                </div>
            </div>

        </div>

        <div class="site-footer__bottom">
            <p class="site-footer__copy">
                Magnet &copy; <?php echo date('Y'); ?> - All Right Reserved.
            </p>
        </div>

    </div>
</footer>

<?php get_template_part('signup-modal'); ?>
<?php wp_footer(); ?>
</body>
</html>