<?php
/**
 * Magnet People — functions.php v2
 * Takariwa Studio
 */

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption']);
    add_theme_support('align-wide');
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
    ]);
    add_image_size('card-square', 600, 600, true);
    add_image_size('card-wide', 800, 450, true);

    register_nav_menus([
        'primary' => __('Menú Principal', 'magnet-people'),
        'footer'  => __('Menú Footer', 'magnet-people'),
    ]);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'magnet-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Nunito+Sans:ital,opsz,wght@0,6..12,400;0,6..12,600;0,6..12,700;1,6..12,400&family=Nunito:wght@600&display=swap',
        [],
        null
    );
    wp_enqueue_style(
        'magnet-main',
        get_template_directory_uri() . '/assets/css/main.css',
        ['magnet-fonts'],
        '2.0.0'
    );
    wp_enqueue_script(
        'magnet-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        '2.0.0',
        true
    );
    wp_localize_script('magnet-main', 'magnetData', [
        'joinUrl'  => home_url('/membresia/'),
        'loginUrl' => wp_login_url(),
    ]);
});

add_filter('excerpt_length', fn() => 20);
add_filter('excerpt_more', fn() => '');

function magnet_is_paid_post($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    if (!function_exists('pmpro_has_membership_access')) return false;
    return !pmpro_has_membership_access($post_id);
}

function magnet_get_join_url() {
    return home_url('/membresia/');
}

// ── ACF Blocks ──
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;

    add_filter('block_categories_all', function ($cats) {
        array_unshift($cats, [
            'slug'  => 'magnet',
            'title' => 'Magnet People',
            'icon'  => 'layout',
        ]);
        return $cats;
    });

    $blocks = [
        'hero'          => 'Magnet Hero',
        'about-section' => 'Magnet About Section',
        'posts-grid'    => 'Magnet Posts Grid',
        'cta-banner'    => 'Magnet CTA Banner',
    ];

    foreach ($blocks as $slug => $title) {
        $render = get_template_directory() . '/blocks/' . $slug . '/render.php';
        $style  = get_template_directory_uri() . '/blocks/' . $slug . '/style.css';

        acf_register_block_type([
            'name'            => $slug,
            'title'           => $title,
            'category'        => 'magnet',
            'icon'            => 'layout',
            'mode'            => 'auto',
            'render_template' => $render,
            'enqueue_style'   => $style,
            'supports'        => ['anchor' => true],
        ]);
    }
});

