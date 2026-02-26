# APOLLO ECOSYSTEM — Plano de Unificação Total

**Data:** 25/02/2026
**Escopo:** Todos os 27+ plugins Apollo
**Objetivo:** Transformar plugins isolados em ecossistema coeso — rendering unificado, zero duplicidade, segurança blindada, inventory atualizado.

---

## - [x] WAVE 0 — KILL SECURITY VULNERABILITIES (2h)

Critério: nenhum código vai pro ar sem estas 7 ações completas.

- [x] **0.1** DELETAR `apollo-templates/templates/create-page.php` — JÁ NÃO EXISTE (skip).
- [x] **0.2** SANITIZAR rota `POST /shortcodes/render` em `apollo-shortcodes/includes/class-plugin.php` — whitelist de shortcodes Apollo permitidos (7 registrados). Rejeitar qualquer `tag` fora da whitelist com `WP_Error 403`. Sanitizar `content` com `wp_kses_post()`.
- [x] **0.3** SANITIZAR `$_SERVER['REQUEST_URI']` em `apollo-templates/includes/pages.php` — `sanitize_text_field(wp_unslash(...))`.
- [x] **0.4** FIXAR CPT slug em `apollo-shortcodes/includes/class-cena-rio-submissions.php` — `event_listing` → `APOLLO_CPT_EVENT` (`event`). Corrigido forbidden term `venue`→`loc`.
- [x] **0.5** ADICIONAR NONCE em `apollo-shortcodes/includes/class-interesse-ranking.php` — `check_ajax_referer('apollo_fav_nonce', 'nonce')`.
- [x] **0.6** SANITIZAR `$_POST['pass']` no apollo-templates — `wp_unslash()` + `sanitize_text_field()`.
- [x] **0.7** REMOVER rota `/registry` duplicada de `HealthController.php`. Em `SettingsController.php`, renomeado para `/admin/registry`.

---

## - [x] WAVE 1 — CRIAR O MOTOR DE RENDERING UNIFICADO (4h)

- [x] **1.1** CRIAR diretório `apollo-core/src/Traits/`
- [x] **1.2** CRIAR `apollo-core/src/Traits/BlankCanvasTrait.php` — trait com `render_blank_canvas()`, `render_blank_canvas_to_string()`, `get_apollo_cdn_url()`, `get_apollo_cdn_core_js()`, `blank_canvas_head()`, `blank_canvas_footer()`
- [x] **1.3** CRIAR `apollo-core/src/Traits/VirtualPageTrait.php` — trait com `init_virtual_pages()`, `vpt_register_query_vars()`, `vpt_parse_request()`, `vpt_handle_redirect()`, `vpt_pattern_to_regex()`, `vpt_add_rewrite_rules()`
- [x] **1.4** CRIAR `apollo-core/src/Traits/TemplateLocatorTrait.php` — trait com `init_locator()`, `locate()`, `render_located()`, `render_located_to_string()` (4-level fallback)
- [x] **1.5** Autoloader PSR-4 do `apollo-core` já mapeia `Apollo\Core\Traits\*` → `src/Traits/` automaticamente.

---

## - [x] WAVE 2 — MIGRAR PLUGINS PARA BLANK CANVAS TRAIT (8h)

### Plugins migrados para `BlankCanvasTrait`:

- [x] **2.7** `apollo-social` — `use BlankCanvasTrait;` + `render_blank_canvas()` no `handle_virtual_pages()`
- [x] **2.9** `apollo-groups` — `use BlankCanvasTrait;` + `render_blank_canvas()` no `handle_virtual_pages()`
- [x] **2.10** `apollo-chat` — `use BlankCanvasTrait;` + `render_blank_canvas()`
- [x] **2.11** `apollo-notif` — `use BlankCanvasTrait;` + `render_blank_canvas()`
- [x] **2.12** `apollo-dashboard` — `use BlankCanvasTrait;` + `render_blank_canvas()`
- [x] **2.13** `apollo-adverts` — `use BlankCanvasTrait;` + fix bug `$this->directory` → `$this->directory_path`
- [x] **2.14** `apollo-sign` — `use BlankCanvasTrait;` + `render_blank_canvas()` com `$signature` var
- [x] **2.15** `apollo-docs` — `use BlankCanvasTrait;` + fix redirect `wp_login_url()` → `/acesso`
- [x] **2.16** `apollo-events` — `use BlankCanvasTrait;` + `render_blank_canvas()`

### Plugins pendentes para futuras waves:

- [ ] **2.1** `apollo-login` — mover `template_include` P1 para `template_redirect` P1 com `BlankCanvas`.
- [ ] **2.2** `apollo-templates` — remover 4 filtros `template_include` (P96-99). Criar `template_redirect` P10 unificado.
- [ ] **2.3** `apollo-events/TemplateLoader` — TemplateLocatorTrait disponível para migração futura
- [ ] **2.4** `apollo-loc/TemplateLoader` — TemplateLocatorTrait disponível para migração futura
- [ ] **2.5** `apollo-classifieds` — migrar para `template_redirect`
- [ ] **2.6** `apollo-journal` — migrar para `template_redirect`
- [ ] **2.8** `apollo-users` — integrar trait
- [ ] **2.16** `apollo-hub` — integrar trait

