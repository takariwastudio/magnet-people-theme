/**
 * Block: magnet/video — carga el iframe de YouTube al hacer click
 * Magnet People v2 — Takariwa Studio
 */

document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-video-yt]');
    if (!btn) return;

    const iframe = document.createElement('iframe');
    iframe.src = 'https://www.youtube-nocookie.com/embed/' + btn.dataset.videoYt + '?autoplay=1&rel=0&playsinline=1';
    iframe.title = btn.getAttribute('aria-label') || 'YouTube video';
    iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
    iframe.allowFullscreen = true;
    iframe.className = 'magnet-video__iframe';

    btn.replaceWith(iframe);
});
