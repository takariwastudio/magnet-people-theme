<?php
/**
 * account-modal.php — Magnet People v2
 * Modal de cuenta (usuario logueado). Todas las vistas viven en el mismo modal:
 * cuenta → Account settings / Email preferences / Change plan / Gift membership
 * Incluir en footer.php antes de wp_footer()
 */
if ( ! is_user_logged_in() ) return;

$user      = wp_get_current_user();
$status    = magnet_member_status( $user->ID );
$plans     = magnet_plans_for( $user->ID );
$prefs     = magnet_email_prefs( $user->ID );
$gift      = magnet_paid_plans()[3];
$gift_url  = magnet_gift_checkout_url() ?: magnet_support_url( 'Gift membership' );
$site_name = get_bloginfo( 'name' );

// Lista de beneficios (plan cards y gift)
$render_features = function () { ?>
    <ul class="account-features">
        <?php foreach ( magnet_plan_features() as $feature ) : ?>
        <li><?php echo magnet_icon( 'check' ); ?> <?php echo esc_html( $feature ); ?></li>
        <?php endforeach; ?>
    </ul>
<?php };

// Cabecera de las sub-vistas: Back + título (la X es la del modal)
$render_head = function ( $title ) { ?>
    <header class="account-view__head">
        <button type="button" class="account-view__back" data-account-go="home">
            <?php echo magnet_icon( 'back' ); ?> Back
        </button>
        <h2 class="account-view__title"><?php echo esc_html( $title ); ?></h2>
    </header>
<?php };
?>

<!-- ─────────────────────────────────────────
     Botón flotante "Your account"
     ───────────────────────────────────────── -->
<a href="#account" class="account-float" data-account-open aria-label="Your account">
    <?php echo magnet_icon( 'avatar' ); ?>
</a>

<!-- ─────────────────────────────────────────
     ACCOUNT MODAL
     ───────────────────────────────────────── -->