---

## - [x] WAVE 3 — CPT `local` SLUG UNIFICATION (2h)

- [x] **3.1** ATUALIZAR `apollo-core/config/cpts.php` — `rewrite: 'gps'→'local'`, `archive: 'gps'→'local'`, `rest_base: 'locals'→'local'`
- [x] **3.2** ATUALIZAR `apollo-loc/src/Registry.php` — `has_archive: 'gps'→'local'`, `rewrite.slug: 'gps'→'local'`, `rest_base: 'locals'→'local'`
- [x] **3.3** ATUALIZAR `apollo-loc/src/API/LocalsController.php` — `$rest_base = 'local'`
- [x] **3.4** ATUALIZAR `_inventory/apollo-registry.json` — CPT rewrite/archive/rest_base, REST endpoints `/locals`→`/local`, pages `/gps`→`/local`, route ownership
- [x] **3.5** ATUALIZAR comentários PHPDoc referenciando `/gps` → `/local`

---

## - [x] WAVE 4 — PURGE TOTAL DE DUPLICIDADES (3h)

- [x] **4.1** DELETAR `apollo-templates/templates/auth/` inteiro — 7 arquivos duplicando apollo-login
- [x] **4.2** DELETAR navbars mortas: `navbar-old-backup.php`, `navbar.v1.php`. Mantido `navbar.v2.php` (ativo em produção) e `navbar.php` (canvas pages)
- [x] **4.3** DELETAR boilerplate WPPB de `apollo-core/includes/`: 5 arquivos `class-plugin-name-*`
- [x] **4.4** DELETAR `apollo-core/admin/class-apollo-core-admin.php` + CSS/JS dead code + `public/` inteiro
- [x] **4.5** CONSOLIDAR Activation — deletar `src/Activation.php` (dead wrapper), manter `src/Core/ActivationHandler.php`
- [x] **4.6** DEDUPLICAR REST `/shortcodes` — removido registro duplicado em `class-apollo-shortcode-registry.php`
- [x] **4.7** DELETAR diretórios vazios: `apollo-templates/docs/`, `apollo-templates/pages/`
- [x] **4.8** DELETAR assets vazios: `shortcodes.css`, `shortcodes.js` + removido enqueues e método `register_assets()`
- [x] **4.9** RESOLVER `/classificados` — deletar `page-classificados.php` e `create-page.php` do apollo-templates, limpar referências em `pages.php` e `apollo-templates.php`

---

## - [x] WAVE 5 — MIGRAR CÓDIGO ORPHAN DO APOLLO-SHORTCODES (6h)

- [x] **5.1** Newsletter → `apollo-email/src/Newsletter.php` — namespace `Apollo\Email`, init via `Plugin::init()`
- [x] **5.2** CENA-RIO → `apollo-events/src/CenaRio.php` — namespace `Apollo\Event`, init via `Plugin::init_components()`
- [x] **5.3** Global Search → `apollo-core/src/API/SearchController.php` — namespace `Apollo\Core\API`, extends `RestBase`, init via `rest_api_init`
- [x] **5.4** User Stats → `apollo-statistics/includes/class-user-stats-widget.php` — namespace `Apollo\Statistics`, init via bootstrap
- [x] **5.5** Interesse Ranking → `apollo-fav/includes/class-fav-ranking.php` — renomeado `Fav_Ranking`, namespace `Apollo\Fav`
- [x] **5.6** User Dashboard Interesse → `apollo-fav/includes/class-fav-sound-dashboard.php` — renomeado `Fav_Sound_Dashboard`, fix `event_listing` bug
- [x] **5.7** LIMPAR apollo-shortcodes — removidos 6 arquivos migrados + `register_search_routes` hook, restam 4 arquivos: registry, plugin, constants, functions

---

## - [x] WAVE 6 — GENOCÍDIO DE TERMOS PROIBIDOS (4h)

- [x] **6.1** `ri-heart-line→ri-fire-line` — 12 ocorrências corrigidas em 8 arquivos (apollo-fav, apollo-loc, apollo-notif, apollo-statistics, apollo-templates)
- [x] **6.2** `likes_received→wows_received` + `Likes→Wows` — corrigido em `class-user-stats-widget.php`
- [x] **6.3** `event_listing→event` — WP_Query em `events-listing.php`, taxonomias em `class-fav-ranking.php`
- [x] **6.4** `venue→loc` em form fields e meta reads — CenaRio.php, events-listing.php, greeting.php, classifieds.php, upcoming.php, admin content.php
- [x] **6.5** `interesse→fav` — shortcode `apollo_fav_dashboard` (com alias legado), textos UI corrigidos, whitelist atualizada
- [x] **6.6** `bookmark→fav` — descrições em `Registry.php`, `reactions.php`
- [ ] **6.7** CSS classes `.venue-*` — 90+ ocorrências em `apollo-loc` templates (reservado para wave frontend)
- [ ] **6.8** Meta keys registradas (`user_location`, `_classified_location`) — reservado (quebraria dados existentes)

