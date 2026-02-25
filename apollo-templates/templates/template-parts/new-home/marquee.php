<?php

/**
 * New Home — Marquee Ticker (Real Data)
 *
 * Animated horizontal ticker strip pulling LIVE data from:
 *   - News/Journal (post type: post)         → ri-newspaper-line
 *   - Community (latest registered users)     → ri-user-community-fill
 *   - Tracks/DJs (post type: dj)             → ri-sound-module-line
 *   - Events (post type: event)              → ri-calendar-line
 *   - Classifieds (post type: classified)    → ri-token-swap-fill
 *   - Accommodations (post type: local)      → ri-home-heart-line
 *
 * Duplicate set for seamless CSS infinite scroll animation.
 * Cached via transient (5 min) to avoid per-request queries.
 *
 * @package Apollo\Templates
 * @since   6.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Build marquee items from real CPT data.
 * Uses transient cache for performance.
 */
function apollo_build_marquee_items()
{
    $cache_key = 'apollo_marquee_items_v2';
    $cached    = get_transient($cache_key);
    if (false !== $cached) {
        return $cached;
    }

    $items = array();

    // ── 1. Latest News / Journal posts ──
    $news_q = new WP_Query(array(
        'post_type'      => 'post',
        'posts_per_page' => 2,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ));
    if ($news_q->have_posts()) {
        while ($news_q->have_posts()) {
            $news_q->the_post();
            $items[] = array(
                'type' => 'news',
                'icon' => 'ri-newspaper-line',
                'text' => wp_trim_words(get_the_title(), 8, '…'),
            );
        }
        wp_reset_postdata();
    }

    // ── 2. Latest community members ──
    $users = get_users(array(
        'orderby' => 'registered',
        'order'   => 'DESC',
        'number'  => 2,
        'fields'  => array('user_login', 'display_name'),
    ));
    foreach ($users as $u) {
        $name    = $u->display_name ? $u->display_name : $u->user_login;
        $items[] = array(
            'type' => 'community',
            'icon' => 'ri-user-community-fill',
            'text' => '@' . sanitize_text_field($name) . ' joined the community',
        );
    }

    // ── 3. Latest DJ tracks (Out Now) ──
    $dj_q = new WP_Query(array(
        'post_type'      => 'dj',
        'posts_per_page' => 2,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ));
    if ($dj_q->have_posts()) {
        while ($dj_q->have_posts()) {
            $dj_q->the_post();
            $artist  = get_post_meta(get_the_ID(), '_dj_artist', true);
            $artist  = $artist ? $artist : get_the_author();
            $items[] = array(
                'type' => 'track',
                'icon' => 'ri-sound-module-line',
                'text' => 'Out Now: "' . wp_trim_words(get_the_title(), 5, '…') . '" — ' . sanitize_text_field($artist),
            );
        }
        wp_reset_postdata();
    }

    // ── 4. Upcoming events ──
    $event_q = new WP_Query(array(
        'post_type'      => 'event',
        'posts_per_page' => 2,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ));
    if ($event_q->have_posts()) {
        while ($event_q->have_posts()) {
            $event_q->the_post();
            $loc_id  = get_post_meta(get_the_ID(), '_event_loc_id', true);
            $loc_txt = '';
            if ($loc_id) {
                $loc_txt = ' · ' . get_the_title($loc_id);
            }
            $items[] = array(
                'type' => 'event',
                'icon' => 'ri-calendar-line',
                'text' => wp_trim_words(get_the_title(), 6, '…') . $loc_txt,
            );
        }
        wp_reset_postdata();
    }

    // ── 5. Latest classifieds / repasses ──
    $class_q = new WP_Query(array(
        'post_type'      => 'classified',
        'posts_per_page' => 2,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ));
    if ($class_q->have_posts()) {
        while ($class_q->have_posts()) {
            $class_q->the_post();
            $price   = get_post_meta(get_the_ID(), '_classified_price', true);
            $price_t = $price ? ' R$ ' . number_format_i18n((float) $price, 0) : '';
            $items[] = array(
                'type' => 'classified',
                'icon' => 'ri-token-swap-fill',
                'text' => 'Repasse: ' . wp_trim_words(get_the_title(), 5, '…') . $price_t,
            );
        }
        wp_reset_postdata();
    }

    // ── 6. Latest accommodations (crash/loc) ──
    $loc_q = new WP_Query(array(
        'post_type'      => 'local',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ));
    if ($loc_q->have_posts()) {
        while ($loc_q->have_posts()) {
            $loc_q->the_post();
            $items[] = array(
                'type' => 'community',
                'icon' => 'ri-home-heart-line',
                'text' => 'Novo Espaço: ' . wp_trim_words(get_the_title(), 5, '…'),
            );
        }
        wp_reset_postdata();
    }

    // ── Fallback if no real data ──
    if (empty($items)) {
        $items = array(
            array('type' => 'news',       'icon' => 'ri-newspaper-line',       'text' => 'Apollo v6 — Underground Culture Guide 2026'),
            array('type' => 'community',   'icon' => 'ri-user-community-fill',  'text' => 'Comunidade Apollo::Rio ativa'),
            array('type' => 'event',       'icon' => 'ri-calendar-line',        'text' => 'Novos eventos em breve — Rio de Janeiro'),
            array('type' => 'classified',  'icon' => 'ri-token-swap-fill',      'text' => 'Marketplace de ingressos — Em breve'),
        );
    }

    // Ensure minimum items for smooth animation (pad with repeats if needed)
    while (count($items) < 6) {
        $items = array_merge($items, $items);
    }
    $items = array_slice($items, 0, 12);

    set_transient($cache_key, $items, 5 * MINUTE_IN_SECONDS);
    return $items;
}

$marquee_items = apply_filters('apollo/home/marquee_items', apollo_build_marquee_items());

// Duplicate for seamless infinite scroll
$marquee_display = array_merge($marquee_items, $marquee_items);
?>

<div class="nh-marquee-wrap" aria-hidden="true">
    <div class="nh-marquee-content">
        <?php foreach ($marquee_display as $item) : ?>
            <div class="nh-marquee-item nh-marquee--<?php echo esc_attr($item['type']); ?>">
                <i class="<?php echo esc_attr($item['icon']); ?>"></i><?php echo esc_html($item['text']); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>