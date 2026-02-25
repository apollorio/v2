<?php

/**
 * Template Name: Classificados (Marketplace)
 *
 * Public marketplace page for classifieds/ads.
 * Modern cards with apollo-wow (reactions), apollo-comment, apollo-chat, apollo-fav integration.
 * Uses Apollo v1 card design pattern.
 *
 * URL: /classificados
 *
 * @package Apollo\Templates
 */

if (! defined('ABSPATH')) {
    exit;
}

// Get current user (allow public access, login only required for interactions)
$user_id      = get_current_user_id();
$is_logged_in = $user_id > 0;

// Get filters from query params
$intent_filter = isset($_GET['intent']) ? sanitize_text_field($_GET['intent']) : '';
$domain_filter = isset($_GET['domain']) ? sanitize_text_field($_GET['domain']) : '';
$search        = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$paged         = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

// Build query args
$query_args = array(
    'post_type'      => 'apollo_classified',
    'posts_per_page' => 20,
    'paged'          => $paged,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
);

// Tax query
$tax_query = array();
if ($intent_filter) {
    $tax_query[] = array(
        'taxonomy' => 'classified_intent',
        'field'    => 'slug',
        'terms'    => $intent_filter,
    );
}
if ($domain_filter) {
    $tax_query[] = array(
        'taxonomy' => 'classified_domain',
        'field'    => 'slug',
        'terms'    => $domain_filter,
    );
}
if (! empty($tax_query)) {
    $query_args['tax_query'] = $tax_query;
}

// Search
if ($search) {
    $query_args['s'] = $search;
}

$classifieds = new WP_Query($query_args);

// Get taxonomy terms for filters
$intents = get_terms(
    array(
        'taxonomy'   => 'classified_intent',
        'hide_empty' => true,
    )
);
$domains = get_terms(
    array(
        'taxonomy'   => 'classified_domain',
        'hide_empty' => true,
    )
);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes" />
    <title>Classificados — Apollo</title>
    <script src="https://cdn.apollo.rio.br/v1.0.0/core.min.js?v=1.0.0" fetchpriority="high"></script>

    <!-- Navbar v2 CSS/JS -->
    <?php if (defined('APOLLO_TEMPLATES_URL') && defined('APOLLO_TEMPLATES_VERSION')) : ?>
        <link rel="stylesheet" href="<?php echo esc_url(APOLLO_TEMPLATES_URL . 'assets/css/navbar.v2.css'); ?>?v=<?php echo esc_attr(APOLLO_TEMPLATES_VERSION); ?>">
        <script src="<?php echo esc_url(APOLLO_TEMPLATES_URL . 'assets/js/navbar.v2.js'); ?>?v=<?php echo esc_attr(APOLLO_TEMPLATES_VERSION); ?>" defer></script>
    <?php endif; ?>

    <link rel="stylesheet" href="<?php echo APOLLO_TEMPLATES_URL; ?>assets/css/classifieds.css">
</head>

