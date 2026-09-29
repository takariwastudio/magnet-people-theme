<?php
/**
 * Block: magnet/about-section
 * Campo ACF:
 *   about_columns — repeater (max 3)
 *     column_image — image (url)
 *     column_title — text
 *     column_text  — wysiwyg
 */

$columns = get_field('about_columns') ?: [];
$count   = count($columns);

$block_id    = 'about-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . $block['className'] : '';

if (empty($columns) && (is_admin() || defined('REST_REQUEST'))) : ?>
    <div class="magnet-about magnet-about--empty">
        <p style="text-align:center;padding:40px;color:#999;">
            Agrega columnas desde el panel de campos ACF →
        </p>
    </div>
<?php return;
endif;
?>

<section id="<?php echo esc_attr($block_id); ?>"
         class="magnet-about magnet-about--cols-<?php echo $count; ?><?php echo esc_attr($extra_class); ?>">
    <div class="container">

        <!-- Fila de imágenes -->
        <div class="magnet-about__images">
            <?php foreach ($columns as $col) :
                $img = $col['column_image'] ?? '';
            ?>
                <div class="magnet-about__img-wrap">
                    <?php if ($img) : ?>
                        <img src="<?php echo esc_url($img); ?>"
                             alt="<?php echo esc_attr($col['column_title'] ?? ''); ?>"
                             loading="lazy">
                    <?php else : ?>
                        <div class="magnet-about__img-placeholder"></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Fila de textos -->
        <div class="magnet-about__texts">
            <?php foreach ($columns as $col) : ?>
                <div class="magnet-about__col">
                    <?php if (!empty($col['column_title'])) : ?>
                        <h3 class="magnet-about__col-title">
                            <?php echo esc_html($col['column_title']); ?>
                        </h3>
                    <?php endif; ?>
                    <?php if (!empty($col['column_text'])) : ?>
                        <div class="magnet-about__col-text">
                            <?php echo wp_kses_post($col['column_text']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>