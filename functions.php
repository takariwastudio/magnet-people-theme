<?php
/**
 * Magnet People — functions.php v2
 * Takariwa Studio
 */

// Versión del tema (de style.css): se usa para invalidar la caché de CSS/JS en cada release
define('MAGNET_VERSION', wp_get_theme(get_template())->get('Version'));

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
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Nunito+Sans:ital,opsz,wght@0,6..12,400;0,6..12,600;0,6..12,700;1,6..12,400&family=Nunito:wght@600&family=Poppins:wght@800&display=swap',
        [],
        null
    );
    wp_enqueue_style(
        'magnet-main',
        get_template_directory_uri() . '/assets/css/main.css',
        ['magnet-fonts'],
        MAGNET_VERSION
    );
    wp_enqueue_script(
        'magnet-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        MAGNET_VERSION,
        true
    );
    wp_enqueue_style(
        'magnet-modal',
        get_template_directory_uri() . '/assets/css/modal.css',
        ['magnet-main'],
        MAGNET_VERSION
    );
    wp_enqueue_script(
        'magnet-modal',
        get_template_directory_uri() . '/assets/js/modal.js',
        ['magnet-main'],
        MAGNET_VERSION,
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

// Card de post (imagen de fondo + título + candado PMPro). Usar dentro del loop.
function magnet_render_post_card() {
    $post_id  = get_the_ID();
    $is_paid  = magnet_is_paid_post($post_id);
    $thumb    = get_the_post_thumbnail_url($post_id, 'card-wide');
    $link     = $is_paid ? magnet_get_join_url() : get_permalink();
    $bg_style = $thumb ? 'style="background-image: url(\'' . esc_url($thumb) . '\')"' : '';
    ?>
    <article class="post-card<?php echo $is_paid ? ' post-card--locked' : ''; ?>">
        <a href="<?php echo esc_url($link); ?>"
           class="post-card__link"
           <?php echo $bg_style; ?>>

            <!-- Overlay oscuro para legibilidad -->
            <div class="post-card__overlay"></div>

            <!-- Candado (solo paid) -->
            <?php if ($is_paid) : ?>
            <span class="post-card__lock">
                <?php echo magnet_icon('lock', 'post-card__lock-icon'); ?>
            </span>
            <?php endif; ?>

            <!-- Título sobre la imagen -->
            <div class="post-card__body">
                <h3 class="post-card__title"><?php the_title(); ?></h3>
            </div>

        </a>
    </article>
    <?php
}

// Card de People Grid: imagen vertical + título, autor y categoría
function magnet_render_people_card() {
    $post_id  = get_the_ID();
    $is_paid  = magnet_is_paid_post($post_id);
    $thumb    = get_the_post_thumbnail_url($post_id, 'large');
    $link     = $is_paid ? magnet_get_join_url() : get_permalink();
    $cats     = get_the_category($post_id);
    $category = $cats ? $cats[0]->name : '';
    ?>
    <article class="people-card<?php echo $is_paid ? ' people-card--locked' : ''; ?><?php echo $thumb ? '' : ' people-card--no-image'; ?>">
        <a href="<?php echo esc_url($link); ?>" class="people-card__link">
            <?php if ($thumb) : ?>
            <img src="<?php echo esc_url($thumb); ?>" alt="" class="people-card__img" loading="lazy">
            <?php endif; ?>

            <div class="people-card__overlay"></div>

            <?php if ($is_paid) : ?>
            <span class="people-card__lock">
                <?php echo magnet_icon('lock', 'people-card__lock-icon'); ?>
            </span>
            <?php endif; ?>

            <div class="people-card__body">
                <h3 class="people-card__title"><?php the_title(); ?></h3>
                <p class="people-card__author"><?php echo esc_html(get_the_author()); ?></p>
                <?php if ($category) : ?>
                <span class="people-card__cat"><?php echo esc_html($category); ?></span>
                <?php endif; ?>
            </div>
        </a>
    </article>
    <?php
}

// Imprime los posts del query en grupos de filas 3 / 3 / 2
function magnet_render_people_batch($query) {
    echo '<div class="people-batch">';
    while ($query->have_posts()) {
        $query->the_post();
        magnet_render_people_card();
    }
    wp_reset_postdata();
    echo '</div>';
}

// ── People Grid: query + Load More (AJAX) ──
function magnet_people_query($category_id, $per_page, $paged = 1) {
    $args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => max(1, intval($per_page)),
        'paged'          => max(1, intval($paged)),
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    if ($category_id) {
        $args['cat'] = intval($category_id);
    }
    return new WP_Query($args);
}

add_action('wp_ajax_magnet_people_load_more', 'magnet_people_load_more');
add_action('wp_ajax_nopriv_magnet_people_load_more', 'magnet_people_load_more');
function magnet_people_load_more() {
    check_ajax_referer('magnet_people_load_more', 'nonce');

    $per_page = min(48, max(1, intval($_POST['per_page'] ?? 8)));
    $paged    = max(2, intval($_POST['page'] ?? 2));
    $category = intval($_POST['category'] ?? 0);

    $query = magnet_people_query($category, $per_page, $paged);

    ob_start();
    magnet_render_people_batch($query);

    wp_send_json_success([
        'html'     => ob_get_clean(),
        'has_more' => $paged < $query->max_num_pages,
    ]);
}

// ── Blog Grid: posts del blog (sin Magnet People) + Load More (AJAX) ──

// Categorías excluidas del blog. Vacío = Magnet People
function magnet_blog_excluded_cats($ids = []) {
    $ids = array_filter(array_map('intval', (array) $ids));
    if (!$ids) {
        $term = get_category_by_slug('magnet-people');
        $ids  = $term ? [$term->term_id] : [];
    }
    return $ids;
}

function magnet_blog_query($exclude, $per_page, $paged = 1) {
    return new WP_Query([
        'post_type'        => 'post',
        'post_status'      => 'publish',
        'posts_per_page'   => max(1, intval($per_page)),
        'paged'            => max(1, intval($paged)),
        'orderby'          => 'date',
        'order'            => 'DESC',
        'category__not_in' => $exclude,
    ]);
}

// Card del blog: imagen + título, autor, candado y categorías como pills
function magnet_render_blog_card($exclude = []) {
    $post_id = get_the_ID();
    $is_paid = magnet_is_paid_post($post_id);
    $thumb   = get_the_post_thumbnail_url($post_id, 'large');
    $link    = $is_paid ? magnet_get_join_url() : get_permalink();

    // Fuera: categorías excluidas, "Sin categoría" y tags internos de Ghost (#...)
    $default_cat = (int) get_option('default_category');
    $cats = array_filter(get_the_category($post_id), function ($cat) use ($exclude, $default_cat) {
        return !in_array($cat->term_id, $exclude, true)
            && $cat->term_id !== $default_cat
            && strpos($cat->slug, 'hash-') !== 0;
    });
    $cats = array_slice($cats, 0, 3);
    ?>
    <article class="blog-card<?php echo $is_paid ? ' blog-card--locked' : ''; ?><?php echo $thumb ? '' : ' blog-card--no-image'; ?>">
        <a href="<?php echo esc_url($link); ?>" class="blog-card__link">
            <?php if ($thumb) : ?>
            <img src="<?php echo esc_url($thumb); ?>" alt="" class="blog-card__img" loading="lazy">
            <div class="blog-card__overlay"></div>
            <?php endif; ?>

            <div class="blog-card__body">
                <h3 class="blog-card__title"><?php the_title(); ?></h3>
                <p class="blog-card__author"><?php echo esc_html(get_the_author()); ?></p>

                <?php if ($is_paid || $cats) : ?>
                <div class="blog-card__meta">
                    <?php if ($is_paid) : ?>
                    <span class="blog-card__lock" aria-label="Contenido exclusivo para miembros">
                        <?php echo magnet_icon('lock', 'blog-card__lock-icon'); ?>
                    </span>
                    <?php endif; ?>
                    <?php foreach ($cats as $cat) : ?>
                    <span class="blog-card__cat"><?php echo esc_html($cat->name); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </a>
    </article>
    <?php
}

// Imprime los posts del query como un grupo (filas 3 / 3 / 2)
function magnet_render_blog_batch($query, $exclude = []) {
    echo '<div class="blog-batch">';
    while ($query->have_posts()) {
        $query->the_post();
        magnet_render_blog_card($exclude);
    }
    wp_reset_postdata();
    echo '</div>';
}

add_action('wp_ajax_magnet_blog_load_more', 'magnet_blog_load_more');
add_action('wp_ajax_nopriv_magnet_blog_load_more', 'magnet_blog_load_more');
function magnet_blog_load_more() {
    check_ajax_referer('magnet_blog_load_more', 'nonce');

    $per_page = min(48, max(1, intval($_POST['per_page'] ?? 8)));
    $paged    = max(2, intval($_POST['page'] ?? 2));
    $exclude  = magnet_blog_excluded_cats(explode(',', sanitize_text_field($_POST['exclude'] ?? '')));

    $query = magnet_blog_query($exclude, $per_page, $paged);

    ob_start();
    magnet_render_blog_batch($query, $exclude);

    wp_send_json_success([
        'html'     => ob_get_clean(),
        'has_more' => $paged < $query->max_num_pages,
    ]);
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
        'media-text'    => 'Magnet Media + Text',
        'image-banner'  => 'Magnet Image Banner',
        'people-grid'   => 'Magnet People Grid',
        'blog-grid'     => 'Magnet Blog Grid',
        'video'       => 'Magnet Video',
        'pricing'       => 'Magnet Pricing',
    ];

    foreach ($blocks as $slug => $title) {
        $render = get_template_directory() . '/blocks/' . $slug . '/render.php';
        $style  = get_template_directory_uri() . '/blocks/' . $slug . '/style.css';

        $config = [
            'name'            => $slug,
            'title'           => $title,
            'category'        => 'magnet',
            'icon'            => 'layout',
            'mode'            => 'auto',
            'render_template' => $render,
            'enqueue_style'   => $style,
            'supports'        => ['anchor' => true],
        ];

        // Bloques con JS propio (blocks/{slug}/script.js). People/Blog Grid además reutilizan título/botón de posts-grid
        $script = get_template_directory() . '/blocks/' . $slug . '/script.js';
        if (file_exists($script)) {
            $config['enqueue_assets'] = function () use ($slug, $script) {
                $base = get_template_directory_uri() . '/blocks/';
                if (in_array($slug, ['people-grid', 'blog-grid'], true)) {
                    wp_enqueue_style('block-acf-posts-grid', $base . 'posts-grid/style.css', [], MAGNET_VERSION);
                }
                // Versión = fecha del archivo, para que cada deploy invalide la caché
                wp_enqueue_script('magnet-' . $slug, $base . $slug . '/script.js', [], filemtime($script), true);
            };
        }

        acf_register_block_type($config);
    }
});

