<?php

/**
 * Template Name: Apollo Mapa
 * Template Post Type: page
 *
 * Full-screen interactive map — events, metros, parks, landmarks.
 * Canvas v2: CDN core.js loads everything. No wp_head/wp_footer.
 * Leaflet.js with CARTO Positron tiles (dark variant for dark-mode).
 *
 * Layers:
 *   - Events (pulsing orange markers from event CPT with _local_lat/_local_lng)
 *   - Metrô Rio stations (static GeoJSON)
 *   - Public parks & praças famosas
 *   - Hot sightseeing landmarks
 *
 * @package Apollo\Templates
 * @since   6.0.0
 * @see     _inventory/pages-rest.json → apollo-loc → /mapa
 * @see     _inventory/apollo-registry.json → meta.local
 */

defined('ABSPATH') || exit;
define('APOLLO_NAVBAR_LOADED', true);

$parts     = plugin_dir_path(__FILE__) . 'template-parts/new-home/';
$is_logged = is_user_logged_in();

// ═══════════════════════════════════════════════════════════════════════
// COLLECT EVENT MARKERS (from event CPT → linked local → _local_lat/lng)
// ═══════════════════════════════════════════════════════════════════════
$event_markers = array();

$events_q = new WP_Query(
    array(
        'post_type'      => 'event',
        'posts_per_page' => 60,
        'post_status'    => 'publish',
        'meta_key'       => '_event_start_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => array(
            array(
                'key'     => '_event_start_date',
                'value'   => current_time('Y-m-d'),
                'compare' => '>=',
                'type'    => 'DATE',
            ),
        ),
    )
);

if ($events_q->have_posts()) {
    while ($events_q->have_posts()) {
        $events_q->the_post();
        $eid    = get_the_ID();
        $loc_id = get_post_meta($eid, '_event_loc_id', true);
        if (! $loc_id) {
            continue;
        }
        $lat = (float) get_post_meta($loc_id, '_local_lat', true);
        $lng = (float) get_post_meta($loc_id, '_local_lng', true);
        if (! $lat || ! $lng) {
            continue;
        }

        $start_date = get_post_meta($eid, '_event_start_date', true);
        $start_time = get_post_meta($eid, '_event_start_time', true);
        $loc_name   = get_the_title($loc_id);

        $event_markers[] = array(
            'id'    => $eid,
            'lat'   => $lat,
            'lng'   => $lng,
            'title' => get_the_title(),
            'loc'   => $loc_name,
            'date'  => $start_date ? date_i18n('d/m', strtotime($start_date)) : '',
            'time'  => $start_time ?: '',
            'url'   => get_permalink(),
        );
    }
    wp_reset_postdata();
}

// Fallback simulated events if no real data
if (empty($event_markers)) {
    $event_markers = array(
        array(
            'id'    => 0,
            'lat'   => -22.9133,
            'lng'   => -43.1787,
            'title' => 'Baile da Lapa',
            'loc'   => 'Arcos da Lapa',
            'date'  => '',
            'time'  => '21h',
            'url'   => '#',
        ),
        array(
            'id'    => 0,
            'lat'   => -22.9711,
            'lng'   => -43.1822,
            'title' => 'Jazz Night Copa',
            'loc'   => 'Copacabana',
            'date'  => '',
            'time'  => '23h',
            'url'   => '#',
        ),
        array(
            'id'    => 0,
            'lat'   => -22.9863,
            'lng'   => -43.2001,
            'title' => 'Sunset Session',
            'loc'   => 'Ipanema',
            'date'  => '',
            'time'  => '17h',
            'url'   => '#',
        ),
        array(
            'id'    => 0,
            'lat'   => -22.9796,
            'lng'   => -43.2211,
            'title' => 'Club Underground',
            'loc'   => 'Leblon',
            'date'  => '',
            'time'  => '00h',
            'url'   => '#',
        ),
        array(
            'id'    => 0,
            'lat'   => -22.9463,
            'lng'   => -43.1875,
            'title' => 'Festival Indie Bota',
            'loc'   => 'Botafogo',
            'date'  => '',
            'time'  => '19h',
            'url'   => '#',
        ),
    );
}

// ═══════════════════════════════════════════════════════════════════════
// COLLECT LOCALS (loc CPT with coords)
// ═══════════════════════════════════════════════════════════════════════
$loc_markers = array();

$locals_q = new WP_Query(
    array(
        'post_type'      => 'local',
        'posts_per_page' => 100,
        'post_status'    => 'publish',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => '_local_lat',
                'compare' => 'EXISTS',
            ),
            array(
                'key'     => '_local_lng',
                'compare' => 'EXISTS',
            ),
        ),
    )
);

