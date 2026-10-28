<?php
/**
 * Miembros — Magnet People v2
 * Takariwa Studio
 *
 * Login sin contraseña (código de 6 dígitos + enlace por correo, como Ghost),
 * datos del modal de cuenta y sus endpoints AJAX.
 */

const MAGNET_LOGIN_TTL      = HOUR_IN_SECONDS; // vigencia del código y del enlace
const MAGNET_LOGIN_MAX_TRIES = 5;              // intentos de código antes de invalidarlo
const MAGNET_LOGIN_MAX_USES  = 3;              // usos del enlace (Outlook/antispam lo abren antes que el usuario)

// ── Planes ──

// Planes pagos por ID de nivel PMPro (Free = nivel 1). Mismos valores que assets/js/modal.js
function magnet_paid_plans() {
    return [
        2 => ['level' => 2, 'name' => 'Magnet People', 'amount' => 35,  'period' => 'month', 'duration' => '1 month'],
        3 => ['level' => 3, 'name' => 'Magnet People', 'amount' => 400, 'period' => 'year',  'duration' => '1 year'],
    ];
}

function magnet_plan_tagline() {
    return 'Somos una comunidad de profesionales que se unen por el interés de crear contenido responsable y de valor en las redes sociales';
}

function magnet_plan_features() {
    return [
        'Acceso a nuestra comunidad privada en WhatsApp, diseñada para conectar, colaborar y crecer',
        'Biblioteca exclusiva de Magnet con blogs, recursos y directorios curados',
        'Exposición en nuestras redes sociales y website al menos una vez al año',
        'Exposición a marcas con potencial para futuras colaboraciones',
        '1 Community Gathering trimestral para networking y aprendizaje',
        'Newsletter mensual con oportunidades, tendencias y noticias de la industria',
        '25% de descuento en talleres y experiencias educativas',
        '10% de descuento en servicios de DOT Communications',
        'Plus 1 para eventos seleccionados',
    ];
}

function magnet_plan_price($plan) {
    return '$' . number_format_i18n($plan['amount']);
}

function magnet_plan_checkout_url($plan) {
    return function_exists('pmpro_url')
        ? pmpro_url('checkout', '?level=' . $plan['level'])
        : magnet_get_join_url();
}

// Membresía del usuario: plan pago (o no) y la línea de detalle ("Complimentary", "$35/month – Renews …")
function magnet_member_status($user_id) {
    $level = function_exists('pmpro_getMembershipLevelForUser') ? pmpro_getMembershipLevelForUser($user_id) : null;
    $plan  = $level ? (magnet_paid_plans()[(int) $level->id] ?? null) : null;

    if (!$plan) {
        return ['paid' => false, 'level' => $level ? (int) $level->id : 0];
    }

    // Sin orden pagada = asignado a mano desde el admin
    $detail = 'Complimentary';
    if (class_exists('MemberOrder')) {
        $order = new MemberOrder();
        $order->getLastMemberOrder($user_id, ['success']);
        if (!empty($order->id) && (float) $order->total > 0) {
            $detail = magnet_plan_price($plan) . '/' . $plan['period'];
            $next   = function_exists('pmpro_next_payment') ? pmpro_next_payment($user_id) : false;
            if ($next) {
                $detail .= ' – Renews ' . date_i18n('j M Y', $next);
            }
        }
    }

    return ['paid' => true, 'level' => $plan['level'], 'name' => $plan['name'], 'detail' => $detail];
}

// Planes que se ofrecen en "Change plan" / "View plans": todos menos el actual
function magnet_plans_for($user_id) {
    $current = magnet_member_status($user_id)['level'];
    return array_filter(magnet_paid_plans(), fn($plan) => $plan['level'] !== $current);
}

// Regalo: URL de compra (p. ej. checkout del add-on PMPro Gift Memberships). Vacío = "Contact support"
function magnet_gift_checkout_url() {
    return apply_filters('magnet_gift_checkout_url', '');
}

function magnet_support_url($subject = '') {
    $email = apply_filters('magnet_support_email', get_option('admin_email'));
    return 'mailto:' . $email . ($subject ? '?subject=' . rawurlencode($subject) : '');
}

// ── Preferencias de correo ──

function magnet_email_lists() {
    return [
        'magnet'   => ['label' => 'Magnet', 'desc' => ''],
        'welcome'  => ['label' => 'Welcome to Magnet People!', 'desc' => ''],
        'comments' => ['label' => 'Comments', 'desc' => 'Get notified when someone replies to your comment'],
    ];
}

// Todas activas por defecto (también las listas nuevas que se agreguen después)
function magnet_email_prefs($user_id) {
    $saved = get_user_meta($user_id, 'magnet_email_prefs', true);
    $prefs = [];
    foreach (array_keys(magnet_email_lists()) as $key) {
        $prefs[$key] = !is_array($saved) || !isset($saved[$key]) || $saved[$key];
    }
    return $prefs;
}

