<?php

/**
 * Plugin Name: Apollo Login
 * Plugin URI: https://apollo.rio.br/plugins/apollo-login
 * Description: Auth: Login, Register, Password Reset, MANDATORY Aptitude Quiz (Pattern, Simon, Ethics, Reaction), URL Protection (Hide My WP native), Rate Limiting
 * Version: 1.0.0
 * Author: Apollo::Rio
 * Author URI: https://apollo.rio.br
 * License: Proprietary
 * Text Domain: apollo-login
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Network: false
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login;

// Prevent direct access.
if (! defined('ABSPATH')) {
    exit;
}

// Plugin constants.
define('APOLLO_LOGIN_VERSION', '1.0.0');
define('APOLLO_LOGIN_FILE', __FILE__);
define('APOLLO_LOGIN_DIR', plugin_dir_path(__FILE__));
define('APOLLO_LOGIN_URL', plugin_dir_url(__FILE__));
define('APOLLO_LOGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Autoloader
 */
// require_once APOLLO_LOGIN_DIR . 'vendor/autoload.php';

/**
 * Include helper files
 */
require_once APOLLO_LOGIN_DIR . 'includes/constants.php';
require_once APOLLO_LOGIN_DIR . 'includes/functions.php';
require_once APOLLO_LOGIN_DIR . 'includes/disable-conflicts.php'; // Prevent apollo-templates conflicts

/**
 * Manual class includes (autoloader disabled)
 */
require_once APOLLO_LOGIN_DIR . 'src/Core/Plugin.php';
require_once APOLLO_LOGIN_DIR . 'src/Auth/LoginHandler.php';
require_once APOLLO_LOGIN_DIR . 'src/Auth/RegisterHandler.php';
require_once APOLLO_LOGIN_DIR . 'src/Auth/PasswordReset.php';
require_once APOLLO_LOGIN_DIR . 'src/Auth/EmailVerification.php';
require_once APOLLO_LOGIN_DIR . 'src/Quiz/QuizManager.php';
require_once APOLLO_LOGIN_DIR . 'src/Quiz/SimonGame.php';
require_once APOLLO_LOGIN_DIR . 'src/Security/URLRewriter.php';
require_once APOLLO_LOGIN_DIR . 'src/Security/RateLimiter.php';
require_once APOLLO_LOGIN_DIR . 'src/Security/Lockout.php';
require_once APOLLO_LOGIN_DIR . 'src/Security/Firewall.php';
require_once APOLLO_LOGIN_DIR . 'src/Security/SecurityHeaders.php';
require_once APOLLO_LOGIN_DIR . 'src/Security/WPHardening.php';

/**
 * API controllers (loaded for REST route registration)
 */
require_once APOLLO_LOGIN_DIR . 'src/API/AuthController.php';
require_once APOLLO_LOGIN_DIR . 'src/API/QuizController.php';
require_once APOLLO_LOGIN_DIR . 'src/API/SecurityController.php';

/**
 * Initialize plugin
 * Priority 10 ensures apollo-core (priority 5) loads first
 */
function apollo_login_init(): void
{
    // Load text domain
    // Temporarily disabled to prevent just-in-time loading issues
    // load_plugin_textdomain(
    // 'apollo-login',
    // false,
    // dirname( APOLLO_LOGIN_BASENAME ) . '/languages/'
    // );

    require_once APOLLO_LOGIN_DIR . 'src/Core/Plugin.php';
    $plugin = Core\Plugin::get_instance();
    $plugin->init();
}
add_action('plugins_loaded', __NAMESPACE__ . '\\apollo_login_init', 10);

/**
 * Suppress textdomain loading notices for this plugin
 */
function apollo_login_suppress_textdomain_notice($message, $error_type): string
{
    if (strpos($message, '_load_textdomain_just_in_time') !== false && strpos($message, 'apollo-login') !== false) {
        return ''; // Suppress this specific notice
    }
    return $message;
}
add_filter('wp_php_error_message', __NAMESPACE__ . '\\apollo_login_suppress_textdomain_notice', 10, 2);

/**
 * Register query vars early - before init
 */
function apollo_login_register_query_vars($vars)
{
    $vars[] = 'apollo_login_page';
    $vars[] = 'apollo_profile_user';
    return $vars;
}
add_filter('query_vars', __NAMESPACE__ . '\\apollo_login_register_query_vars');

/**
 * Handle virtual pages directly via parse_request
 */
function apollo_login_parse_request($wp)
{
    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    // Virtual pages mapping
    $virtual_pages = array(
        'acesso'          => 'login',
        'registre'        => 'register',
        'reset'           => 'reset',
        'verificar-email' => 'verify-email',
        'sair'            => 'logout',
    );

    if (isset($virtual_pages[$path])) {
        $wp->query_vars['apollo_login_page'] = $virtual_pages[$path];
        return;
    }

    // Profile page: /id/username or /id/username/tab
    if (preg_match('#^id/([^/]+)(?:/([^/]+))?/?$#', $path, $matches)) {
        $wp->query_vars['apollo_login_page']   = 'profile';
        $wp->query_vars['apollo_profile_user'] = sanitize_text_field($matches[1]);
        if (! empty($matches[2])) {
            $wp->query_vars['apollo_profile_tab'] = sanitize_text_field($matches[2]);
        }
    }
}
add_action('parse_request', __NAMESPACE__ . '\\apollo_login_parse_request');

/**
 * Serve template for virtual pages via template_redirect.
 * Priority 1 fires before any other plugin (including apollo-templates P10).
 */
function apollo_login_template_redirect(): void
{
    $page = get_query_var('apollo_login_page', '');

    if (empty($page)) {
        return;
    }

    // Handle logout
    if ('logout' === $page) {
        wp_logout();
        wp_redirect(home_url());
        exit;
    }

    $template_file = APOLLO_LOGIN_DIR . 'templates/' . $page . '.php';

    if (file_exists($template_file)) {
        global $wp_query;
        $wp_query->is_404 = false;
        status_header(200);

        include $template_file;
        exit;
    }
}
// Priority 1 — login wins over all other plugins.
add_action('template_redirect', __NAMESPACE__ . '\\apollo_login_template_redirect', 1);
function apollo_login_activate(): void
{
    require_once APOLLO_LOGIN_DIR . 'includes/activation.php';
    Core\Activation::activate();
}
register_activation_hook(__FILE__, __NAMESPACE__ . '\\apollo_login_activate');

/**
 * Deactivation hook
 */
function apollo_login_deactivate(): void
{
    // Cleanup temporary data, flush rewrite rules, etc.
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\\apollo_login_deactivate');
