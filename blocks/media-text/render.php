<?php
/**
 * Block: magnet/media-text
 * Magnet People v2 — Takariwa Studio
 *
 * Campos ACF:
 *   mt_title          — text
 *   mt_content        — wysiwyg (párrafos)
 *   mt_btn_text       — text
 *   mt_btn_url        — url
 *   mt_btn_style      — select (dark | red)
 *   mt_image          — image (return: id) — WP genera srcset automáticamente
 *   mt_image_position — select (right | left)
 */

$title     = get_field('mt_title')          ?: '';
$content   = get_field('mt_content')        ?: '';
$btn_text  = get_field('mt_btn_text')       ?: '';
$btn_url   = get_field('mt_btn_url')        ?: '#';
$btn_style = get_field('mt_btn_style')      ?: 'dark';
$image_id  = get_field('mt_image')          ?: 0;
$position  = get_field('mt_image_position') ?: 'right';

$block_id    = 'media-text-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . esc_attr($block['className']) : '';

if ( ! $title && ! $content && ! $image_id && ( is_admin() || defined('REST_REQUEST') ) ) : ?>
    <div class="magnet-media magnet-media--empty">
        <p style="text-align:center;padding:40px;color:#999;">
            Agrega título, texto e imagen desde el panel de campos ACF →
        </p>
    </div>
<?php return;
endif;
?>

<section id="<?php echo esc_attr($block_id); ?>"
         class="magnet-media magnet-media--img-<?php echo esc_attr($position); ?><?php echo $extra_class; ?>">
    <div class="magnet-media__inner">

        <div class="magnet-media__content">
            <?php if ($title) : ?>
            <h2 class="magnet-media__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if ($content) : ?>
            <div class="magnet-media__text"><?php echo wp_kses_post($content); ?></div>
            <?php endif; ?>

            <?php if ($btn_text) : ?>
            <a href="<?php echo esc_url($btn_url); ?>"
               class="magnet-media__btn magnet-media__btn--<?php echo esc_attr($btn_style); ?>">
                <?php echo esc_html($btn_text); ?>
            </a>
            <?php endif; ?>
        </div>

        <?php if ($image_id) : ?>
        <div class="magnet-media__image">
            <?php echo wp_get_attachment_image($image_id, 'large', false, [
                'sizes'   => '(max-width: 768px) calc(100vw - 40px), 50vw',
                'loading' => 'lazy',
            ]); ?>
        </div>
        <?php endif; ?>

    </div>
</section>
