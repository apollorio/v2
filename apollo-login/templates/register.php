<?php

/**
 * Apollo Registration Template
 *
 * @package Apollo\Login
 * @since 2.0.0
 */

// Prevent direct access.
if (! defined('ABSPATH')) {
    exit;
}

// Redirect already-logged-in users to explore
if (is_user_logged_in()) {
    wp_safe_redirect(home_url('/explore'));
    exit;
}

// Get configuration (mirrored from login.php — required for apolloAuthConfig).
$auth_config = apply_filters(
    'apollo_auth_config',
    array(
        'ajax_url'             => admin_url('admin-ajax.php'),
        'nonce'                => wp_create_nonce('apollo_auth_nonce'),
        'max_failed_attempts'  => 3,
        'lockout_duration'     => 60,
        'simon_levels'         => 4,
        'reaction_targets'     => 4,
        'redirect_after_login' => home_url('/explore'),
        'terms_url'            => 'https://apollo.rio.br/politica',
        'bug_report_url'       => 'https://apollo.rio.br/bug/',
        'show_instagram'       => true,
        'require_cpf'          => true,
    )
);

// Get available sounds/genres for registration from apollo-core GLOBAL BRIDGE taxonomy
$available_sounds = array();
if (taxonomy_exists('sound')) {
    $sound_terms = get_terms(
        array(
            'taxonomy'   => 'sound',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        )
    );

    if (! is_wp_error($sound_terms) && ! empty($sound_terms)) {
        foreach ($sound_terms as $term) {
            $available_sounds[$term->slug] = $term->name;
        }
    }
}

// Fallback if taxonomy not available yet
if (empty($available_sounds)) {
    $available_sounds = apply_filters(
        'apollo_registration_sounds',
        array(
            // Techno & Derivatives
            'techno'                  => 'Techno',
            'detroit-techno'          => 'Detroit Techno',
            'minimal-techno'          => 'Minimal Techno',
            'acid-techno'             => 'Acid Techno',
            'industrial-techno'       => 'Industrial Techno',
            'schranz'                 => 'Schranz',
            'tekno'                   => 'Tekno',

            // House & Variants
            'house'                   => 'House',
            'deep-house'              => 'Deep House',
            'chicago-house'           => 'Chicago House',
            'progressive-house'       => 'Progressive House',
            'electro-house'           => 'Electro House',
            'funky-house'             => 'Funky House',
            'tech-house'              => 'Tech House',
            'micro-house'             => 'Micro House',
            'future-house'            => 'Future House',
            'tropical-house'          => 'Tropical House',

            // Trance & Psy
            'trance'                  => 'Trance',
            'progressive-trance'      => 'Progressive Trance',
            'uplifting-trance'        => 'Uplifting Trance',
            'psytrance'               => 'Psytrance',
            'full-on'                 => 'Full On',
            'goa-trance'              => 'Goa Trance',
            'dark-psy'                => 'Dark Psy',
            'forest-psy'              => 'Forest Psy',

            // Bass Music
            'drum-bass'               => 'Drum & Bass',
            'jungle'                  => 'Jungle',
            'liquid-funk'             => 'Liquid Funk',
            'neurofunk'               => 'Neurofunk',
            'dubstep'                 => 'Dubstep',
            'brostep'                 => 'Brostep',
            'post-dubstep'            => 'Post Dubstep',
            'trap'                    => 'Trap',
            'future-bass'             => 'Future Bass',

            // Breakbeat & Funk
            'funk'                    => 'Funk',
            'breakbeat'               => 'Breakbeat',
            'big-beat'                => 'Big Beat',
            'nu-funk'                 => 'Nu Funk',
            'broken-beat'             => 'Broken Beat',

            // Ambient & Experimental
            'ambient'                 => 'Ambient',
            'intelligent-dance-music' => 'Intelligent Dance Music',
            'glitch'                  => 'Glitch',
            'experimental'            => 'Experimental',
            'noise'                   => 'Noise',

            // Hard & Extreme
            'hard'                    => 'Hard',
            'hardcore'                => 'Hardcore',
            'hardstyle'               => 'Hardstyle',
            'gabber'                  => 'Gabber',
            'speedcore'               => 'Speedcore',

            // Other Electronic
            'electro'                 => 'Electro',
            'electroclash'            => 'Electroclash',
            'synthwave'               => 'Synthwave',
            'retrowave'               => 'Retrowave',
            'outrun'                  => 'Outrun',
            'vaporwave'               => 'Vaporwave',
            'chillwave'               => 'Chillwave',
        )
    );
}

