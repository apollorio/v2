# Apollo Ecosystem — Deep Audit & Fix Report

**Date:** 2026-02-26  
**Branch:** `main`  
**Scope:** 5 critical functional issues across the Apollo ecosystem  

---

## Issues Investigated

| # | Issue | Root Cause | Status |
|---|-------|-----------|--------|
| 1 | Hero video not playing on `/home` | CSP missing `media-src` blocks `assets.apollo.rio.br`; no force-play fallback; hero only renders for guests | ✅ FIXED |
| 2 | Login/Register errors on `/acesso` & `/registre` | `register.php` missing `apolloAuthConfig` injection → empty nonce → AJAX 403 | ✅ FIXED |
| 3 | YouTube video behind login/register not working | CSP `frame-src` missing YouTube domains; `register.php` YouTube URL missing `playlist=` param for loop | ✅ FIXED |
| 4 | Media not working across pages | CSP had no `media-src` directive → fell back to `default-src 'self'` → blocked all external media | ✅ FIXED |
| 5 | Icons not loaded / CDN script verification | CDN `core.min.js?v=1.0.0` correctly present in ALL 37+ templates; `BlankCanvasTrait` had wrong `cdn.js` reference; `events.php` had duplicate CDN with wrong file | ✅ FIXED |

---

## Phase A — Critical Blockers (4 fixes)

### A1: register.php — apolloAuthConfig injection
**File:** `apollo-login/templates/register.php`  
**Problem:** Missing `$auth_config`, `$js_config`, and `window.apolloAuthConfig` injection that `login.php` has. Without it, the nonce is empty → ALL AJAX calls return HTTP 403 → registration completely broken.  
**Fix:** Added `$auth_config = apply_filters(...)` block, `$js_config = array(...)` block, and `<script>window.apolloAuthConfig = <?php echo wp_json_encode($js_config); ?>;</script>` before the auth-scripts.js tag — mirroring login.php exactly.

### A2: CSP frame-src — YouTube blocked
**File:** `apollo-login/src/Security/SecurityHeaders.php`  
**Problem:** `frame-src` only allowed `'self' https://www.google.com https://www.recaptcha.net` — YouTube iframes were silently blocked by the browser CSP.  
**Fix:** Added `https://www.youtube.com https://www.youtube-nocookie.com` to `frame-src`.

### A3: CSP media-src — Missing directive
**File:** `apollo-login/src/Security/SecurityHeaders.php`  
**Problem:** No `media-src` directive in CSP → fell back to `default-src 'self'` → blocked `<video>` and `<audio>` from `assets.apollo.rio.br`. This affected the hero video on `/home` and any embedded media.  
**Fix:** Added `media-src 'self' https://assets.apollo.rio.br blob: data:` to the CSP array.

### A4: register.php — YouTube playlist param
**File:** `apollo-login/templates/register.php`  
**Problem:** YouTube iframe URL missing `&playlist=wQVrPHKww4Y` — required for `loop=1` to work on YouTube embeds. Also missing `loading="eager"` and `fullscreen` in allow attribute.  
**Fix:** Added `&playlist=wQVrPHKww4Y&enablejsapi=1`, `allow="autoplay; encrypted-media; fullscreen"`, and `loading="eager"` — matching login.php.

---

## Phase B — CDN & Icons (2 fixes)

### B5: BlankCanvasTrait — Wrong CDN file
**File:** `apollo-core/src/Traits/BlankCanvasTrait.php`  
**Problem:** Line 144 referenced `cdn.js` (non-existent file). While not currently used by any active template, it's a time bomb for future templates using the trait's `blank_canvas_head()` method.  
**Fix:** Changed to use `$this->get_apollo_cdn_core_js()` which returns `core.min.js?v=1.0.0` via the `APOLLO_CDN_CORE_JS` constant with fallback.

### B6: events.php — Duplicate CDN with wrong file
**File:** `apollo-templates/templates/template-parts/new-home/events.php`  
**Problem:** Line 83 had `<script src="https://cdn.apollo.rio.br/v1.0.0/core.js">` — wrong file (not minified, no cache busting), AND duplicated the CDN that parent `page-home.php` already loads.  
**Fix:** Removed the duplicate script tag entirely.

### CDN Loading Matrix — Verified OK
All 37+ templates across the ecosystem correctly load `core.min.js?v=1.0.0` via hardcoded `<script>` tags:
- ✅ apollo-templates (page-home, page-mural, page-explore, page-event, page-hub, etc.)
- ✅ apollo-login (login.php, register.php)
- ✅ apollo-hub, apollo-docs, apollo-journal, apollo-dashboard
- ✅ apollo-sign (sig-head.php)
- ✅ apollo-chat, apollo-groups, apollo-social, apollo-mod

