/**
 * Magnet People — main.js v2
 * Takariwa Studio
 */

document.addEventListener('DOMContentLoaded', () => {

    // ── Header: transparente → blanco al scroll ──
    const header = document.getElementById('site-header');
    if (header) {
        const onScroll = () => {
            header.classList.toggle('scrolled', window.scrollY > 20);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll(); // estado inicial
    }

    // ── Mobile nav toggle ──
    const toggle  = document.querySelector('.nav-toggle');
    const mobileNav = document.getElementById('mobile-nav');

    if (toggle && mobileNav) {
        toggle.addEventListener('click', () => {
            const isOpen = mobileNav.classList.toggle('is-open');
            toggle.classList.toggle('is-open', isOpen);
            toggle.setAttribute('aria-expanded', isOpen);
            document.body.style.overflow = isOpen ? 'hidden' : '';
        });

        // Cerrar al hacer clic en un enlace
        mobileNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileNav.classList.remove('is-open');
                toggle.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            });
        });
    }

    // ── Cerrar nav al hacer clic fuera ──
    document.addEventListener('click', (e) => {
        if (mobileNav?.classList.contains('is-open') &&
            !mobileNav.contains(e.target) &&
            !toggle?.contains(e.target)) {
            mobileNav.classList.remove('is-open');
            toggle?.classList.remove('is-open');
            toggle?.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }
    });

});
