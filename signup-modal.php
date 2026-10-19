<?php
/**
 * signup-modal.php — Magnet People v2
 * Modal de signup con planes PMPro
 * Incluir en footer.php antes de wp_footer()
 */
if ( is_user_logged_in() ) return;
?>

<!-- Botón flotante -->
<a href="#" class="signup-float" id="signupFloatBtn" aria-label="Get Access">
    <?php echo magnet_icon('user', 'signup-float__icon'); ?>
    <span>Signup</span>
</a>

<!-- Modal overlay -->
<div class="magnet-modal" id="signupModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" hidden>
    <div class="magnet-modal__backdrop" id="modalBackdrop"></div>

    <div class="magnet-modal__box">

        <!-- Cerrar -->
        <button class="magnet-modal__close" id="modalClose" aria-label="Cerrar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Título -->
        <h2 class="magnet-modal__title" id="modalTitle">Join Magnet</h2>

        <!-- Toggle mensual / anual -->
        <div class="magnet-modal__toggle">
            <button class="toggle-btn toggle-btn--active" data-period="monthly">Monthly</button>
            <button class="toggle-btn" data-period="yearly">Yearly <span class="toggle-badge">Save 14%</span></button>
        </div>

        <!-- Cards de planes -->
        <div class="magnet-modal__plans">

            <!-- Plan Free -->
            <div class="plan-card">
                <div class="plan-card__header">
                    <span class="plan-card__name">Free</span>
                    <div class="plan-card__price">
                        <span class="plan-card__amount">$0</span>
                    </div>
                    <p class="plan-card__tagline">Get amazing benefits</p>
                </div>
                <ul class="plan-card__features">
                    <li><?php echo magnet_icon('check'); ?> Receive our Newsletter</li>
                    <li><?php echo magnet_icon('check'); ?> Access to all public posts</li>
                </ul>
                <a href="<?php echo esc_url( pmpro_url('checkout', '?level=1') ); ?>"
                   class="plan-card__btn plan-card__btn--outline">
                    Choose Free
                </a>
            </div>

            <!-- Plan Pago -->
            <div class="plan-card plan-card--featured">
                <div class="plan-card__header">
                    <span class="plan-card__name">Magnet People</span>
                    <div class="plan-card__price">
                        <span class="plan-card__amount" id="paidAmount">$35</span>
                        <span class="plan-card__period" id="paidPeriod">/month</span>
                    </div>
                    <p class="plan-card__tagline">Somos una comunidad de profesionales que se unen por el interés de crear contenido responsable y de valor en las redes sociales</p>
                </div>
                <ul class="plan-card__features">
                    <li><?php echo magnet_icon('check'); ?> Acceso a nuestra comunidad privada en WhatsApp</li>
                    <li><?php echo magnet_icon('check'); ?> Biblioteca exclusiva de blogs, recursos y directorios</li>
                    <li><?php echo magnet_icon('check'); ?> Exposición en redes sociales y website al menos una vez al año</li>
                    <li><?php echo magnet_icon('check'); ?> Exposición a marcas con potencial para futuras colaboraciones</li>
                    <li><?php echo magnet_icon('check'); ?> 1 Community Gathering trimestral</li>
                    <li><?php echo magnet_icon('check'); ?> Newsletter mensual con oportunidades y tendencias</li>
                    <li><?php echo magnet_icon('check'); ?> 25% de descuento en talleres y experiencias educativas</li>
                    <li><?php echo magnet_icon('check'); ?> 10% de descuento en servicios de DOT Communications</li>
                    <li><?php echo magnet_icon('check'); ?> Plus 1 para eventos seleccionados</li>
                </ul>
                <a href="<?php echo esc_url( pmpro_url('checkout', '?level=2') ); ?>"
                   class="plan-card__btn plan-card__btn--red"
                   id="paidCheckoutBtn">
                    Get Access
                </a>
            </div>

        </div>

        <p class="magnet-modal__signin">
            Already a member? <a href="<?php echo esc_url( wp_login_url() ); ?>">Sign in</a>
        </p>

    </div>
</div>