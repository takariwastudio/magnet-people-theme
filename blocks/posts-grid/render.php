<?php
/**
 * Block: magnet/posts-grid
 * Magnet People v2 — Takariwa Studio
 *
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
$btn_text      = get_field('grid_btn_text') ?: 'More';
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
$extra_class = isset($block['className']) ? ' ' . esc_attr($block['className']) : '';
?>

<section id="<?php echo esc_attr($block_id); ?>"
         class="magnet-posts-grid<?php echo $extra_class; ?>">
    <div class="magnet-posts-grid__inner">

        <?php if ($section_title) : ?>
            <h2 class="magnet-posts-grid__title"><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>

        <?php if ($posts->have_posts()) : ?>
            <div class="magnet-posts-grid__grid">
                <?php while ($posts->have_posts()) : $posts->the_post();
                    magnet_render_post_card();
                endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p class="magnet-posts-grid__empty">No hay posts publicados aún.</p>
        <?php endif; ?>

        <?php if ($btn_text && $btn_url) : ?>
            <div class="magnet-posts-grid__footer">
                <a href="<?php echo esc_url($btn_url); ?>" class="magnet-posts-grid__btn">
                    <?php echo esc_html($btn_text); ?>
                </a>
            </div>
        <?php endif; ?>

    </div>
</section>