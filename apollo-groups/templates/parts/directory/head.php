<?php

/**
 * Directory Part — Head
 *
 * HTML head with meta, CDN, GSAP, Navbar, CSS.
 * Expects: $page_title (string)
 *
 * @package Apollo\Groups
 * @since   3.0.0
 */

defined('ABSPATH') || exit;

$groups_css_url = '';
if (defined('APOLLO_GROUPS_URL')) {
    $groups_css_url = APOLLO_GROUPS_URL . 'assets/css/groups-directory.css';
} else {
    $groups_css_url = plugin_dir_url(dirname(__DIR__)) . 'assets/css/groups-directory.css';
}
$ver = defined('APOLLO_VERSION') ? APOLLO_VERSION : '3.0.0';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apollo · <?php echo esc_html($page_title); ?></title>

    <!-- Apollo CDN -->
    <script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>

    <!-- GSAP already loaded by CDN core.js (v3.14.2) -->

    <!-- Navbar -->
    <?php if (defined('APOLLO_TEMPLATES_URL') && defined('APOLLO_TEMPLATES_VERSION')) : ?>
        <link rel="stylesheet" href="<?php echo esc_url(APOLLO_TEMPLATES_URL . 'assets/css/navbar.css'); ?>?v=<?php echo esc_attr(APOLLO_TEMPLATES_VERSION); ?>">
        <script src="<?php echo esc_url(APOLLO_TEMPLATES_URL . 'assets/js/navbar.js'); ?>?v=<?php echo esc_attr(APOLLO_TEMPLATES_VERSION); ?>" defer></script>
    <?php endif; ?>

    <!-- Directory stylesheet -->
    <link rel="stylesheet" href="<?php echo esc_url($groups_css_url); ?>?v=<?php echo esc_attr($ver); ?>">

    <!-- Fonts: Shrikhand only (Space Grotesk + Space Mono already loaded by CDN) -->
    <link href="https://fonts.googleapis.com/css2?family=Shrikhand&display=swap" rel="stylesheet">
</head>

<body>