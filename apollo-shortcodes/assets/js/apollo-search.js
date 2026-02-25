/**
 * Apollo Search — Reusable Typeahead / Live Search Component
 *
 * Usage:
 *   <input type="text" data-apollo-search="events" placeholder="Buscar evento...">
 *   <input type="text" data-apollo-search="users" placeholder="Buscar usuário...">
 *   <input type="text" data-apollo-search="classifieds" placeholder="Buscar anúncio...">
 *   <input type="text" data-apollo-search="posts" placeholder="Buscar...">
 *   <input type="text" data-apollo-search="pages" placeholder="Buscar página...">
 *   <input type="text" data-apollo-search="all" placeholder="Buscar tudo...">
 *
 * Options via data attributes:
 *   data-apollo-search="events"          — CPT to search (events|users|classifieds|posts|pages|djs|locs|all)
 *   data-search-min="2"                  — Min chars to trigger (default: 2)
 *   data-search-limit="10"               — Max results (default: 10)
 *   data-search-select="true"            — Selection mode: sets hidden input value
 *   data-search-target="#hidden_field"    — Hidden input for selected ID
 *   data-search-placeholder="Buscar..."  — Custom placeholder
 *   data-search-navigate="true"          — Navigate to item URL on click
 *   data-search-callback="fnName"        — Custom JS callback on select
 *
 * Events dispatched on the input:
 *   apollo:search:select   — { detail: { id, title, subtitle, url, type } }
 *   apollo:search:clear    — Cleared
 *   apollo:search:results  — { detail: { results, query } }
 *
 * @package Apollo\Shortcode
 * @since   1.0.0
 */

