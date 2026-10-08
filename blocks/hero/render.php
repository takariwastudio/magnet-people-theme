<?php
/**
 * Block: magnet/hero
 */

$title     = get_field('hero_title')    ?: 'The largest network of Hispanic influencers';
$subtitle  = get_field('hero_subtitle') ?: 'Connect, grow and collaborate with the most influential voices of the Hispanic community.';
$btn_text  = get_field('hero_btn_text') ?: 'Join the Network';
$btn_url   = get_field('hero_btn_url')  ?: home_url('/membresia/');
$bg_color  = get_field('hero_bg_color') ?: '#D8E4EC';
$bg_image  = get_field('hero_bg_image') ?: '';
$show_deco = get_field('hero_show_deco');
if ($show_deco === null) $show_deco = true;

$block_id    = 'hero-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . $block['className'] : '';

$style = '--hero-bg: ' . esc_attr($bg_color) . ';';
if ($bg_image) {
    $style .= ' --hero-bg-image: url(' . esc_url($bg_image) . ');';
}
?>

<section id="<?php echo esc_attr($block_id); ?>"
         class="magnet-hero<?php echo esc_attr($extra_class); ?><?php echo $bg_image ? ' magnet-hero--has-image' : ''; ?>"
         style="<?php echo $style; ?>">

    <div class="container magnet-hero__inner">

        <div class="magnet-hero__content">
            <?php if ($title) : ?>
                <h1 class="magnet-hero__title"><?php echo esc_html($title); ?></h1>
            <?php endif; ?>
            <?php if ($subtitle) : ?>
                <p class="magnet-hero__subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
            <?php if ($btn_text && $btn_url) : ?>
                <a href="<?php echo esc_url($btn_url); ?>" class="btn btn--red btn--lg">
                    <?php echo esc_html($btn_text); ?>
                </a>
            <?php endif; ?>
        </div>

        <?php if ($show_deco) : ?>
        <div class="magnet-hero__deco" aria-hidden="true">
            <div class="magnet-hero__deco-magnet">
                <svg viewBox="0 0 120 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 10 H50 V90 A30 30 0 0 0 110 90 V10 H50" stroke="#E31E24" stroke-width="18" stroke-linecap="round" fill="none"/>
                    <rect x="0" y="0" width="50" height="25" rx="6" fill="#1A1A1A"/>
                    <rect x="70" y="0" width="50" height="25" rx="6" fill="#E31E24"/>
                </svg>
            </div>
            <div class="magnet-hero__deco-heart">
                <svg viewBox="0 0 40 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 34 C20 34 2 22 2 11 A9 9 0 0 1 20 7 A9 9 0 0 1 38 11 C38 22 20 34 20 34Z" fill="#E31E24"/>
                </svg>
            </div>
            <div class="magnet-hero__deco-bubble">
                <svg viewBox="0 0 56 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0" y="0" width="56" height="38" rx="10" fill="#1A1A1A"/>
                    <path d="M12 38 L8 48 L22 38Z" fill="#1A1A1A"/>
                    <circle cx="16" cy="19" r="3" fill="white"/>
                    <circle cx="28" cy="19" r="3" fill="white"/>
                    <circle cx="40" cy="19" r="3" fill="white"/>
                </svg>
            </div>
            <div class="magnet-hero__deco-like">
                <svg viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="44" height="44" rx="12" fill="#E31E24"/>
                    <path d="M14 24 L14 34 M14 24 C14 24 12 22 12 18 C12 15 14 13 17 13 C19 13 20 14 22 16 C24 14 25 13 27 13 C30 13 32 15 32 18 C32 22 28 26 22 30 C20 28 17 26 14 24Z" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="magnet-hero__deco-bolt1"><?php echo magnet_icon('bolt'); ?></div>
            <div class="magnet-hero__deco-bolt2"><?php echo magnet_icon('bolt'); ?></div>
        </div>
        <?php endif; ?>

    </div>
</section>