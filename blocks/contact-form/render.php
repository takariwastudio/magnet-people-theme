<?php
/**
 * Block: magnet/contact-form
 * Magnet People v2 — Takariwa Studio
 *
 * Campos ACF:
 *   cf_title     — text ("Let's talk", opcional)
 *   cf_intro     — textarea (texto debajo del título, opcional)
 *   cf_shortcode — text (shortcode de Contact Form 7)
 *   cf_bg_color  — color_picker (default #9dc8d8) — se ve mientras carga la imagen
 *   cf_bg_image  — image (return: id) — textura + manchas rojas, ideal 3240×1220 px
 */

$title     = get_field('cf_title')     ?: '';
$intro     = get_field('cf_intro')     ?: '';
$shortcode = get_field('cf_shortcode') ?: '';
$bg_color  = get_field('cf_bg_color')  ?: '#9dc8d8';
$bg_image  = get_field('cf_bg_image')  ?: 0;

$block_id    = 'contact-form-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . esc_attr($block['className']) : '';
$bg_url      = $bg_image ? wp_get_attachment_image_url($bg_image, 'full') : '';

$style = 'background-color: ' . esc_attr($bg_color) . ';';
if ($bg_url) {
    $style .= ' background-image: url(' . esc_url($bg_url) . ');';
}
?>

<section id="<?php echo esc_attr($block_id); ?>" class="magnet-contact<?php echo $extra_class; ?>">

    <?php if ($title || $intro) : ?>
    <header class="magnet-contact__header">
        <?php if ($title) : ?>
        <h1 class="magnet-contact__title"><?php echo esc_html($title); ?></h1>
        <?php endif; ?>
        <?php if ($intro) : ?>
        <p class="magnet-contact__intro"><?php echo nl2br(esc_html($intro)); ?></p>
        <?php endif; ?>
    </header>
    <?php endif; ?>

    <div class="magnet-contact__band" style="<?php echo $style; ?>">
        <div class="magnet-contact__card">
            <?php if ($shortcode) : ?>
            <div class="magnet-contact__form">
                <?php echo do_shortcode($shortcode); ?>
            </div>
            <?php elseif (!empty($is_preview)) : ?>
            <p class="magnet-contact__empty">Pega el shortcode del formulario en el campo «Shortcode del formulario».</p>
            <?php endif; ?>
        </div>
    </div>

</section>
