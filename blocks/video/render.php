<?php
/**
 * Block: magnet/video
 * Magnet People v2 — Takariwa Studio
 *
 * Campos ACF:
 *   vid_title    — text (opcional, encima del video)
 *   vid_text     — wysiwyg (opcional)
 *   vid_source   — button_group (youtube | file)
 *   vid_youtube  — url (youtube.com/watch?v=…, youtu.be/…, /shorts/…, /embed/…)
 *   vid_file     — file mp4/webm (return: url)
 *   vid_poster   — image (return: id) — portada; con YouTube es opcional
 *   vid_autoplay — true_false — arranca solo, silenciado y en bucle (los navegadores exigen mute)
 *   vid_bg_color — color_picker
 */

$title    = get_field('vid_title')    ?: '';
$text     = get_field('vid_text')     ?: '';
$source   = get_field('vid_source')   ?: 'youtube';
$yt_url   = get_field('vid_youtube')  ?: '';
$file_url = get_field('vid_file')     ?: '';
$poster   = get_field('vid_poster')   ?: 0;
$autoplay = (bool) get_field('vid_autoplay');
$bg_color = get_field('vid_bg_color') ?: '#D8E4EC';

$block_id    = 'video-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . esc_attr($block['className']) : '';

// ID de YouTube a partir de cualquier formato de URL
$yt_id = '';
if ($source === 'youtube' && $yt_url && preg_match('~(?:youtu\.be/|v=|/embed/|/shorts/|/live/)([A-Za-z0-9_-]{11})~', $yt_url, $m)) {
    $yt_id = $m[1];
}
$has_video = $yt_id || ($source === 'file' && $file_url);

if ( ! $has_video && ! $title && ! $text && ( is_admin() || defined('REST_REQUEST') ) ) : ?>
    <div class="magnet-video magnet-video--empty">
        <p style="text-align:center;padding:40px;color:#999;">
            Agrega el link de YouTube o sube el video desde el panel de campos ACF →
        </p>
    </div>
<?php return;
endif;

$poster_url = $poster ? wp_get_attachment_image_url($poster, 'full') : '';
?>

<?php if (current_user_can('edit_posts')) : // Diagnóstico temporal: visible solo en "ver código fuente" para editores ?>
<!-- magnet/video debug · v4 · source=<?php echo esc_html($source); ?> · yt_id=<?php echo esc_html($yt_id ?: '-'); ?> · vid_autoplay=<?php echo esc_html(var_export(get_field('vid_autoplay'), true)); ?> -->
<?php endif; ?>

<section id="<?php echo esc_attr($block_id); ?>"
         class="magnet-video<?php echo $extra_class; ?>"
         style="--video-bg: <?php echo esc_attr($bg_color); ?>;">
    <div class="magnet-video__inner">

        <?php if ($title || $text) : ?>
        <div class="magnet-video__intro">
            <?php if ($title) : ?>
            <h2 class="magnet-video__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <?php if ($text) : ?>
            <div class="magnet-video__text"><?php echo wp_kses_post($text); ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($yt_id && $autoplay) :
            // loop en YouTube requiere playlist = el mismo ID
            $yt_src = add_query_arg([
                'autoplay'    => 1,
                'mute'        => 1,
                'loop'        => 1,
                'playlist'    => $yt_id,
                'playsinline' => 1,
                'rel'         => 0,
            ], 'https://www.youtube-nocookie.com/embed/' . $yt_id); ?>
        <div class="magnet-video__frame">
            <iframe class="magnet-video__iframe"
                    src="<?php echo esc_url($yt_src); ?>"
                    title="<?php echo esc_attr($title ?: 'YouTube video'); ?>"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen></iframe>
        </div>

        <?php elseif ($yt_id) : ?>
        <!-- Fachada: el iframe de YouTube solo se carga al hacer click -->
        <div class="magnet-video__frame">
            <button type="button" class="magnet-video__play"
                    data-video-yt="<?php echo esc_attr($yt_id); ?>"
                    aria-label="<?php echo esc_attr($title ?: 'Reproducir video'); ?>">
                <?php if ($poster) :
                    echo wp_get_attachment_image($poster, 'full', false, [
                        'class'   => 'magnet-video__poster',
                        'sizes'   => '(max-width: 1208px) calc(100vw - 48px), 1160px',
                        'loading' => 'lazy',
                        'alt'     => '',
                    ]);
                else : ?>
                <img class="magnet-video__poster" src="https://i.ytimg.com/vi/<?php echo esc_attr($yt_id); ?>/maxresdefault.jpg" alt="" loading="lazy">
                <?php endif; ?>
                <span class="magnet-video__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.14v13.72a1 1 0 0 0 1.5.86l11.04-6.86a1 1 0 0 0 0-1.72L9.5 4.28A1 1 0 0 0 8 5.14z"/></svg>
                </span>
            </button>
        </div>

        <?php elseif ($source === 'file' && $file_url) : ?>
        <div class="magnet-video__frame">
            <video class="magnet-video__file" controls playsinline
                   <?php echo $autoplay ? 'autoplay muted loop preload="auto" data-video-autoplay' : 'preload="metadata"'; ?>
                   <?php echo $poster_url ? 'poster="' . esc_url($poster_url) . '"' : ''; ?>>
                <source src="<?php echo esc_url($file_url); ?>">
            </video>
        </div>
        <?php endif; ?>

    </div>
</section>
