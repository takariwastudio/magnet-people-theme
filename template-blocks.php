<?php
/**
 * Template Name: Bloques (ancho completo)
 * Magnet People v2 — Takariwa Studio
 *
 * Sin título ni contenedor: el contenido lo arman los bloques ACF.
 */
get_header();

while (have_posts()) : the_post();
    the_content();
endwhile;

get_footer();
