<?php
/**
 * Block: magnet/blog-grid
 * Magnet People v2 — Takariwa Studio
 *
 * Posts del blog (sin Magnet People). Muestra `bg_per_load` posts (8 = filas 3 / 3 / 2)
 * y cada clic en Load More trae otro grupo igual vía AJAX (magnet_blog_load_more en functions.php).
 *
 * Campos ACF:
 *   bg_title    — text
 *   bg_exclude  — taxonomy (category, ids) — vacío = Magnet People
 *   bg_per_load — number (default 8)
 *   bg_btn_text — text (default "Load more")
 */

$section_title = get_field('bg_title')    ?: '';
$exclude       = magnet_blog_excluded_cats(get_field('bg_exclude') ?: []);
$per_page      = intval(get_field('bg_per_load') ?: 8);
$btn_text      = get_field('bg_btn_text') ?: 'Load more';

$posts = magnet_blog_query($exclude, $per_page, 1);

$block_id    = 'blog-grid-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . esc_attr($block['className']) : '';
?>

<section id="<?php echo esc_attr($block_id); ?>"
         class="magnet-blog-grid magnet-posts-grid<?php echo $extra_class; ?>">
    <div class="magnet-posts-grid__inner">

        <?php if ($section_title) : ?>
            <h2 class="magnet-posts-grid__title"><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>

        <?php if ($posts->have_posts()) : ?>
            <div class="blog-grid__list" data-blog-grid-list>
                <?php magnet_render_blog_batch($posts, $exclude); ?>
            </div>

            <?php if ($posts->max_num_pages > 1) : ?>
                <div class="magnet-posts-grid__footer">
                    <button type="button"
                            class="magnet-posts-grid__btn magnet-blog-grid__btn"
                            data-blog-grid-more
                            data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
                            data-nonce="<?php echo esc_attr(wp_create_nonce('magnet_blog_load_more')); ?>"
                            data-exclude="<?php echo esc_attr(implode(',', $exclude)); ?>"
                            data-per-page="<?php echo esc_attr($per_page); ?>"
                            data-page="1">
                        <?php echo esc_html($btn_text); ?>
                    </button>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <p class="magnet-posts-grid__empty">No hay posts publicados aún.</p>
        <?php endif; ?>

    </div>
</section>