// Localize script configuration (required for apolloAuthConfig nonce injection).
$js_config = array(
    'ajaxUrl'            => $auth_config['ajax_url'],
    'nonce'              => $auth_config['nonce'],
    'maxFailedAttempts'  => $auth_config['max_failed_attempts'],
    'lockoutDuration'    => $auth_config['lockout_duration'],
    'simonLevels'        => $auth_config['simon_levels'],
    'reactionTargets'    => $auth_config['reaction_targets'],
    'redirectAfterLogin' => $auth_config['redirect_after_login'],
    'strings'            => array(
        'loginSuccess'   => __('Acesso autorizado. Redirecionando...', 'apollo-login'),
        'loginFailed'    => __('Credenciais incorretas. Tente novamente.', 'apollo-login'),
        'warningState'   => __('Atenção: última tentativa antes do bloqueio.', 'apollo-login'),
        'lockedOut'      => __('Sistema bloqueado por segurança.', 'apollo-login'),
        'quizComplete'   => __('Teste de aptidão concluído com sucesso!', 'apollo-login'),
        'quizFailed'     => __('Resposta incorreta. Reiniciando pergunta...', 'apollo-login'),
        'patternCorrect' => '♫♫♫',
        'ethicsCorrect'  => __('É trabalho, renda, a sonoridade e arte favorita de alguem.', 'apollo-login'),
    ),
);

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="robots" content="noindex,nofollow">
    <title><?php echo esc_html(get_bloginfo('name')); ?> - Registro</title>

    <!-- DIRECT ASSETS - NO wp_head() TO PREVENT INTERFERENCE -->
    <script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>
    <link rel="stylesheet"
        href="<?php echo esc_url(APOLLO_LOGIN_URL . 'assets/css/apollo-auth-complete.css?v=' . APOLLO_LOGIN_VERSION); ?>">

</head>

<body data-state="normal">

    <!-- YouTube Background Video -->
    <div class="youtube-bg">
        <iframe
            src="https://www.youtube.com/embed/wQVrPHKww4Y?autoplay=1&mute=1&loop=1&playlist=wQVrPHKww4Y&controls=0&modestbranding=1&rel=0&playsinline=1&iv_load_policy=3&enablejsapi=1"
            title="Apollo Background" frameborder="0" allow="autoplay; encrypted-media; fullscreen" allowfullscreen loading="eager">
        </iframe>
    </div>
    <!-- <div class="bg-layer"></div>
	<div class="grid-overlay"></div>
	<div class="noise-overlay"></div> -->

    <div class="terminal-wrapper">
        <div class="scan-line"></div>
        <div class="notification-area"></div>

        <?php require APOLLO_LOGIN_DIR . 'templates/parts/new_header.php'; ?>

        <div class="scroll-area">
            <section id="register-section">
                <?php require APOLLO_LOGIN_DIR . 'templates/parts/new_register-form.php'; ?>
            </section>
        </div>

        <?php require APOLLO_LOGIN_DIR . 'templates/parts/new_footer.php'; ?>
        <?php require APOLLO_LOGIN_DIR . 'templates/parts/new_aptitude-quiz.php'; ?>
    </div>

    <!-- APOLLO REPORT MODAL (Manually injected - no wp_footer in Blank Canvas) -->
    <?php
    if (function_exists('apollo_render_report_modal')) {
        apollo_render_report_modal('apolloReportTrigger', 'frontend');
    }
    ?>

    <!-- SELF-CONTAINED CONFIGURATION - NO wp_footer() TO PREVENT INTERFERENCE -->
    <script>
        window.apolloAuthConfig = <?php echo wp_json_encode($js_config); ?>;
    </script>
    <script
        src="<?php echo esc_url(APOLLO_LOGIN_URL . 'assets/js/apollo-auth-scripts.js?v=' . APOLLO_LOGIN_VERSION); ?>">
    </script>

</body>

</html>