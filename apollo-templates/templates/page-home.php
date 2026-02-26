<?php

/**
 * Template Name: Apollo Home
 * Template Post Type: page
 *
 * Apollo Home — Canvas v2 Panel Engine
 * Dual-state skeleton:
 *   GUEST  → home(center) + acesso(DOWN) + event-page(RIGHT) + mural(UP)
 *   LOGGED → explore(center) + forms(DOWN) + dynamic(RIGHT) + mural(UP) + chat(LEFT)
 *
 * page-layout.js auto-activates the FIRST data-panel in DOM order.
 * Canvas mode: NO wp_head/wp_footer — CDN core.js loads everything.
 *
 * @package Apollo\Templates
 * @since   6.0.0
 * @see     _inventory/pages-layout.json
 * @see     _inventory/SKELETON.md
 */

/*
════════════════════════════════════════════════════════════════════
APOLLO ICON REGISTRY — Canonical RemixIcon mapping for all plugins
────────────────────────────────────────────────────────────────────
Plugin               │ Primary Icon                │ Alt / Fill
─────────────────────┼─────────────────────────────┼──────────────────────
apollo-core          │ ri-settings-3-line          │ ri-settings-3-fill
apollo-login         │ ri-login-circle-line        │ ri-login-circle-fill
apollo-users         │ ri-user-smile-line          │ ri-user-smile-fill
apollo-membership    │ ri-vip-crown-line           │ ri-vip-crown-fill
apollo-events        │ ri-calendar-line            │ ri-calendar-fill
apollo-djs           │ ri-sound-module-line        │ ri-sound-module-fill
apollo-loc           │ ri-map-pin-2-line           │ ri-map-pin-2-fill
apollo-classifieds   │ ri-token-swap-line          │ ri-token-swap-fill
apollo-suppliers     │ ri-store-2-line             │ ri-store-2-fill
apollo-social        │ ri-share-forward-line       │ ri-share-forward-fill
apollo-groups        │ ri-user-community-line      │ ri-user-community-fill
apollo-wow           │ ri-emotion-happy-line       │ ri-emotion-happy-fill
apollo-fav           │ ri-bookmark-line            │ ri-bookmark-fill
apollo-comment       │ ri-chat-quote-line          │ ri-chat-quote-fill
apollo-notif         │ ri-signal-tower-line        │ ri-signal-tower-fill
apollo-email         │ ri-mail-line                │ ri-mail-fill
apollo-chat          │ ri-message-3-line           │ ri-message-3-fill
apollo-shortcodes    │ ri-code-s-slash-line        │ ri-code-s-slash-fill
apollo-templates     │ ri-layout-masonry-line      │ ri-layout-masonry-fill
apollo-dashboard     │ ri-dashboard-line           │ ri-dashboard-fill
apollo-hub           │ ri-compass-3-line           │ ri-compass-3-fill
apollo-admin         │ ri-admin-line               │ ri-admin-fill
apollo-mod           │ ri-shield-check-line        │ ri-shield-check-fill
apollo-coauthor      │ ri-team-line                │ ri-team-fill
apollo-statistics    │ ri-bar-chart-box-line       │ ri-bar-chart-box-fill
apollo-cult          │ ri-fire-line                │ ri-fire-fill
apollo-pwa           │ ri-smartphone-line          │ ri-smartphone-fill
apollo-sign          │ ri-quill-pen-line           │ ri-quill-pen-fill
apollo-seo           │ ri-search-eye-line          │ ri-search-eye-fill
apollo-adverts       │ ri-advertisement-line       │ ri-advertisement-fill
apollo-gestor        │ ri-briefcase-line           │ ri-briefcase-fill
apollo-sheets        │ ri-file-list-3-line         │ ri-file-list-3-fill
apollo-docs          │ ri-book-open-line           │ ri-book-open-fill
apollo-runtime       │ ri-terminal-box-line        │ ri-terminal-box-fill
─────────────────────┼─────────────────────────────┼──────────────────────
UI Actions           │ Icon                        │ Context
─────────────────────┼─────────────────────────────┼──────────────────────
Profile              │ ri-user-smile-line/fill     │ navbar, panels
Chat                 │ ri-message-3-line/fill      │ navbar, panel-chat
Notifications        │ SVG broadcast/radar         │ navbar, panel-notif
Menu FAB             │ ri-apps-2-line              │ fixed bottom-right
Radio                │ custom SVG play/pause       │ sidebar widget
Events               │ ri-calendar-event-line      │ menu sheet
DJs & Artistas       │ ri-music-2-line             │ menu sheet
Espaços              │ ri-map-pin-2-line           │ menu sheet
Classificados        │ ri-price-tag-3-line         │ menu sheet
Acomoda              │ ri-home-heart-line          │ menu sheet
Login CTA            │ ri-login-circle-line        │ menu sheet
Logout               │ ri-logout-box-r-line        │ profile dropdown
Dashboard            │ ri-dashboard-line           │ profile dropdown
Verified             │ ri-shield-check-line        │ badges, disclaimer
Lock / gated         │ ri-lock-2-line              │ crash cards
Play                 │ ri-play-fill                │ track overlay
Headphones           │ ri-headphone-line           │ track meta
Play count           │ ri-play-circle-line         │ track meta
Arrow down           │ ri-arrow-down-wide-line     │ scroll hint
Arrow left           │ ri-arrow-left-line          │ panel back
Arrow up             │ ri-arrow-up-line            │ panel close
─────────────────────┼─────────────────────────────┼──────────────────────
Marquee Categories   │ Icon                        │ Feed Type
─────────────────────┼─────────────────────────────┼──────────────────────
News                 │ ri-newspaper-line           │ fixed positions 1,7
Community            │ ri-user-community-fill      │ joins, groups
Tracks (Out Now)     │ ri-sound-module-line        │ new releases
Events               │ ri-calendar-line            │ upcoming
Classifieds          │ ri-token-swap-fill          │ repasses
════════════════════════════════════════════════════════════════════

PHP MODULAR BREAKDOWN — /home template parts
────────────────────────────────────────────────────────────────────
PART 01  └─ templates/page-home.php (this file)
              wp_head equivalent: CDN scripts, inline CSS, constants
PART 02  └─ template-parts/new-home/navbar.php
              <nav class="nh-navbar"> — always visible persistent UI
PART 03  └─ template-parts/new-home/menu-fab.php
              FAB button + upward sheet — always visible
PART 04  └─ template-parts/new-home/radio.php
              <aside class="nh-radio"> — synced SoundCloud widget
PART 05  └─ template-parts/new-home/hero.php
              <div class="nh-hero"> video bg + headline + scroll-hint
PART 06  └─ template-parts/new-home/marquee.php
              Animated ticker — live data from CPTs (5 min transient)
PART 07  └─ template-parts/new-home/tracks.php
              Out Now grid — CPT: apollo_dj | REST: apollo/v1/djs
PART 08  └─ template-parts/new-home/events.php
              Event cards — CPT: event | REST: apollo/v1/events
PART 09  └─ template-parts/new-home/classifieds.php
              Classified rows — CPT: classified | REST: apollo/v1/classifieds
PART 10  └─ template-parts/new-home/crash.php
              Sleep/Crash grid — CPT: local | REST: apollo/v1/loc?type=crash
PART 11  └─ template-parts/new-home/map.php
              Leaflet map — event markers via _event_loc_id → _local_lat/lng
PART 12  └─ template-parts/new-home/footer.php
              Footer grid + links
PART 13  └─ template-parts/new-home/panel-explore.php     [LOGGED]
PART 14  └─ template-parts/new-home/panel-mural.php       [BOTH]
PART 15  └─ template-parts/new-home/panel-forms.php       [LOGGED]
PART 16  └─ template-parts/new-home/panel-chat-list.php   [LOGGED]
PART 17  └─ template-parts/new-home/panel-chat.php        [LOGGED]
PART 18  └─ template-parts/new-home/panel-chat-inbox.php  [LOGGED]
PART 19  └─ template-parts/new-home/panel-dynamic.php     [LOGGED]
PART 20  └─ template-parts/new-home/panel-acesso.php      [GUEST]
PART 21  └─ template-parts/new-home/panel-event-page.php  [GUEST]
PART 22  └─ template-parts/new-home/panel-notif.php       [BOTH]
PART 23  └─ template-parts/new-home/panel-detail.php      [BOTH]
════════════════════════════════════════════════════════════════════
*/

