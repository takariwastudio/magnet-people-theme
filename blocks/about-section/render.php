<?php
/**
 * Block: magnet/about-section
 * Magnet People v2 — Takariwa Studio
 *
 * Campos ACF:
 *   about_image       — image (return: url)
 *   about_title       — text
 *   about_description — textarea
 *   about_btn_text    — text
 *   about_btn_url     — url
 */

$image       = get_field('about_image')       ?: '';
$title       = get_field('about_title')       ?: '';
$description = get_field('about_description') ?: '';
$btn_text    = get_field('about_btn_text')    ?: '';
$btn_url     = get_field('about_btn_url')     ?: '#';

$block_id    = 'about-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . esc_attr($block['className']) : '';

if ( ! $title && ! $image && ( is_admin() || defined('REST_REQUEST') ) ) : ?>
    <div class="magnet-about-section magnet-about-section--empty">
        <p style="text-align:center;padding:40px;color:#999;">
            Completa los campos del bloque →
        </p>
    </div>
<?php return;
endif;
?>

<div id="<?php echo esc_attr( $block_id ); ?>" class="magnet-about-section<?php echo $extra_class; ?>">

    <?php if ( $image ) : ?>
    <div
        class="magnet-about__image"
        role="img"
        aria-label="<?php echo esc_attr( $title ); ?>"
        style="background-image: url('<?php echo esc_url( $image ); ?>');"
    ></div>
    <?php endif; ?>

    <?php if ( $title ) : ?>
    <h2 class="magnet-about__title"><?php echo esc_html( $title ); ?></h2>
    <?php endif; ?>

    <?php if ( $description ) : ?>
    <p class="magnet-about__description"><?php echo nl2br( esc_html( $description ) ); ?></p>
    <?php endif; ?>

    <?php if ( $btn_text ) : ?>
    <a href="<?php echo esc_url( $btn_url ); ?>" class="magnet-about__btn">
        <?php echo esc_html( $btn_text ); ?>
    </a>
    <?php endif; ?>

</div>