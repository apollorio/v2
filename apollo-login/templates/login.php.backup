<?php
/**
 * Apollo Login Template
 * Uses apollo-templates plugin for rendering the /acesso page
 *
 * @package Apollo_Login
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
exit;
}

// Check if apollo-templates is active
if ( ! defined( 'APOLLO_TEMPLATES_DIR' ) ) {
wp_die(
esc_html__( 'Apollo Templates plugin is required but not active.', 'apollo-login' ),
esc_html__( 'Plugin Dependency Error', 'apollo-login' ),
array( 'back_link' => true )
);
}

// Get configuration.
$auth_config = apply_filters(
'apollo_auth_config',
array(
'ajax_url'             => admin_url( 'admin-ajax.php' ),
'nonce'                => wp_create_nonce( 'apollo_auth_nonce' ),
'max_failed_attempts'  => 3,
'lockout_duration'     => 60,
'simon_levels'         => 4,
'reaction_targets'     => 4,
'redirect_after_login' => home_url( '/painel/' ),
'terms_url'            => 'https://apollo.rio.br/politica',
'bug_report_url'       => 'https://apollo.rio.br/bug/',
'show_instagram'       => true,
'require_cpf'          => true,
)
);

// Get available sounds/genres for registration from apollo-core GLOBAL BRIDGE taxonomy
$available_sounds = array();
if ( taxonomy_exists( 'sound' ) ) {
$sound_terms = get_terms(
array(
'taxonomy'   => 'sound',
'hide_empty' => false,
'orderby'    => 'name',
'order'      => 'ASC',
)
);

if ( ! is_wp_error( $sound_terms ) && ! empty( $sound_terms ) ) {
foreach ( $sound_terms as $term ) {
$available_sounds[ $term->slug ] = $term->name;
}
}
}

// Fallback if taxonomy not available yet
if ( empty( $available_sounds ) ) {
$available_sounds = apply_filters(
'apollo_registration_sounds',
array(
'techno'      => 'Techno',
'house'       => 'House',
'trance'      => 'Trance',
'drum_bass'   => 'Drum & Bass',
'funk'        => 'Funk',
'tribal'      => 'Tribal',
'minimal'     => 'Minimal',
'progressive' => 'Progressive',
'melodic'     => 'Melódico',
'hard'        => 'Hard',
'psy'         => 'Psy',
'ambient'     => 'Ambient',
)
);
}

// Localize script configuration
$js_config = array(
'ajaxUrl'            => $auth_config['ajax_url'],
'nonce'              => $auth_config['nonce'],
'maxFailedAttempts'  => $auth_config['max_failed_attempts'],
'lockoutDuration'    => $auth_config['lockout_duration'],
'simonLevels'        => $auth_config['simon_levels'],
'reactionTargets'    => $auth_config['reaction_targets'],
'redirectAfterLogin' => $auth_config['redirect_after_login'],
'strings'            => array(
'loginSuccess'   => __( 'Acesso autorizado. Redirecionando...', 'apollo-login' ),
'loginFailed'    => __( 'Credenciais incorretas. Tente novamente.', 'apollo-login' ),
'warningState'   => __( 'Atenção: última tentativa antes do bloqueio.', 'apollo-login' ),
'lockedOut'      => __( 'Sistema bloqueado por segurança.', 'apollo-login' ),
'quizComplete'   => __( 'Teste de aptidão concluído com sucesso!', 'apollo-login' ),
'quizFailed'     => __( 'Resposta incorreta. Reiniciando pergunta...', 'apollo-login' ),
'patternCorrect' => '♫♫♫',
'ethicsCorrect'  => __( 'É trabalho, renda, a sonoridade e arte favorita de alguem.', 'apollo-login' ),
),
);

// Include the auth template from apollo-templates
require APOLLO_TEMPLATES_DIR . 'templates/auth/login-register.php';
