/**
 * Apollo Journal — Main JavaScript
 *
 * Lightweight: scroll-based lazy reveal for news grid items.
 *
 * @package Apollo\Journal
 */

(function ($) {
    'use strict';

    /** Apollo standard time-ago HTML block. Input: '53min' → icon+spans. */
    function tempoHTML(str) {
        if (!str) return '';
        var m = String(str).match(/^(\d+)([a-z]+)$/i);
        var num  = m ? m[1] : str;
        var unit = m ? m[2] : '';
        return '<i class="tempo-v"></i>\u00a0<span class="time-ago">' + num + '</span><span class="when-ago">' + unit + '</span>';
    }

    const CONFIG = window.apolloJournalConfig || {
        ajaxUrl: '/wp-admin/admin-ajax.php',
        restUrl: '/wp-json/apollo/v1/',
        nonce: ''
    };

    $(function () {
        initReveal();
        initLoadMore();
    });

    /**
     * Staggered reveal animation for .aj-ng-item and .aj-card elements.
     */
    function initReveal() {
        if (!('IntersectionObserver' in window)) return;

        const items = document.querySelectorAll('.aj-ng-item, .aj-card');
        if (!items.length) return;

        items.forEach(function (el) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(16px)';
            el.style.transition = 'opacity .4s var(--ease-default, cubic-bezier(.16,1,.3,1)), transform .4s var(--ease-default)';
        });

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry, i) {
                if (!entry.isIntersecting) return;

                const el = entry.target;
                const delay = Array.from(items).indexOf(el) * 60;

                setTimeout(function () {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, delay);

                observer.unobserve(el);
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        items.forEach(function (el) {
            observer.observe(el);
        });
    }

    /**
     * Optional "load more" for news grid via REST.
     * Activated by adding data-aj-loadmore to .aj-news-grid.
     */
    function initLoadMore() {
        var $grid = $('.aj-news-grid[data-aj-loadmore]');
        if (!$grid.length) return;

        var page = 2;
        var loading = false;
        var $btn = $('<button class="pnl-btn" style="margin:24px auto;display:block">' +
            '<i class="ri-add-line"></i> Carregar mais</button>');

        $grid.after($btn);

        $btn.on('click', function () {
            if (loading) return;
            loading = true;
            $btn.text('Carregando...');

            $.ajax({
                url: CONFIG.restUrl + 'journal/posts',
                method: 'GET',
                data: { page: page, per_page: 6 },
                beforeSend: function (xhr) {
                    if (CONFIG.nonce) {
                        xhr.setRequestHeader('X-WP-Nonce', CONFIG.nonce);
                    }
                },
                success: function (data) {
                    if (!data.length) {
                        $btn.remove();
                        return;
                    }

                    data.forEach(function (post) {
                        var html = '<a href="' + post.link + '" class="aj-ng-item">';
                        if (post.thumbnail) {
                            html += '<img class="aj-ng-item__img" src="' + post.thumbnail + '" alt="" loading="lazy">';
                        }
                        html += '<div class="aj-ng-item__body">';
                        html += '<div class="aj-ng-item__top">';
                        html += '<span class="aj-ng-badge">' + (post.badge || 'NEWS') + '</span>';
                        html += '<span class="aj-ng-item__time">' + tempoHTML(post.time_ago || '') + '</span>';
                        html += '</div>';
                        html += '<div class="aj-ng-item__title">' + post.title.rendered + '</div>';
                        html += '<div class="aj-ng-item__author">' + (post.author_name || '') + '</div>';
                        html += '</div></a>';

                        $grid.append(html);
                    });

                    page++;
                    loading = false;
                    $btn.html('<i class="ri-add-line"></i> Carregar mais');
                    initReveal(); // Re-observe new items
                },
                error: function () {
                    loading = false;
                    $btn.text('Erro. Tentar novamente.');
                }
            });
        });
    }

})(jQuery);