// ── ACF Field Groups ──
add_action('acf/init', 'magnet_register_acf_fields');
function magnet_register_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) return;

    // Hero
    $hero_is_text    = [[['field'=>'field_hero_layout', 'operator'=>'==', 'value'=>'text']]];
    $hero_is_default = [[['field'=>'field_hero_layout', 'operator'=>'!=', 'value'=>'text']]];

    acf_add_local_field_group([
        'key'      => 'group_magnet_hero',
        'title'    => 'Hero',
        'fields'   => [
            ['key'=>'field_hero_layout',    'label'=>'Diseño',               'name'=>'hero_layout',    'type'=>'button_group', 'choices'=>['default'=>'Título + botón','text'=>'Solo texto'], 'default_value'=>'default'],
            ['key'=>'field_hero_text',      'label'=>'Texto',                'name'=>'hero_text',      'type'=>'wysiwyg', 'toolbar'=>'basic', 'media_upload'=>0, 'conditional_logic'=>$hero_is_text],
            ['key'=>'field_hero_title',     'label'=>'Título',               'name'=>'hero_title',     'type'=>'text', 'conditional_logic'=>$hero_is_default],
            ['key'=>'field_hero_subtitle',  'label'=>'Subtítulo',            'name'=>'hero_subtitle',  'type'=>'textarea', 'rows'=>2, 'conditional_logic'=>$hero_is_default],
            ['key'=>'field_hero_btn_text',  'label'=>'Texto del botón',      'name'=>'hero_btn_text',  'type'=>'text', 'conditional_logic'=>$hero_is_default],
            ['key'=>'field_hero_btn_url',   'label'=>'URL del botón',        'name'=>'hero_btn_url',   'type'=>'url', 'conditional_logic'=>$hero_is_default],
            ['key'=>'field_hero_bg_color',  'label'=>'Color de fondo',       'name'=>'hero_bg_color',  'type'=>'color_picker', 'default_value'=>'#D8E4EC'],
            ['key'=>'field_hero_show_deco', 'label'=>'Mostrar ilustraciones','name'=>'hero_show_deco', 'type'=>'true_false', 'default_value'=>1, 'ui'=>1, 'conditional_logic'=>$hero_is_default],
            ['key'=>'field_hero_bg_image',  'label'=>'Imagen de fondo',      'name'=>'hero_bg_image',  'type'=>'url', 'instructions'=>'Pega la URL de la imagen desde Media Library'],
        ],
        'location' => [[['param'=>'block','operator'=>'==','value'=>'magnet/hero']]],
    ]);

    // About Section — un bloque, hasta 3 columnas vía repeater
    acf_add_local_field_group([
        'key'    => 'group_magnet_about',
        'title'  => 'About Section',
        'fields' => [
            [
                'key'          => 'field_about_columns',
                'label'        => 'Columnas',
                'name'         => 'about_columns',
                'type'         => 'repeater',
                'min'          => 1,
                'max'          => 3,
                'layout'       => 'block',
                'button_label' => 'Agregar columna',
                'sub_fields'   => [
                    ['key'=>'field_col_image',    'label'=>'Imagen',          'name'=>'col_image',    'type'=>'image',    'return_format'=>'url', 'preview_size'=>'medium'],
                    ['key'=>'field_col_title',    'label'=>'Título',          'name'=>'col_title',    'type'=>'text'],
                    ['key'=>'field_col_desc',     'label'=>'Descripción',     'name'=>'col_desc',     'type'=>'textarea', 'rows'=>4],
                    ['key'=>'field_col_btn_text', 'label'=>'Texto del botón', 'name'=>'col_btn_text', 'type'=>'text'],
                    ['key'=>'field_col_btn_url',  'label'=>'URL del botón',   'name'=>'col_btn_url',  'type'=>'url'],
                ],
            ],
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

    // People Grid — posts en grilla de 3 columnas con botón Load More
    acf_add_local_field_group([
        'key'    => 'group_magnet_people_grid',
        'title'  => 'People Grid',
        'fields' => [
            ['key'=>'field_pg_title',    'label'=>'Título de sección',     'name'=>'pg_title',    'type'=>'text'],
            ['key'=>'field_pg_category', 'label'=>'Categoría (opcional)',  'name'=>'pg_category', 'type'=>'taxonomy', 'taxonomy'=>'category', 'field_type'=>'select', 'allow_null'=>1, 'return_format'=>'id', 'instructions'=>'Vacío = todos los posts.'],
            ['key'=>'field_pg_per_load', 'label'=>'Posts por carga',       'name'=>'pg_per_load', 'type'=>'number', 'default_value'=>8, 'min'=>1, 'max'=>24, 'instructions'=>'8 = filas de 3 / 3 / 2. Load More trae la misma cantidad cada vez.'],
            ['key'=>'field_pg_btn_text', 'label'=>'Texto del botón',       'name'=>'pg_btn_text', 'type'=>'text', 'default_value'=>'Load More'],
        ],
        'location' => [[['param'=>'block','operator'=>'==','value'=>'acf/people-grid']]],
    ]);

    // Blog Grid — posts del blog (excluye Magnet People) con botón Load More
    acf_add_local_field_group([
        'key'    => 'group_magnet_blog_grid',
        'title'  => 'Blog Grid',
        'fields' => [
            ['key'=>'field_bg_title',    'label'=>'Título de sección',      'name'=>'bg_title',    'type'=>'text'],
            ['key'=>'field_bg_exclude',  'label'=>'Excluir categorías',     'name'=>'bg_exclude',  'type'=>'taxonomy', 'taxonomy'=>'category', 'field_type'=>'multi_select', 'allow_null'=>1, 'add_term'=>0, 'save_terms'=>0, 'load_terms'=>0, 'return_format'=>'id', 'instructions'=>'Vacío = excluye Magnet People.'],
            ['key'=>'field_bg_per_load', 'label'=>'Posts por carga',        'name'=>'bg_per_load', 'type'=>'number', 'default_value'=>8, 'min'=>1, 'max'=>24, 'instructions'=>'8 = filas de 3 / 3 / 2. Load More trae la misma cantidad cada vez.'],
            ['key'=>'field_bg_btn_text', 'label'=>'Texto del botón',        'name'=>'bg_btn_text', 'type'=>'text', 'default_value'=>'Load more'],
        ],
        'location' => [[['param'=>'block','operator'=>'==','value'=>'acf/blog-grid']]],
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
            ['key'=>'field_cta_bolt_image', 'label'=>'Imagen del rayo (decorativo)', 'name'=>'cta_bolt_image', 'type'=>'image', 'return_format'=>'url', 'preview_size'=>'medium'],
        ],
        'location' => [[['param'=>'block','operator'=>'==','value'=>'magnet/cta-banner']]],
    ]);

    // Media + Text — título, texto, botón e imagen lado a lado
    acf_add_local_field_group([
        'key'    => 'group_magnet_media_text',
        'title'  => 'Media + Text',
        'fields' => [
            ['key'=>'field_mt_title',          'label'=>'Título',             'name'=>'mt_title',          'type'=>'text'],
            ['key'=>'field_mt_content',        'label'=>'Texto',              'name'=>'mt_content',        'type'=>'wysiwyg', 'toolbar'=>'basic', 'media_upload'=>0],
            ['key'=>'field_mt_btn_text',       'label'=>'Texto del botón',    'name'=>'mt_btn_text',       'type'=>'text'],
            ['key'=>'field_mt_btn_url',        'label'=>'URL del botón',      'name'=>'mt_btn_url',        'type'=>'url'],
            ['key'=>'field_mt_btn_style',      'label'=>'Estilo del botón',   'name'=>'mt_btn_style',      'type'=>'select', 'choices'=>['dark'=>'Negro','red'=>'Rojo'], 'default_value'=>'dark'],
            ['key'=>'field_mt_image',          'label'=>'Imagen',             'name'=>'mt_image',          'type'=>'image', 'return_format'=>'id', 'preview_size'=>'medium', 'instructions'=>'Proporción 3:2, mínimo 1200×800 px. Se recorta automáticamente.'],
            ['key'=>'field_mt_image_position', 'label'=>'Posición de imagen', 'name'=>'mt_image_position', 'type'=>'button_group', 'choices'=>['right'=>'Derecha','left'=>'Izquierda'], 'default_value'=>'right'],
        ],
        'location' => [[['param'=>'block','operator'=>'==','value'=>'magnet/media-text']]],
    ]);

    // Image Banner — imagen de fondo con botón centrado
    acf_add_local_field_group([
        'key'    => 'group_magnet_image_banner',
        'title'  => 'Image Banner',
        'fields' => [
            ['key'=>'field_ib_image',        'label'=>'Imagen (desktop)',      'name'=>'ib_image',        'type'=>'image', 'return_format'=>'id', 'preview_size'=>'medium', 'instructions'=>'Proporción 5:3, ideal 2000×1200 px.'],
            ['key'=>'field_ib_image_mobile', 'label'=>'Imagen (móvil)',        'name'=>'ib_image_mobile', 'type'=>'image', 'return_format'=>'id', 'preview_size'=>'medium', 'instructions'=>'Opcional. Proporción 4:5, ideal 800×1000 px. Si se omite, se usa la de desktop recortada.'],
            ['key'=>'field_ib_bg_color',     'label'=>'Color de fondo',        'name'=>'ib_bg_color',     'type'=>'color_picker', 'default_value'=>'#7FCFF2'],
            ['key'=>'field_ib_title',        'label'=>'Título (opcional)',     'name'=>'ib_title',        'type'=>'text'],
            ['key'=>'field_ib_btn_text',     'label'=>'Texto del botón',       'name'=>'ib_btn_text',     'type'=>'text', 'default_value'=>"Let's Talk"],
            ['key'=>'field_ib_btn_url',      'label'=>'URL del botón',         'name'=>'ib_btn_url',      'type'=>'url'],
        ],
        'location' => [[['param'=>'block','operator'=>'==','value'=>'magnet/image-banner']]],
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
        'check'     => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>',
    ];
    $svg = $icons[$name] ?? '';
    if ($class) {
        $svg = str_replace('<svg ', '<svg class="' . esc_attr($class) . '" ', $svg);
    }
    return $svg;
}