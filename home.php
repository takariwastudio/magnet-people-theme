<?php
/**
 * Blog (Página de entradas)
 * Magnet People v2 — Takariwa Studio
 *
 * Si la página asignada en Ajustes > Lectura > "Página de entradas" tiene bloques,
 * se renderizan esos bloques ACF (Hero, People Grid, Image Banner…) en lugar del
 * listado por defecto. Sin bloques, cae al listado de index.php.
 */
$blog_page_id = (int) get_option('page_for_posts');

if (!$blog_page_id || !has_blocks($blog_page_id)) {
    get_template_part('index');
    return;
}

get_header();

echo apply_filters('the_content', get_post_field('post_content', $blog_page_id));

get_footer();
