<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- HEADER -->
<header class="site-header" id="site-header">
    <div class="site-header__inner">

        <!-- Logo -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" aria-label="<?php bloginfo('name'); ?>">
            <?php if (has_custom_logo()) : the_custom_logo();
            else : ?><span>magnet</span><?php endif; ?>
        </a>

        <!-- Nav principal -->
        <nav class="site-nav" aria-label="Menú principal">
            <?php wp_nav_menu([
                'theme_location' => 'primary',
                'menu_class'     => 'site-nav__list',
                'container'      => false,
                'fallback_cb'    => function() { ?>
                    <ul class="site-nav__list">
                        <li><a href="<?php echo home_url('/about/'); ?>">About</a></li>
                        <li><a href="<?php echo home_url('/magnet-people/'); ?>">Magnet People</a></li>
                        <li><a href="<?php echo home_url('/membresia/'); ?>">Join Us!</a></li>
                        <li><a href="<?php echo home_url('/blog/'); ?>">Blog</a></li>
                        <li><a href="<?php echo home_url('/contacto/'); ?>">Let's Talk!</a></li>
                    </ul>
                <?php },
            ]); ?>
        </nav>

        <!-- Acciones -->
        <div class="site-header__actions">
            <?php if (is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(home_url('/cuenta/')); ?>" class="btn btn--ghost">Mi cuenta</a>
                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="btn btn--ghost">Sign out</a>
            <?php else : ?>
                <a href="<?php echo esc_url(wp_login_url()); ?>" class="btn btn--ghost">Sign in</a>
                <a href="<?php echo esc_url(home_url('/membresia/')); ?>" class="btn btn--black">Sign up</a>
            <?php endif; ?>
        </div>

        <!-- Toggle mobile -->
        <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="mobile-nav">
            <span></span><span></span><span></span>
        </button>

    </div>
</header>

<!-- MOBILE NAV -->
<nav class="mobile-nav" id="mobile-nav" aria-label="Menú móvil">
    <a href="<?php echo home_url('/about/'); ?>">About</a>
    <a href="<?php echo home_url('/magnet-people/'); ?>">Magnet People</a>
    <a href="<?php echo home_url('/membresia/'); ?>">Join Us!</a>
    <a href="<?php echo home_url('/blog/'); ?>">Blog</a>
    <a href="<?php echo home_url('/contacto/'); ?>">Let's Talk!</a>
    <?php if (is_user_logged_in()) : ?>
        <a href="<?php echo esc_url(home_url('/cuenta/')); ?>" class="btn btn--ghost">Mi cuenta</a>
        <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="btn btn--ghost">Sign out</a>
    <?php else : ?>
        <a href="<?php echo esc_url(wp_login_url()); ?>" class="btn btn--ghost">Sign in</a>
        <a href="<?php echo esc_url(home_url('/membresia/')); ?>" class="btn btn--red">Sign up</a>
    <?php endif; ?>
</nav>

<!-- BOTÓN FLOTANTE SIGNUP -->
<?php if (!is_user_logged_in()) : ?>
<a href="<?php echo esc_url(home_url('/membresia/')); ?>" class="signup-float" aria-label="Únete a Magnet People">
    <?php echo magnet_icon('user'); ?>
    <span>Signup</span>
</a>
<?php endif; ?>
