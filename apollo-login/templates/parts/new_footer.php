<?php

/**
 * ================================================================================
 * APOLLO AUTH - Footer Template Part
 * ================================================================================
 * Displays the footer with copyright and node information.
 *
 * @package Apollo\Login
 * @since 1.0.0
 *
 * PLACEHOLDERS:
 * - {{year}} - Current year
 * - {{node_name}} - Node identifier
 * - {{version}} - Plugin version
 * ================================================================================
 */

// Prevent direct access
if (! defined('ABSPATH')) {
    exit;
}

$current_year = gmdate('Y');
$node_name    = apply_filters('apollo_auth_node_name', 'Rio de Janeiro');
$version      = defined('APOLLO_SOCIAL_VERSION') ? APOLLO_SOCIAL_VERSION : '1.0.0';
?>

<footer style="position: absolute; bottom: 0px; left: 0; right: 0;" data-tooltip="<?php esc_attr_e('Rodapé Apollo', 'apollo-social'); ?>">
    <p data-tooltip="<?php esc_attr_e('Informações do nó', 'apollo-social'); ?>">

    </p>
    <p style="margin: bottom 11px;" data-tooltip="<?php esc_attr_e('Copyright', 'apollo-social'); ?>">
        <i class="ri-copyright-line"></i> <?php echo esc_html($current_year); ?> Apollo::Rio •
        <?php esc_html_e('Todos os direitos reservados', 'apollo-social'); ?>
    </p>
</footer>