/**
 * Apollo Navbar v2 — JavaScript
 *
 * Handles:
 * - FAB (#nhMenuFab) toggle → opens #nhMenuSheet upward sheet
 * - Apps dropdown (#nhAppsBtn / #nhAppsDropdown) — logged only
 * - Profile dropdown (#nhProfileBtn / #nhProfileDropdown) — logged only
 * - Scroll detection → .scrolled class on #nhNav
 * - Click-outside to close all open panels
 * - Keyboard: Escape closes all
 *
 * No login form AJAX (v2 redirects guests → /acesso)
 * No clock (removed in v2 design)
 *
 * @package Apollo\Templates
 * @since   6.2.0
 */

(function () {
    'use strict';

    // Prevent double initialization
    if (window.__APOLLO_NAVBAR_V2__) return;
    window.__APOLLO_NAVBAR_V2__ = 1;

    // ─── DOM refs ────────────────────────────────────────────────────────────
    var nav            = document.getElementById('nhNav');
    var fabBtn         = document.getElementById('nhMenuFab');
    var fabSheet       = document.getElementById('nhMenuSheet');
    var appsBtn        = document.getElementById('nhAppsBtn');
    var appsDropdown   = document.getElementById('nhAppsDropdown');
    var profileBtn     = document.getElementById('nhProfileBtn');
    var profileDropdown= document.getElementById('nhProfileDropdown');

    if (!nav) return; // navbar not in DOM — nothing to do

    // ─── State ───────────────────────────────────────────────────────────────
    var openPanel = null; // 'fab' | 'apps' | 'profile' | null

    // ─── Helpers ─────────────────────────────────────────────────────────────
    function openEl(el, btn) {
        if (!el) return;
        el.classList.add('is-open');
        if (btn) btn.setAttribute('aria-expanded', 'true');
    }

    function closeEl(el, btn) {
        if (!el) return;
        el.classList.remove('is-open');
        if (btn) btn.setAttribute('aria-expanded', 'false');
    }

    function closeAll() {
        closeEl(fabSheet,       fabBtn);
        closeEl(appsDropdown,   appsBtn);
        closeEl(profileDropdown,profileBtn);
        if (fabBtn) fabBtn.classList.remove('is-open');
        openPanel = null;
    }

    function toggle(panelKey, el, btn) {
        if (openPanel === panelKey) {
            closeAll();
        } else {
            closeAll();
            openPanel = panelKey;
            openEl(el, btn);
            if (panelKey === 'fab' && fabBtn) {
                fabBtn.classList.add('is-open');
            }
        }
    }

    // ─── FAB ─────────────────────────────────────────────────────────────────
    if (fabBtn && fabSheet) {
        fabBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggle('fab', fabSheet, fabBtn);
        });
    }

    // ─── Apps dropdown (logged only) ─────────────────────────────────────────
    if (appsBtn && appsDropdown) {
        appsBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggle('apps', appsDropdown, appsBtn);
        });
    }

    // ─── Profile dropdown (logged only) ──────────────────────────────────────
    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggle('profile', profileDropdown, profileBtn);
        });
    }

    // ─── Click outside → close all ───────────────────────────────────────────
    document.addEventListener('click', function (e) {
        if (!openPanel) return;

        var insideFab     = (fabBtn && fabBtn.contains(e.target)) || (fabSheet && fabSheet.contains(e.target));
        var insideApps    = (appsBtn && appsBtn.contains(e.target)) || (appsDropdown && appsDropdown.contains(e.target));
        var insideProfile = (profileBtn && profileBtn.contains(e.target)) || (profileDropdown && profileDropdown.contains(e.target));

        if (!insideFab && !insideApps && !insideProfile) {
            closeAll();
        }
    });

    // ─── Keyboard: Escape closes all ─────────────────────────────────────────
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && openPanel) {
            closeAll();
        }
    });

    // ─── Scroll detection → .scrolled on navbar ──────────────────────────────
    var ticking = false;
    window.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(function () {
                if (nav) {
                    nav.classList.toggle('scrolled', window.scrollY > 20);
                }
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });

    // Trigger on load in case page is already scrolled (e.g. browser restore)
    if (nav && window.scrollY > 20) {
        nav.classList.add('scrolled');
    }

    // ─── Sheet links — close sheet on navigate ────────────────────────────────
    [fabSheet, appsDropdown, profileDropdown].forEach(function (panel) {
        if (!panel) return;
        panel.addEventListener('click', function (e) {
            var target = e.target.closest('a, button');
            // Close if clicking a real link (not a button that acts as panel toggle)
            if (target && target.tagName === 'A') {
                closeAll();
            }
        });
    });

})();
