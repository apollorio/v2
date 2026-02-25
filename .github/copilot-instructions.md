# Apollo Ecosystem — Copilot Instructions

## ⚠️ MANDATORY — Read First

**Before ANY work, open and comply with `_inventory/apollo-registry.json`** — it is the single source of truth for the entire ecosystem. Every CPT slug, taxonomy, meta key, REST endpoint, table name, naming rule, and forbidden term is defined there. NEVER invent or create new structures — always integrate with what exists.

## Architecture Overview

Apollo is an **ultra-modular WordPress platform** (27 plugins) for Rio de Janeiro's culture/nightlife industry. Each plugin = ONE responsibility, connected via `apollo-core` hooks.

### Layer Hierarchy (load order matters)

| Layer | Plugins | Role |
|-------|---------|------|
| L0 | `apollo-core` | **MASTER REGISTRY** — ALL CPTs, taxonomies, meta keys, 22 tables, fallback system |
| L1 | `apollo-login`, `apollo-users`, `apollo-membership` | Auth, profiles (`/id/username`), gamification |
| L2 | `apollo-event`, `apollo-djs`, `apollo-loc`, `apollo-classifieds`, `apollo-suppliers` | Content CPTs |
| L3 | `apollo-social`, `apollo-groups`, `apollo-wow`, `apollo-fav`, `apollo-comment`, `apollo-notif`, `apollo-email`, `apollo-chat` | Social + Communication |
| L4+ | `apollo-shortcodes`, `apollo-templates`, `apollo-dashboard`, `apollo-hub`, `apollo-admin`, `apollo-mod`, `apollo-coauthor`, `apollo-statistics`, `apollo-cult`, `apollo-pwa` | Frontend, admin, industry |

### Critical: Central Registry Pattern

`apollo-core` **owns all definitions**. If `apollo-event` is active, it registers the `event` CPT. If NOT active, `apollo-core` registers it as **fallback bridge**. See `apollo-core/src/Core/CPTRegistry.php` — singleton with `check if owner active → fallback` logic. Same pattern for `TaxonomyRegistry`, `MetaRegistry`, `DatabaseBuilder`.

## Naming Rules — FORBIDDEN Terms

From `apollo-registry.json → namingRules.FORBIDDEN_TERMS`:

- **venue/local/location** → use `loc`
- **interesse/interest/bookmark** → use `fav`
- **like/heart/reaction** → use `wow`
- **comment/review** → use `depoimento`
- **cena-rio/cenario** → use `cult`
- **/user/** → use `/id/` (profiles are `/id/{username}`)

## Code Conventions

### Namespace & Autoload

Each plugin uses PSR-4: `Apollo\{PluginName}\` mapped to `src/`. Autoloader in main plugin file:

```php
// Example from apollo-core.php
spl_autoload_register(function (string $class) {
    $prefix = 'Apollo\\Core\\';
    $base_dir = APOLLO_CORE_PATH . 'src/';
    // ... PSR-4 resolution
});
```

### REST API Pattern

All controllers extend `Apollo\Core\API\RestBase` (extends `WP_REST_Controller`):
- Namespace: `apollo/v1`
- Permission helpers: `$this->is_logged_in()`, `$this->is_admin()`
- Response: `$this->prepare_response($data, $status)`
- **ALWAYS** include `permission_callback` on every endpoint

### Plugin File Structure

```
apollo-{name}/
├── apollo-{name}.php      # Main file: constants, autoloader, bootstrap
├── src/                    # PSR-4 classes (Apollo\{Name}\)
│   ├── Core/               # Core logic, registries
│   └── API/                # REST controllers
├── includes/               # Legacy includes, functions, constants
├── templates/              # PHP templates
├── assets/                 # CSS/JS
├── composer.json
└── uninstall.php
```

### Security (NON-NEGOTIABLE)

- Escape output: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`
- Sanitize input: `sanitize_text_field()`, `sanitize_email()`, `absint()`
- Nonces: `wp_nonce_field()` / `wp_verify_nonce()` / `check_ajax_referer()`
- Capabilities: always check `current_user_can()` before mutations
- Every PHP file starts with `if (!defined('ABSPATH')) { exit; }`

## CDN — Mandatory for Frontend

Every frontend template **must** load the Apollo CDN:

```html
<script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>
```

Includes: Apollo CSS, GSAP 3.13, jQuery 3.7.1, RemixIcon, dark theme, `Translate` i18n (v2.2.4), `ApolloTrack` analytics. Use `APOLLO_CDN_URL` constant in PHP.

## Key Constants

```php
APOLLO_VERSION          // '6.0.0'
APOLLO_CDN_URL          // 'https://cdn.apollo.rio.br/v1.0.0/'
APOLLO_REST_NAMESPACE   // 'apollo/v1'
APOLLO_TABLE_PREFIX     // 'apollo_'
APOLLO_MIN_PHP          // '8.1'
APOLLO_MIN_WP           // '6.4'
```

## Hooks Pattern

Core fires: `do_action('apollo/core/initialized', $info)` — plugins hook into this.
Naming: `apollo/{plugin}/{action}` (e.g., `apollo/event/created`, `apollo/login/registered`).

## Reference Library

`_library/` contains **source material** for adapted code (BuddyPress, BadgeOS, UserSWP, Hide My WP, etc.). These are NOT active plugins — they are reference for building Apollo's native implementations. **Never install them directly.**

## Workflow Rules for AI Agents

1. **Read `_inventory/apollo-registry.json` FIRST** — comply with every definition
2. **NEVER create/invent** new CPTs, taxonomies, meta keys, or table names — use what's registered
3. **Search `_library/` reference code** before building — adapt, don't reinvent
4. **Connect and integrate** — plugins perform as one ecosystem via hooks and the registry
5. Use WordPress coding standards (tabs→4 spaces, snake_case functions, PHPDoc blocks)
6. Template types: **Canvas** (uses `wp_head`/`wp_footer`) vs **Blank Canvas** (zero theme — apollo-login pattern)
7. All REST routes under `apollo/v1` — check registry for exact endpoint definitions