defined('ABSPATH') || exit;
define('APOLLO_NAVBAR_LOADED', true);

$parts     = plugin_dir_path(__FILE__) . 'template-parts/new-home/';
$is_logged = is_user_logged_in();

do_action('apollo/home/before_content');

$ver = defined('APOLLO_TEMPLATES_VERSION') ? APOLLO_TEMPLATES_VERSION : '6.0.0';
$css = defined('APOLLO_TEMPLATES_URL') ? APOLLO_TEMPLATES_URL . 'assets/css/new-home.css?v=' . $ver : '';
$js  = defined('APOLLO_TEMPLATES_URL') ? APOLLO_TEMPLATES_URL . 'assets/js/new-home.js?v=' . $ver : '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="theme-color" content="#0A0A0A">
    <title><?php echo esc_html(get_bloginfo('name')); ?> — Underground Culture Guide</title>

    <!-- Apollo CDN — core.js auto-loads: CSS vars, GSAP, jQuery, Icons -->
    <script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>
    <!-- Panel Engine — required for [data-panel] slide/tab navigation -->
    <script src="https://cdn.apollo.rio.br/v1.0.0/js/page-layout.js" defer></script>

    <?php if ($css) : ?>
        <link rel="stylesheet" href="<?php echo esc_url($css); ?>">
    <?php endif; ?>

    <!-- Leaflet CSS — required for map section tiles -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">

    <!-- Panel System Styles (design tokens from CDN :root) -->
    <style id="apollo-panel-system">
        /* ══════════════════════════════════════════════════════════
           APOLLO HOME — Missing CDN variable aliases
           CDN :root defines --primary, --bg, --surface etc.
           These extend with home-specific tokens not in CDN.
        ══════════════════════════════════════════════════════════ */
        :root {
            /* Text alias — CDN uses --txt-color-heading; --text used by new-home.css */
            --text: rgba(19, 21, 23, 0.92);
            --white: #ffffff;

            /* Backgrounds — CDN only has --bg, --bg-neutral, --surface */
            --bg-card: rgba(0, 0, 0, 0.025);
            --bg-deep: rgba(0, 0, 0, 0.06);

            /* Borders — CDN has --border, --border-hover */
            --border-ultra: rgba(0, 0, 0, 0.055);
            --border-light: rgba(0, 0, 0, 0.10);

            /* Muted variant */
            --muted-light: rgba(19, 21, 23, 0.22);

            /* Shadows */
            --shadow-lg: 0 20px 60px rgba(0, 0, 0, 0.18), 0 4px 16px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.12);

            /* Radius */
            --radius-pill: 100px;
            --radius-card: 14px;

            /* Easings — CDN has --ease-default, --ease-smooth, --ease-snappy */
            --ease-out: cubic-bezier(.22, 1, .36, 1);
            --ease-spring: cubic-bezier(.34, 1.56, .64, 1);

            /* Primary RGB — for rgba() usage e.g. rgba(var(--primary-rgb),0.15) */
            --primary-rgb: 244, 95, 0;

            /* Accent colors */
            --accent-lime: #a3e636;
            --accent-blue: #3b82f6;
        }

        /* ── Panel Engine Reset ── */
        [data-panel] {
            position: fixed;
            inset: 0;
            overflow: hidden;
            z-index: 1;
            background: var(--bg);
            will-change: transform, opacity
        }

        [data-panel]:first-of-type {
            z-index: 2
        }

        /* ── Home panel: scrollable (hero + marquee + tracks + events + etc.) ──
           All other panels keep overflow:hidden — their .pnl-body handles inner scroll.
           [data-panel="home"] must scroll the full page-long content below the hero.
        ── */
        [data-panel="home"] {
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-y: contain;
            scroll-behavior: smooth
        }

        /* ── Panel Structure ── */
        .pnl-head {
            position: sticky;
            top: 0;
            z-index: var(--z-ui);
            display: flex;
            align-items: center;
            gap: var(--space-2);
            padding: env(safe-area-inset-top, 12px) var(--space-4) var(--space-2);
            background: color-mix(in srgb, var(--bg) 85%, transparent);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            backdrop-filter: saturate(180%) blur(20px)
        }

        .pnl-head__back {
            all: unset;
            cursor: pointer;
            display: grid;
            place-items: center;
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            color: var(--txt-color);
            transition: var(--transition-ui);
            flex-shrink: 0
        }

        .pnl-head__back:hover {
            background: var(--surface-hover)
        }

        .pnl-head__title {
            flex: 1;
            font: 700 var(--fs-h5)/1 var(--ff-mono);
            color: var(--txt-color-heading);
            letter-spacing: -.02em;
            margin: 0;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap
        }

        .pnl-head__actions {
            display: flex;
            gap: var(--space-1);
            flex-shrink: 0
        }

        .pnl-head__user {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            flex: 1;
            min-width: 0
        }

        .pnl-head__status {
            font: 400 11px/1 var(--ff-main);
            color: var(--txt-muted)
        }

        /* ── Tabs ── */
        .pnl-tabs {
            display: flex;
            gap: var(--space-1);
            padding: 0 var(--space-4) var(--space-2);
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none
        }

        .pnl-tabs::-webkit-scrollbar {
            display: none
        }

        .pnl-tab {
            all: unset;
            cursor: pointer;
            font: 500 12px/1 var(--ff-main);
            color: var(--txt-muted);
            padding: 8px 16px;
            border-radius: var(--radius-lg);
            background: var(--surface);
            transition: var(--transition-ui);
            white-space: nowrap
        }

        .pnl-tab:hover,
        .pnl-tab--active {
            color: var(--txt-color-heading);
            background: var(--surface-hover)
        }

        /* ── Buttons ── */
        .pnl-btn {
            all: unset;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-1);
            padding: 8px 16px;
            font: 500 13px/1 var(--ff-main);
            border-radius: var(--radius-sm);
            transition: var(--transition-ui);
            color: var(--txt-color)
        }

        .pnl-btn:hover {
            background: var(--surface-hover)
        }

        .pnl-btn--icon {
            padding: 8px;
            border-radius: 50%
        }

        .pnl-btn--primary {
            background: var(--primary);
            color: #fff
        }

        .pnl-btn--primary:hover {
            opacity: .88
        }

        /* ── Body ── */
        .pnl-body {
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            height: 100%;
            overscroll-behavior-y: contain;
            padding-bottom: env(safe-area-inset-bottom, 24px)
        }

        .pnl-body--chat {
            display: flex;
            flex-direction: column;
            padding-bottom: 0
        }

        /* ── Cards ── */
        .pnl-card {
            all: unset;
            cursor: pointer;
            display: block;
            width: 100%;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: var(--space-3);
            margin: 0 0 var(--space-2);
            transition: var(--transition-ui)
        }

        .pnl-card:hover {
            background: var(--card-hover);
            border-color: var(--border-hover)
        }

        .pnl-card__row {
            display: flex;
            align-items: center;
            gap: var(--space-2)
        }

        .pnl-card__avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--surface);
            display: grid;
            place-items: center;
            font: 700 13px/1 var(--ff-mono);
            color: var(--txt-muted);
            flex-shrink: 0;
            overflow: hidden
        }

        .pnl-card__avatar--sm {
            width: 32px;
            height: 32px;
            font-size: 11px
        }

        .pnl-card__info {
            flex: 1;
            min-width: 0
        }

        .pnl-card__name {
            font: 600 14px/1.2 var(--ff-main);
            color: var(--txt-color-heading);
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            margin: 0
        }

        .pnl-card__meta {
            font: 400 12px/1.4 var(--ff-main);
            color: var(--txt-muted);
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            margin: 0
        }

        .pnl-card__badge {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--primary);
            flex-shrink: 0
        }

        /* ── Form Nav ── */
        .pnl-form-nav {
            display: flex;
            flex-direction: column;
            gap: var(--space-2);
            padding: var(--space-3) var(--space-4)
        }

        .pnl-form-section {
            padding: var(--space-3) var(--space-4)
        }

        .pnl-form-back {
            all: unset;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: var(--space-1);
            font: 500 13px/1 var(--ff-main);
            color: var(--txt-muted);
            padding: var(--space-2) 0;
            margin-bottom: var(--space-3);
            transition: var(--transition-ui)
        }

        .pnl-form-back:hover {
            color: var(--txt-color)
        }

        /* ── Form Inputs ── */
        .pnl-form {
            display: flex;
            flex-direction: column;
            gap: var(--space-3);
            padding: var(--space-4)
        }

        .pnl-input {
            display: flex;
            flex-direction: column;
            gap: var(--space-1)
        }

        .pnl-input__label {
            font: 500 11px/1 var(--ff-mono);
            color: var(--txt-muted);
            text-transform: uppercase;
            letter-spacing: .05em
        }

        .pnl-input__field {
            all: unset;
            font: 400 14px/1.5 var(--ff-main);
            color: var(--txt-color);
            padding: 10px 14px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            transition: var(--transition-ui)
        }

        .pnl-input__field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(244, 95, 0, .1)
        }

        .pnl-input__row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--space-2)
        }

        /* ── Search ── */
        .pnl-search {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            padding: 0 var(--space-4) var(--space-2)
        }

        .pnl-search__icon {
            color: var(--txt-muted);
            font-size: 16px;
            flex-shrink: 0
        }

        .pnl-search__input {
            all: unset;
            flex: 1;
            font: 400 14px/1.5 var(--ff-main);
            color: var(--txt-color);
            padding: 8px 12px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            transition: var(--transition-ui)
        }

        .pnl-search__input:focus {
            border-color: var(--primary)
        }

        /* ── Chat Messages ── */
        .pnl-msg {
            display: flex;
            gap: var(--space-2);
            padding: var(--space-1) var(--space-4)
        }

        .pnl-msg--self {
            flex-direction: row-reverse
        }

        .pnl-msg__bubble {
            max-width: 75%;
            padding: 10px 14px;
            border-radius: var(--radius) var(--radius) var(--radius) var(--radius-xs);
            background: var(--surface);
            font: 400 14px/1.5 var(--ff-main);
            color: var(--txt-color)
        }

        .pnl-msg--self .pnl-msg__bubble {
            border-radius: var(--radius) var(--radius) var(--radius-xs) var(--radius);
            background: var(--primary);
            color: #fff
        }

        /* ── Compose Bar ── */
        .pnl-compose {
            position: sticky;
            bottom: 0;
            display: flex;
            align-items: center;
            gap: var(--space-2);
            padding: var(--space-3) var(--space-4);
            padding-bottom: calc(var(--space-3) + env(safe-area-inset-bottom, 12px));
            background: var(--bg);
            border-top: 1px solid var(--border)
        }

        .pnl-compose__input {
            flex: 1;
            all: unset;
            font: 400 14px/1.5 var(--ff-main);
            color: var(--txt-color);
            padding: 10px 14px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            transition: var(--transition-ui)
        }

        .pnl-compose__input:focus {
            border-color: var(--primary)
        }

        .pnl-compose__send {
            all: unset;
            cursor: pointer;
            display: grid;
            place-items: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            transition: var(--transition-ui);
            flex-shrink: 0
        }

        .pnl-compose__send:hover {
            opacity: .85
        }

        /* ── Empty/Loader ── */
        .pnl-empty {
            display: grid;
            place-items: center;
            padding: var(--space-6);
            text-align: center;
            color: var(--txt-muted);
            gap: var(--space-2)
        }

        .pnl-empty__icon {
            font-size: 48px;
            opacity: .3
        }

        .pnl-loader {
            display: grid;
            place-items: center;
            padding: var(--space-6)
        }

        .pnl-loader__ring {
            width: 28px;
            height: 28px;
            border: 2px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: pnl-spin .7s linear infinite
        }

        @keyframes pnl-spin {
            to {
                transform: rotate(360deg)
            }
        }

        .pnl-divider {
            height: 1px;
            background: var(--border);
            margin: var(--space-3) var(--space-4)
        }

        /* ── Skeleton ── */
        .pnl-skeleton__card {
            height: 180px;
            background: var(--surface);
            border-radius: var(--radius);
            margin: 0 var(--space-4) var(--space-3);
            animation: pnl-pulse 1.5s ease-in-out infinite
        }

        @keyframes pnl-pulse {

            0%,
            100% {
                opacity: .4
            }

            50% {
                opacity: .8
            }
        }

        /* ── CTA Gate (guest lock) ── */
        .pnl-cta-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--space-3);
            padding: var(--space-4);
            text-align: center
        }

        .pnl-cta-block__icon {
            font-size: 32px;
            color: var(--txt-muted);
            opacity: .5
        }

        .pnl-cta-block__text {
            font: 400 14px/1.6 var(--ff-main);
            color: var(--txt-muted);
            max-width: 300px;
            margin: 0
        }

        /* ── Dynamic Actions Row ── */
        .pnl-dyn-actions__row {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-2);
            padding: var(--space-2) var(--space-4)
        }

        /* ── Event Suggest Form ── */
        .pnl-suggest {
            padding: var(--space-4)
        }

        .pnl-suggest__title {
            font: 700 var(--fs-h5)/1.2 var(--ff-mono);
            color: var(--txt-color-heading);
            margin: 0 0 var(--space-1)
        }

        .pnl-suggest__desc {
            font: 400 13px/1.5 var(--ff-main);
            color: var(--txt-muted);
            margin: 0 0 var(--space-4)
        }

        /* ══════════════════════════════════════════════════════════
		Apollo v2 — Event Card (a-v2-eve-C)
		Orange left bar reveal on hover + lift + shadow
	══════════════════════════════════════════════════════════ */
        .a-v2-eve-C {
            background: var(--surface, #F8F8F8);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            transition: all .3s var(--ease-default, ease);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            display: block;
            text-decoration: none;
            color: inherit
        }

        .a-v2-eve-C::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary, #FF6B35);
            transform: scaleY(0);
            transform-origin: bottom;
            transition: transform .4s var(--ease-default, ease)
        }

        .a-v2-eve-C:hover::before {
            transform: scaleY(1)
        }

        .a-v2-eve-C:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, .06)
        }

        .a-v2-eve-C__media {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 16px;
            aspect-ratio: 16/9
        }

        .a-v2-eve-C__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .5s var(--ease-default, ease)
        }

        .a-v2-eve-C:hover .a-v2-eve-C__media img {
            transform: scale(1.04)
        }

        .a-v2-eve-C__tags {
            position: absolute;
            top: 10px;
            left: 10px;
            display: flex;
            gap: 6px;
            flex-wrap: wrap
        }

        .a-v2-eve-C__tag {
            font: 500 10px/1 var(--ff-mono);
            color: #fff;
            background: rgba(0, 0, 0, .55);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            padding: 5px 10px;
            border-radius: 100px;
            text-transform: uppercase;
            letter-spacing: .04em
        }

        .a-v2-eve-C__date {
            position: absolute;
            bottom: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgba(0, 0, 0, .65);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            border-radius: 10px;
            padding: 8px 12px;
            min-width: 48px
        }

        .a-v2-eve-C__date-day {
            font: 700 20px/1 var(--ff-mono);
            color: #fff
        }

        .a-v2-eve-C__date-month {
            font: 500 10px/1 var(--ff-mono);
            color: rgba(255, 255, 255, .7);
            text-transform: uppercase;
            margin-top: 2px
        }

        .a-v2-eve-C__body {
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .a-v2-eve-C__title {
            font: 600 15px/1.3 var(--ff-main);
            color: var(--txt-color-heading);
            margin: 0;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap
        }

        .a-v2-eve-C__meta {
            display: flex;
            align-items: center;
            gap: 6px;
            font: 400 12px/1.4 var(--ff-main);
            color: var(--txt-muted)
        }

        .a-v2-eve-C__meta i {
            font-size: 13px;
            color: var(--txt-muted);
            flex-shrink: 0
        }

        /* ── Events Grid ── */
        .a-v2-eve-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px
        }

        @media(max-width:640px) {
            .a-v2-eve-grid {
                grid-template-columns: 1fr;
                gap: 16px
            }
        }

        /* ── Explore CTA card inside grid ── */
        .a-v2-eve-C--explore {
            display: grid;
            place-items: center;
            min-height: 180px;
            border: 2px dashed var(--border);
            background: transparent
        }

        .a-v2-eve-C--explore:hover {
            border-color: var(--primary);
            background: var(--surface)
        }

        .a-v2-eve-C--explore .xp-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            text-align: center
        }

        .a-v2-eve-C--explore .xp-icon {
            font-size: 28px;
            color: var(--txt-muted);
            transition: color .3s
        }

        .a-v2-eve-C--explore:hover .xp-icon {
            color: var(--primary)
        }

        .a-v2-eve-C--explore .xp-all {
            font: 700 24px/1 var(--ff-mono);
            color: var(--txt-color-heading);
            letter-spacing: -.02em
        }

        .a-v2-eve-C--explore .xp-label {
            font: 400 12px/1 var(--ff-main);
            color: var(--txt-muted);
            text-transform: uppercase;
            letter-spacing: .06em
        }

        /* ══════════════════════════════════════════════════════════
		Apollo v2 — List Row (a-v2-list-B)
		Re-sell tickets / Classifieds / CPT listings
	══════════════════════════════════════════════════════════ */
        .a-v2-list-B {
            display: grid;
            grid-template-columns: 80px 1fr auto;
            align-items: center;
            padding: 24px 28px;
            border-bottom: 1px solid var(--border);
            transition: all .2s var(--ease-default, ease);
            gap: 20px;
            text-decoration: none;
            color: inherit;
            cursor: pointer
        }

        .a-v2-list-B:last-child {
            border-bottom: none
        }

        .a-v2-list-B:hover {
            background: var(--surface, #fff);
            padding-left: 36px;
            padding-right: 36px
        }

        .a-v2-list-B__time {
            font: 500 13px/1 var(--ff-mono);
            color: var(--txt-muted);
            letter-spacing: .02em
        }

        .a-v2-list-B__info {
            display: flex;
            flex-direction: column;
            gap: 3px;
            min-width: 0
        }

        .a-v2-list-B__title {
            font: 600 14px/1.3 var(--ff-main);
            color: var(--txt-color-heading);
            margin: 0;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap
        }

        .a-v2-list-B__seller {
            font: 400 12px/1 var(--ff-main);
            color: var(--txt-muted)
        }

        .a-v2-list-B__seller--hidden {
            font: 400 12px/1 var(--ff-mono);
            color: var(--border);
            letter-spacing: .15em
        }

        .a-v2-list-B__price-wrap {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
            flex-shrink: 0
        }

        .a-v2-list-B__price {
            font: 700 14px/1 var(--ff-mono);
            color: var(--txt-color-heading)
        }

        .a-v2-list-B__badge {
            font: 500 9px/1 var(--ff-mono);
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 4px 8px;
            border-radius: 100px;
            background: var(--surface);
            color: var(--txt-muted)
        }

        .a-v2-list-B__badge--verified {
            background: rgba(34, 197, 94, .1);
            color: #16a34a
        }

        .a-v2-list-B--cta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 28px;
            border-bottom: none;
            background: var(--text, #0A0A0A);
            color: #fff;
            transition: background .35s var(--ease-default, ease), padding-left .35s var(--ease-default, ease)
        }

        .a-v2-list-B--cta:hover {
            background: var(--primary, #FF6B35);
            color: #fff;
            padding-left: 36px
        }

        .a-v2-list-B--cta .nh-rall-label {
            font: 400 11px/1 var(--ff-mono);
            color: rgba(255, 255, 255, 0.45);
            text-transform: uppercase;
            letter-spacing: .14em;
            margin: 0 0 5px
        }

        .a-v2-list-B--cta .nh-rall-title {
            font-size: 1.5rem;
            font-weight: 900;
            letter-spacing: -0.04em;
            line-height: 1;
            text-transform: uppercase;
            color: #fff;
            margin: 0
        }

        .a-v2-list-B--cta .nh-rall-icon {
            font-size: 2.2rem;
            opacity: 0.65;
            color: #fff;
            transition: transform .4s var(--ease-default, ease), opacity .3s
        }

        .a-v2-list-B--cta:hover .nh-rall-icon {
            transform: rotate(-45deg) scale(1.2);
            opacity: 1
        }

        /* ── List container ── */
        .a-v2-list-wrap {
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            background: var(--card, transparent)
        }

        /* Footer — all styles from external new-home.css (no inline overrides) */
    </style>

    <?php do_action('apollo/home/head'); ?>
</head>

<body>


    <!-- ═══════════════════════════════════════════════════════════════
		PERSISTENT UI — always visible, outside all panels
	═══════════════════════════════════════════════════════════════ -->
    <?php
    require $parts . 'navbar.php';
    require $parts . 'menu-fab.php';
    require $parts . 'radio.php';
    ?>


    <?php if ($is_logged) : ?>
        <!-- ═══════════════════════════════════════════════════════════════
		LOGGED STATE — explore center, forms DOWN, chat LEFT,
		dynamic RIGHT, mural UP
	═══════════════════════════════════════════════════════════════ -->

        <?php include $parts . 'panel-explore.php'; ?>

        <?php include $parts . 'panel-mural.php'; ?>

        <?php include $parts . 'panel-forms.php'; ?>

        <?php include $parts . 'panel-chat-list.php'; ?>

        <?php include $parts . 'panel-chat.php'; ?>

        <?php include $parts . 'panel-chat-inbox.php'; ?>

        <?php include $parts . 'panel-dynamic.php'; ?>


    <?php else : ?>
        <!-- ═══════════════════════════════════════════════════════════════
		GUEST STATE — home center, acesso DOWN, event-page RIGHT,
		mural UP. All restricted URLs → acesso DOWN.
	═══════════════════════════════════════════════════════════════ -->

        <section data-panel="home" data-glyph="H">
            <?php include $parts . 'hero.php'; ?>
            <?php include $parts . 'marquee.php'; ?>
            <?php include $parts . 'tracks.php'; ?>
            <?php include $parts . 'events.php'; ?>
            <?php include $parts . 'classifieds.php'; ?>
            <?php include $parts . 'crash.php'; ?>
            <?php include $parts . 'map.php'; ?>
            <?php include $parts . 'footer.php'; ?>
        </section>

        <?php include $parts . 'panel-mural.php'; ?>

        <?php include $parts . 'panel-acesso.php'; ?>

        <?php include $parts . 'panel-event-page.php'; ?>

    <?php endif; ?>

    <?php include $parts . 'panel-notif.php'; ?>

    <?php include $parts . 'panel-detail.php'; ?>


    <!-- ═══════════════════════════════════════════════════════════════
		PAGE SCRIPTS
	═══════════════════════════════════════════════════════════════ -->
    <?php if ($js) : ?>
        <script src="<?php echo esc_url($js); ?>"></script>
    <?php endif; ?>

    <script>
        (function() {
            'use strict';

            <?php if (! $is_logged) : ?>
                /* ── Auth Gate: restricted elements → acesso DOWN ── */
                document.addEventListener('click', function(e) {
                    var t = e.target.closest('[data-auth-required]');
                    if (t) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (window.ApolloSlider) ApolloSlider.navigate('acesso', 'down');
                    }
                }, true);
            <?php endif; ?>

            /* ── Post-login redirect via ?pra= param ── */
            var pra = new URLSearchParams(location.search).get('pra');
            if (pra) {
                var MAP = {
                    'chat': ['chat-list', 'left'],
                    'forms': ['forms', 'down'],
                    'mural': ['mural', 'up'],
                    'explore': ['explore', null],
                    'dynamic': ['dynamic', 'right']
                };
                window.addEventListener('apollo:ready', function() {
                    var target = MAP[pra];
                    if (target && window.ApolloSlider) {
                        ApolloSlider.navigate(target[0], target[1] || 'right');
                    }
                });
            }

            <?php if ($is_logged) : ?>
                /* ── FAB create actions dispatch to forms panel ── */
                document.addEventListener('click', function(e) {
                    var fab = e.target.closest('[data-to="acesso"][data-form]');
                    if (!fab) return;
                    e.preventDefault();
                    var formId = fab.getAttribute('data-form');
                    /* Redirect to forms panel instead of acesso for logged users */
                    if (window.ApolloSlider) ApolloSlider.navigate('forms', 'down');
                    setTimeout(function() {
                        document.dispatchEvent(new CustomEvent('apollo:form:open', {
                            detail: {
                                form: formId
                            }
                        }));
                    }, 400);
                });
            <?php endif; ?>
        })();
    </script>

    <script>
        /* ═══════════════════════════════════════════════════════════════
       APOLLO HOME — Video Force-Play + Scroll Detection + FAB Panel Nav
    ═══════════════════════════════════════════════════════════════ */
        (function() {
            'use strict';

            /* ── 1. VIDEO — browser-native autoplay (muted+playsinline).
               No force-play script needed — matching working HTML. ── */

            /* ── 2. SCROLL DETECTION for panel-based pages ────────────────
               navbar.v2.js listens on window.scroll, but [data-panel="home"]
               is position:fixed with overflow-y:auto — scroll happens INSIDE
               the panel, not on window. This bridges the gap.
            ─────────────────────────────────────────────────────────────── */
            var homePanel = document.querySelector('[data-panel="home"]');
            var nav = document.getElementById('nhNav');
            if (homePanel && nav) {
                var sTick = false;
                homePanel.addEventListener('scroll', function() {
                    if (!sTick) {
                        requestAnimationFrame(function() {
                            nav.classList.toggle('scrolled', homePanel.scrollTop > 20);
                            sTick = false;
                        });
                        sTick = true;
                    }
                }, {
                    passive: true
                });
                // Initial check
                if (homePanel.scrollTop > 20) nav.classList.add('scrolled');
            }

            /* ── 3. FAB PANEL-AWARE LOGIN (guest only) ────────────────────
               On pages with data-panel elements, guest CTA in FAB sheet
               should slide to acesso panel instead of navigating away.
            ─────────────────────────────────────────────────────────────── */
            <?php if (! $is_logged) : ?>
                var hasPanels = document.querySelector('[data-panel]');
                if (hasPanels) {
                    var fabSheet = document.getElementById('nhMenuSheet');
                    if (fabSheet) {
                        fabSheet.addEventListener('click', function(e) {
                            var link = e.target.closest('a[href*="/acesso"]');
                            if (link && window.ApolloSlider) {
                                e.preventDefault();
                                // Close FAB sheet
                                fabSheet.classList.remove('is-open');
                                var fabBtn = document.getElementById('nhMenuFab');
                                if (fabBtn) {
                                    fabBtn.classList.remove('is-open');
                                    fabBtn.setAttribute('aria-expanded', 'false');
                                }
                                // Navigate to acesso panel
                                ApolloSlider.navigate('acesso', 'down');
                            }
                        });
                    }
                }
            <?php endif; ?>

        })();
    </script>

    <?php
    do_action('apollo/home/after_content');
    /* Canvas Mode — NO wp_footer() to prevent theme interference */
    ?>

</body>

</html>