---

## - [x] WAVE 7 — NAMESPACE + PSR-4 CLEANUP (3h)

- [x] **7.1** `apollo-shortcodes` — namespace `Apollo\Shortcode` → `Apollo\Shortcodes` (plural) em 7 arquivos
- [x] **7.2** `@package` headers — corrigidos 24 arquivos: `Apollo_Core→Apollo\Core`, `Apollo_Sign→Apollo\Sign`, `Apollo_Login→Apollo\Login`, `Apollo_Social→Apollo\Login/Templates`
- [x] **7.3** Plugins migrados (wave 5) — namespaces corretos verificados (`Apollo\Email`, `Apollo\Event`, `Apollo\Core\API`, `Apollo\Statistics`, `Apollo\Fav`)
- [x] **7.4** Autoloader do apollo-shortcodes atualizado: prefix `Apollo\Shortcodes\\` no PSR-4

---

## - [x] WAVE 8 — CDN STANDARDIZATION (2h) ✅ CLEAN PASS

- [x] **8.1** GREP `<script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>` em todos templates PHP — **ZERO hardcoded CDN encontrado em plugins ativos**
- [x] **8.2** Já usa `APOLLO_CDN_CORE_JS` constante em `BlankCanvasTrait` e `CDN.php` — nenhuma substituição necessária
- [x] **8.3** `APOLLO_CDN_CORE_JS` já aponta para `core.min.js` (produção)
- [x] **8.4** `CDN.php` injeta via `wp_head` priority 1 para Canvas pages; `BlankCanvasTrait` injeta direto — padrões complementares, ambos corretos

---

## - [x] WAVE 9 — FIX REMAINING CONFLICTS (3h) ✅ 6 FIXES

- [x] **9.1** Version checks — `PHP 7.4→8.1`, `WP 5.8→6.4` em `apollo-shortcodes/Activation.php` + `@package` fix
- [x] **9.2** Removido admin menu duplicado em apollo-shortcodes (`add_menu_page` + 3 métodos mortos, ~70 linhas)
- [x] **9.3** Audit log ATIVADO — removido `return;` de `apollo_log_audit()` + SQL injection fix com `$wpdb->prepare()`
- [x] **9.4** Guard `WPINC→ABSPATH` em `apollo-core.php` (linha 33)
- [x] **9.5** Removidos 2 blocos `eval()` de `apollo-users.php` (~120 linhas) — funções já existiam em `includes/functions.php`
- [x] **9.6** Removido hook `template_redirect` no-op de `mural-router.php` — `template_include` priority 99 é o mecanismo real

---

## - [x] WAVE 10 — ATUALIZAR TODOS OS ARQUIVOS `_inventory/` (4h) ✅ 4 FILES UPDATED

- [x] **10.1** `apollo-registry.json` — version 6.3.0→6.4.0, namespace fix, rotas migradas (search→core, cena-rio→events, newsletter→email), classificados removido de pages
- [x] **10.2** `pages-rest.json` — version/date bump, classificados removidos, gps→local em loc, rotas migradas movidas, shortcodes note atualizado, page-classificados.php removido
- [x] **10.3** `APOLLO_ALL_ROUTES.json` — /locs→/local (frontend+REST), /classificados removido, 14 rotas reatribuídas de apollo-shortcodes para apollo-core/events/email
- [x] **10.4** `pages-layout.json` — Tier 2 `/gps→/local`

---

## Verificação por Wave

| Wave | Check |
|------|-------|
| 0 | `create-page.php` não existe; shortcodes/render com tag malicioso = 403; AJAX sem nonce = 403; cena-rio cria CPT `event` |
| 1 | `BlankCanvas.php` e `VirtualPage.php` existem; `composer dump-autoload` sem erros |
| 2 | Grep `template_include` = ZERO em `apollo-*`; todas virtual pages = 200 blank canvas |
| 3 | `/local` = archive; `/local/{slug}` = single; `GET apollo/v1/local` = JSON; `/gps` = 404 |
| 4 | `auth/` não existe; grep `navbar-old-backup` = 0; grep `class-plugin-name` = 0 |
| 5 | apollo-shortcodes/includes = 4 arquivos; apollo-email responde `/newsletter/subscribe` |
| 6 | Grep `venue\|location\|interesse\|bookmark\|likes.*received` = ZERO |
| 7 | Grep `namespace Apollo_Core` fora do apollo-core = ZERO |
| 8 | Grep `cdn.apollo.rio.br` em templates = ZERO |
| 9 | Version checks = 8.1/6.4; `eval(` em apollo-users = ZERO; audit log grava |
| 10 | Registry JSON valida; todas rotas documentadas existem no código |

---

_Plano gerado em 25/02/2026 — 10 waves, 70+ ações, ~200 arquivos_
