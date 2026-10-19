<?php
/**
 * Block: magnet/image-banner
 * Magnet People v2 — Takariwa Studio
 *
 * Campos ACF:
 *   ib_image        — image (return: id) — desktop, ideal 1920×1150 (5:3)
 *   ib_image_mobile — image (return: id) — opcional, ideal 800×1000 (4:5)
 *   ib_bg_color     — color_picker (default #7fcff2) — se ve mientras carga la imagen
 *   ib_title        — text (opcional)
 *   ib_btn_text     — text
 *   ib_btn_url      — url
 */

$image_id   = get_field('ib_image')        ?: 0;
$mobile_id  = get_field('ib_image_mobile') ?: 0;
$bg_color   = get_field('ib_bg_color')     ?: '#7fcff2';
$title      = get_field('ib_title')        ?: '';
$btn_text   = get_field('ib_btn_text')     ?: "Let's Talk";
$btn_url    = get_field('ib_btn_url')      ?: '#';

$block_id    = 'image-banner-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . esc_attr($block['className']) : '';
$mobile_src  = $mobile_id ? wp_get_attachment_image_srcset($mobile_id, 'large') : '';
?>

<section id="<?php echo esc_attr($block_id); ?>"
         class="magnet-ibanner<?php echo $mobile_src ? ' magnet-ibanner--has-mobile' : ''; ?><?php echo $extra_class; ?>">
    <div class="magnet-ibanner__inner"
         style="background-color: <?php echo esc_attr($bg_color); ?>;">

        <?php if ($image_id) : ?>
        <picture class="magnet-ibanner__media" aria-hidden="true">
            <?php if ($mobile_src) : ?>
            <source media="(max-width: 600px)"
                    srcset="<?php echo esc_attr($mobile_src); ?>"
                    sizes="100vw">
            <?php endif; ?>
            <?php echo wp_get_attachment_image($image_id, 'full', false, [
                'alt'     => '',
                'sizes'   => '(max-width: 1040px) 100vw, 1000px',
                'loading' => 'lazy',
            ]); ?>
        </picture>
        <?php endif; ?>

        <div class="magnet-ibanner__content">
            <?php if ($title) : ?>
            <h2 class="magnet-ibanner__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if ($btn_text) : ?>
            <a href="<?php echo esc_url($btn_url); ?>" class="magnet-ibanner__btn">
                <?php echo esc_html($btn_text); ?>
            </a>
            <?php endif; ?>
        </div>

    </div>
</section>
