/**
 * Block: magnet/blog-grid — Load More
 * Magnet People v2 — Takariwa Studio
 */

document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('[data-blog-grid-more]').forEach((btn) => {
        const section = btn.closest('.magnet-blog-grid');
        const list    = section?.querySelector('[data-blog-grid-list]');
        if (!list) return;

        const label = btn.textContent.trim();

        btn.addEventListener('click', async () => {
            if (btn.classList.contains('is-loading')) return;

            const nextPage = parseInt(btn.dataset.page, 10) + 1;
            btn.classList.add('is-loading');
            btn.textContent = 'Loading…';

            const body = new FormData();
            body.append('action', 'magnet_blog_load_more');
            body.append('nonce', btn.dataset.nonce);
            body.append('page', nextPage);
            body.append('per_page', btn.dataset.perPage);
            body.append('exclude', btn.dataset.exclude);

            try {
                const res  = await fetch(btn.dataset.ajaxUrl, { method: 'POST', body, credentials: 'same-origin' });
                const json = await res.json();
                if (!json.success) throw new Error('Load more failed');

                const tpl = document.createElement('template');
                tpl.innerHTML = json.data.html;
                tpl.content.querySelectorAll('.blog-batch').forEach((batch) => batch.classList.add('is-new'));
                list.appendChild(tpl.content);

                btn.dataset.page = nextPage;
                if (!json.data.has_more) {
                    btn.parentElement.remove();
                    return;
                }
            } catch (err) {
                console.error(err);
            }

            btn.classList.remove('is-loading');
            btn.textContent = label;
        });
    });

});