(function () {
    'use strict';

    const DEBOUNCE_MS = 280;
    const MIN_CHARS_DEFAULT = 2;
    const LIMIT_DEFAULT = 10;

    // REST base — injected via wp_localize_script or fallback
    const REST_BASE = (window.apolloSearch && window.apolloSearch.restUrl)
        ? window.apolloSearch.restUrl
        : '/wp-json/apollo/v1/';

    const NONCE = (window.apolloSearch && window.apolloSearch.nonce) || '';

    // ── Utility ──────────────────────────────────────────────────
    function debounce(fn, ms) {
        let t;
        return function (...args) {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), ms);
        };
    }

    function escHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function highlight(text, query) {
        if (!query) return escHtml(text);
        const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const re = new RegExp('(' + escaped + ')', 'gi');
        return escHtml(text).replace(re, '<mark>$1</mark>');
    }

    // ── Icons per type ───────────────────────────────────────────
    const ICONS = {
        events: 'ri-calendar-event-line',
        users: 'ri-user-3-line',
        classifieds: 'ri-megaphone-line',
        posts: 'ri-article-line',
        pages: 'ri-pages-line',
        djs: 'ri-disc-line',
        locs: 'ri-map-pin-2-line',
        all: 'ri-search-line',
    };

    // ── Endpoint mapping ─────────────────────────────────────────
    function getEndpoint(type) {
        const map = {
            events: 'search/events',
            users: 'search/users',
            classifieds: 'search/classifieds',
            posts: 'search/posts',
            pages: 'search/pages',
            djs: 'search/djs',
            locs: 'search/locs',
            all: 'search',
        };
        return map[type] || 'search';
    }

    // ── ApolloSearch Class ───────────────────────────────────────
    class ApolloSearch {
        constructor(input) {
            this.input = input;
            this.type = input.dataset.apolloSearch || 'all';
            this.minChars = parseInt(input.dataset.searchMin) || MIN_CHARS_DEFAULT;
            this.limit = parseInt(input.dataset.searchLimit) || LIMIT_DEFAULT;
            this.isSelect = input.dataset.searchSelect === 'true';
            this.targetSelector = input.dataset.searchTarget || null;
            this.navigateOnSelect = input.dataset.searchNavigate === 'true';
            this.callbackName = input.dataset.searchCallback || null;

            this.dropdown = null;
            this.selectedItem = null;
            this.activeIndex = -1;
            this.results = [];
            this.abortController = null;
            this.isOpen = false;

            this._build();
            this._bindEvents();
        }

        // ── DOM Setup ────────────────────────────────────────────
        _build() {
            // Wrap input
            const wrapper = document.createElement('div');
            wrapper.className = 'apollo-search-wrap';
            this.input.parentNode.insertBefore(wrapper, this.input);
            wrapper.appendChild(this.input);

            // Add icon
            const icon = document.createElement('i');
            icon.className = (ICONS[this.type] || 'ri-search-line') + ' apollo-search-icon';
            wrapper.insertBefore(icon, this.input);

            // Spinner
            const spinner = document.createElement('span');
            spinner.className = 'apollo-search-spinner';
            spinner.innerHTML = '<i class="ri-loader-4-line"></i>';
            wrapper.appendChild(spinner);
            this.spinner = spinner;

            // Clear button
            if (this.isSelect) {
                const clear = document.createElement('button');
                clear.type = 'button';
                clear.className = 'apollo-search-clear';
                clear.innerHTML = '<i class="ri-close-line"></i>';
                clear.title = 'Limpar';
                clear.style.display = 'none';
                clear.addEventListener('click', () => this.clear());
                wrapper.appendChild(clear);
                this.clearBtn = clear;
            }

            // Dropdown
            const dd = document.createElement('div');
            dd.className = 'apollo-search-dropdown';
            dd.setAttribute('role', 'listbox');
            wrapper.appendChild(dd);
            this.dropdown = dd;

            this.wrapper = wrapper;
            this.input.setAttribute('autocomplete', 'off');
            this.input.setAttribute('role', 'combobox');
            this.input.setAttribute('aria-expanded', 'false');
            this.input.setAttribute('aria-haspopup', 'listbox');
        }

        // ── Events ───────────────────────────────────────────────
        _bindEvents() {
            this.input.addEventListener('input', debounce(() => this._onInput(), DEBOUNCE_MS));
            this.input.addEventListener('focus', () => {
                if (this.results.length > 0) this._showDropdown();
            });
            this.input.addEventListener('keydown', (e) => this._onKeydown(e));

            // Close on outside click
            document.addEventListener('click', (e) => {
                if (!this.wrapper.contains(e.target)) this._hideDropdown();
            });
        }

        _onInput() {
            const q = this.input.value.trim();

            if (q.length < this.minChars) {
                this.results = [];
                this._hideDropdown();
                return;
            }

            this._fetch(q);
        }

        _onKeydown(e) {
            if (!this.isOpen) return;

            const items = this.dropdown.querySelectorAll('.apollo-search-item');
            if (!items.length) return;

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    this.activeIndex = Math.min(this.activeIndex + 1, items.length - 1);
                    this._highlightItem(items);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    this.activeIndex = Math.max(this.activeIndex - 1, 0);
                    this._highlightItem(items);
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (this.activeIndex >= 0 && items[this.activeIndex]) {
                        items[this.activeIndex].click();
                    }
                    break;
                case 'Escape':
                    this._hideDropdown();
                    break;
            }
        }

        _highlightItem(items) {
            items.forEach((el, i) => {
                el.classList.toggle('is-active', i === this.activeIndex);
                if (i === this.activeIndex) el.scrollIntoView({ block: 'nearest' });
            });
        }

        // ── Fetch ────────────────────────────────────────────────
        async _fetch(query) {
            if (this.abortController) this.abortController.abort();
            this.abortController = new AbortController();

            this.wrapper.classList.add('is-loading');

            const endpoint = REST_BASE + getEndpoint(this.type);
            const params = new URLSearchParams({ q: query, limit: this.limit });
            const url = endpoint + '?' + params.toString();

            const headers = { 'Content-Type': 'application/json' };
            if (NONCE) headers['X-WP-Nonce'] = NONCE;

            try {
                const res = await fetch(url, {
                    headers,
                    signal: this.abortController.signal,
                });

                if (!res.ok) throw new Error(res.statusText);

                const data = await res.json();
                this.results = Array.isArray(data) ? data : (data.results || []);
                this._renderResults(query);

                this.input.dispatchEvent(new CustomEvent('apollo:search:results', {
                    detail: { results: this.results, query },
                }));
            } catch (err) {
                if (err.name !== 'AbortError') {
                    console.error('[ApolloSearch]', err);
                    this.results = [];
                    this._renderNoResults();
                }
            } finally {
                this.wrapper.classList.remove('is-loading');
            }
        }

        // ── Render ───────────────────────────────────────────────
        _renderResults(query) {
            this.activeIndex = -1;

            if (!this.results.length) {
                this._renderNoResults();
                return;
            }

            let html = '';
            this.results.forEach((item, i) => {
                const icon = ICONS[item.type || this.type] || 'ri-search-line';
                const title = highlight(item.title || item.name || '', query);
                const subtitle = item.subtitle || item.meta || '';
                const subtitleHtml = subtitle
                    ? `<span class="search-item-subtitle">${escHtml(subtitle)}</span>`
                    : '';

                html += `<div class="apollo-search-item" role="option" data-index="${i}"
                              data-id="${escHtml(String(item.id || ''))}"
                              data-url="${escHtml(item.url || '')}"
                              data-title="${escHtml(item.title || item.name || '')}"
                              data-type="${escHtml(item.type || this.type)}">
                    <i class="${icon} search-item-icon"></i>
                    <div class="search-item-content">
                        <span class="search-item-title">${title}</span>
                        ${subtitleHtml}
                    </div>
                </div>`;
            });

            this.dropdown.innerHTML = html;

            // Bind clicks
            this.dropdown.querySelectorAll('.apollo-search-item').forEach((el) => {
                el.addEventListener('click', () => this._selectItem(el));
                el.addEventListener('mouseenter', () => {
                    this.activeIndex = parseInt(el.dataset.index);
                    this._highlightItem(this.dropdown.querySelectorAll('.apollo-search-item'));
                });
            });

            this._showDropdown();
        }

        _renderNoResults() {
            this.dropdown.innerHTML = `
                <div class="apollo-search-empty">
                    <i class="ri-inbox-line"></i>
                    <span>Nenhum resultado encontrado</span>
                </div>`;
            this._showDropdown();
        }

        // ── Selection ────────────────────────────────────────────
        _selectItem(el) {
            const detail = {
                id: el.dataset.id,
                title: el.dataset.title,
                url: el.dataset.url,
                type: el.dataset.type,
            };

            this.selectedItem = detail;
            this.input.value = detail.title;

            // Set hidden target
            if (this.isSelect && this.targetSelector) {
                const target = document.querySelector(this.targetSelector);
                if (target) target.value = detail.id;
            }

            // Show clear button
            if (this.clearBtn) {
                this.clearBtn.style.display = '';
                this.input.classList.add('has-selection');
            }

            this._hideDropdown();

            // Dispatch event
            this.input.dispatchEvent(new CustomEvent('apollo:search:select', { detail }));

            // Navigate
            if (this.navigateOnSelect && detail.url) {
                window.location.href = detail.url;
            }

            // Custom callback
            if (this.callbackName && typeof window[this.callbackName] === 'function') {
                window[this.callbackName](detail);
            }
        }

        clear() {
            this.input.value = '';
            this.selectedItem = null;
            this.results = [];
            this.activeIndex = -1;
            this._hideDropdown();

            if (this.isSelect && this.targetSelector) {
                const target = document.querySelector(this.targetSelector);
                if (target) target.value = '';
            }

            if (this.clearBtn) {
                this.clearBtn.style.display = 'none';
                this.input.classList.remove('has-selection');
            }

            this.input.dispatchEvent(new CustomEvent('apollo:search:clear'));
            this.input.focus();
        }

        // ── Show/Hide ────────────────────────────────────────────
        _showDropdown() {
            this.dropdown.classList.add('is-open');
            this.isOpen = true;
            this.input.setAttribute('aria-expanded', 'true');
        }

        _hideDropdown() {
            this.dropdown.classList.remove('is-open');
            this.isOpen = false;
            this.activeIndex = -1;
            this.input.setAttribute('aria-expanded', 'false');
        }
    }

    // ── Static list search (for tables/lists with 10+ items) ─────
    class ApolloListFilter {
        constructor(input) {
            this.input = input;
            this.listSelector = input.dataset.apolloListFilter;
            this.list = document.querySelector(this.listSelector);
            if (!this.list) return;

            this.items = Array.from(this.list.querySelectorAll(
                input.dataset.filterItem || 'tr, li, .list-item, .apollo-card, article'
            ));
            this.emptyMsg = null;

            this._buildEmpty();
            this._bindEvents();
        }

        _buildEmpty() {
            this.emptyMsg = document.createElement('div');
            this.emptyMsg.className = 'apollo-list-filter-empty';
            this.emptyMsg.innerHTML = '<i class="ri-inbox-line"></i> Nenhum resultado encontrado';
            this.emptyMsg.style.display = 'none';
            this.list.parentNode.insertBefore(this.emptyMsg, this.list.nextSibling);
        }

        _bindEvents() {
            this.input.addEventListener('input', debounce(() => this._filter(), 200));
        }

        _filter() {
            const q = this.input.value.trim().toLowerCase();
            let visible = 0;

            this.items.forEach((item) => {
                const text = item.textContent.toLowerCase();
                const match = !q || text.includes(q);
                item.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            this.emptyMsg.style.display = visible === 0 ? '' : 'none';

            // Update counter if exists
            const counter = document.querySelector(this.input.dataset.filterCounter || '.apollo-filter-count');
            if (counter) counter.textContent = visible;
        }
    }

    // ── Auto-init ────────────────────────────────────────────────
    function initAll() {
        // REST-powered typeahead search
        document.querySelectorAll('[data-apollo-search]').forEach((input) => {
            if (input._apolloSearch) return;
            input._apolloSearch = new ApolloSearch(input);
        });

        // Static list filter (for tables/lists)
        document.querySelectorAll('[data-apollo-list-filter]').forEach((input) => {
            if (input._apolloListFilter) return;
            input._apolloListFilter = new ApolloListFilter(input);
        });
    }

    // Init on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // Re-init on AJAX loads
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('ajaxComplete', initAll);
    }

    // Expose globally
    window.ApolloSearch = ApolloSearch;
    window.ApolloListFilter = ApolloListFilter;
    window.apolloSearchInit = initAll;
})();
