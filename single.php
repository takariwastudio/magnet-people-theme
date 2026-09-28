<?php get_header(); ?>

<?php while (have_posts()) : the_post();
    $is_locked = magnet_is_paid_post(get_the_ID());
    $cats = get_the_category();
?>

<section class="single-hero">
    <div class="container container--narrow">
        <div class="single-hero__meta">
            <?php if ($cats) : ?>
                <span class="single-hero__tag"><?php echo esc_html($cats[0]->name); ?></span>
            <?php endif; ?>
            <time class="single-hero__date" datetime="<?php the_date('c'); ?>"><?php the_date(); ?></time>
        </div>
        <h1 class="single-hero__title"><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?>
            <p class="single-hero__excerpt"><?php the_excerpt(); ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="single-content">
    <div class="container container--narrow">

        <?php if (has_post_thumbnail()) : ?>
            <div class="single-thumbnail">
                <?php the_post_thumbnail('full'); ?>
            </div>
        <?php endif; ?>

        <?php if ($is_locked) : ?>
            <!-- Contenido bloqueado por PMPro -->
            <div class="pmpro-paywall">
                <div><?php echo magnet_icon('lock'); ?></div>
                <h3>Contenido exclusivo para miembros</h3>
                <p>Únete a Magnet People para acceder a todo el contenido exclusivo.</p>
                <a href="<?php echo esc_url(home_url('/membresia/')); ?>" class="btn btn--red">Join Us!</a>
            </div>
        <?php else : ?>
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
