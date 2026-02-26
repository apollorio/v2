<?php

/**
 * Apollo Sign — <head> meta block
 * Part: sig-head.php
 *
 * Outputs DOCTYPE, <html>, <head> with Apollo CDN,
 * font preloads, and optional GSAP + Interact.js.
 *
 * Variables expected from parent:
 *   $doc_title  (string) — Browser tab title
 *
 * @package Apollo\Sign
 */

if (! defined('ABSPATH')) {
    exit;
}

$doc_title = $doc_title ?? 'Apollo Docs — Assinatura Digital';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    <!-- PWA / mobile -->
    <meta name="theme-color" content="#ececed">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <!-- Security: no indexing for signing pages -->
    <meta name="robots" content="noindex,nofollow">

    <title><?php echo esc_html($doc_title); ?></title>

    <!-- Apollo CDN: base CSS, GSAP 3.13, jQuery, RemixIcon, dark theme, i18n, analytics -->
    <script src="<?php echo esc_url(APOLLO_CDN_URL); ?>core.js" fetchpriority="high"></script>

    <!-- GSAP already loaded by CDN core.js (v3.14.2) -->

    <!-- Interact.js — drag/resize for signature placement -->
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

    <!-- Fonts: Space Grotesk + Space Mono already loaded by CDN core.js -->
    <link rel="preconnect" href="https://fonts.googleapis.com">