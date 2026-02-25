/**
 * Apollo Search — WP-Admin Auto-Enhancer
 *
 * Auto-applies ApolloListFilter to admin list tables with 10+ rows.
 * Adds live filter input above tables for instant row filtering.
 *
 * @package Apollo\Shortcode
 * @since   1.0.0
 */
(function () {
    'use strict';

    if (typeof window.ApolloListFilter === 'undefined') return;

    document.addEventListener('DOMContentLoaded', function () {
        // Target all WP list tables on admin screens
        var tables = document.querySelectorAll('.wp-list-table');

        tables.forEach(function (table) {
            var rows = table.querySelectorAll('tbody tr');
            if (rows.length < 10) return; // Only enhance tables with 10+ items

            // Check if already enhanced
            if (table.dataset.apolloEnhanced) return;
            table.dataset.apolloEnhanced = 'true';

            // Create filter input
            var wrapper = document.createElement('div');
            wrapper.className = 'apollo-admin-filter-wrap';
            wrapper.style.cssText = 'margin: 8px 0; display: flex; align-items: center; gap: 8px;';

            var input = document.createElement('input');
            input.type = 'text';
            input.placeholder = 'Filtrar lista... (' + rows.length + ' itens)';
            input.className = 'apollo-admin-filter-input';
            input.style.cssText = 'padding: 6px 12px; border: 1px solid #c3c4c7; border-radius: 4px; font-size: 13px; width: 260px; background: #fff;';

            var counter = document.createElement('span');
            counter.className = 'apollo-admin-filter-counter';
            counter.style.cssText = 'font-size: 12px; color: #646970;';
            counter.textContent = rows.length + ' itens';

            wrapper.appendChild(input);
            wrapper.appendChild(counter);

            // Insert before the table
            var tablenav = table.closest('.wrap')?.querySelector('.tablenav.top');
            if (tablenav) {
                tablenav.parentNode.insertBefore(wrapper, tablenav.nextSibling);
            } else {
                table.parentNode.insertBefore(wrapper, table);
            }

            // Apply ApolloListFilter
            input.setAttribute('data-apollo-list-filter', '#' + (table.id || 'the-list'));
            input.setAttribute('data-filter-item', 'tr');
            input.setAttribute('data-filter-counter', '.apollo-admin-filter-counter');

            // Manual init since DOM was modified after DOMContentLoaded
            new window.ApolloListFilter(input);
        });
    });
})();
