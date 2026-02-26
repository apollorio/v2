<?php

/**
 * Archive Event — Apollo Blank Canvas
 *
 * Radar-style events listing with full client-side filtering.
 * Template-parts architecture for modularity.
 *
 * @package Apollo\Event
 * @since   1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

/* ─── Data Collection ─────────────────────────────────────── */

$event_cpt = defined('APOLLO_EVENT_CPT') ? APOLLO_EVENT_CPT : 'event';
$tax_sound = defined('APOLLO_EVENT_TAX_SOUND') ? APOLLO_EVENT_TAX_SOUND : 'sound';
$tax_cat   = defined('APOLLO_EVENT_TAX_CATEGORY') ? APOLLO_EVENT_TAX_CATEGORY : 'event_category';

// Sound terms for filter pills
$sound_terms = get_terms(
    array(
        'taxonomy'   => $tax_sound,
        'hide_empty' => true,
        'orderby'    => 'count',
        'order'      => 'DESC',
    )
);
if (is_wp_error($sound_terms)) {
    $sound_terms = array();
}

// Category terms for secondary filter
$category_terms = get_terms(
    array(
        'taxonomy'   => $tax_cat,
        'hide_empty' => true,
    )
);
if (is_wp_error($category_terms)) {
    $category_terms = array();
}

// Custom query — load ALL published events for client-side filtering
$events_query = new WP_Query(
    array(
        'post_type'      => $event_cpt,
        'posts_per_page' => 200,
        'post_status'    => 'publish',
        'meta_key'       => '_event_start_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
    )
);

$events      = array();
$today_stamp = current_time('Y-m-d');

if ($events_query->have_posts()) {
    while ($events_query->have_posts()) {
        $events_query->the_post();
        $eid     = get_the_ID();
        $is_gone = function_exists('apollo_event_is_gone') ? apollo_event_is_gone($eid) : false;

        // Optionally skip gone events
        if ($is_gone && function_exists('apollo_event_option') && ! apollo_event_option('show_gone_events', true)) {
            continue;
        }

        $start_date = get_post_meta($eid, '_event_start_date', true);
        $loc_data   = function_exists('apollo_event_get_loc') ? apollo_event_get_loc($eid) : null;
        $djs_data   = function_exists('apollo_event_get_djs') ? apollo_event_get_djs($eid) : array();

        $events[] = array(
            'id'           => $eid,
            'title'        => get_the_title(),
            'url'          => get_permalink(),
            'banner'       => function_exists('apollo_event_get_banner')
                ? apollo_event_get_banner($eid)
                : (get_the_post_thumbnail_url($eid, 'large') ?: ''),
            'start_date'   => $start_date ?: $today_stamp,
            'start_time'   => get_post_meta($eid, '_event_start_time', true),
            'end_time'     => get_post_meta($eid, '_event_end_time', true),
            'dj_names'     => implode(', ', array_column($djs_data, 'title')),
            'loc_name'     => $loc_data['title'] ?? '',
            'ticket_price' => get_post_meta($eid, '_event_ticket_price', true),
            'ticket_url'   => get_post_meta($eid, '_event_ticket_url', true),
            'sounds'       => wp_get_post_terms($eid, $tax_sound, array('fields' => 'names')),
            'sound_slugs'  => wp_get_post_terms($eid, $tax_sound, array('fields' => 'slugs')),
            'categories'   => wp_get_post_terms($eid, $tax_cat, array('fields' => 'names')),
            'is_gone'      => $is_gone,
            'privacy'      => get_post_meta($eid, '_event_privacy', true) ?: 'public',
            'status'       => get_post_meta($eid, '_event_status', true) ?: 'scheduled',
        );
    }
    wp_reset_postdata();
}

$total_found = count($events);
$rest_base   = esc_url(rest_url('apollo/v1/'));
$nonce       = wp_create_nonce('wp_rest');

// Path to template-parts
$parts = __DIR__ . '/template-parts/archive/';

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Eventos — <?php bloginfo('name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Fonts: Shrikhand only (Space Grotesk + Space Mono already loaded by CDN) -->
    <link href="https://fonts.googleapis.com/css2?family=Shrikhand&display=swap" rel="stylesheet">
    <?php require $parts . 'styles.php'; ?>
</head>

<body>

    <!-- Page Loader -->
    <div class="page-loader"></div>

    <!-- Apollo CDN -->
    <script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>

    <!-- Navbar -->
    <?php
    if (function_exists('apollo_get_navbar')) {
        apollo_get_navbar();
    }
    ?>

    <!-- Hero -->
    <?php require $parts . 'hero.php'; ?>

    <!-- Main Content -->
    <main class="ev-main" data-rest="<?php echo esc_attr($rest_base); ?>" data-nonce="<?php echo esc_attr($nonce); ?>" data-today="<?php echo esc_attr($today_stamp); ?>">

        <!-- Toolbar (filters + search) -->
        <?php require $parts . 'toolbar.php'; ?>

        <!-- Events Grid -->
        <section class="ev-grid-wrap">
            <div class="ev-grid" id="ev-grid">
                <?php if (! empty($events)) : ?>
                    <?php foreach ($events as $ev) : ?>
                        <?php include $parts . 'event-card.php'; ?>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="ev-empty" id="ev-empty">
                        <i class="ri-calendar-event-line"></i>
                        <p>Nenhum evento encontrado</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Live empty state (shown by JS when filters hide all) -->
            <div class="ev-empty ev-empty--hidden" id="ev-empty-live">
                <i class="ri-filter-off-line"></i>
                <p>Nenhum evento corresponde aos filtros</p>
                <button class="ev-empty__clear" id="ev-clear-all">Limpar filtros</button>
            </div>
        </section>

        <!-- Counter -->
        <div class="ev-counter">
            <span class="ev-counter__num" id="ev-counter"><?php echo esc_html($total_found); ?></span>
            <span class="ev-counter__label">eventos encontrados</span>
        </div>

    </main>

    <!-- GSAP already loaded by CDN core.js (v3.14.2) -->

    <!-- Scripts -->
    <?php require $parts . 'scripts.php'; ?>

</body>

</html>