<div class="magnet-modal magnet-modal--sm account-modal" id="accountModal" role="dialog" aria-modal="true" aria-label="Your account" hidden>
    <div class="magnet-modal__backdrop" data-account-close></div>

    <div class="magnet-modal__box magnet-modal__box--sm">

        <!-- Cerrar -->
        <button class="magnet-modal__close" data-account-close aria-label="Cerrar">
            <?php echo magnet_icon( 'close' ); ?>
        </button>

        <!-- Vista: cuenta -->
        <section class="account-view" data-view="home">
            <span class="account-view__avatar"><?php echo magnet_icon( 'avatar' ); ?></span>
            <h2 class="magnet-modal__title">Your account</h2>

            <?php if ( ! $status['paid'] ) : ?>
            <p class="account-view__text">You currently have a free membership, upgrade to a paid subscription for full access.</p>
            <button type="button" class="signin-form__btn account-view__cta" data-account-go="plans">View plans</button>
            <?php endif; ?>

            <div class="account-list">
                <div class="account-list__row">
                    <div class="account-list__info">
                        <p class="account-list__label" data-account-name><?php echo esc_html( $user->display_name ); ?></p>
                        <p class="account-list__sub" data-account-email><?php echo esc_html( $user->user_email ); ?></p>
                    </div>
                    <button type="button" class="account-list__action" data-account-go="profile">Edit</button>
                </div>

                <?php if ( $status['paid'] ) : ?>
                <div class="account-list__row">
                    <div class="account-list__info">
                        <p class="account-list__label"><?php echo esc_html( $status['name'] ); ?></p>
                        <p class="account-list__sub"><?php echo esc_html( $status['detail'] ); ?></p>
                    </div>
                    <?php if ( $plans ) : ?>
                    <button type="button" class="account-list__action" data-account-go="plans">Change</button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="account-list__row">
                    <div class="account-list__info">
                        <p class="account-list__label">Emails</p>
                        <p class="account-list__sub">Update your preferences</p>
                    </div>
                    <button type="button" class="account-list__action" data-account-go="emails">Manage</button>
                </div>
            </div>

            <?php if ( $status['paid'] ) : ?>
            <div class="account-list">
                <div class="account-list__row">
                    <div class="account-list__info">
                        <p class="account-list__label">Gift membership</p>
                        <p class="account-list__sub">For a friend or colleague</p>
                    </div>
                    <button type="button" class="account-list__action" data-account-go="gift">
                        <?php echo magnet_icon( 'gift' ); ?> Buy
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <div class="account-view__footer">
                <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="magnet-btn-outline magnet-btn-outline--sm">Sign out</a>
                <a href="<?php echo esc_url( magnet_support_url() ); ?>" class="magnet-btn-outline magnet-btn-outline--sm magnet-btn-outline--red">Contact support</a>
            </div>
        </section>

        <!-- Vista: Account settings -->
        <section class="account-view" data-view="profile" hidden>
            <?php $render_head( 'Account settings' ); ?>

            <form class="signin-form" id="accountProfileForm" novalidate>
                <div class="signin-form__field">
                    <label for="accountName" class="signin-form__label">Name</label>
                    <input type="text" id="accountName" name="name" class="signin-form__input"
                           value="<?php echo esc_attr( $user->display_name ); ?>" autocomplete="name">
                </div>
                <div class="signin-form__field">
                    <label for="accountEmail" class="signin-form__label">Email</label>
                    <input type="email" id="accountEmail" name="email" class="signin-form__input"
                           value="<?php echo esc_attr( $user->user_email ); ?>" required autocomplete="email">
                </div>

                <p class="signin-form__error" role="alert" hidden></p>

                <button type="submit" class="signin-form__btn">Save</button>
            </form>
        </section>

        <!-- Vista: Email preferences (se guarda al cambiar cada toggle) -->
        <section class="account-view" data-view="emails" hidden>
            <?php $render_head( 'Email preferences' ); ?>

            <form id="accountEmailsForm">
                <div class="account-list">
                    <?php foreach ( magnet_email_lists() as $key => $list ) : ?>
                    <label class="account-list__row">
                        <span class="account-list__info">
                            <span class="account-list__label"><?php echo esc_html( $list['label'] ); ?></span>
                            <?php if ( $list['desc'] ) : ?>
                            <span class="account-list__sub"><?php echo esc_html( $list['desc'] ); ?></span>
                            <?php endif; ?>
                        </span>
                        <input type="checkbox" class="account-toggle" name="prefs[<?php echo esc_attr( $key ); ?>]" value="1"
                               <?php checked( $prefs[ $key ] ); ?>>
                    </label>
                    <?php endforeach; ?>

                    <div class="account-list__help">
                        <span>Not receiving emails?</span>
                        <a href="<?php echo esc_url( magnet_support_url( 'Not receiving emails' ) ); ?>">Get help &rarr;</a>
                    </div>
                </div>

                <button type="button" class="magnet-btn-outline" id="accountUnsubscribeAll">Unsubscribe from all emails</button>
            </form>
        </section>

        <!-- Vista: Change plan / View plans (todos los planes pagos menos el actual) -->
        <section class="account-view" data-view="plans" hidden>
            <?php $render_head( $status['paid'] ? 'Change plan' : 'Choose a plan' ); ?>

            <?php foreach ( $plans as $plan ) : ?>
            <div class="account-plan">
                <p class="account-plan__name"><?php echo esc_html( $plan['name'] ); ?></p>
                <p class="account-plan__price">
                    <span class="account-plan__currency">$</span><span class="account-plan__amount"><?php echo esc_html( number_format_i18n( $plan['amount'] ) ); ?></span>
                    <span class="account-plan__period">/<?php echo esc_html( $plan['period'] ); ?></span>
                </p>
                <p class="account-plan__tagline"><?php echo esc_html( magnet_plan_tagline() ); ?></p>
                <?php $render_features(); ?>
                <a href="<?php echo esc_url( magnet_plan_checkout_url( $plan ) ); ?>" class="signin-form__btn account-plan__btn">Continue</a>
            </div>
            <?php endforeach; ?>
        </section>

        <!-- Vista: Gift a membership (pantalla completa) -->
        <?php if ( $status['paid'] ) : ?>
        <section class="account-view gift" data-view="gift" hidden>
            <div class="gift__main">
                <button type="button" class="gift__back" data-account-go="home">&larr; Back</button>

                <div class="gift__content">
                    <h2 class="gift__title">Gift a membership</h2>
                    <p class="gift__subtitle">Share a full membership to <?php echo esc_html( $site_name ); ?> with a friend or colleague</p>

                    <p class="gift__label">Membership details</p>
                    <p class="gift__duration"><?php echo esc_html( $gift['duration'] ); ?> membership</p>

                    <div class="gift__plan">
                        <div class="gift__plan-head">
                            <span><?php echo esc_html( $gift['name'] ); ?></span>
                            <span><?php echo esc_html( magnet_plan_price( $gift ) ); ?></span>
                        </div>
                        <p class="gift__plan-desc"><?php echo esc_html( magnet_plan_tagline() ); ?></p>
                        <?php $render_features(); ?>
                    </div>

                    <a href="<?php echo esc_url( $gift_url ); ?>" class="gift__btn">Continue to delivery details</a>
                </div>
            </div>

            <div class="gift__aside">
                <div class="gift-card">
                    <span class="gift-card__slot"></span>
                    <p class="gift-card__duration"><?php echo esc_html( $gift['duration'] ); ?></p>
                    <p class="gift-card__plan"><?php echo esc_html( $gift['name'] ); ?> membership</p>
                    <dl class="gift-card__meta">
                        <dt>From</dt>
                        <dd data-account-name><?php echo esc_html( $user->display_name ); ?></dd>
                        <dt>Gift value</dt>
                        <dd><?php echo esc_html( magnet_plan_price( $gift ) ); ?></dd>
                    </dl>
                    <div class="gift-card__brand"><?php echo esc_html( $site_name ); ?></div>
                </div>
            </div>
        </section>
        <?php endif; ?>

    </div><!-- /.magnet-modal__box -->
</div><!-- /#accountModal -->