// ── Login sin contraseña ──

function magnet_login_hash($value) {
    return hash_hmac('sha256', $value, wp_salt('auth'));
}

// Botón "Open Gmail/Outlook/…" según el proveedor del correo (dominio conocido o registros MX)
function magnet_email_provider($email) {
    $gmail   = ['Gmail', 'https://mail.google.com/mail/u/0/#inbox'];
    $outlook = ['Outlook', 'https://outlook.live.com/mail/'];
    $office  = ['Outlook', 'https://outlook.office.com/mail/'];
    $yahoo   = ['Yahoo Mail', 'https://mail.yahoo.com/'];
    $icloud  = ['iCloud Mail', 'https://www.icloud.com/mail/'];
    $known   = [
        'gmail.com'   => $gmail,   'googlemail.com' => $gmail,
        'outlook.com' => $outlook, 'hotmail.com'    => $outlook, 'live.com' => $outlook, 'msn.com' => $outlook,
        'yahoo.com'   => $yahoo,   'ymail.com'      => $yahoo,
        'icloud.com'  => $icloud,  'me.com'         => $icloud,  'mac.com'  => $icloud,
    ];

    $domain   = strtolower(substr(strrchr($email, '@'), 1));
    $provider = $known[$domain] ?? null;

    if (!$provider && $domain && function_exists('getmxrr')) {
        $hosts = [];
        @getmxrr($domain, $hosts);
        foreach ($hosts as $host) {
            $host = strtolower(rtrim($host, '.'));
            if (str_ends_with($host, 'google.com') || str_ends_with($host, 'googlemail.com')) { $provider = $gmail; break; }
            if (str_ends_with($host, 'outlook.com')) { $provider = $office; break; }
            if (str_contains($host, 'yahoodns')) { $provider = $yahoo; break; }
        }
    }

    return $provider ? ['name' => $provider[0], 'url' => $provider[1]] : null;
}

// Genera código + token, los guarda hasheados y envía el correo
function magnet_issue_login($user, $redirect) {
    $prev = get_user_meta($user->ID, '_magnet_login', true);
    if (is_array($prev) && time() - $prev['issued'] < 30) return; // evita reenvíos en ráfaga

    $code  = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $token = wp_generate_password(32, false);

    update_user_meta($user->ID, '_magnet_login', [
        'code'   => magnet_login_hash($code),
        'token'  => magnet_login_hash($token),
        'issued' => time(),
        'tries'  => 0,
        'uses'   => 0,
    ]);

    $link = add_query_arg([
        'magnet_login' => $token,
        'u'            => $user->ID,
        'r'            => rawurlencode($redirect),
    ], home_url('/'));

    magnet_send_login_email($user, $code, $link);
}

// Valida código o token. Cada intento de código cuenta; el enlace solo cuenta los usos válidos
function magnet_verify_login($user_id, $type, $value) {
    $rec = get_user_meta($user_id, '_magnet_login', true);
    if (!is_array($rec) || time() - $rec['issued'] > MAGNET_LOGIN_TTL) return false;

    $counter = $type === 'code' ? 'tries' : 'uses';
    $max     = $type === 'code' ? MAGNET_LOGIN_MAX_TRIES : MAGNET_LOGIN_MAX_USES;
    if ($rec[$counter] >= $max) return false;

    $ok = hash_equals($rec[$type], magnet_login_hash($value));
    if ($ok || $type === 'code') {
        $rec[$counter]++;
        update_user_meta($user_id, '_magnet_login', $rec);
    }
    return $ok;
}

function magnet_log_user_in($user) {
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);
    do_action('wp_login', $user->user_login, $user);
}

// Paso 1: email → envía el correo. Misma respuesta exista o no la cuenta (no revela quién es miembro)
add_action('wp_ajax_magnet_send_login', 'magnet_send_login');
add_action('wp_ajax_nopriv_magnet_send_login', 'magnet_send_login');
function magnet_send_login() {
    check_ajax_referer('magnet_login', 'nonce');

    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Please enter a valid email address.']);
    }

    $redirect = wp_validate_redirect(esc_url_raw(wp_unslash($_POST['redirect'] ?? '')), home_url('/'));
    $user     = get_user_by('email', $email);
    if ($user) {
        magnet_issue_login($user, remove_query_arg('magnet_notice', $redirect));
    }

    wp_send_json_success(['provider' => magnet_email_provider($email)]);
}

