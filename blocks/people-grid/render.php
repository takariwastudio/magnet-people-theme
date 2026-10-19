<?php
/**
 * Block: magnet/people-grid
 * Magnet People v2 — Takariwa Studio
 *
 * Grilla de 3 columnas. Muestra `pg_rows` filas y cada clic en Load More
 * trae `pg_rows` filas más vía AJAX (magnet_people_load_more en functions.php).
 *
 * Campos ACF:
 *   pg_title    — text
 *   pg_category — taxonomy (category, id)
 *   pg_rows     — number (default 3)
 *   pg_btn_text — text (default "Load More")
 */

$columns       = 3;
$section_title = get_field('pg_title')    ?: '';
$category_id   = intval(get_field('pg_category') ?: 0);
$rows          = intval(get_field('pg_rows') ?: 3);
$btn_text      = get_field('pg_btn_text') ?: 'Load More';
$per_page      = $rows * $columns;

$posts = magnet_people_query($category_id, $per_page, 1);

$block_id    = 'people-grid-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . esc_attr($block['className']) : '';
?>

<section id="<?php echo esc_attr($block_id); ?>"
         class="magnet-people-grid magnet-posts-grid<?php echo $extra_class; ?>">
    <div class="magnet-posts-grid__inner">

        <?php if ($section_title) : ?>
            <h2 class="magnet-posts-grid__title"><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>

        <?php if ($posts->have_posts()) : ?>
            <div class="magnet-posts-grid__grid" data-people-grid-list>
                <?php while ($posts->have_posts()) : $posts->the_post();
                    magnet_render_post_card();
                endwhile; wp_reset_postdata(); ?>
            </div>

            <?php if ($posts->max_num_pages > 1) : ?>
                <div class="magnet-posts-grid__footer">
                    <button type="button"
                            class="magnet-posts-grid__btn magnet-people-grid__btn"
                            data-people-grid-more
                            data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
                            data-nonce="<?php echo esc_attr(wp_create_nonce('magnet_people_load_more')); ?>"
                            data-category="<?php echo esc_attr($category_id); ?>"
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
