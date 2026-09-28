<?php get_header(); ?>

<section class="archive-hero">
    <div class="container">
        <h1 class="archive-hero__title"><?php the_archive_title(); ?></h1>
        <?php the_archive_description('<p class="archive-hero__desc">', '</p>'); ?>
    </div>
</section>

<section class="archive-section">
    <div class="container">
        <div class="archive-grid">
            <?php if (have_posts()) :
                while (have_posts()) : the_post();
                    get_template_part('template-parts/card', 'post');
                endwhile;
            else : ?>
                <p class="no-results">No se encontraron artículos.</p>
            <?php endif; ?>

            <div class="pagination">
                <?php the_posts_pagination(['mid_size' => 2]); ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
