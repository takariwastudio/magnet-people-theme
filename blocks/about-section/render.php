<?php
/**
 * Block: magnet/about-section
 * Magnet People v2 — Takariwa Studio
 *
 * Campo ACF:
 *   about_columns — repeater (max 3)
 *     col_image    — image (return: url)
 *     col_title    — text
 *     col_desc     — textarea
 *     col_btn_text — text
 *     col_btn_url  — url
 */

$columns     = get_field('about_columns') ?: [];
$count       = count($columns);
$block_id    = 'about-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . esc_attr($block['className']) : '';

if ( empty($columns) && ( is_admin() || defined('REST_REQUEST') ) ) : ?>
    <div class="magnet-about magnet-about--empty">
        <p style="text-align:center;padding:40px;color:#999;">
            Agrega columnas desde el panel de campos ACF →
        </p>
    </div>
<?php return;
endif;
?>

<section id="<?php echo esc_attr($block_id); ?>"
         class="magnet-about magnet-about--cols-<?php echo (int)$count; ?><?php echo $extra_class; ?>">
    <div class="magnet-about__grid">

        <?php foreach ($columns as $col) :
            $img      = $col['col_image']    ?? '';
            $title    = $col['col_title']    ?? '';
            $desc     = $col['col_desc']     ?? '';
            $btn_text = $col['col_btn_text'] ?? '';
            $btn_url  = $col['col_btn_url']  ?? '#';
        ?>
        <div class="magnet-about__col">

            <?php if ($img) : ?>
            <div class="magnet-about__image"
                 role="img"
                 aria-label="<?php echo esc_attr($title); ?>"
                 style="background-image: url('<?php echo esc_url($img); ?>');">
            </div>
            <?php endif; ?>

            <?php if ($title) : ?>
            <h2 class="magnet-about__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if ($desc) : ?>
            <p class="magnet-about__description"><?php echo nl2br(esc_html($desc)); ?></p>
            <?php endif; ?>

            <?php if ($btn_text) : ?>
            <a href="<?php echo esc_url($btn_url); ?>" class="magnet-about__btn">
                <?php echo esc_html($btn_text); ?>
            </a>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>

    </div>
</section>