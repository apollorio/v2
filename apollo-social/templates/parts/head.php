<?php

/**
 * Template Part: Head — Blank Canvas <head> for /explore
 *
 * Loads: Apollo CDN (core.js), GSAP+ScrollTrigger, Navbar, Explore CSS.
 * Blank Canvas: NO wp_head(), NO theme conflicts.
 *
 * @package Apollo\Social
 * @since   3.0.0
 */

defined('ABSPATH') || exit;

// Apollo SEO hook (blank canvas support)
$has_seo = has_action('apollo/seo/head');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Apollo · Feed</title>

    <?php
    if ($has_seo) {
        do_action('apollo/seo/head');
    }
    ?>

    <!-- Apollo CDN (base styles, GSAP 3.14.2, RemixIcon, jQuery, dark theme, translate, page-layout) -->
    <script src="https://cdn.apollo.rio.br/v1.0.0/core.min.js?v=1.0.0" fetchpriority="high"></script>

    <!-- GSAP already loaded by CDN core.min.js (v3.14.2) -->

    <!-- Navbar (apollo-templates) -->
    <?php if (defined('APOLLO_TEMPLATES_URL') && defined('APOLLO_TEMPLATES_VERSION')) : ?>
        <link rel="stylesheet" href="<?php echo esc_url(APOLLO_TEMPLATES_URL . 'assets/css/navbar.css'); ?>?v=<?php echo esc_attr(APOLLO_TEMPLATES_VERSION); ?>">
        <script src="<?php echo esc_url(APOLLO_TEMPLATES_URL . 'assets/js/navbar.js'); ?>?v=<?php echo esc_attr(APOLLO_TEMPLATES_VERSION); ?>" defer></script>
    <?php endif; ?>

    <!-- Explore CSS -->
    <?php if (defined('APOLLO_SOCIAL_URL') && defined('APOLLO_SOCIAL_VERSION')) : ?>
        <link rel="stylesheet" href="<?php echo esc_url(APOLLO_SOCIAL_URL . 'assets/css/explore.css'); ?>?v=<?php echo esc_attr(APOLLO_SOCIAL_VERSION); ?>">
    <?php endif; ?>
</head>