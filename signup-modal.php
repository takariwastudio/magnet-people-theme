<?php
/**
 * signup-modal.php — Magnet People v2
 * Signup modal (plans) + Sign-in modal
 * Incluir en footer.php antes de wp_footer()
 */
if ( is_user_logged_in() ) return;
?>

<!-- ─────────────────────────────────────────
     Botón flotante "Sign up"
     ───────────────────────────────────────── -->
<a href="#" class="signup-float" id="signupFloatBtn" aria-label="Get Access">
    <?php echo magnet_icon('user', 'signup-float__icon'); ?>
    <span>Sign up</span>
</a>

<!-- ─────────────────────────────────────────
     SIGNUP MODAL — planes Free / Paid
     ───────────────────────────────────────── -->
<div class="magnet-modal" id="signupModal" role="dialog" aria-modal="true" aria-labelledby="signupModalTitle" hidden>
    <div class="magnet-modal__backdrop" id="signupBackdrop"></div>

    <div class="magnet-modal__box">

        <!-- Cerrar -->
        <button class="magnet-modal__close" id="signupModalClose" aria-label="Cerrar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Título -->
        <h2 class="magnet-modal__title" id="signupModalTitle">Join Magnet</h2>

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

        </div><!-- /.magnet-modal__plans -->

        <p class="magnet-modal__footer-link">
            Already a member?
            <a href="#" id="switchToSignin">Sign in</a>
        </p>

    </div><!-- /.magnet-modal__box -->
</div><!-- /#signupModal -->


<!-- ─────────────────────────────────────────
     SIGN-IN MODAL
     ───────────────────────────────────────── -->
<div class="magnet-modal magnet-modal--sm" id="signinModal" role="dialog" aria-modal="true" aria-labelledby="signinModalTitle" hidden>
    <div class="magnet-modal__backdrop" id="signinBackdrop"></div>

    <div class="magnet-modal__box magnet-modal__box--sm">

        <!-- Cerrar -->
        <button class="magnet-modal__close" id="signinModalClose" aria-label="Cerrar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.25" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 20L20 4M4 4l16 16"/>
            </svg>
        </button>

        <!-- Paso 1: email → se envía código + enlace por correo -->
        <div class="signin-step" id="signinStepEmail">
            <h2 class="magnet-modal__title" id="signinModalTitle">Sign in</h2>

            <form class="signin-form" id="signinForm" novalidate>
                <div class="signin-form__field">
                    <label for="signinEmail" class="signin-form__label">Email</label>
                    <input
                        type="email"
                        id="signinEmail"
                        name="email"
                        class="signin-form__input"
                        placeholder="jamie@example.com"
                        required
                        autocomplete="email"
                    >
                </div>

                <p class="signin-form__error" id="signinError" role="alert" hidden></p>

                <button type="submit" class="signin-form__btn" id="signinContinueBtn">
                    Continue
                </button>
            </form>

            <p class="magnet-modal__footer-link">
                Don't have an account?
                <a href="#" id="switchToSignup">Sign up</a>
            </p>
        </div>

        <!-- Paso 2: código de 6 dígitos (o clic en el enlace del correo) -->
        <div class="signin-step signin-step--code" id="signinStepCode" hidden>
            <span class="signin-step__icon"><?php echo magnet_icon('mail'); ?></span>
            <h2 class="magnet-modal__title">Now check your email!</h2>
            <p class="signin-step__text">
                If you have an account, an email has been sent to <span id="signinSentTo"></span>.
                Click the link inside or enter your code below.
            </p>

            <form class="signin-form" id="signinCodeForm" novalidate>
                <input
                    type="text"
                    id="signinCode"
                    name="code"
                    class="signin-form__input signin-form__input--code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="6"
                    placeholder="– – – – – –"
                    aria-label="Sign-in code"
                >
                <p class="signin-form__error" id="signinCodeError" role="alert" hidden></p>

                <a href="#" class="magnet-btn-outline" id="signinOpenMail" target="_blank" rel="noopener" hidden></a>
            </form>
        </div>

    </div><!-- /.magnet-modal__box -->
</div><!-- /#signinModal -->