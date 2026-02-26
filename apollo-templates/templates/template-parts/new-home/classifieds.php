<?php

/**
 * New Home — Classifieds / Resale Section
 *
 * WP_Query loop on `classified` CPT (from apollo-adverts).
 * Seller identity hidden for non-logged users (●●●).
 * Falls back to "Em breve.." if no classifieds published.
 *
 * @package Apollo\Templates
 * @since   6.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

$is_logged_in = is_user_logged_in();
$login_url    = home_url('/acesso');

$classifieds_query = new WP_Query(
    array(
        'post_type'      => 'classified',
        'posts_per_page' => 5,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    )
);
?>

<section class="section" id="resell" aria-labelledby="resell-title">
    <div class="container">
        <div class="nh-section-head ai">
            <h2 id="resell-title">Classificados</h2>
            <a href="<?php echo esc_url($is_logged_in ? home_url('/criar-anuncio') : $login_url); ?>"
                aria-label="<?php esc_attr_e('Anunciar ingresso', 'apollo-templates'); ?>"
                style="font-family:var(--ff-mono);font-size:0.72rem;color:var(--muted);letter-spacing:0.02em;transition:color .25s;">
                Sell Ticket +
            </a>
        </div>
        <p class="nh-resale-intro ai">Marketplace peer-to-peer. Identidade do vendedor visível apenas para membros.</p>

        <div class="a-v2-list-wrap" role="list">

            <?php if ($classifieds_query->have_posts()) : ?>
                <?php
                while ($classifieds_query->have_posts()) :
                    $classifieds_query->the_post();
                ?>
                    <?php
                    $classified_id = get_the_ID();
                    $price         = get_post_meta($classified_id, '_classified_price', true);
                    $is_verified   = get_post_meta($classified_id, '_classified_negotiable', true);
                    $permalink     = $is_logged_in ? get_permalink() : $login_url;

                    // Format post time
                    $post_time = get_the_time('H:i');

                    // Price display
                    $price_display = $price ? 'R$ ' . number_format_i18n((float) $price, 0) : '—';

                    // Verified badge
                    $badge_class = $is_verified ? 'a-v2-list-B__badge a-v2-list-B__badge--verified' : 'a-v2-list-B__badge';
                    $badge_text  = $is_verified ? 'Verificado' : 'Não verificado';
                    ?>
                    <a href="<?php echo esc_url($permalink); ?>" class="a-v2-list-B reveal-up" role="listitem">
                        <span class="a-v2-list-B__time"
                            aria-label="<?php echo esc_attr($post_time); ?>"><?php echo esc_html($post_time); ?></span>
                        <div class="a-v2-list-B__info">
                            <h3 class="a-v2-list-B__title"><?php the_title(); ?></h3>
                            <?php if (! $is_logged_in) : ?>
                                <span class="a-v2-list-B__seller--hidden">●&ensp;●&ensp;●</span>
                            <?php else : ?>
                                <span class="a-v2-list-B__seller"><?php echo esc_html(get_the_author()); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="a-v2-list-B__price-wrap">
                            <span class="a-v2-list-B__price"><?php echo esc_html($price_display); ?></span>
                            <span class="<?php echo esc_attr($badge_class); ?>"><?php echo esc_html($badge_text); ?></span>
                        </div>
                    </a>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <!-- Em breve — no classifieds published yet -->
                <div class="nh-empty-state ai" style="padding:40px 28px;text-align:center">
                    <i class="ri-token-swap-line" aria-hidden="true"></i>
                    <p>Em breve..</p>
                    <span class="nh-empty-sub">O marketplace abrirá em breve com ingressos e itens da comunidade.</span>
                </div>
            <?php endif; ?>

            <!-- ALL Tickets Re-sell CTA (always shown) -->
            <a href="<?php echo esc_url(home_url('/classificados')); ?>" class="a-v2-list-B a-v2-list-B--cta"
                role="listitem" aria-label="<?php esc_attr_e('Ver todos os ingressos', 'apollo-templates'); ?>">
                <div>
                    <p class="nh-rall-label">Marketplace</p>
                    <p class="nh-rall-title">ALL Tickets Re-sell</p>
                </div>
                <i class="ri-arrow-right-up-line nh-rall-icon" aria-hidden="true"></i>
            </a>

        </div><!-- /.a-v2-list-wrap -->

        <aside class="nh-disclaimer ai" role="note">
            <div class="nh-disclaimer-accent" aria-hidden="true"></div>
            <div class="nh-disclaimer-body">
                <div class="nh-disclaimer-header">
                    <i class="ri-shield-check-fill" aria-hidden="true"></i>
                    <h4><?php esc_html_e('Segurança em Primeiro Lugar', 'apollo-templates'); ?></h4>
                </div>
                <p><?php esc_html_e('Apollo é uma ponte de conexão. Não processamos pagamentos. Sempre verifique a reputação do vendedor. Encontre-se em local público.', 'apollo-templates'); ?>
                </p>
            </div>
        </aside>
    </div>
</section>