<?php
/**
 * Template Part: Post Card
 * Usado en index.php, archive.php, y la home
 */
$post_id    = get_the_ID();
$is_locked  = magnet_is_paid_post($post_id);
$has_thumb  = has_post_thumbnail();
$thumb_url  = $has_thumb ? get_the_post_thumbnail_url($post_id, 'card-square') : '';
$card_class = 'post-card';
$card_class .= $has_thumb ? ' post-card--has-image' : ' post-card--no-image';
?>
<article id="post-<?php the_ID(); ?>" class="<?php echo esc_attr($card_class); ?>">
    <a href="<?php the_permalink(); ?>" class="post-card__link" aria-label="<?php the_title_attribute(); ?>">

        <?php if ($has_thumb) : ?>
            <img
                src="<?php echo esc_url($thumb_url); ?>"
                alt="<?php the_title_attribute(); ?>"
                class="post-card__bg"
                loading="lazy"
            >
        <?php endif; ?>

        <div class="post-card__body">
            <?php if ($is_locked) : ?>
                <div class="post-card__lock" aria-label="Contenido exclusivo para miembros">
                    <?php echo magnet_icon('lock'); ?>
                </div>
            <?php endif; ?>

            <?php if ($has_thumb) : ?>
                <h3 class="post-card__title"><?php the_title(); ?></h3>
            <?php else : ?>
                <h3 class="post-card__title"><?php the_title(); ?></h3>
            <?php endif; ?>
        </div>

    </a>
</article>
