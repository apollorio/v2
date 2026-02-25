<?php
/**
 * ================================================================================
 * APOLLO AUTH - Lockout Overlay Template Part
 * ================================================================================
 * Displays the security lockout overlay when too many failed attempts occur.
 * Only visible when body has data-state="danger"
 *
 * @package Apollo_Social
 * @since 1.0.0
 *
 * PLACEHOLDERS:
 * - {{lockout_timer}} - Countdown timer (MM:SS)
 * - {{lockout_message}} - Lockout explanation message
 * ================================================================================
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lockout_duration = isset( $auth_config['lockout_duration'] ) ? $auth_config['lockout_duration'] : 60;
$minutes          = floor( $lockout_duration / 60 );
$seconds          = $lockout_duration % 60;
$initial_timer    = sprintf( '%d:%02d', $minutes, $seconds );
?>

<div class="lockout-overlay" data-tooltip="<?php esc_attr_e( 'Sistema bloqueado', 'apollo-login' ); ?>">
	
	<div style="margin-bottom: 24px;" data-tooltip="<?php esc_attr_e( 'Ícone de alerta', 'apollo-login' ); ?>">
		<i class="ri-alarm-warning-fill" style="font-size: 64px; color: var(--color-danger);"></i>
	</div>
	
	<h2 style="color: var(--color-danger); margin-bottom: 12px;" data-tooltip="<?php esc_attr_e( 'Título do bloqueio', 'apollo-login' ); ?>">
		<?php esc_html_e( 'ACESSO BLOQUEADO', 'apollo-login' ); ?>
	</h2>
	
	<p style="color: rgba(148,163,184,0.9); margin-bottom: 20px; font-size: 13px;" data-tooltip="<?php esc_attr_e( 'Mensagem de segurança', 'apollo-login' ); ?>">
		<?php esc_html_e( 'Múltiplas tentativas de acesso detectadas.', 'apollo-login' ); ?><br>
		<?php esc_html_e( 'Sistema temporariamente bloqueado por segurança.', 'apollo-login' ); ?>
	</p>
	
	<div style="font-family: var(--font-mono); font-size: 32px; color: var(--color-danger); margin-bottom: 12px;" data-tooltip="<?php esc_attr_e( 'Tempo restante', 'apollo-login' ); ?>">
		<span id="lockout-timer"><?php echo esc_html( $initial_timer ); ?></span>
	</div>
	
	<p style="font-family: var(--font-mono); font-size: 11px; color: rgba(148,163,184,0.7);" data-tooltip="<?php esc_attr_e( 'Instrução', 'apollo-login' ); ?>">
		<?php esc_html_e( 'Aguarde o timer zerar para tentar novamente.', 'apollo-login' ); ?>
	</p>
	
</div>
