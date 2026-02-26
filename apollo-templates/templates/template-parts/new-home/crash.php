<?php

/**
 * New Home — Crash / Acomoda::Rio Section
 *
 * WP_Query loop on `local` CPT (apollo-loc), filtered by
 * local_type taxonomy = "crash" or "hospedagem".
 * Host identity gated behind login.
 * Falls back to "Em breve.." if no locations.
 *
 * @package Apollo\Templates
 * @since   6.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

$is_logged_in = is_user_logged_in();
$login_url    = home_url('/acesso');

$crash_query = new WP_Query(
    array(
        'post_type'      => 'local',
        'posts_per_page' => 8,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => array(
            array(
                'taxonomy' => 'local_type',
                'field'    => 'slug',
                'terms'    => array('crash', 'hospedagem', 'acomoda'),
                'operator' => 'IN',
            ),
        ),
    )
);

// If taxonomy filter returns no results, try without filter
if (! $crash_query->have_posts()) {
    $crash_query = new WP_Query(
        array(
            'post_type'      => 'local',
            'posts_per_page' => 8,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );
}
?>

<section class="section" id="crash" aria-labelledby="crash-title">
    <div class="container">
        <div class="nh-section-head ai">
            <h2 id="crash-title">Acomoda::Rio</h2>
            <a href="<?php echo esc_url(home_url('/acomoda')); ?>"
                aria-label="<?php esc_attr_e('Ver todos os espaços', 'apollo-templates'); ?>">Ver Todos →</a>
        </div>

        <div class="nh-crash-grid">

            <?php if ($crash_query->have_posts()) : ?>
                <?php
                $card_count = 0;
                while ($crash_query->have_posts()) :
                    $crash_query->the_post();
                    ++$card_count;
                    $local_id    = get_the_ID();
                    $thumb_url   = get_the_post_thumbnail_url($local_id, 'medium');
                    $placeholder = 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600&q=75';
                    $image       = $thumb_url ? $thumb_url : $placeholder;
                    $permalink   = $is_logged_in ? get_permalink() : $login_url;

                    // Price
                    $price_range   = get_post_meta($local_id, '_local_price_range', true);
                    $price_display = $price_range ? $price_range : '—';

                    // Area / neighborhood
                    $areas = wp_get_post_terms($local_id, 'local_area', array('fields' => 'names'));
                    $area  = ! is_wp_error($areas) && ! empty($areas) ? $areas[0] : '';

                    // Type
                    $types = wp_get_post_terms($local_id, 'local_type', array('fields' => 'names'));
                    $type  = ! is_wp_error($types) && ! empty($types) ? $types[0] : '';

                    $meta_text = $area;
                    if ($type) {
                        $meta_text .= $area ? ' · ' . $type : $type;
                    }

                    // Insert CTA card after 3rd card
                    if ($card_count === 4) :
                ?>
                        <a href="<?php echo esc_url($is_logged_in ? home_url('/criar-anuncio') : $login_url); ?>"
                            class="nh-crash-card reveal-up nh-crash-card--cta ai"
                            aria-label="<?php esc_attr_e('Anuncie seu espaço', 'apollo-templates'); ?>" translate="no">
                            <span class="nh-cta-title" translate="no">Host Your Spot</span>
                            <span class="nh-cta-sub"
                                translate="no"><?php esc_html_e('Anuncie seu espaço', 'apollo-templates'); ?></span>
                            <span class="nh-cta-ptbr" translate="no">Join the network</span>
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo esc_url($permalink); ?>" class=" reveal-up nh-crash-card ai">
                        <div class="nh-crash-img-wrap">
                            <img src="<?php echo esc_url($image); ?>" class="nh-crash-img"
                                alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy" />
                            <?php if (! $is_logged_in) : ?>
                                <div class="nh-crash-login-hint" aria-hidden="true"><i class="ri-lock-2-line"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="nh-crash-info">
                            <div class="nh-crash-info-header">
                                <h3><?php the_title(); ?></h3>
                                <span class="nh-crash-price"><?php echo esc_html($price_display); ?></span>
                            </div>
                            <?php if ($meta_text) : ?>
                                <p class="nh-crash-meta"><?php echo esc_html($meta_text); ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>

                <?php
                // If we had fewer than 4 cards, the CTA wasn't inserted yet
                if ($card_count < 4) :
                ?>
                    <a href="<?php echo esc_url($is_logged_in ? home_url('/criar-anuncio') : $login_url); ?>"
                        class="nh-crash-card nh-crash-card--cta ai"
                        aria-label="<?php esc_attr_e('Anuncie seu espaço', 'apollo-templates'); ?>" translate="no">
                        <span class="nh-cta-title" translate="no">Host Your Spot</span>
                        <span class="nh-cta-sub"
                            translate="no"><?php esc_html_e('Anuncie seu espaço', 'apollo-templates'); ?></span>
                        <span class="nh-cta-ptbr" translate="no">Join the network</span>
                    </a>
                <?php endif; ?>

            <?php else : ?>
                <!-- Em breve — no crash locations published yet -->
                <div class="nh-empty-state ai" style="grid-column:1/-1;">
                    <i class="ri-home-heart-line" aria-hidden="true"></i>
                    <p>Em breve..</p>
                    <span class="nh-empty-sub">A rede Acomoda::Rio está sendo construída. Em breve você poderá hospedar ou
                        encontrar um lugar.</span>
                </div>

                <!-- CTA always shown -->
                <a href="<?php echo esc_url($is_logged_in ? home_url('/criar-anuncio') : $login_url); ?>"
                    class="nh-crash-card nh-crash-card--cta ai"
                    aria-label="<?php esc_attr_e('Anuncie seu espaço', 'apollo-templates'); ?>" translate="no">
                    <span class="nh-cta-title" translate="no">Host Your Spot</span>
                    <span class="nh-cta-sub"
                        translate="no"><?php esc_html_e('Anuncie seu espaço', 'apollo-templates'); ?></span>
                    <span class="nh-cta-ptbr" translate="no">Join the network</span>
                </a>
            <?php endif; ?>

        </div><!-- /.nh-crash-grid -->
    </div>
</section>