// Paso 2: código de 6 dígitos
add_action('wp_ajax_magnet_verify_code', 'magnet_verify_code');
add_action('wp_ajax_nopriv_magnet_verify_code', 'magnet_verify_code');
function magnet_verify_code() {
    check_ajax_referer('magnet_login', 'nonce');

    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $code  = preg_replace('/\D/', '', (string) ($_POST['code'] ?? ''));
    $user  = get_user_by('email', $email);

    if (!$user || strlen($code) !== 6 || !magnet_verify_login($user->ID, 'code', $code)) {
        wp_send_json_error(['message' => 'Invalid or expired code. Please try again.']);
    }

    delete_user_meta($user->ID, '_magnet_login');
    magnet_log_user_in($user);
    wp_send_json_success();
}

// Enlace "Sign in now" del correo
add_action('init', function () {
    if (empty($_GET['magnet_login']) || empty($_GET['u'])) return;

    $user  = get_user_by('id', absint($_GET['u']));
    $token = sanitize_text_field(wp_unslash($_GET['magnet_login']));
    $back  = wp_validate_redirect(wp_unslash($_GET['r'] ?? ''), home_url('/'));

    if ($user && magnet_verify_login($user->ID, 'token', $token)) {
        magnet_log_user_in($user);
        $notice = 'signin';
    } else {
        $notice = 'link-expired';
    }

    wp_safe_redirect(add_query_arg('magnet_notice', $notice, $back));
    exit;
});

function magnet_send_login_email($user, $code, $link) {
    $site    = get_bloginfo('name');
    $subject = sprintf('🔑 Sign in to %s with code %s', $site, $code);

    ob_start(); ?>
<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#ffffff;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;">
    <tr><td align="center" style="padding:40px 16px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#15212a;">
            <tr><td style="font-size:18px;font-weight:700;padding-bottom:16px;">Hey there,</td></tr>
            <tr><td style="font-size:16px;line-height:1.5;padding-bottom:20px;">Welcome back! Here's your code to sign in to <?php echo esc_html($site); ?>:</td></tr>
            <tr><td align="center" style="background:#f0f0f0;border-radius:6px;padding:16px;font-size:28px;font-weight:700;letter-spacing:6px;color:#15212a;"><?php echo esc_html($code); ?></td></tr>
            <tr><td style="font-size:16px;line-height:1.5;padding:28px 0 16px;">Or, skip the code and sign in directly:</td></tr>
            <tr><td align="center" style="background:#dc2327;border-radius:6px;">
                <a href="<?php echo esc_url($link); ?>" style="display:block;padding:12px 24px;font-size:16px;font-weight:600;color:#ffffff;text-decoration:none;">Sign in now</a>
            </td></tr>
            <tr><td style="font-size:14px;line-height:1.5;color:#738a94;padding-top:28px;">
                You can also copy &amp; paste this URL into your browser:<br>
                <a href="<?php echo esc_url($link); ?>" style="color:#738a94;word-break:break-all;"><?php echo esc_html($link); ?></a>
            </td></tr>
            <tr><td style="font-size:12px;line-height:1.5;color:#9aa5ab;padding-top:40px;">
                If you did not make this request, you can safely ignore this email.<br>
                This message was sent from <a href="<?php echo esc_url(home_url('/')); ?>" style="color:#9aa5ab;"><?php echo esc_html(wp_parse_url(home_url(), PHP_URL_HOST)); ?></a>
                to <a href="mailto:<?php echo esc_attr($user->user_email); ?>" style="color:#9aa5ab;"><?php echo esc_html($user->user_email); ?></a>.
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
    <?php
    wp_mail($user->user_email, $subject, ob_get_clean(), ['Content-Type: text/html; charset=UTF-8']);
}

// ── Cuenta (usuario logueado) ──

add_action('wp_ajax_magnet_update_account', function () {
    check_ajax_referer('magnet_account', 'nonce');

    $user  = wp_get_current_user();
    $name  = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));

    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Please enter a valid email address.']);
    }
    $owner = email_exists($email);
    if ($owner && $owner !== $user->ID) {
        wp_send_json_error(['message' => 'That email is already in use.']);
    }

    $parts  = explode(' ', $name, 2);
    $result = wp_update_user([
        'ID'           => $user->ID,
        'user_email'   => $email,
        'display_name' => $name ?: $user->user_login,
        'first_name'   => $parts[0],
        'last_name'    => $parts[1] ?? '',
    ]);
    if (is_wp_error($result)) {
        wp_send_json_error(['message' => $result->get_error_message()]);
    }

    wp_send_json_success(['name' => $name, 'email' => $email]);
});

add_action('wp_ajax_magnet_email_prefs', function () {
    check_ajax_referer('magnet_account', 'nonce');

    $in    = (array) ($_POST['prefs'] ?? []);
    $prefs = [];
    foreach (array_keys(magnet_email_lists()) as $key) {
        $prefs[$key] = !empty($in[$key]);
    }
    update_user_meta(get_current_user_id(), 'magnet_email_prefs', $prefs);

    wp_send_json_success($prefs);
});
