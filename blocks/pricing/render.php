<?php
/**
 * Block: magnet/pricing
 * Magnet People v2 — Takariwa Studio
 *
 * Campos ACF:
 *   pr_intro          — wysiwyg (texto encima, opcional)
 *   pr_title          — text ("Choose the best program for you!")
 *   pr_plan_name      — text (etiqueta sobre la card)
 *   pr_monthly_*      — label / price / period / url (Stripe Payment Link)
 *   pr_yearly_*       — label / price / period / url — sin URL se oculta el toggle
 *   pr_description    — textarea
 *   pr_features       — repeater (feature_text)
 *   pr_btn_text       — text
 *   pr_bg_color       — color_picker
 *   pr_gradient_color — color_picker (franja degradada detrás de la card)
 */

$intro       = get_field('pr_intro')          ?: '';
$title       = get_field('pr_title')          ?: '';
$plan_name   = get_field('pr_plan_name')      ?: '';
$description = get_field('pr_description')    ?: '';
$features    = get_field('pr_features')       ?: [];
$btn_text    = get_field('pr_btn_text')       ?: 'Sign up now';
$bg_color    = get_field('pr_bg_color')       ?: '#D8E4EC';
$gradient    = get_field('pr_gradient_color') ?: '#00D4FF';

$plans = [
    'monthly' => [
        'label'  => get_field('pr_monthly_label')  ?: 'Monthly',
        'price'  => get_field('pr_monthly_price')  ?: '',
        'period' => get_field('pr_monthly_period') ?: '',
        'url'    => get_field('pr_monthly_url')    ?: '',
    ],
    'yearly' => [
        'label'  => get_field('pr_yearly_label')   ?: 'Yearly',
        'price'  => get_field('pr_yearly_price')   ?: '',
        'period' => get_field('pr_yearly_period')  ?: '',
        'url'    => get_field('pr_yearly_url')     ?: '',
    ],
];
$has_toggle = $plans['yearly']['price'] && $plans['yearly']['url'];
$current    = $plans['monthly'];

$block_id    = 'pricing-' . $block['id'];
$extra_class = isset($block['className']) ? ' ' . esc_attr($block['className']) : '';

if ( ! $current['price'] && ( is_admin() || defined('REST_REQUEST') ) ) : ?>
    <div class="magnet-pricing magnet-pricing--empty">
        <p style="text-align:center;padding:40px;color:#999;">
            Agrega precios y links de Stripe desde el panel de campos ACF →
        </p>
    </div>
<?php return;
endif;
?>

<section id="<?php echo esc_attr($block_id); ?>"
         class="magnet-pricing<?php echo $extra_class; ?>"
         style="--pricing-bg: <?php echo esc_attr($bg_color); ?>; --pricing-gradient: <?php echo esc_attr($gradient); ?>;"
         data-pricing>

    <?php if ($intro || $title) : ?>
    <div class="magnet-pricing__intro">
        <?php if ($intro) : ?>
        <div class="magnet-pricing__text"><?php echo wp_kses_post($intro); ?></div>
        <?php endif; ?>
        <?php if ($title) : ?>
        <h2 class="magnet-pricing__title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="magnet-pricing__band">

        <?php if ($has_toggle) : ?>
        <div class="magnet-pricing__toggle" role="group" aria-label="Billing period">
            <?php foreach ($plans as $period => $plan) : ?>
            <button type="button"
                    class="magnet-pricing__toggle-btn<?php echo $period === 'monthly' ? ' is-active' : ''; ?>"
                    aria-pressed="<?php echo $period === 'monthly' ? 'true' : 'false'; ?>"
                    data-price="<?php echo esc_attr($plan['price']); ?>"
                    data-period="<?php echo esc_attr($plan['period']); ?>"
                    data-url="<?php echo esc_url($plan['url']); ?>">
                <?php echo esc_html($plan['label']); ?>
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="magnet-pricing__card">
            <?php if ($plan_name) : ?>
            <span class="magnet-pricing__badge"><?php echo esc_html($plan_name); ?></span>
            <?php endif; ?>

            <p class="magnet-pricing__price">
                <span class="magnet-pricing__amount" data-pricing-amount><?php echo esc_html($current['price']); ?></span>
                <span class="magnet-pricing__period" data-pricing-period><?php echo esc_html($current['period']); ?></span>
            </p>

            <?php if ($description) : ?>
            <p class="magnet-pricing__desc"><?php echo esc_html($description); ?></p>
            <?php endif; ?>

            <?php if ($features) : ?>
            <ul class="magnet-pricing__features">
                <?php foreach ($features as $row) : if (empty($row['feature_text'])) continue; ?>
                <li><?php echo esc_html($row['feature_text']); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <?php if ($current['url']) : ?>
            <a href="<?php echo esc_url($current['url']); ?>" class="magnet-pricing__btn" data-pricing-btn>
                <?php echo esc_html($btn_text); ?>
            </a>
            <?php endif; ?>
        </div>

    </div>
</section>
