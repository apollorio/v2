<?php

/**
 * Template: Single Event — Apollo V2 Style
 *
 * Exotic modern clean minimalist event page with GSAP animations,
 * full-bleed video footer, immersive hero, glassmorphism ticket widget.
 *
 * ═══════════════════════════════════════════════════════════════════════════
 * FIELD MAPPING — How this template connects to WordPress post meta
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * CORE EVENT FIELDS (already in apollo-events Registry):
 *   get_the_title()                          → Event name (hero title, split into words)
 *   _event_start_date  (Y-m-d)              → Parsed via apollo_event_parse_date()
 *   _event_end_date    (Y-m-d)              → Optional multi-day support
 *   _event_start_time  (HH:MM)              → Shown in hero info grid
 *   _event_end_time    (HH:MM)              → Shown as "HH:MM - HH:MM (+1)"
 *   _event_loc_id      (int → loc CPT)      → Location name, address, lat/lng for map
 *   _event_dj_ids      (array of int)        → DJ lineup with avatars + time slots
 *   _event_dj_slots    (array)               → Per-DJ time slots: [{dj_id, start_time}]
 *   _event_banner      (attachment ID)       → Hero background (if no video)
 *   _event_ticket_url  (URL)                 → "COMPRAR INGRESSO" button href
 *   _event_ticket_price (string)             → Display price
 *   _event_privacy     (public|private|invite)
 *   _event_status      (scheduled|ongoing|finished|cancelled|postponed)
 *
 * TAXONOMIES:
 *   event_category  → Hero pills (e.g. "UNDERGROUND")
 *   event_type      → Hero pills (e.g. "TECHNO / INDUSTRIAL")
 *   sound           → Tag bubbles in hero (e.g. "Industrial", "Hard Groove")
 *   event_tag       → Additional tags
 *   season          → Seasonal grouping
 *
 * ═══════════════════════════════════════════════════════════════════════════
 * NEW META FIELDS (to be registered in Registry::register_meta)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * EVENT VIDEO URL — Promotional/ambient video for the event page:
 *   Meta key:   _event_video_url
 *   Type:       string (URL)
 *   Sanitize:   esc_url_raw
 *   Source:     YouTube embed URL or direct .mp4 URL
 *   Template:   Rendered as <iframe> (YouTube) or <video> (mp4) in media section
 *   Example:    https://www.youtube.com/embed/I5VCMnWbwcE
 *   Fallback:   Section hidden if empty
 *
 * EVENT IMAGE GALLERY — Up to 3 gallery images displayed in masonry grid:
 *   Meta key:   _event_gallery
 *   Type:       array (attachment IDs)
 *   Sanitize:   array_map( 'absint', $value )
 *   Source:     WordPress Media Library attachment IDs
 *   Template:   3-image grid: 2x square + 1x wide panoramic
 *   Fallback:   Gallery section hidden if empty
 *   Admin:      Comma-separated IDs or media picker
 *
 * EVENT COUPON CODE — Discount code displayed in ticket widget:
 *   Meta key:   _event_coupon_code
 *   Type:       string
 *   Sanitize:   sanitize_text_field (uppercase)
 *   Template:   Dashed box with click-to-copy in ticket widget
 *   Example:    "APOLLO10"
 *
 * EVENT LIST URL — "Lista Amiga" / guest list external link:
 *   Meta key:   _event_list_url
 *   Type:       string (URL)
 *   Sanitize:   esc_url_raw
 *   Template:   Secondary button in ticket widget
 *   Fallback:   Button hidden if empty
 *
 * ═══════════════════════════════════════════════════════════════════════════
 * CONNECTED CPTs
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * DJ (CPT: "dj") — Connected via _event_dj_ids:
 *   →  dj.post_title            → Timetable artist name
 *   →  dj.thumbnail             → Timetable avatar (44px circle, grayscale)
 *   →  dj.permalink             → Link to single-dj.php
 *   →  _event_dj_slots[dj_id]   → Start time in timetable
 *
 * LOCATION (CPT: "local") — Connected via _event_loc_id:
 *   →  loc.post_title           → Location name in hero + sidebar
 *   →  _local_address           → Full street address
 *   →  _local_city              → City name
 *   →  _local_lat / _local_lng  → OpenStreetMap embed + Google Maps directions
 *   →  loc.permalink            → Link to single-loc.php
 *
 * ═══════════════════════════════════════════════════════════════════════════
 * VIDEO FOOTER — Always uses: https://assets.apollo.rio.br/vid/luz.mp4
 * Muted autoplay loop, covers full viewport as closing visual.
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * @package Apollo\Event
 * @style   apollo-v2
 */