// ── ACF Field Groups ──
add_action('acf/init', 'magnet_register_acf_fields');
function magnet_register_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) return;

    // Hero
    acf_add_local_field_group([
        'key'      => 'group_magnet_hero',
        'title'    => 'Hero',
        'fields'   => [
            ['key'=>'field_hero_title',     'label'=>'Título',               'name'=>'hero_title',     'type'=>'text'],
            ['key'=>'field_hero_subtitle',  'label'=>'Subtítulo',            'name'=>'hero_subtitle',  'type'=>'textarea', 'rows'=>2],
            ['key'=>'field_hero_btn_text',  'label'=>'Texto del botón',      'name'=>'hero_btn_text',  'type'=>'text'],
            ['key'=>'field_hero_btn_url',   'label'=>'URL del botón',        'name'=>'hero_btn_url',   'type'=>'url'],
            ['key'=>'field_hero_bg_color',  'label'=>'Color de fondo',       'name'=>'hero_bg_color',  'type'=>'color_picker', 'default_value'=>'#D8E4EC'],
            ['key'=>'field_hero_show_deco', 'label'=>'Mostrar ilustraciones','name'=>'hero_show_deco', 'type'=>'true_false', 'default_value'=>1, 'ui'=>1],
            ['key'=>'field_hero_bg_image',  'label'=>'Imagen de fondo',      'name'=>'hero_bg_image',  'type'=>'url', 'instructions'=>'Pega la URL de la imagen desde Media Library'],
        ],
        'location' => [[['param'=>'block','operator'=>'==','value'=>'magnet/hero']]],
    ]);

    // About Section — un bloque por columna, se repite 3 veces en el homepage
    acf_add_local_field_group([
        'key'    => 'group_magnet_about',
        'title'  => 'About Section',
        'fields' => [
            ['key'=>'field_about_image',       'label'=>'Imagen',          'name'=>'about_image',       'type'=>'image',    'return_format'=>'url', 'preview_size'=>'medium'],
            ['key'=>'field_about_title',       'label'=>'Título',          'name'=>'about_title',       'type'=>'text'],
            ['key'=>'field_about_description', 'label'=>'Descripción',     'name'=>'about_description', 'type'=>'textarea', 'rows'=>4],
            ['key'=>'field_about_btn_text',    'label'=>'Texto del botón', 'name'=>'about_btn_text',    'type'=>'text'],
            ['key'=>'field_about_btn_url',     'label'=>'URL del botón',   'name'=>'about_btn_url',     'type'=>'url'],
        ],
        'location' => [[['param'=>'block','operator'=>'==','value'=>'magnet/about-section']]],
    ]);

    // Posts Grid
    acf_add_local_field_group([
        'key'    => 'group_magnet_posts_grid',
        'title'  => 'Posts Grid',
        'fields' => [
            ['key'=>'field_grid_title',    'label'=>'Título de sección',    'name'=>'grid_title',    'type'=>'text'],
            ['key'=>'field_grid_count',    'label'=>'Cantidad de posts',    'name'=>'grid_count',    'type'=>'number', 'default_value'=>3, 'min'=>1, 'max'=>12],
            ['key'=>'field_grid_category', 'label'=>'Categoría (opcional)', 'name'=>'grid_category', 'type'=>'taxonomy', 'taxonomy'=>'category', 'field_type'=>'select', 'allow_null'=>1, 'return_format'=>'object'],
            ['key'=>'field_grid_btn_text', 'label'=>'Texto botón',          'name'=>'grid_btn_text', 'type'=>'text'],
            ['key'=>'field_grid_btn_url',  'label'=>'URL botón',            'name'=>'grid_btn_url',  'type'=>'url'],
        ],
        'location' => [[['param'=>'block','operator'=>'==','value'=>'magnet/posts-grid']]],
    ]);

    // CTA Banner
    acf_add_local_field_group([
        'key'    => 'group_magnet_cta',
        'title'  => 'CTA Banner',
        'fields' => [
            ['key'=>'field_cta_title',      'label'=>'Título',            'name'=>'cta_title',      'type'=>'text'],
            ['key'=>'field_cta_btn_text',   'label'=>'Texto botón',       'name'=>'cta_btn_text',   'type'=>'text'],
            ['key'=>'field_cta_btn_url',    'label'=>'URL botón',         'name'=>'cta_btn_url',    'type'=>'url'],
            ['key'=>'field_cta_bg_color',   'label'=>'Color de fondo',    'name'=>'cta_bg_color',   'type'=>'color_picker', 'default_value'=>'#D6E8F5'],
            ['key'=>'field_cta_text_color', 'label'=>'Color de texto',    'name'=>'cta_text_color', 'type'=>'color_picker', 'default_value'=>'#1A1A1A'],
            ['key'=>'field_cta_show_bolt',  'label'=>'Mostrar rayo deco', 'name'=>'cta_show_bolt',  'type'=>'true_false', 'default_value'=>1, 'ui'=>1],
        ],
        'location' => [[['param'=>'block','operator'=>'==','value'=>'magnet/cta-banner']]],
    ]);
}

// SVG icons inline
function magnet_icon($name, $class = '') {
    $icons = [
        'lock'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>',
        'user'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>',
        'facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
        'linkedin'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
        'bolt'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>',
    ];
    $svg = $icons[$name] ?? '';
    if ($class) {
        $svg = str_replace('<svg ', '<svg class="' . esc_attr($class) . '" ', $svg);
    }
    return $svg;
}