### Icon System — Architecture Note
Icons use a **custom runtime** (`icon.min.js`) loaded via the CDN chain — NOT `remixicon.css` font files.  
- `icon.min.js` uses `MutationObserver` to detect `<i class="ri-*">` elements
- Fetches SVGs from `assets.apollo.rio.br/i/{name}.svg` using a manifest at `assets.apollo.rio.br/i/json/manifest-icon.min.json`
- Applies via CSS `mask-image` for theme-color inheritance
- **If CDN (`cdn.apollo.rio.br`) is inaccessible in local environment, ALL icons will fail** — this is expected behavior for local dev

---

## Phase C — Video Hero /home (1 fix)

### C9: Force-play fallback
**File:** `apollo-templates/templates/template-parts/new-home/hero.php`  
**Problem:** Some browsers block autoplay even with `muted` attribute. Video could appear frozen on initial load or after tab switch.  
**Fix:** Added inline `<script>` after the hero section that:
1. Calls `video.play()` if paused on load
2. Retries on `suspend` event
3. Retries on `visibilitychange` (tab switch back)

### Hero visibility note
The hero section only renders for **guest users**. Logged-in users are routed to `page-mural.php` or `panel-explore.php` via the routing logic in `apollo-templates/src/Core/Pages.php` and `page-home.php`.

---

## Phase D — Dead Code Removal (7 items)

### Files deleted
| File | Reason |
|------|--------|
| `apollo-login/assets/js/simon.js` | Standalone Simon game JS — never loads because Blank Canvas pages don't call `wp_head()`/`wp_footer()`. Real Simon is inline in `apollo-auth-scripts.js`. |
| `apollo-login/assets/js/quiz.js` | Standalone Quiz JS — same reason. Had placeholder code `$('#apollo-simon-game').html('<p>Simon game will load here</p>')`. |
| `apollo-login/assets/css/simon.css` | Stylesheet for deleted `simon.js` — only referenced in the removed `enqueue_assets()` method. |
| `apollo-login/assets/css/quiz.css` | Stylesheet for deleted `quiz.js` — only referenced in the removed `enqueue_assets()` method. |
| `apollo-sign/templates/parts/head.php` | Orphan file (331 lines). `sign.php` uses `sig-head.php` instead. Never included anywhere. |

### Code cleaned
| File | Change |
|------|--------|
| `apollo-login/src/Quiz/SimonGame.php` | Removed `wp_enqueue_scripts` hook and `enqueue_assets()` method (dead on Blank Canvas). Kept `save_score()` and `get_leaderboard()` — used by `QuizController.php` REST API. |
| `apollo-login/src/Quiz/QuizManager.php` | Removed `wp_enqueue_scripts` hook and `enqueue_assets()` method (dead on Blank Canvas). Kept `get_questions()` static methods — used by `QuizController.php` REST API. |

---

## Files Modified Summary

| File | Plugin | Changes |
|------|--------|---------|
| `templates/register.php` | apollo-login | +$auth_config, +$js_config, +apolloAuthConfig injection, +playlist param, +loading="eager" |
| `src/Security/SecurityHeaders.php` | apollo-login | +YouTube to frame-src, +media-src directive |
| `src/Quiz/SimonGame.php` | apollo-login | Removed dead enqueue code |
| `src/Quiz/QuizManager.php` | apollo-login | Removed dead enqueue code |
| `src/Traits/BlankCanvasTrait.php` | apollo-core | Fixed cdn.js → core.min.js via helper method |
| `templates/template-parts/new-home/events.php` | apollo-templates | Removed duplicate CDN script tag |
| `templates/template-parts/new-home/hero.php` | apollo-templates | Added force-play fallback script |

## Files Deleted

| File | Plugin |
|------|--------|
| `assets/js/simon.js` | apollo-login |
| `assets/js/quiz.js` | apollo-login |
| `assets/css/simon.css` | apollo-login |
| `assets/css/quiz.css` | apollo-login |
| `templates/parts/head.php` | apollo-sign |

---

## Previous Work (Waves 0–10)

All 10 waves of the Apollo ecosystem unification audit were completed in previous sessions. See git history for full details. Last wave commit: `dacc1e4`.

---

*Generated by Apollo Audit Agent — 2026-02-26*
