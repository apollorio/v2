<?php
/**
 * ================================================================================
 * APOLLO AUTH - Footer Template Part
 * ================================================================================
 * Displays the footer with copyright and node information.
 *
 * @package Apollo_Social
 * @since 1.0.0
 *
 * PLACEHOLDERS:
 * - {{year}} - Current year
 * - {{node_name}} - Node identifier
 * - {{version}} - Plugin version
 * ================================================================================
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_year = gmdate( 'Y' );
$node_name    = apply_filters( 'apollo_auth_node_name', 'Rio de Janeiro' );
$version      = defined( 'APOLLO_LOGIN_VERSION' ) ? APOLLO_LOGIN_VERSION : '1.0.0';
?>

<footer data-tooltip="<?php esc_attr_e( 'Rodapé Apollo', 'apollo-login' ); ?>">
	<p data-tooltip="<?php esc_attr_e( 'Informações do nó', 'apollo-login' ); ?>">
		Apollo::Rio Node • <?php echo esc_html( $node_name ); ?> • v<?php echo esc_html( $version ); ?>
	</p>
	<p data-tooltip="<?php esc_attr_e( 'Copyright', 'apollo-login' ); ?>">
		© <?php echo esc_html( $current_year ); ?> Apollo::Rio • 
		<?php esc_html_e( 'Todos os direitos reservados', 'apollo-login' ); ?>
	</p>
</footer>
