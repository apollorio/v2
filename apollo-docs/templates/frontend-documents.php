<?php

/**
 * Apollo Docs — Frontend File Manager (Canvas Template)
 *
 * Virtual page at /documentos for logged-in users.
 * Uses wp_head/wp_footer (Canvas pattern) + Apollo CDN.
 *
 * @package Apollo\Docs
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

$cdn_url = defined('APOLLO_CDN_URL') ? APOLLO_CDN_URL : 'https://cdn.apollo.rio.br/v1.0.0/';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos — Apollo</title>
    <script src="<?php echo esc_url($cdn_url . 'core.min.js?v=1.0.0'); ?>" fetchpriority="high"></script>
    <?php wp_head(); ?>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            background: var(--bg, #0a0a0a);
            color: var(--ink, #fafafa);
            font-family: var(--ff, 'Space Grotesk', sans-serif);
            min-height: 100vh;
        }

        /* Push content below fixed navbar */
        .apollo-docs-page {
            padding-top: 60px;
            min-height: 100vh;
        }

        /* Full-width file manager */
        .apollo-docs-page .apollo-fm {
            max-width: 1400px;
            margin: 0 auto;
            border-radius: 0;
            border-left: 1px solid var(--brd, rgba(255, 255, 255, .06));
            border-right: 1px solid var(--brd, rgba(255, 255, 255, .06));
            min-height: calc(100vh - 60px);
        }
    </style>
</head>

<body <?php body_class('apollo-docs-frontend'); ?>>

    <?php
    /* Load navbar if available */
    $navbar_path = WP_PLUGIN_DIR . '/apollo-templates/templates/template-parts/navbar.php';
    if (file_exists($navbar_path)) {
        define('APOLLO_NAVBAR_LOADED', true);
        include $navbar_path;
    }
    ?>

    <main class="apollo-docs-page">
        <?php require APOLLO_DOCS_DIR . 'templates/documents.php'; ?>
    </main>

    <?php wp_footer(); ?>
</body>

</html>