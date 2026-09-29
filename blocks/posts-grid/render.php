<?php
/**
 * Block: magnet/posts-grid
 * Campos ACF:
 *   grid_title    — text
 *   grid_count    — number (default 3)
 *   grid_category — taxonomy (category, object)
 *   grid_btn_text — text
 *   grid_btn_url  — url
 */

$section_title = get_field('grid_title')    ?: '';
$count         = get_field('grid_count')    ?: 3;
$category      = get_field('grid_category');
$btn_text      = get_field('grid_btn_text') ?: 'Ver todos';
$btn_url       = get_field('grid_btn_url')  ?: home_url('/blog/');

$query_args = [
    'post_type'      => 'post',
    'posts_per_page' => intval($count),
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
];
if (!empty($category) && isset($category->term_id)) {
    $query_args['cat'] = $category->term_id;
}

$posts = new WP_Query($query_args);

$block_id    = 'posts-grid-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . $block['className'] : '';
?>

<section id="<?php echo esc_attr($block_id); ?>"
         class="magnet-posts-grid<?php echo esc_attr($extra_class); ?>">
    <div class="container">

        <?php if ($section_title) : ?>
            <h2 class="magnet-posts-grid__title"><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>

        <?php if ($posts->have_posts()) : ?>
            <div class="magnet-posts-grid__grid">
                <?php while ($posts->have_posts()) : $posts->the_post();
                    $post_id   = get_the_ID();
                    $is_paid   = magnet_is_paid_post($post_id);
                    $thumb     = get_the_post_thumbnail_url($post_id, 'card-wide');
                    $excerpt   = get_the_excerpt();
                    $cat_obj   = get_the_category();
                    $cat_name  = !empty($cat_obj) ? $cat_obj[0]->name : '';
                ?>
                    <article class="post-card<?php echo $is_paid ? ' post-card--locked' : ''; ?>">
                        <a href="<?php echo $is_paid ? esc_url(magnet_get_join_url()) : esc_url(get_permalink()); ?>"
                           class="post-card__thumb-link">
                            <?php if ($thumb) : ?>
                                <img class="post-card__thumb"
                                     src="<?php echo esc_url($thumb); ?>"
                                     alt="<?php the_title_attribute(); ?>"
                                     loading="lazy">
                            <?php else : ?>
                                <div class="post-card__thumb post-card__thumb--placeholder"></div>
                            <?php endif; ?>
                            <?php if ($is_paid) : ?>
                                <span class="post-card__lock">
                                    <?php echo magnet_icon('lock', 'post-card__lock-icon'); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <div class="post-card__body">
                            <?php if ($cat_name) : ?>
                                <span class="post-card__cat"><?php echo esc_html($cat_name); ?></span>
                            <?php endif; ?>
                            <h3 class="post-card__title">
                                <a href="<?php echo $is_paid ? esc_url(magnet_get_join_url()) : esc_url(get_permalink()); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h3>
                            <?php if ($excerpt && !$is_paid) : ?>
                                <p class="post-card__excerpt"><?php echo esc_html($excerpt); ?></p>
                            <?php endif; ?>
                            <div class="post-card__actions">
                                <?php if ($is_paid) : ?>
                                    <a href="<?php echo esc_url(magnet_get_join_url()); ?>" class="btn btn--red btn--sm">
                                        ¡Únete!
                                    </a>
                                <?php else : ?>
                                    <a href="<?php echo esc_url(get_permalink()); ?>" class="btn btn--black btn--sm">
                                        Leer más
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p class="magnet-posts-grid__empty">No hay posts publicados aún.</p>
        <?php endif; ?>

        <?php if ($btn_text && $btn_url) : ?>
            <div class="magnet-posts-grid__footer">
                <a href="<?php echo esc_url($btn_url); ?>" class="btn btn--outline">
                    <?php echo esc_html($btn_text); ?>
                </a>
            </div>
        <?php endif; ?>

    </div>
</section>