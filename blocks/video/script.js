/**
 * Block: magnet/video — autoplay de archivos + carga el iframe de YouTube al hacer click
 * Magnet People v2 — Takariwa Studio
 */

// Autoplay: el navegador solo lo permite con el video silenciado, y el atributo
// muted se pierde en la vista previa de ACF (React), así que se fuerza por JS.
const magnetVideoAutoplay = () => {
    document.querySelectorAll('video[data-video-autoplay]:not([data-autoplay-ready])').forEach((video) => {
        video.dataset.autoplayReady = '1';
        video.muted = true;
        video.play().catch(() => {});
    });
};

magnetVideoAutoplay();
// El editor vuelve a renderizar el bloque al cambiar campos
new MutationObserver(magnetVideoAutoplay).observe(document.documentElement, { childList: true, subtree: true });

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
