<?php
/**
 * Front Page
 * El contenido lo arman los bloques ACF en el editor de Gutenberg.
 */
get_header();

while (have_posts()) : the_post();
    the_content();
endwhile;

get_footer();