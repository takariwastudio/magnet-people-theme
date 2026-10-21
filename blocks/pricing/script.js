/**
 * Block: magnet/pricing — toggle Monthly / Yearly
 * Magnet People v2 — Takariwa Studio
 */

document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('[data-pricing]').forEach((section) => {
        const buttons = section.querySelectorAll('.magnet-pricing__toggle-btn');
        const amount  = section.querySelector('[data-pricing-amount]');
        const period  = section.querySelector('[data-pricing-period]');
        const cta     = section.querySelector('[data-pricing-btn]');

        buttons.forEach((btn) => {
            btn.addEventListener('click', () => {
                buttons.forEach((b) => {
                    b.classList.toggle('is-active', b === btn);
                    b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
                });

                amount.textContent = btn.dataset.price;
                period.textContent = btn.dataset.period;
                if (cta) cta.href = btn.dataset.url;
            });
        });
    });

});