if ($locals_q->have_posts()) {
    while ($locals_q->have_posts()) {
        $locals_q->the_post();
        $lid = get_the_ID();
        $lat = (float) get_post_meta($lid, '_local_lat', true);
        $lng = (float) get_post_meta($lid, '_local_lng', true);
        if (! $lat || ! $lng) {
            continue;
        }

        $types = wp_get_post_terms($lid, 'local_type', array('fields' => 'names'));
        $areas = wp_get_post_terms($lid, 'local_area', array('fields' => 'names'));

        $loc_markers[] = array(
            'id'   => $lid,
            'lat'  => $lat,
            'lng'  => $lng,
            'name' => get_the_title(),
            'type' => (! is_wp_error($types) && ! empty($types)) ? $types[0] : '',
            'area' => (! is_wp_error($areas) && ! empty($areas)) ? $areas[0] : '',
            'url'  => get_permalink(),
        );
    }
    wp_reset_postdata();
}

// ═══════════════════════════════════════════════════════════════════════
// STATIC POI DATA — Metrô, Parks, Landmarks
// ═══════════════════════════════════════════════════════════════════════

$metro_stations = array(
    array(
        'name' => 'Uruguai',
        'lat'  => -22.9067,
        'lng'  => -43.2385,
        'line' => 1,
    ),
    array(
        'name' => 'Saens Peña',
        'lat'  => -22.9093,
        'lng'  => -43.2339,
        'line' => 1,
    ),
    array(
        'name' => 'São Francisco Xavier',
        'lat'  => -22.9117,
        'lng'  => -43.2238,
        'line' => 1,
    ),
    array(
        'name' => 'Afonso Pena',
        'lat'  => -22.9130,
        'lng'  => -43.2176,
        'line' => 1,
    ),
    array(
        'name' => 'Estácio',
        'lat'  => -22.9135,
        'lng'  => -43.2050,
        'line' => 1,
    ),
    array(
        'name' => 'Praça Onze',
        'lat'  => -22.9112,
        'lng'  => -43.1998,
        'line' => 1,
    ),
    array(
        'name' => 'Central',
        'lat'  => -22.9027,
        'lng'  => -43.1720,
        'line' => 1,
    ),
    array(
        'name' => 'Presidente Vargas',
        'lat'  => -22.9033,
        'lng'  => -43.1797,
        'line' => 1,
    ),
    array(
        'name' => 'Uruguaiana',
        'lat'  => -22.9048,
        'lng'  => -43.1793,
        'line' => 1,
    ),
    array(
        'name' => 'Carioca',
        'lat'  => -22.9068,
        'lng'  => -43.1766,
        'line' => 1,
    ),
    array(
        'name' => 'Cinelândia',
        'lat'  => -22.9101,
        'lng'  => -43.1756,
        'line' => 1,
    ),
    array(
        'name' => 'Glória',
        'lat'  => -22.9176,
        'lng'  => -43.1767,
        'line' => 1,
    ),
    array(
        'name' => 'Catete',
        'lat'  => -22.9270,
        'lng'  => -43.1773,
        'line' => 1,
    ),
    array(
        'name' => 'Largo do Machado',
        'lat'  => -22.9329,
        'lng'  => -43.1778,
        'line' => 1,
    ),
    array(
        'name' => 'Flamengo',
        'lat'  => -22.9391,
        'lng'  => -43.1732,
        'line' => 1,
    ),
    array(
        'name' => 'Botafogo',
        'lat'  => -22.9515,
        'lng'  => -43.1872,
        'line' => 1,
    ),
    array(
        'name' => 'Cardeal Arcoverde',
        'lat'  => -22.9587,
        'lng'  => -43.1815,
        'line' => 1,
    ),
    array(
        'name' => 'Siqueira Campos',
        'lat'  => -22.9719,
        'lng'  => -43.1817,
        'line' => 1,
    ),
    array(
        'name' => 'Cantagalo',
        'lat'  => -22.9783,
        'lng'  => -43.1916,
        'line' => 1,
    ),
    array(
        'name' => 'General Osório',
        'lat'  => -22.9862,
        'lng'  => -43.1978,
        'line' => 1,
    ),
    array(
        'name' => 'Jardim de Alah',
        'lat'  => -22.9845,
        'lng'  => -43.2076,
        'line' => 4,
    ),
    array(
        'name' => 'Antero de Quental',
        'lat'  => -22.9782,
        'lng'  => -43.2179,
        'line' => 4,
    ),
    array(
        'name' => 'Jardim Oceânico',
        'lat'  => -22.9879,
        'lng'  => -43.3656,
        'line' => 4,
    ),
    array(
        'name' => 'São Conrado',
        'lat'  => -22.9933,
        'lng'  => -43.2726,
        'line' => 4,
    ),
    array(
        'name' => 'Barra da Tijuca',
        'lat'  => -22.9994,
        'lng'  => -43.3652,
        'line' => 4,
    ),
);

