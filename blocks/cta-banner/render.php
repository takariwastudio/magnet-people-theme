<?php
/**
 * Block: magnet/cta-banner
 * Magnet People v2 — Takariwa Studio
 *
 * Campos ACF:
 *   cta_title      — text
 *   cta_btn_text   — text
 *   cta_btn_url    — url
 *   cta_bg_color   — color_picker (default #D6E8F5)
 *   cta_text_color — color_picker (default #ffffff)
 *   cta_bolt_image — image (return: url) — imagen decorativa del rayo
 */

$title      = get_field('cta_title')      ?: 'Want to be part of the biggest Hispanic influencer network?';
$btn_text   = get_field('cta_btn_text')   ?: 'Get Access';
$btn_url    = get_field('cta_btn_url')    ?: home_url('/membresia/');
$bg_color   = get_field('cta_bg_color')   ?: '#D6E8F5';
$text_color = get_field('cta_text_color') ?: '#ffffff';
$bolt_image = get_field('cta_bolt_image') ?: '';

$block_id    = 'cta-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . esc_attr($block['className']) : '';
?>

<section id="<?php echo esc_attr($block_id); ?>"
         class="magnet-cta<?php echo $extra_class; ?>">
    <div class="magnet-cta__inner"
         style="background-color: <?php echo esc_attr($bg_color); ?>;">

        <!-- Rayo decorativo -->
        <?php if ($bolt_image) : ?>
        <div class="magnet-cta__bolt" aria-hidden="true">
            <img src="<?php echo esc_url($bolt_image); ?>" alt="">
        </div>
        <?php endif; ?>

        <!-- Texto -->
        <?php if ($title) : ?>
        <h2 class="magnet-cta__title" style="color: <?php echo esc_attr($text_color); ?>;">
            <?php echo esc_html($title); ?>
        </h2>
        <?php endif; ?>

        <!-- Botón -->
        <?php if ($btn_text && $btn_url) : ?>
        <a href="<?php echo esc_url($btn_url); ?>" class="magnet-cta__btn">
            <?php echo esc_html($btn_text); ?>
        </a>
        <?php endif; ?>

    </div>
</section>