<body <?php body_class('page-classifieds'); ?>>

    <?php
    // Navbar v2
    $navbar_path = APOLLO_TEMPLATES_DIR . 'templates/template-parts/navbar.v2.php';
    if (file_exists($navbar_path)) {
        include $navbar_path;
    }
    ?>

    <div class="classifieds-container">

        <!-- Header -->
        <header class="classifieds-header">
            <div class="classifieds-header-content">
                <h1 class="classifieds-title">
                    <i class="ri-megaphone-line"></i>
                    Classificados Apollo
                </h1>
                <p class="classifieds-subtitle">Marketplace da comunidade — anúncios, repasses, hospedagem</p>
            </div>

            <?php if ($is_logged_in) : ?>
                <a href="<?php echo home_url('/classificados/novo'); ?>" class="btn-create-ad">
                    <i class="ri-add-line"></i>
                    Criar Anúncio
                </a>
            <?php else : ?>
                <a href="<?php echo home_url('/acesso?redirect=/classificados'); ?>" class="btn-create-ad">
                    <i class="ri-login-box-line"></i>
                    Entrar para Anunciar
                </a>
            <?php endif; ?>
        </header>

        <!-- Filters -->
        <div class="classifieds-filters">
            <?php
            // Enqueue composite search
            if (function_exists('apollo_enqueue_composite_search')) {
                apollo_enqueue_composite_search();
            }

            // Composite Search
            if (function_exists('apollo_composite_search')) {
                apollo_composite_search(
                    array(
                        'context'     => 'classifieds',
                        'placeholder' => __('Buscar anúncios...', 'apollo-templates'),
                        'class'       => 'classifieds-search-composite',
                    )
                );
            }
            ?>

            <form method="get" action="" class="filters-form">

                <!-- Intent Filter -->
                <div class="filter-dropdown">
                    <select name="intent" class="filter-select">
                        <option value="">Todas as Intenções</option>
                        <?php if ($intents && ! is_wp_error($intents)) : ?>
                            <?php foreach ($intents as $term) : ?>
                                <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($intent_filter, $term->slug); ?>>
                                    <?php echo esc_html($term->name); ?> (<?php echo $term->count; ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Domain Filter -->
                <div class="filter-dropdown">
                    <select name="domain" class="filter-select">
                        <option value="">Todas as Categorieas</option>
                        <?php if ($domains && ! is_wp_error($domains)) : ?>
                            <?php foreach ($domains as $term) : ?>
                                <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($domain_filter, $term->slug); ?>>
                                    <?php echo esc_html($term->name); ?> (<?php echo $term->count; ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <button type="submit" class="filter-btn-submit">
                    <i class="ri-filter-3-line"></i>
                    Filtrar
                </button>

                <?php if ($intent_filter || $domain_filter || $search) : ?>
                    <a href="<?php echo home_url('/classificados'); ?>" class="filter-btn-clear">
                        <i class="ri-close-line"></i>
                        Limpar
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Results Count -->
        <div class="classifieds-results-count">
            <span class="count-number"><?php echo $classifieds->found_posts; ?></span>
            <span class="count-label">
                <?php echo $classifieds->found_posts === 1 ? 'anúncio encontrado' : 'anúncios encontrados'; ?>
            </span>
        </div>

        <!-- Grid -->
        <?php if ($classifieds->have_posts()) : ?>
            <div class="classifieds-grid">
                <?php
                while ($classifieds->have_posts()) :
                    $classifieds->the_post();
                    $ad_id           = get_the_ID();
                    $thumbnail       = get_the_post_thumbnail_url($ad_id, 'medium');
                    $price           = get_post_meta($ad_id, '_classified_price', true);
                    $location        = get_post_meta($ad_id, '_classified_location', true);
                    $author_id       = get_post_field('post_author', $ad_id);
                    $author_name     = get_the_author_meta('display_name', $author_id);
                    $author_username = get_the_author_meta('user_login', $author_id);
                    $intent_terms    = wp_get_post_terms($ad_id, 'classified_intent', array('fields' => 'names'));
                    $intent_label    = ! empty($intent_terms) && ! is_wp_error($intent_terms) ? $intent_terms[0] : '';

                    // Get WOW count
                    $wow_count = function_exists('apollo_wow_get_count') ? apollo_wow_get_count($ad_id) : 0;

                    // Get comment count
                    $comment_count  = wp_count_comments($ad_id);
                    $comments_total = $comment_count->approved;

                    // Check if user favorited (only if logged in)
                    $is_favorited = false;
                    if ($is_logged_in && function_exists('apollo_fav_is_favorited')) {
                        $is_favorited = apollo_fav_is_favorited($user_id, $ad_id);
                    }
                ?>
                    <article class="ad-card" data-id="<?php echo $ad_id; ?>">

                        <!-- Image -->
                        <div class="ad-card-media">
                            <a href="<?php the_permalink(); ?>">
                                <?php if ($thumbnail) : ?>
                                    <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                                <?php else : ?>
                                    <div class="ad-card-no-image">
                                        <i class="ri-image-line"></i>
                                    </div>
                                <?php endif; ?>
                            </a>

                            <!-- Intent Badge -->
                            <?php if ($intent_label) : ?>
                                <span class="ad-card-badge"><?php echo esc_html($intent_label); ?></span>
                            <?php endif; ?>

                            <!-- Quick Actions (Overlay) - Only for logged users -->
                            <?php if ($is_logged_in) : ?>
                                <div class="ad-card-quick-actions">
                                    <!-- Favorite Button (apollo-fav) -->
                                    <button
                                        class="quick-action-btn fav-btn <?php echo $is_favorited ? 'is-favorited' : ''; ?>"
                                        data-action="toggle-fav"
                                        data-post-id="<?php echo $ad_id; ?>"
                                        aria-label="Favoritar">
                                        <i class="ri-heart-<?php echo $is_favorited ? 'fill' : 'line'; ?>"></i>
                                    </button>

                                    <!-- WOW Button (apollo-wow) -->
                                    <button
                                        class="quick-action-btn wow-btn"
                                        data-action="wow"
                                        data-post-id="<?php echo $ad_id; ?>"
                                        aria-label="WOW">
                                        <i class="ri-fire-line"></i>
                                        <?php if ($wow_count > 0) : ?>
                                            <span class="wow-count"><?php echo $wow_count; ?></span>
                                        <?php endif; ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Content -->
                        <div class="ad-card-content">
                            <a href="<?php the_permalink(); ?>" class="ad-card-title-link">
                                <h2 class="ad-card-title"><?php the_title(); ?></h2>
                            </a>

                            <?php if ($price) : ?>
                                <p class="ad-card-price">
                                    <i class="ri-price-tag-3-line"></i>
                                    R$ <?php echo esc_html(number_format((float) $price, 2, ',', '.')); ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($location) : ?>
                                <p class="ad-card-location">
                                    <i class="ri-map-pin-2-line"></i>
                                    <?php echo esc_html($location); ?>
                                </p>
                            <?php endif; ?>

                            <!-- Author -->
                            <div class="ad-card-author">
                                <a href="<?php echo home_url('/id/' . $author_username); ?>" class="author-link">
                                    <i class="ri-user-3-line"></i>
                                    <span><?php echo esc_html($author_name); ?></span>
                                </a>
                                <span class="ad-card-time"><?php echo wp_kses_post(apollo_time_ago_html(get_the_time('Y-m-d H:i:s'))); ?></span>
                            </div>

                            <!-- Actions -->
                            <div class="ad-card-actions">
                                <!-- Message Button (apollo-chat) - Only if logged and not own ad -->
                                <?php if ($is_logged_in && $author_id !== $user_id && function_exists('apollo_chat_get_or_create_thread')) : ?>
                                    <button
                                        class="ad-action-btn btn-message"
                                        data-action="send-message"
                                        data-author-id="<?php echo $author_id; ?>"
                                        data-ad-id="<?php echo $ad_id; ?>">
                                        <i class="ri-message-3-line"></i>
                                        Negociar
                                    </button>
                                <?php elseif (! $is_logged_in) : ?>
                                    <a href="<?php echo home_url('/acesso?redirect=/classificados'); ?>" class="ad-action-btn btn-message">
                                        <i class="ri-login-box-line"></i>
                                        Entrar para Negociar
                                    </a>
                                <?php endif; ?>

                                <!-- View Details -->
                                <a href="<?php the_permalink(); ?>" class="ad-action-btn btn-view">
                                    <i class="ri-eye-line"></i>
                                    Ver Detalhes
                                </a>
                            </div>

                            <!-- Comments Info (apollo-comment) -->
                            <?php if ($comments_total > 0) : ?>
                                <div class="ad-card-engagement">
                                    <span class="engagement-item">
                                        <i class="ri-chat-3-line"></i>
                                        <?php echo $comments_total; ?> <?php echo $comments_total === 1 ? 'comentário' : 'comentários'; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php
            if ($classifieds->max_num_pages > 1) :
                $prev_url = add_query_arg(array('paged' => max(1, $paged - 1)));
                $next_url = add_query_arg(array('paged' => min($classifieds->max_num_pages, $paged + 1)));
            ?>
                <nav class="classifieds-pagination">
                    <?php if ($paged > 1) : ?>
                        <a href="<?php echo esc_url($prev_url); ?>" class="pagination-btn pagination-prev">
                            <i class="ri-arrow-left-s-line"></i>
                            Anterior
                        </a>
                    <?php endif; ?>

                    <span class="pagination-info">
                        Página <?php echo $paged; ?> de <?php echo $classifieds->max_num_pages; ?>
                    </span>

                    <?php if ($paged < $classifieds->max_num_pages) : ?>
                        <a href="<?php echo esc_url($next_url); ?>" class="pagination-btn pagination-next">
                            Próximo
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>

        <?php else : ?>
            <div class="classifieds-empty">
                <i class="ri-inbox-line"></i>
                <h2>Nenhum anúncio encontrado</h2>
                <p>Tente ajustar os filtros ou faça uma nova busca.</p>
                <a href="<?php echo home_url('/classificados'); ?>" class="btn-reset">
                    <i class="ri-refresh-line"></i>
                    Ver Todos
                </a>
            </div>
        <?php endif; ?>

        <script>
            (function() {
                'use strict';
                const REST_BASE = '<?php echo esc_url(rest_url('apollo/v1/')); ?>';
                const NONCE = '<?php echo wp_create_nonce('wp_rest'); ?>';
                const LOGGED_IN = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

                function requireLogin(msg) {
                    alert(msg || 'Você precisa estar logado.');
                    window.location.href = '<?php echo home_url('/acesso?redirect=/classificados'); ?>';
                }

                // ── Favorite toggle (apollo-fav) ──
                document.addEventListener('click', function(e) {
                    const btn = e.target.closest('[data-action="toggle-fav"]');
                    if (!btn) return;

                    if (!LOGGED_IN) return requireLogin('Você precisa estar logado para favoritar anúncios');

                    const postId = btn.dataset.postId;
                    const icon = btn.querySelector('i');
                    const action = btn.classList.contains('is-favorited') ? 'remove' : 'add';

                    fetch(REST_BASE + 'fav', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-WP-Nonce': NONCE
                            },
                            body: JSON.stringify({
                                post_id: parseInt(postId),
                                action: action
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success || data.status === 'added' || data.status === 'removed') {
                                btn.classList.toggle('is-favorited');
                                icon.className = btn.classList.contains('is-favorited') ? 'ri-heart-fill' : 'ri-heart-line';
                            }
                        })
                        .catch(console.error);
                });

                // ── WOW reaction (apollo-wow) ──
                document.addEventListener('click', function(e) {
                    const btn = e.target.closest('[data-action="wow"]');
                    if (!btn) return;

                    if (!LOGGED_IN) return requireLogin('Você precisa estar logado para reagir aos anúncios');

                    const postId = btn.dataset.postId;
                    const countSpan = btn.querySelector('.wow-count');

                    fetch(REST_BASE + 'wow', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-WP-Nonce': NONCE
                            },
                            body: JSON.stringify({
                                post_id: parseInt(postId),
                                reaction_type: 'fire'
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                btn.classList.add('is-animated');
                                setTimeout(() => btn.classList.remove('is-animated'), 600);
                                if (data.count !== undefined) {
                                    if (countSpan) {
                                        countSpan.textContent = data.count;
                                    } else {
                                        btn.insertAdjacentHTML('beforeend', '<span class="wow-count">' + data.count + '</span>');
                                    }
                                }
                            }
                        })
                        .catch(console.error);
                });

                // ── Send message (apollo-chat) ──
                document.addEventListener('click', function(e) {
                    const btn = e.target.closest('[data-action="send-message"]');
                    if (!btn) return;

                    if (!LOGGED_IN) return requireLogin('Você precisa estar logado para enviar mensagens');

                    const authorId = btn.dataset.authorId;
                    const adId = btn.dataset.adId;
                    const message = prompt('Digite sua mensagem para negociar:');
                    if (!message) return;

                    fetch(REST_BASE + 'chat/threads', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-WP-Nonce': NONCE
                            },
                            body: JSON.stringify({
                                recipient_id: parseInt(authorId),
                                message: message,
                                context: {
                                    type: 'classified',
                                    id: parseInt(adId)
                                }
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                alert('Mensagem enviada com sucesso!');
                                window.location.href = '<?php echo home_url('/chat'); ?>';
                            } else {
                                alert('Erro ao enviar mensagem. Tente novamente.');
                            }
                        })
                        .catch(console.error);
                });

                // ── Auto-submit filters on select change ──
                document.querySelectorAll('.filter-select').forEach(function(sel) {
                    sel.addEventListener('change', function() {
                        this.closest('form').submit();
                    });
                });
            })();
        </script>

        <?php
        // wp_footer(); // Removed for blank canvas
        ?>
</body>

</html>