$parks = array(
    array(
        'name' => 'Aterro do Flamengo',
        'lat'  => -22.9345,
        'lng'  => -43.1695,
    ),
    array(
        'name' => 'Jardim Botânico',
        'lat'  => -22.9673,
        'lng'  => -43.2247,
    ),
    array(
        'name' => 'Parque Lage',
        'lat'  => -22.9590,
        'lng'  => -43.2124,
    ),
    array(
        'name' => 'Floresta da Tijuca',
        'lat'  => -22.9503,
        'lng'  => -43.2805,
    ),
    array(
        'name' => 'Quinta da Boa Vista',
        'lat'  => -22.9054,
        'lng'  => -43.2269,
    ),
    array(
        'name' => 'Campo de Santana',
        'lat'  => -22.9076,
        'lng'  => -43.1891,
    ),
    array(
        'name' => 'Praça Mauá',
        'lat'  => -22.8962,
        'lng'  => -43.1761,
    ),
    array(
        'name' => 'Praia de Copacabana',
        'lat'  => -22.9711,
        'lng'  => -43.1822,
    ),
    array(
        'name' => 'Praia de Ipanema',
        'lat'  => -22.9863,
        'lng'  => -43.2001,
    ),
    array(
        'name' => 'Mirante Dona Marta',
        'lat'  => -22.9452,
        'lng'  => -43.1911,
    ),
);