if (! defined('ABSPATH')) {
    exit;
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title><?php the_title(); ?> - Apollo::Rio</title>

    <!-- Apollo CDN - Mandatory for all pages -->
    <script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>

    <!-- Page styles -->
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
            font-family: 'Manrope', sans-serif;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <?php

    $post_id      = get_the_ID();
    $start_date   = get_post_meta($post_id, '_event_start_date', true);
    $end_date     = get_post_meta($post_id, '_event_end_date', true);
    $start_time   = get_post_meta($post_id, '_event_start_time', true);
    $end_time     = get_post_meta($post_id, '_event_end_time', true);
    $parsed       = apollo_event_parse_date($start_date ?: current_time('Y-m-d'));
    $loc          = apollo_event_get_loc($post_id);
    $djs          = apollo_event_get_djs($post_id);
    $dj_slots     = get_post_meta($post_id, '_event_dj_slots', true) ?: array();
    $banner       = apollo_event_get_banner($post_id);
    $ticket_url   = get_post_meta($post_id, '_event_ticket_url', true);
    $ticket_price = get_post_meta($post_id, '_event_ticket_price', true);
    $privacy      = get_post_meta($post_id, '_event_privacy', true) ?: 'public';
    $status       = get_post_meta($post_id, '_event_status', true) ?: 'scheduled';
    $is_gone      = apollo_event_is_gone($post_id);
    $sounds       = wp_get_post_terms($post_id, APOLLO_EVENT_TAX_SOUND, array('fields' => 'names'));
    $categories   = wp_get_post_terms($post_id, APOLLO_EVENT_TAX_CATEGORY, array('fields' => 'names'));
    $types        = wp_get_post_terms($post_id, APOLLO_EVENT_TAX_TYPE, array('fields' => 'names'));

    /* ── NEW META (apollo-v2 extensions) ────────────────────────────────── */
    $video_url   = get_post_meta($post_id, '_event_video_url', true);
    $gallery_ids = get_post_meta($post_id, '_event_gallery', true) ?: array();
    $coupon_code = get_post_meta($post_id, '_event_coupon_code', true);
    $list_url    = get_post_meta($post_id, '_event_list_url', true);

    /* ── Build gallery URLs from attachment IDs ─────────────────────────── */
    $gallery_images = array();
    if (! empty($gallery_ids) && is_array($gallery_ids)) {
        foreach ($gallery_ids as $att_id) {
            $url = wp_get_attachment_image_url((int) $att_id, 'large');
            if ($url) {
                $gallery_images[] = $url;
            }
        }
    }

    /* ── Split title into words for reveal animation ────────────────────── */
    $title_raw   = get_the_title();
    $title_words = preg_split('/\s+/', $title_raw);

    $gone_class = $is_gone ? ' apollo-v2-gone' : '';

    /* ── Enqueue style ──────────────────────────────────────────────────── */
    $style_url = APOLLO_EVENT_URL . 'styles/apollo-v2/style.css';
    wp_enqueue_style('apollo-events-v2', $style_url, array('apollo-events'), APOLLO_EVENT_VERSION);

    /* ── Map ────────────────────────────────────────────────────────────── */
    if ($loc && ! empty($loc['lat']) && apollo_event_option('enable_osm_map', true)) {
        wp_enqueue_style('leaflet');
        wp_enqueue_script('leaflet');
        wp_enqueue_script('apollo-events-map');
    }
    ?>

    <!-- ═══════════════════════════════════════════════════════════════════
	APOLLO V2 — SINGLE EVENT
	Style: Exotic Modern Clean Minimalist
	═══════════════════════════════════════════════════════════════════ -->

    <div class="av2-event<?php echo esc_attr($gone_class); ?>"
        data-event-id="<?php echo esc_attr($post_id); ?>"
        data-status="<?php echo esc_attr($status); ?>">

        <!-- ═══ PAGE LOADER CURTAIN ═══ -->
        <div class="av2-loader"></div>

        <!-- ═══════════════════════════════════════════════════════════════
		HERO SECTION
		─ Background marquee text (event title, scrolls on scroll)
		─ Category & type pills
		─ Massive title with word-by-word reveal
		─ Info grid: date, hours, location, sound tags
		═══════════════════════════════════════════════════════════════ -->
        <div class="av2-container">
            <section class="av2-hero">

                <!-- Background Animated Text → get_the_title() -->
                <div class="av2-hero__marquee"><?php echo esc_html(strtoupper($title_raw)); ?></div>

                <!-- ─── Taxonomy Pills ──────────────────────────────────
				.pill.status → event_category taxonomy
				.pill.cat    → event_type taxonomy
				─────────────────────────────────────────────────── -->
                <div class="av2-hero__meta">
                    <?php if ('scheduled' !== $status) : ?>
                        <div class="av2-pill av2-pill--status av2-pill--<?php echo esc_attr($status); ?>">
                            <?php echo esc_html(strtoupper($status)); ?>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($categories as $cat) : ?>
                        <div class="av2-pill av2-pill--status"><?php echo esc_html(strtoupper($cat)); ?></div>
                    <?php endforeach; ?>

                    <?php if (! empty($types)) : ?>
                        <div class="av2-pill av2-pill--cat">
                            <?php echo esc_html(strtoupper(implode(' / ', $types))); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ('public' !== $privacy) : ?>
                        <div class="av2-pill av2-pill--cat">
                            🔒 <?php echo 'private' === $privacy ? esc_html__('PRIVADO', 'apollo-events') : esc_html__('CONVIDADOS', 'apollo-events'); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ─── Hero Title ──────────────────────────────────────
				Each word is wrapped for staggered reveal animation.
				Source: get_the_title() split by whitespace.
				─────────────────────────────────────────────────── -->
                <div class="av2-hero__title-wrap">
                    <h1 class="av2-hero__title">
                        <?php foreach ($title_words as $i => $word) : ?>
                            <span class="av2-title-line">
                                <span class="av2-title-word"><?php echo esc_html(strtoupper($word)); ?></span>
                            </span>
                        <?php endforeach; ?>
                    </h1>
                </div>

                <?php if ($is_gone) : ?>
                    <div class="av2-gone-notice">
                        <?php esc_html_e('Este evento já encerrou.', 'apollo-events'); ?>
                    </div>
                <?php endif; ?>

                <!-- ─── Info Grid ───────────────────────────────────────
				Date  → _event_start_date parsed to "DD MMM"
				Hours → _event_start_time + _event_end_time
				Loc   → _event_loc_id → loc.post_title
				Sound → sound taxonomy terms as tag bubbles
				─────────────────────────────────────────────────── -->
                <div class="av2-hero__info">
                    <div class="av2-info-block">
                        <span class="av2-info-label"><?php esc_html_e('Data', 'apollo-events'); ?></span>
                        <span class="av2-info-value av2-info-value--date">
                            <?php echo esc_html($parsed['day'] . ' ' . strtoupper($parsed['month_pt'])); ?>
                        </span>
                    </div>

                    <?php if ($start_time) : ?>
                        <div class="av2-info-block">
                            <span class="av2-info-label"><?php esc_html_e('Horário', 'apollo-events'); ?></span>
                            <span class="av2-info-value">
                                <?php
                                echo esc_html($start_time);
                                if ($end_time) {
                                    echo ' - ' . esc_html($end_time);
                                    // Show (+1) if end time is earlier than start (next day)
                                    if ($end_time < $start_time) {
                                        echo ' (+1)';
                                    }
                                }
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if ($loc) : ?>
                        <div class="av2-info-block">
                            <span class="av2-info-label"><?php esc_html_e('Local', 'apollo-events'); ?></span>
                            <span class="av2-info-value">
                                <a href="<?php echo esc_url($loc['permalink'] ?? '#'); ?>"><?php echo esc_html($loc['title']); ?></a>
                                <?php
                                if (! empty($loc['city'])) {
                                    echo ', ' . esc_html($loc['city']);
                                }
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if (! empty($sounds) && ! is_wp_error($sounds)) : ?>
                        <div class="av2-info-block">
                            <span class="av2-info-label"><?php esc_html_e('Sounds', 'apollo-events'); ?></span>
                            <div class="av2-hero__tags">
                                <?php foreach ($sounds as $sound) : ?>
                                    <span class="av2-tag"><?php echo esc_html($sound); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </section>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════
		MAIN CONTENT LAYOUT — 2-Column (content + sidebar)
		═══════════════════════════════════════════════════════════════ -->
        <div class="av2-container av2-layout">

            <!-- ═══ LEFT COLUMN: Content ═══ -->
            <div class="av2-content">

                <!-- ─── Event Video ─────────────────────────────────────
				Source: _event_video_url (new meta field)
				Supports: YouTube embed URLs → <iframe>
							Direct .mp4 URLs  → <video>
				Fallback: Section hidden if _event_video_url is empty
				─────────────────────────────────────────────────── -->
                <?php if ($video_url) : ?>
                    <div class="av2-media av2-reveal">
                        <div class="av2-video-frame">
                            <?php if (str_contains($video_url, 'youtube.com') || str_contains($video_url, 'youtu.be')) : ?>
                                <iframe
                                    src="<?php echo esc_url($video_url); ?>?autoplay=1&mute=1&loop=1&playlist=<?php echo esc_attr(basename(parse_url($video_url, PHP_URL_PATH))); ?>&controls=0&showinfo=0&modestbranding=1&rel=0&iv_load_policy=3&playsinline=1"
                                    allow="autoplay; encrypted-media"
                                    allowfullscreen
                                    loading="lazy">
                                </iframe>
                            <?php else : ?>
                                <video autoplay muted loop playsinline>
                                    <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                                </video>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ─── Event Description (Bio) ─────────────────────────
				Source: post_content via the_content()
				Rendered with WP content filters (shortcodes, etc.)
				─────────────────────────────────────────────────── -->
                <div class="av2-bio av2-reveal">
                    <?php the_content(); ?>
                </div>

                <!-- ─── Timetable / Line-up ─────────────────────────────
				Source: _event_dj_ids → array of DJ CPT IDs
						_event_dj_slots → [{dj_id: int, start_time: "HH:MM"}]
				Each DJ row links to: get_permalink( dj_id ) → single-dj.php
				Avatar: get_the_post_thumbnail_url( dj_id, 'thumbnail' )
				Fallback: Section hidden if no DJs assigned
				─────────────────────────────────────────────────── -->
                <?php if (! empty($djs)) : ?>
                    <div class="av2-timetable av2-reveal">
                        <h3 class="av2-section-title"><?php esc_html_e('Timetable', 'apollo-events'); ?></h3>
                        <div class="av2-timetable__list">
                            <?php
                            foreach ($djs as $dj) :
                                // Find slot time for this DJ
                                $slot_time = '';
                                if (! empty($dj_slots) && is_array($dj_slots)) {
                                    foreach ($dj_slots as $slot) {
                                        if (isset($slot['dj_id']) && (int) $slot['dj_id'] === $dj['id']) {
                                            $slot_time = $slot['start_time'] ?? '';
                                            break;
                                        }
                                    }
                                }
                            ?>
                                <a href="<?php echo esc_url(get_permalink($dj['id'])); ?>" class="av2-tt-item">
                                    <span class="av2-tt-time"><?php echo esc_html($slot_time); ?></span>
                                    <div class="av2-tt-artist">
                                        <?php if (! empty($dj['image'])) : ?>
                                            <img src="<?php echo esc_url($dj['image']); ?>"
                                                class="av2-tt-avatar"
                                                alt="<?php echo esc_attr($dj['title']); ?>"
                                                loading="lazy">
                                        <?php endif; ?>
                                        <span class="av2-tt-name"><?php echo esc_html($dj['title']); ?></span>
                                    </div>
                                    <i class="ri-arrow-right-line" style="opacity:0.3"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ─── Image Gallery ───────────────────────────────────
				Source: _event_gallery (new meta field)
				Type:   Array of WordPress attachment IDs
				Layout: 3 images → 2x square (1:1) + 1x wide (2:1)
				Effect: Grayscale → color on hover, parallax on scroll
				Admin:  Comma-separated attachment IDs: "101,102,103"
				Fallback: Section hidden if _event_gallery is empty
				─────────────────────────────────────────────────── -->
                <?php if (! empty($gallery_images)) : ?>
                    <div class="av2-gallery">
                        <?php foreach ($gallery_images as $idx => $img_url) : ?>
                            <div class="av2-gallery__item<?php echo $idx === 2 ? ' av2-gallery__item--wide' : ''; ?>">
                                <img src="<?php echo esc_url($img_url); ?>"
                                    class="av2-gallery__img av2-reveal-img"
                                    alt="<?php echo esc_attr($title_raw . ' — ' . ($idx + 1)); ?>"
                                    loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php
                /**
                 * Hook: apollo_event_single_after_content
                 * Used by integrations (Wow reactions, Fav button, etc.)
                 */
                do_action('apollo_event_single_after_content', $post_id);
                ?>

            </div>

            <!-- ═══ RIGHT COLUMN: Action Sidebar (sticky) ═══ -->
            <aside class="av2-sidebar">

                <!-- ─── Ticket Widget ───────────────────────────────────
				COMPRAR INGRESSO → _event_ticket_url
				Price display    → _event_ticket_price
				Coupon           → _event_coupon_code (new meta)
				LISTA AMIGA      → _event_list_url (new meta)
				─────────────────────────────────────────────────── -->
                <div class="av2-ticket-widget">
                    <span class="av2-ticket-header"><?php esc_html_e('Acessos Oficiais', 'apollo-events'); ?></span>

                    <?php if ($ticket_price) : ?>
                        <div class="av2-ticket-price"><?php echo esc_html($ticket_price); ?></div>
                    <?php endif; ?>

                    <?php if ($ticket_url && ! $is_gone) : ?>
                        <a href="<?php echo esc_url($ticket_url); ?>" target="_blank" rel="noopener" class="av2-btn av2-btn--primary">
                            <span><?php esc_html_e('COMPRAR INGRESSO', 'apollo-events'); ?></span>
                        </a>
                    <?php elseif ($is_gone) : ?>
                        <div class="av2-btn av2-btn--disabled">
                            <span><?php esc_html_e('ENCERRADO', 'apollo-events'); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($coupon_code) : ?>
                        <div class="av2-coupon" id="av2-coupon" title="<?php esc_attr_e('Clique para copiar', 'apollo-events'); ?>">
                            <?php esc_html_e('Use our official coupon:', 'apollo-events'); ?>
                            <span class="av2-coupon__code"><?php echo esc_html($coupon_code); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($list_url && ! $is_gone) : ?>
                        <a href="<?php echo esc_url($list_url); ?>" target="_blank" rel="noopener" class="av2-btn av2-btn--secondary">
                            <span><?php esc_html_e('LISTA AMIGA', 'apollo-events'); ?></span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- ─── Location Mini ───────────────────────────────────
				Source: _event_loc_id → local CPT
				Title:    loc.post_title
				Address:  _local_address
				Map:      OpenStreetMap via _local_lat + _local_lng
				Directions: Google Maps form with _local_lat/_local_lng
				Link:     loc.permalink → single-loc.php
				─────────────────────────────────────────────────── -->
                <?php if ($loc) : ?>
                    <div class="av2-location">
                        <h3 class="av2-location__name">
                            <a href="<?php echo esc_url($loc['permalink'] ?? '#'); ?>">
                                <?php echo esc_html($loc['title']); ?>
                            </a>
                        </h3>

                        <?php if (! empty($loc['address'])) : ?>
                            <p class="av2-location__address"><?php echo esc_html($loc['address']); ?></p>
                        <?php endif; ?>

                        <?php if (! empty($loc['lat']) && ! empty($loc['lng']) && apollo_event_option('enable_osm_map', true)) : ?>
                            <div class="av2-location__map"
                                id="a-eve-single-map"
                                data-lat="<?php echo esc_attr($loc['lat']); ?>"
                                data-lng="<?php echo esc_attr($loc['lng']); ?>"
                                data-title="<?php echo esc_attr($loc['title']); ?>">
                                <iframe
                                    src="https://www.openstreetmap.org/export/embed.html?bbox=
							<?php
                            $lng = (float) $loc['lng'];
                            $lat = (float) $loc['lat'];
                            echo esc_attr(($lng - 0.025) . ',' . ($lat - 0.025) . ',' . ($lng + 0.025) . ',' . ($lat + 0.025));
                            ?>
							&layer=mapnik&marker=<?php echo esc_attr($lat . ',' . $lng); ?>"
                                    width="100%" height="100%" frameborder="0"
                                    loading="lazy">
                                </iframe>
                            </div>

                            <form class="av2-directions" action="https://www.google.com/maps/dir/" target="_blank">
                                <input type="text" name="saddr" placeholder="<?php esc_attr_e('De onde você vem?', 'apollo-events'); ?>" class="av2-directions__input">
                                <input type="hidden" name="daddr" value="<?php echo esc_attr($loc['lat'] . ',' . $loc['lng']); ?>">
                                <button type="submit" class="av2-directions__go"><i class="ri-guide-line"></i></button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php
                /**
                 * Hook: apollo_event_single_sidebar
                 * Used by integrations to add sidebar widgets
                 */
                do_action('apollo_event_single_sidebar', $post_id);
                ?>

            </aside>

        </div>

        <?php do_action('apollo_event_single_after', $post_id); ?>

        <!-- ═══════════════════════════════════════════════════════════════
		TICKET RESALE — apollo-adverts (classifieds linked to event)
		Same query as base template for consistency
		═══════════════════════════════════════════════════════════════ -->
        <?php
        $resale_args  = array(
            'post_type'      => 'classified',
            'post_status'    => 'publish',
            'posts_per_page' => 6,
            'meta_query'     => array(
                array(
                    'key'   => '_classified_event_id',
                    'value' => $post_id,
                    'type'  => 'NUMERIC',
                ),
            ),
        );
        $resale_query = new \WP_Query($resale_args);

        if ($resale_query->have_posts()) :
        ?>
            <div class="av2-container">
                <div class="a-eve-single__section a-eve-resale">
                    <div class="a-eve-resale__header">
                        <h3 class="a-eve-resale__title"><?php esc_html_e('Ingressos à venda por usuários', 'apollo-events'); ?></h3>
                        <span class="a-eve-resale__count"><?php echo esc_html($resale_query->found_posts); ?> <?php esc_html_e('ofertas', 'apollo-events'); ?></span>
                    </div>
                    <div class="a-eve-resale__grid">
                        <?php
                        while ($resale_query->have_posts()) :
                            $resale_query->the_post();
                            $r_id     = get_the_ID();
                            $r_price  = get_post_meta($r_id, '_classified_price', true);
                            $r_curr   = get_post_meta($r_id, '_classified_currency', true) ?: 'BRL';
                            $r_neg    = get_post_meta($r_id, '_classified_negotiable', true);
                            $r_avatar = get_avatar_url(get_the_author_meta('ID'), array('size' => 80));
                            $r_date   = get_the_time('Y-m-d H:i:s');
                        ?>
                            <a href="<?php the_permalink(); ?>" class="a-eve-resale__card">
                                <div class="a-eve-resale__card-top">
                                    <img src="<?php echo esc_url($r_avatar); ?>" alt="<?php the_author(); ?>" class="a-eve-resale__avatar" />
                                    <div class="a-eve-resale__seller">
                                        <span class="a-eve-resale__seller-name"><?php the_author(); ?></span>
                                        <span class="a-eve-resale__seller-time"><?php echo wp_kses_post(apollo_time_ago_html($r_date)); ?></span>
                                    </div>
                                </div>
                                <h4 class="a-eve-resale__card-title"><?php the_title(); ?></h4>
                                <div class="a-eve-resale__card-bottom">
                                    <?php if ($r_price) : ?>
                                        <span class="a-eve-resale__price"><?php echo esc_html($r_curr . ' ' . number_format((float) $r_price, 0, ',', '.')); ?></span>
                                    <?php endif; ?>
                                    <?php if ($r_neg) : ?>
                                        <span class="a-eve-resale__neg"><?php esc_html_e('Negociável', 'apollo-events'); ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- ═══════════════════════════════════════════════════════════════
		DEPOIMENTOS — Timeline (comments)
		═══════════════════════════════════════════════════════════════ -->
        <?php if (comments_open($post_id) || get_comments_number($post_id)) : ?>
            <div class="av2-container">
                <div class="a-eve-single__section a-eve-depoimentos">
                    <div class="a-eve-depo__header">
                        <h3 class="a-eve-depo__title"><?php esc_html_e('Depoimentos', 'apollo-events'); ?></h3>
                        <span class="a-eve-depo__count"><?php echo esc_html(get_comments_number($post_id)); ?></span>
                    </div>
                    <?php
                    $depoimentos = get_comments(
                        array(
                            'post_id' => $post_id,
                            'status'  => 'approve',
                            'number'  => 10,
                            'orderby' => 'comment_date',
                            'order'   => 'DESC',
                        )
                    );
                    ?>
                    <?php if (! empty($depoimentos)) : ?>
                        <div class="a-eve-depo__timeline">
                            <?php
                            foreach ($depoimentos as $idx => $depo) :
                                $d_avatar = get_avatar_url($depo->comment_author_email, array('size' => 80));
                                $d_time   = $depo->comment_date;
                            ?>
                                <div class="a-eve-depo__item<?php echo $idx === 0 ? ' is-latest' : ''; ?>">
                                    <div class="a-eve-depo__line">
                                        <span class="a-eve-depo__dot"></span>
                                        <?php if ($idx < count($depoimentos) - 1) : ?>
                                            <span class="a-eve-depo__connector"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="a-eve-depo__card">
                                        <div class="a-eve-depo__card-header">
                                            <img src="<?php echo esc_url($d_avatar); ?>" alt="" class="a-eve-depo__avatar" />
                                            <div class="a-eve-depo__meta">
                                                <span class="a-eve-depo__author"><?php echo esc_html($depo->comment_author); ?></span>
                                                <span class="a-eve-depo__time"><?php echo wp_kses_post(apollo_time_ago_html($d_time)); ?></span>
                                            </div>
                                        </div>
                                        <p class="a-eve-depo__text"><?php echo wp_kses_post($depo->comment_content); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (comments_open($post_id) && is_user_logged_in()) : ?>
                        <div class="a-eve-depo__form-wrap">
                            <?php
                            comment_form(
                                array(
                                    'title_reply'          => '',
                                    'comment_notes_before' => '',
                                    'comment_notes_after'  => '',
                                    'label_submit'         => __('Enviar Depoimento', 'apollo-events'),
                                    'comment_field'        => '<div class="a-eve-depo__input-wrap"><textarea name="comment" class="a-eve-depo__input" placeholder="' . esc_attr__('Compartilhe sua experiência...', 'apollo-events') . '" rows="3" required></textarea></div>',
                                    'class_form'           => 'a-eve-depo__form',
                                    'class_submit'         => 'a-eve-depo__submit',
                                ),
                                $post_id
                            );
                            ?>
                        </div>
                    <?php elseif (! is_user_logged_in() && comments_open($post_id)) : ?>
                        <p class="a-eve-depo__login-cta">
                            <a href="<?php echo esc_url(wp_login_url(get_permalink($post_id))); ?>">
                                <?php esc_html_e('Faça login para deixar um depoimento', 'apollo-events'); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ═══════════════════════════════════════════════════════════════
		FOOTER VISUAL — Video (replaces static image)
		Source: https://assets.apollo.rio.br/vid/luz.mp4
		Always muted, autoplay, infinite loop
		Overlaid with event closing text
		═══════════════════════════════════════════════════════════════ -->
        <div class="av2-footer-visual">
            <video class="av2-footer-video"
                autoplay muted loop playsinline
                poster="<?php echo esc_url($banner); ?>">
                <source src="https://assets.apollo.rio.br/vid/luz.mp4" type="video/mp4">
            </video>
            <div class="av2-footer-text">
                <h2><?php esc_html_e('See you on the floor', 'apollo-events'); ?></h2>
            </div>
        </div>

    </div>

    <!-- ═══════════════════════════════════════════════════════════════════
	GSAP Animations — Load from CDN
	═══════════════════════════════════════════════════════════════════ -->
    <!-- GSAP already loaded by CDN core.js (v3.14.2) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof gsap === 'undefined') return;
            gsap.registerPlugin(ScrollTrigger);

            const tl = gsap.timeline();

            // 1. Lift loader curtain
            tl.to('.av2-loader', {
                scaleY: 0,
                duration: 1,
                ease: 'power4.inOut'
            });

            // 2. Reveal title words staggered
            tl.to('.av2-title-word', {
                y: '0%',
                duration: 1.2,
                stagger: 0.1,
                ease: 'power3.out'
            }, '-=0.5');

            // 3. Reveal hero meta & info
            tl.from('.av2-hero__meta, .av2-hero__info', {
                y: 40,
                opacity: 0,
                duration: 0.8,
                stagger: 0.1,
                ease: 'power2.out'
            }, '-=0.8');

            // Background text parallax
            gsap.to('.av2-hero__marquee', {
                xPercent: -20,
                ease: 'none',
                scrollTrigger: {
                    trigger: 'body',
                    start: 'top top',
                    end: 'bottom top',
                    scrub: 1
                }
            });

            // Scroll reveal for content sections
            gsap.utils.toArray('.av2-reveal').forEach(function(elem) {
                gsap.from(elem, {
                    scrollTrigger: {
                        trigger: elem,
                        start: 'top 85%'
                    },
                    y: 50,
                    opacity: 0,
                    duration: 1,
                    ease: 'power3.out'
                });
            });

            // Gallery image parallax
            gsap.utils.toArray('.av2-gallery__img').forEach(function(img) {
                gsap.fromTo(img, {
                    scale: 1.2
                }, {
                    scale: 1,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: img,
                        start: 'top bottom',
                        end: 'bottom top',
                        scrub: true
                    }
                });
            });

            // Footer video parallax
            gsap.to('.av2-footer-video', {
                yPercent: 20,
                ease: 'none',
                scrollTrigger: {
                    trigger: '.av2-footer-visual',
                    start: 'top bottom',
                    end: 'bottom top',
                    scrub: true
                }
            });

            // Coupon click-to-copy
            var couponEl = document.getElementById('av2-coupon');
            if (couponEl) {
                couponEl.addEventListener('click', function() {
                    var code = couponEl.querySelector('.av2-coupon__code');
                    if (code && navigator.clipboard) {
                        navigator.clipboard.writeText(code.textContent.trim());
                        couponEl.classList.add('av2-coupon--copied');
                        setTimeout(function() {
                            couponEl.classList.remove('av2-coupon--copied');
                        }, 2000);
                    }
                });
            }
        });
    </script>

    <?php if (is_user_logged_in()) : ?>
        <!-- Apollo Navbar -->
        <?php include plugin_dir_path(__DIR__) . '../../../apollo-templates/templates/template-parts/navbar.php'; ?>
    <?php endif; ?>

</body>

</html>