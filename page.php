<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

<section class="page-hero">
    <div class="container container--narrow">
        <h1 class="page-hero__title"><?php the_title(); ?></h1>
    </div>
</section>

<section class="page-content-section">
    <div class="container container--narrow">
        <div class="entry-content">
            <?php the_content(); ?>
        </div>
    </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