$landmarks = array(
    array(
        'name' => 'Cristo Redentor',
        'lat'  => -22.9519,
        'lng'  => -43.2105,
        'icon' => 'ri-landscape-fill',
    ),
    array(
        'name' => 'Pão de Açúcar',
        'lat'  => -22.9488,
        'lng'  => -43.1563,
        'icon' => 'ri-landscape-fill',
    ),
    array(
        'name' => 'Maracanã',
        'lat'  => -22.9121,
        'lng'  => -43.2302,
        'icon' => 'ri-football-fill',
    ),
    array(
        'name' => 'Arcos da Lapa',
        'lat'  => -22.9133,
        'lng'  => -43.1787,
        'icon' => 'ri-ancient-gate-fill',
    ),
    array(
        'name' => 'Escadaria Selarón',
        'lat'  => -22.9152,
        'lng'  => -43.1794,
        'icon' => 'ri-stairs-fill',
    ),
    array(
        'name' => 'MAM Rio',
        'lat'  => -22.9136,
        'lng'  => -43.1704,
        'icon' => 'ri-gallery-fill',
    ),
    array(
        'name' => 'Museu do Amanhã',
        'lat'  => -22.8941,
        'lng'  => -43.1793,
        'icon' => 'ri-building-4-fill',
    ),
    array(
        'name' => 'Theatro Municipal',
        'lat'  => -22.9092,
        'lng'  => -43.1765,
        'icon' => 'ri-building-fill',
    ),
    array(
        'name' => 'AquaRio',
        'lat'  => -22.8939,
        'lng'  => -43.1861,
        'icon' => 'ri-water-flash-fill',
    ),
    array(
        'name' => 'Pedra do Telégrafo',
        'lat'  => -23.0520,
        'lng'  => -43.5045,
        'icon' => 'ri-camera-fill',
    ),
);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="dark-mode">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="theme-color" content="#0A0A0A">
    <title><?php echo esc_html(get_bloginfo('name')); ?> — Mapa</title>

    <script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>
    <link rel="stylesheet" href="https://cdn.apollo.rio.br/v1.0.0/css/99-osm-map.min.css">

    <style id="apollo-mapa">
        /* ── Full-screen map layout ── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
            background: var(--bg, #0A0A0A);
            color: var(--txt-color, #E8E8E8);
            font-family: var(--ff-main, 'Space Grotesk', system-ui, sans-serif)
        }

        .mapa-wrap {
            position: fixed;
            inset: 0
        }

        #apolloMapFull {
            width: 100%;
            height: 100%
        }

        /* ── Controls overlay ── */
        .mapa-controls {
            position: fixed;
            top: env(safe-area-inset-top, 12px);
            left: var(--space-4, 16px);
            right: var(--space-4, 16px);
            z-index: 800;
            display: flex;
            align-items: center;
            gap: 8px;
            pointer-events: none
        }

        .mapa-controls>* {
            pointer-events: auto
        }

        .mapa-back {
            all: unset;
            cursor: pointer;
            display: grid;
            place-items: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(10, 10, 10, .75);
            -webkit-backdrop-filter: blur(16px);
            backdrop-filter: blur(16px);
            color: #fff;
            font-size: 18px;
            transition: all .2s;
            flex-shrink: 0
        }

        .mapa-back:hover {
            background: rgba(10, 10, 10, .9)
        }

        .mapa-title {
            font: 700 16px/1 var(--ff-mono, 'Space Mono', monospace);
            color: #fff;
            text-shadow: 0 1px 4px rgba(0, 0, 0, .4);
            letter-spacing: -.01em
        }

        /* ── Layer toggles ── */
        .mapa-layers {
            position: fixed;
            bottom: calc(env(safe-area-inset-bottom, 12px) + 80px);
            left: var(--space-4, 16px);
            z-index: 800;
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .mapa-layer-btn {
            all: unset;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 100px;
            background: rgba(10, 10, 10, .7);
            -webkit-backdrop-filter: blur(16px);
            backdrop-filter: blur(16px);
            color: #fff;
            font: 500 12px/1 var(--ff-main);
            transition: all .25s;
            border: 1px solid transparent
        }

        .mapa-layer-btn:hover {
            background: rgba(10, 10, 10, .85)
        }

        .mapa-layer-btn.active {
            border-color: var(--primary, #FF6B35);
            background: rgba(244, 95, 0, .15)
        }

        .mapa-layer-btn i {
            font-size: 15px
        }

        /* ── Info panel (bottom sheet) ── */
        .mapa-info {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 900;
            transform: translateY(100%);
            transition: transform .4s cubic-bezier(.22, 1, .36, 1);
            background: rgba(10, 10, 10, .92);
            -webkit-backdrop-filter: saturate(180%) blur(24px);
            backdrop-filter: saturate(180%) blur(24px);
            border-radius: 20px 20px 0 0;
            max-height: 50vh;
            overflow-y: auto;
            padding: 24px var(--space-4, 16px) calc(24px + env(safe-area-inset-bottom, 12px))
        }

        .mapa-info.open {
            transform: translateY(0)
        }

        .mapa-info__handle {
            width: 36px;
            height: 4px;
            border-radius: 2px;
            background: rgba(255, 255, 255, .2);
            margin: 0 auto 16px
        }

        .mapa-info__title {
            font: 700 18px/1.2 var(--ff-mono);
            color: #fff;
            margin: 0 0 6px
        }

        .mapa-info__sub {
            font: 400 13px/1.5 var(--ff-main);
            color: rgba(255, 255, 255, .55);
            margin: 0 0 16px
        }

        .mapa-info__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px
        }

        .mapa-info__tag {
            font: 500 11px/1 var(--ff-mono);
            color: rgba(255, 255, 255, .7);
            background: rgba(255, 255, 255, .08);
            padding: 6px 12px;
            border-radius: 100px;
            text-transform: uppercase;
            letter-spacing: .04em
        }

        .mapa-info__tag--event {
            background: rgba(244, 95, 0, .15);
            color: var(--primary, #FF6B35)
        }

        .mapa-info__tag--metro {
            background: rgba(59, 130, 246, .15);
            color: #60A5FA
        }

        .mapa-info__tag--park {
            background: rgba(34, 197, 94, .15);
            color: #4ADE80
        }

        .mapa-info__tag--landmark {
            background: rgba(168, 85, 247, .15);
            color: #C084FC
        }

        .mapa-info__actions {
            display: flex;
            gap: 8px
        }

        .mapa-info__btn {
            all: unset;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: 100px;
            font: 500 13px/1 var(--ff-main);
            transition: all .2s
        }

        .mapa-info__btn--primary {
            background: var(--primary, #FF6B35);
            color: #fff
        }

        .mapa-info__btn--primary:hover {
            opacity: .85
        }

        .mapa-info__btn--ghost {
            background: rgba(255, 255, 255, .08);
            color: rgba(255, 255, 255, .7)
        }

        .mapa-info__btn--ghost:hover {
            background: rgba(255, 255, 255, .14)
        }

        .mapa-info__close {
            position: absolute;
            top: 16px;
            right: 16px;
            all: unset;
            cursor: pointer;
            color: rgba(255, 255, 255, .4);
            font-size: 18px;
            transition: color .2s
        }

        .mapa-info__close:hover {
            color: #fff
        }

        /* ── Distance badge ── */
        .mapa-distance {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font: 400 12px/1 var(--ff-mono);
            color: rgba(255, 255, 255, .5);
            margin-top: 8px
        }

        .mapa-distance i {
            font-size: 14px
        }

        /* ── Leaflet overrides ── */
        .leaflet-control-attribution {
            display: none !important
        }

        .leaflet-popup-content-wrapper {
            background: rgba(10, 10, 10, .85);
            border-radius: 12px;
            color: #fff;
            -webkit-backdrop-filter: blur(12px);
            backdrop-filter: blur(12px)
        }

        .leaflet-popup-tip {
            background: rgba(10, 10, 10, .85)
        }

        .leaflet-popup-content {
            font: 400 13px/1.4 var(--ff-main);
            margin: 10px 14px
        }

        /* ── Pulse marker (events) ── */
        .mapa-pulse {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--primary, #FF6B35);
            box-shadow: 0 0 0 0 rgba(244, 95, 0, .5);
            animation: mapaPulse 2s ease-out infinite;
            cursor: pointer;
            border: 2px solid rgba(255, 255, 255, .9)
        }

        @keyframes mapaPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(244, 95, 0, .5)
            }

            70% {
                box-shadow: 0 0 0 16px rgba(244, 95, 0, 0)
            }

            100% {
                box-shadow: 0 0 0 0 rgba(244, 95, 0, 0)
            }
        }

        /* ── Static markers (metro/park/landmark) ── */
        .mapa-marker {
            display: grid;
            place-items: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 14px;
            border: 2px solid rgba(255, 255, 255, .7);
            transition: transform .2s
        }

        .mapa-marker:hover {
            transform: scale(1.2)
        }

        .mapa-marker--metro {
            background: #3B82F6;
            color: #fff
        }

        .mapa-marker--park {
            background: #22C55E;
            color: #fff
        }

        .mapa-marker--landmark {
            background: #A855F7;
            color: #fff
        }

        .mapa-marker--loc {
            background: rgba(255, 255, 255, .12);
            color: #fff;
            border-color: rgba(255, 255, 255, .3)
        }
    </style>

    <?php do_action('apollo/mapa/head'); ?>
</head>

<body>

    <!-- ═══════════════════════════════════════════════════════════════
		PERSISTENT UI — Navbar + FAB
	═══════════════════════════════════════════════════════════════ -->
    <?php
    require $parts . 'navbar.php';
    require $parts . 'menu-fab.php';
    ?>

    <!-- ═══════════════════════════════════════════════════════════════
		MAP CANVAS
	═══════════════════════════════════════════════════════════════ -->
    <div class="mapa-wrap">
        <div id="apolloMapFull"
            data-lat="-22.9502"
            data-lng="-43.1903"
            data-zoom="12"
            role="application"
            aria-label="<?php esc_attr_e('Mapa interativo do Rio de Janeiro', 'apollo-templates'); ?>">
        </div>
    </div>

    <!-- ── Controls ── -->
    <div class="mapa-controls">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="mapa-back" aria-label="<?php esc_attr_e('Voltar ao início', 'apollo-templates'); ?>">
            <i class="ri-arrow-left-s-line"></i>
        </a>
        <span class="mapa-title"><?php esc_html_e('Mapa', 'apollo-templates'); ?></span>
    </div>

    <!-- ── Layer Toggle Buttons ── -->
    <div class="mapa-layers">
        <button class="mapa-layer-btn active" data-layer="events" aria-pressed="true">
            <i class="ri-calendar-event-fill"></i> <?php esc_html_e('Eventos', 'apollo-templates'); ?>
        </button>
        <button class="mapa-layer-btn" data-layer="metros" aria-pressed="false">
            <i class="ri-train-fill"></i> <?php esc_html_e('Metrô', 'apollo-templates'); ?>
        </button>
        <button class="mapa-layer-btn" data-layer="parks" aria-pressed="false">
            <i class="ri-leaf-fill"></i> <?php esc_html_e('Parques', 'apollo-templates'); ?>
        </button>
        <button class="mapa-layer-btn" data-layer="landmarks" aria-pressed="false">
            <i class="ri-camera-fill"></i> <?php esc_html_e('Pontos Turísticos', 'apollo-templates'); ?>
        </button>
        <?php if (! empty($loc_markers)) : ?>
            <button class="mapa-layer-btn" data-layer="locals" aria-pressed="false">
                <i class="ri-map-pin-user-fill"></i> <?php esc_html_e('Espaços', 'apollo-templates'); ?>
            </button>
        <?php endif; ?>
    </div>

    <!-- ── Info Panel (bottom sheet) ── -->
    <div class="mapa-info" id="mapaInfo" aria-hidden="true">
        <button class="mapa-info__close" id="mapaInfoClose" aria-label="Fechar"><i class="ri-close-line"></i></button>
        <div class="mapa-info__handle"></div>
        <h3 class="mapa-info__title" id="mapaInfoTitle"></h3>
        <p class="mapa-info__sub" id="mapaInfoSub"></p>
        <div class="mapa-info__meta" id="mapaInfoMeta"></div>
        <div class="mapa-distance" id="mapaInfoDist" style="display:none">
            <i class="ri-walk-fill"></i>
            <span id="mapaInfoDistText"></span>
        </div>
        <div class="mapa-info__actions" id="mapaInfoActions"></div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════
		LEAFLET + MAP ENGINE
	═══════════════════════════════════════════════════════════════ -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        (function() {
            'use strict';

            /* ── Data from PHP ── */
            var EVENTS = <?php echo wp_json_encode($event_markers); ?>;
            var LOCALS = <?php echo wp_json_encode($loc_markers); ?>;
            var METROS = <?php echo wp_json_encode($metro_stations); ?>;
            var PARKS = <?php echo wp_json_encode($parks); ?>;
            var LANDMARKS = <?php echo wp_json_encode($landmarks); ?>;
            var IS_LOGGED = <?php echo $is_logged ? 'true' : 'false'; ?>;

            /* ── Wait for Leaflet ── */
            function waitForL(cb, n) {
                n = n || 0;
                if (typeof L !== 'undefined') cb();
                else if (n < 100) setTimeout(function() {
                    waitForL(cb, n + 1);
                }, 50);
            }

            waitForL(function() {
                var el = document.getElementById('apolloMapFull');
                if (!el) return;

                var map = L.map(el, {
                    center: [-22.9502, -43.1903],
                    zoom: 12,
                    zoomControl: false,
                    attributionControl: false
                });

                /* ── CARTO Dark Matter tiles (dark mode) ── */
                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    subdomains: 'abcd',
                    maxZoom: 19
                }).addTo(map);

                /* ── Zoom control bottom-right ── */
                L.control.zoom({
                    position: 'bottomright'
                }).addTo(map);

                /* ── Layer Groups ── */
                var layers = {
                    events: L.layerGroup().addTo(map),
                    metros: L.layerGroup(),
                    parks: L.layerGroup(),
                    landmarks: L.layerGroup(),
                    locals: L.layerGroup()
                };

                /* ── Info Panel ── */
                var infoEl = document.getElementById('mapaInfo');
                var infoTitle = document.getElementById('mapaInfoTitle');
                var infoSub = document.getElementById('mapaInfoSub');
                var infoMeta = document.getElementById('mapaInfoMeta');
                var infoDist = document.getElementById('mapaInfoDist');
                var infoDistT = document.getElementById('mapaInfoDistText');
                var infoActions = document.getElementById('mapaInfoActions');

                function openInfo(data) {
                    infoTitle.textContent = data.title || '';
                    infoSub.textContent = data.sub || '';
                    infoMeta.innerHTML = data.tags || '';
                    infoActions.innerHTML = data.actions || '';
                    infoEl.classList.add('open');
                    infoEl.setAttribute('aria-hidden', 'false');

                    /* Distance from user if geolocation available */
                    infoDist.style.display = 'none';
                    if (data.lat && data.lng && navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(pos) {
                            var d = haversine(pos.coords.latitude, pos.coords.longitude, data.lat, data.lng);
                            infoDistT.textContent = d < 1 ? Math.round(d * 1000) + 'm' : d.toFixed(1) + 'km';
                            infoDist.style.display = '';
                        }, function() {}, {
                            timeout: 3000
                        });
                    }
                }

                function closeInfo() {
                    infoEl.classList.remove('open');
                    infoEl.setAttribute('aria-hidden', 'true');
                }

                document.getElementById('mapaInfoClose').addEventListener('click', closeInfo);

                /* ── Haversine distance (km) ── */
                function haversine(lat1, lon1, lat2, lon2) {
                    var R = 6371;
                    var dLat = (lat2 - lat1) * Math.PI / 180;
                    var dLon = (lon2 - lon1) * Math.PI / 180;
                    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
                }

                /* ── Nearest metro from a point ── */
                function nearestMetro(lat, lng) {
                    var best = null,
                        bestD = Infinity;
                    METROS.forEach(function(m) {
                        var d = haversine(lat, lng, m.lat, m.lng);
                        if (d < bestD) {
                            bestD = d;
                            best = m;
                        }
                    });
                    return best ? {
                        name: best.name,
                        dist: bestD
                    } : null;
                }

                /* ══ POPULATE EVENTS ══ */
                EVENTS.forEach(function(ev) {
                    var icon = L.divIcon({
                        className: '',
                        html: '<div class="mapa-pulse"></div>',
                        iconSize: [14, 14],
                        iconAnchor: [7, 7]
                    });

                    var marker = L.marker([ev.lat, ev.lng], {
                        icon: icon
                    }).addTo(layers.events);
                    marker.on('click', function() {
                        var metro = nearestMetro(ev.lat, ev.lng);
                        var metroTag = metro ? '<span class="mapa-info__tag mapa-info__tag--metro"><i class="ri-train-fill" style="margin-right:4px"></i>' + metro.name + ' · ' + (metro.dist < 1 ? Math.round(metro.dist * 1000) + 'm' : metro.dist.toFixed(1) + 'km') + '</span>' : '';
                        var dateTag = ev.date ? '<span class="mapa-info__tag mapa-info__tag--event">' + ev.date + (ev.time ? ' · ' + ev.time : '') + '</span>' : '';

                        var actions = '';
                        if (IS_LOGGED) {
                            actions = '<a href="' + ev.url + '" class="mapa-info__btn mapa-info__btn--primary"><i class="ri-eye-line"></i> Ver Evento</a>';
                            actions += '<button class="mapa-info__btn mapa-info__btn--ghost" onclick="navigator.share?navigator.share({title:\'' + ev.title.replace(/'/g, '') + '\',url:\'' + ev.url + '\'}):void 0"><i class="ri-share-forward-line"></i></button>';
                        } else {
                            actions = '<a href="' + ev.url + '" class="mapa-info__btn mapa-info__btn--primary"><i class="ri-eye-line"></i> Ver Evento</a>';
                        }

                        openInfo({
                            title: ev.title,
                            sub: ev.loc || '',
                            tags: dateTag + metroTag,
                            actions: actions,
                            lat: ev.lat,
                            lng: ev.lng
                        });

                        map.flyTo([ev.lat, ev.lng], 15, {
                            duration: .6
                        });
                    });
                });

                /* ══ POPULATE METROS ══ */
                METROS.forEach(function(m) {
                    var icon = L.divIcon({
                        className: '',
                        html: '<div class="mapa-marker mapa-marker--metro"><i class="ri-train-fill"></i></div>',
                        iconSize: [28, 28],
                        iconAnchor: [14, 14]
                    });
                    var marker = L.marker([m.lat, m.lng], {
                        icon: icon
                    }).addTo(layers.metros);
                    marker.on('click', function() {
                        openInfo({
                            title: 'Metrô ' + m.name,
                            sub: 'Linha ' + m.line,
                            tags: '<span class="mapa-info__tag mapa-info__tag--metro">MetrôRio</span>',
                            actions: '<button class="mapa-info__btn mapa-info__btn--ghost" onclick="window.open(\'https://www.google.com/maps/dir/?api=1&destination=' + m.lat + ',' + m.lng + '\',\'_blank\')"><i class="ri-route-fill"></i> Rota</button>',
                            lat: m.lat,
                            lng: m.lng
                        });
                        map.flyTo([m.lat, m.lng], 16, {
                            duration: .5
                        });
                    });
                });

                /* ══ POPULATE PARKS ══ */
                PARKS.forEach(function(p) {
                    var icon = L.divIcon({
                        className: '',
                        html: '<div class="mapa-marker mapa-marker--park"><i class="ri-leaf-fill"></i></div>',
                        iconSize: [28, 28],
                        iconAnchor: [14, 14]
                    });
                    var marker = L.marker([p.lat, p.lng], {
                        icon: icon
                    }).addTo(layers.parks);
                    marker.on('click', function() {
                        var metro = nearestMetro(p.lat, p.lng);
                        var metroTag = metro ? '<span class="mapa-info__tag mapa-info__tag--metro"><i class="ri-train-fill" style="margin-right:4px"></i>' + metro.name + ' · ' + (metro.dist < 1 ? Math.round(metro.dist * 1000) + 'm' : metro.dist.toFixed(1) + 'km') + '</span>' : '';
                        openInfo({
                            title: p.name,
                            sub: 'Parque / Área Verde',
                            tags: '<span class="mapa-info__tag mapa-info__tag--park">Parque</span>' + metroTag,
                            actions: '<button class="mapa-info__btn mapa-info__btn--ghost" onclick="window.open(\'https://www.google.com/maps/dir/?api=1&destination=' + p.lat + ',' + p.lng + '\',\'_blank\')"><i class="ri-route-fill"></i> Rota</button>',
                            lat: p.lat,
                            lng: p.lng
                        });
                        map.flyTo([p.lat, p.lng], 15, {
                            duration: .5
                        });
                    });
                });

                /* ══ POPULATE LANDMARKS ══ */
                LANDMARKS.forEach(function(lm) {
                    var iconCls = lm.icon || 'ri-camera-fill';
                    var icon = L.divIcon({
                        className: '',
                        html: '<div class="mapa-marker mapa-marker--landmark"><i class="' + iconCls + '"></i></div>',
                        iconSize: [28, 28],
                        iconAnchor: [14, 14]
                    });
                    var marker = L.marker([lm.lat, lm.lng], {
                        icon: icon
                    }).addTo(layers.landmarks);
                    marker.on('click', function() {
                        var metro = nearestMetro(lm.lat, lm.lng);
                        var metroTag = metro ? '<span class="mapa-info__tag mapa-info__tag--metro"><i class="ri-train-fill" style="margin-right:4px"></i>' + metro.name + ' · ' + (metro.dist < 1 ? Math.round(metro.dist * 1000) + 'm' : metro.dist.toFixed(1) + 'km') + '</span>' : '';
                        openInfo({
                            title: lm.name,
                            sub: 'Ponto Turístico',
                            tags: '<span class="mapa-info__tag mapa-info__tag--landmark">Landmark</span>' + metroTag,
                            actions: '<button class="mapa-info__btn mapa-info__btn--ghost" onclick="window.open(\'https://www.google.com/maps/dir/?api=1&destination=' + lm.lat + ',' + lm.lng + '\',\'_blank\')"><i class="ri-route-fill"></i> Rota</button>',
                            lat: lm.lat,
                            lng: lm.lng
                        });
                        map.flyTo([lm.lat, lm.lng], 16, {
                            duration: .5
                        });
                    });
                });

                /* ══ POPULATE LOCALS (loc CPT) ══ */
                LOCALS.forEach(function(loc) {
                    var icon = L.divIcon({
                        className: '',
                        html: '<div class="mapa-marker mapa-marker--loc"><i class="ri-map-pin-user-fill"></i></div>',
                        iconSize: [28, 28],
                        iconAnchor: [14, 14]
                    });
                    var marker = L.marker([loc.lat, loc.lng], {
                        icon: icon
                    }).addTo(layers.locals);
                    marker.on('click', function() {
                        var metro = nearestMetro(loc.lat, loc.lng);
                        var metroTag = metro ? '<span class="mapa-info__tag mapa-info__tag--metro"><i class="ri-train-fill" style="margin-right:4px"></i>' + metro.name + ' · ' + (metro.dist < 1 ? Math.round(metro.dist * 1000) + 'm' : metro.dist.toFixed(1) + 'km') + '</span>' : '';
                        var typeTag = loc.type ? '<span class="mapa-info__tag">' + loc.type + '</span>' : '';
                        openInfo({
                            title: loc.name,
                            sub: loc.area || '',
                            tags: typeTag + metroTag,
                            actions: '<a href="' + loc.url + '" class="mapa-info__btn mapa-info__btn--primary"><i class="ri-eye-line"></i> Ver Espaço</a>',
                            lat: loc.lat,
                            lng: loc.lng
                        });
                        map.flyTo([loc.lat, loc.lng], 16, {
                            duration: .5
                        });
                    });
                });

                /* ══ LAYER TOGGLE ══ */
                document.querySelectorAll('.mapa-layer-btn').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var layerKey = btn.getAttribute('data-layer');
                        var isActive = btn.classList.toggle('active');
                        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');

                        if (isActive) {
                            map.addLayer(layers[layerKey]);
                        } else {
                            map.removeLayer(layers[layerKey]);
                        }
                        closeInfo();
                    });
                });

                /* ── Close info on map click ── */
                map.on('click', closeInfo);

                /* ── Expose for external use ── */
                window.ApolloMapFull = map;
            });
        })();
    </script>

    <?php do_action('apollo/mapa/after_content'); ?>
</body>

</html>