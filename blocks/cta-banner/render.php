<?php
/**
 * Block: magnet/cta-banner
 * Campos ACF:
 *   cta_title        — text
 *   cta_btn_text     — text
 *   cta_btn_url      — url
 *   cta_bg_color     — color_picker (default #D6E8F5)
 *   cta_text_color   — color_picker (default #1A1A1A)
 *   cta_show_bolt    — true_false
 */

$title      = get_field('cta_title')      ?: 'Want to be part of the biggest Hispanic influencer network?';
$btn_text   = get_field('cta_btn_text')   ?: 'Get Access';
$btn_url    = get_field('cta_btn_url')    ?: home_url('/membresia/');
$bg_color   = get_field('cta_bg_color')   ?: '#D6E8F5';
$text_color = get_field('cta_text_color') ?: '#1A1A1A';
$show_bolt  = get_field('cta_show_bolt');
if ($show_bolt === null) $show_bolt = true;

$block_id    = 'cta-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . $block['className'] : '';
?>

<section id="<?php echo esc_attr($block_id); ?>"
         class="magnet-cta<?php echo esc_attr($extra_class); ?>">
    <div class="container">
        <div class="magnet-cta__inner"
             style="background: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>;">

            <?php if ($show_bolt) : ?>
                <div class="magnet-cta__bolt" aria-hidden="true">
                    <?php echo magnet_icon('bolt'); ?>
                </div>
            <?php endif; ?>

            <?php if ($title) : ?>
                <h2 class="magnet-cta__title" style="color: <?php echo esc_attr($text_color); ?>;">
                    <?php echo esc_html($title); ?>
                </h2>
            <?php endif; ?>

            <?php if ($btn_text && $btn_url) : ?>
                <a href="<?php echo esc_url($btn_url); ?>" class="btn btn--red">
                    <?php echo esc_html($btn_text); ?>
                </a>
            <?php endif; ?>

        </div>
    </div>
</section>