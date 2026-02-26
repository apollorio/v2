# APOLLO ECOSYSTEM — Relatório de Auditoria Completa: Pages, Templates & Frontend Rendering

**Data:** 25/02/2026
**Escopo:** `apollo-core`, `apollo-templates`, `apollo-shortcodes`, `apollo-admin`
**Referência:** `_inventory/` (pages-layout.json, pages-rest.json, apollo-registry.json, SKELETON.md, FRONTEND_FORMS_AUDIT.md)
**Auditor:** GitHub Copilot (Claude Opus 4.6)

---

## PARTE 1 — COMO O SISTEMA IMPRIME PÁGINAS NO FRONTEND

### 1.1 Pipeline de Renderização (URL → Template)

O Apollo usa **dois mecanismos de WordPress** para servir páginas:

```
URL Request
    │
    ├─ init: Rewrite rules registradas por CADA plugin via add_rewrite_rule()
    │        Cada plugin registra suas próprias regras → query vars
    │
    ├─ template_redirect (executa PRIMEIRO — padrão include+exit)
    │   ├─ Prio 0:  apollo-seo (sitemap XML)
    │   ├─ Prio 1:  apollo-users (/id/{username}, /radar, /editar-perfil)
    │   ├─ Prio 5:  apollo-social (/explore), apollo-groups (/grupos, /comunas, /nucleos,
    │   │           /grupo/{slug}, /criar-grupo), apollo-events (/criar-evento),
    │   │           apollo-chat (/mensagens), apollo-notif (/notificacoes),
    │   │           apollo-dashboard (/painel), apollo-adverts (/novo-anuncio),
    │   │           apollo-templates/FrontendRouter (/editar/{cpt}/{id})
    │   └─ Default: apollo-docs (/documentos), apollo-sign (/assinar/{hash}),
    │               apollo-hub (/editar-hub)
    │
    ├─ template_include (SÓ executa se template_redirect NÃO fez exit)
    │   ├─ Prio 0:  apollo-login/disable-conflicts (limpa filtros)
    │   ├─ Prio 1:  apollo-login (/acesso, /registre, /reset, /verificar-email, /sair)
    │   ├─ Prio 10: apollo-events/TemplateLoader (single-event, archive-event)
    │   ├─ Prio 10: apollo-classifieds (/marketplace)
    │   ├─ Prio 10: apollo-journal (archives de categorias/taxonomias)
    │   ├─ Prio 96: apollo-templates (/mapa)
    │   ├─ Prio 97: apollo-templates (front page guest → page-home.php)
    │   ├─ Prio 98: apollo-templates (page templates WP dropdown)
    │   └─ Prio 99: apollo-templates/pages.php (/home, /sobre, /test, /classificados)
    │               + mural-router.php (front page logged → page-mural.php)
    │
    └─ WordPress carrega o template PHP selecionado
```

### 1.2 Dois Padrões Conflitantes de Template Loading

| Padrão | Plugins que usam | Mecanismo |
|--------|-----------------|-----------|
| **`include + exit`** (via `template_redirect`) | apollo-users, apollo-social, apollo-groups, apollo-chat, apollo-notif, apollo-dashboard, apollo-docs, apollo-sign, apollo-hub, apollo-adverts, apollo-events (create) | Intercepta request, faz `include $template; exit;` — bypassa pipeline WP |
| **`return $path`** (via `template_include`) | apollo-login, apollo-templates, apollo-events/TemplateLoader, apollo-classifieds, apollo-journal | Retorna path do template — WP processa normalmente com `wp_head`/`wp_footer` |

**Consequência:** Plugins que usam `template_redirect + exit` NÃO passam pelo filtro `template_include`, então hooks como `wp_head`/`wp_footer` podem não executar dependendo de como o template inclui os headers.

### 1.3 Tier System (pages-layout.json)

| Tier | Descrição | Páginas |
|------|-----------|---------|
| **Tier 1** | Fullscreen panels — slider engine dual-state | `/` (home guest), `/explore` (logged) |
| **Tier 2** | Single panel + navbar + FAB | `/id/{username}`, `/mensagens`, `/notificacoes`, `/documentos`, `/djs`, `/gps`, `/grupos`, `/sobre`, `/radar`, `/conquistas`, `/placar`, `/mapa` |
| **Tier 3** | Blank Canvas — zero theme | `/acesso`, `/registre`, `/assinar/{hash}` |

### 1.4 Mapa Completo de Rewrites por Plugin

| Plugin | URL Pattern | Query Var | Template |
|--------|-------------|-----------|----------|
| **apollo-login** | `^acesso/?$` | `apollo_login_page=login` | login.php (blank canvas) |
| **apollo-login** | `^registre/?$` | `apollo_login_page=register` | register.php (blank canvas) |
| **apollo-login** | `^reset/?$` | `apollo_login_page=reset` | reset.php |
| **apollo-login** | `^verificar-email/?$` | `apollo_login_page=verify-email` | verify-email.php |
| **apollo-login** | `^sair/?$` | `apollo_login_page=logout` | redirect (logout) |
| **apollo-users** | `^id/([^/]+)/?$` | `apollo_user_page=profile` | single-profile.php |
| **apollo-users** | `^radar/?$` | `apollo_user_page=radar` | user-radar.php |
| **apollo-users** | `^editar-perfil/?$` | `apollo_user_page=edit-profile` | edit-profile.php |
| **apollo-templates** | `^home/?$` | `apollo_home_page=1` | page-home / page-mural |
| **apollo-templates** | `^sobre/?$` | `apollo_sobre_page=1` | page-sobre.php |
| **apollo-templates** | `^about-us/?$` | `apollo_about_redirect=1` | 301 → /sobre |
| **apollo-templates** | `^test/?$` | `apollo_test_page=1` | page-test.php |
| **apollo-templates** | `^classificados/?$` | `pagename=classificados` | page-classificados.php |
| **apollo-templates** | `^classificados/novo/?$` | `pagename=classificados&action=new` | page-classificados.php |
| **apollo-templates** | `^editar/(.+)/([0-9]+)/?$` | `apollo_edit_cpt + apollo_edit_id` | edit-post.php |
| **apollo-social** | `^explore/?$` | `apollo_social_page=explore` | explore.php |
| **apollo-social** | `^mural/?$` | 301 redirect → /explore | — |
| **apollo-social** | `^feed/?$` | 301 redirect → /explore | — |
| **apollo-groups** | `^grupos/?$` | `apollo_groups_page=directory` | groups.php |
| **apollo-groups** | `^comunas/?$` | `apollo_groups_page=comunas` | comunas.php |
| **apollo-groups** | `^nucleos/?$` | `apollo_groups_page=nucleos` | nucleos.php |
| **apollo-groups** | `^grupo/([^/]+)/?$` | `apollo_groups_page=single` | single-group.php |
| **apollo-groups** | `^criar-grupo/?$` | `apollo_groups_page=create` | create-group.php |
| **apollo-events** | `^novo-evento/?$` | `apollo_event_page=create` | create-event.php |
| **apollo-events** | `^criar-evento/?$` | `apollo_event_page=create` | create-event.php |
| **apollo-chat** | `^mensagens/?$` | `apollo_chat_page=inbox` | chat.php |
| **apollo-chat** | `^mensagens/(\d+)/?$` | `apollo_chat_page=thread` | chat.php |
| **apollo-notif** | `^notificacoes/?$` | `apollo_notif_page=notifications` | notifications.php |
| **apollo-dashboard** | `^painel/?$` | `apollo_dashboard_page=dashboard` | dashboard.php |
| **apollo-dashboard** | `^painel/{tab}/?$` | `apollo_dashboard_page=dashboard&tab=` | dashboard.php |
| **apollo-adverts** | `^novo-anuncio/?$` | `apollo_adverts_page=create` | form.php |
| **apollo-hub** | `^editar-hub/?$` | `apollo_hub_edit=1` | edit-hub.php |
| **apollo-docs** | `^documentos/?$` | `apollo_docs_page=1` | frontend-documents.php |
| **apollo-sign** | `^assinar/([a-f0-9]{64})/?$` | `apollo_sign_hash=` | sign.php |

---

## PARTE 2 — PAPEL DE CADA PLUGIN AUDITADO

### 2.1 apollo-core (L0 — Foundation)

**Papel:** Registry backend puro. NÃO registra templates ou rewrite rules para páginas frontend.

**O que FAZ:**
- Define e registra fallback para 9 CPTs + 14 taxonomias com pattern GLOBAL BRIDGE
- Registra 80+ meta keys (MetaRegistry) + cria 22+ tabelas DB (DatabaseBuilder)
- Injeta CDN script em wp_head (priority 1) via CDN.php
- Expõe REST API: `/health`, `/registry/*`, `/sounds/*`
- Fornece RestBase abstrato, helpers globais, componente report-modal, composite search

**O que NÃO faz:** Nenhum `add_rewrite_rule`, `template_include`, `template_redirect`, nenhum diretório `templates/`.

**Árvore-chave:**
```
apollo-core/
├── apollo-core.php         # Bootstrap: autoloader, activation, CDN, REST, hooks
├── config/                 # 9 config files: constants, cpts, hooks, meta, options,
│                             roles, routes, tables, taxonomies
├── src/
│   ├── API/                # RestBase, HealthController, RegistryController, SoundController
│   ├── Config/             # ApolloCPT, ApolloHook, ApolloMeta, ApolloRoute, ApolloTable,
│   │                         ApolloTax, ConfigLoader
│   └── Core/               # CDN, CPTRegistry, TaxonomyRegistry, MetaRegistry,
│                             DatabaseBuilder, Registry, ActivationHandler
├── includes/               # functions.php, report-modal.php, search-helpers.php,
│                             + 5 classes WPPB legadas (dead code)
└── admin/                  # SettingsPage + Apollo_Core_Admin legado (dead code)
```

### 2.2 apollo-templates (L6 — Frontend)

**Papel:** Page builder, template loader, frontend editor, navbar e home/mural rendering.

**O que FAZ:**
- Registra 6 rewrite rules (classificados, classificados/novo, home, sobre, about-us, test)
- FrontendRouter registra rewrites para `/editar/{cpt}/{id}`
- 5 filtros `template_include` em prioridades 96-99
- Contém 5+ templates completos: page-home, page-mural, page-sobre, page-classificados, page-mapa, page-test, edit-post
- Template parts organizados em: auth/, home/, new-home/, mural/, template-parts/
- Navbar principal (navbar.v1.php) + menu FAB + weather helpers
- REST: `/templates`, `/templates/calendars`, `/canvas/save`, `/canvas/blocks`

**Árvore-chave:**
```
apollo-templates/
├── apollo-templates.php        # Bootstrap + 4 template_include filters (prio 96-98)
├── src/
│   ├── Plugin.php              # REST route registration
│   └── FrontendRouter.php      # /editar/{cpt}/{id} rewrite + template_redirect
├── includes/
│   ├── pages.php               # Rewrite rules + template_include prio 99
│   ├── mural-router.php        # Front page logged → page-mural.php (prio 99)
│   ├── class-navbar-settings.php
│   └── weather-helpers.php
└── templates/
    ├── page-home.php, page-mural.php, page-sobre.php,
    │   page-classificados.php, page-mapa.php, page-test.php, edit-post.php
    ├── auth/                   # Duplicatas do apollo-login!
    ├── event/                  # listing.php, card-style-01.php
    └── template-parts/
        ├── navbar.v1.php       # Navbar ativa
        ├── navbar-old-backup.php (dead)
        ├── new-home/           # 12 partes (hero, events, tracks, etc.)
        ├── home/               # 9 partes (versão anterior)
        └── mural/              # 8 partes (greeting, weather, upcoming, etc.)
```

### 2.3 apollo-shortcodes (L6 — Frontend)

**Papel:** Registro global de shortcodes + funcionalidades mistas (6 features de outros plugins).

**O que FAZ:**
- 5 shortcodes executáveis: `apollo_newsletter`, `apollo_cena_submit_event`, `apollo_top_sounds`, `apollo_interesse_dashboard`, `apollo_user_stats`
- 24 shortcodes no registry apenas como documentação (sem callbacks funcionais)
- REST: `/shortcodes`, `/shortcodes/{tag}`, `/shortcodes/render`, `/search/*` (8 endpoints), `/newsletter/*`, `/cena-rio/*`
- Não registra rewrites nem templates

**Árvore-chave:**
```
apollo-shortcodes/
├── apollo-shortcodes.php           # Bootstrap + constants
├── src/Plugin.php                  # Proxy → includes/class-plugin.php
├── includes/
│   ├── class-plugin.php            # REST + admin menu + shortcode funcs
│   ├── class-apollo-shortcode-registry.php  # 24 shortcodes doc + REST duplicada!
│   ├── class-apollo-search-controller.php   # /search/* (8 endpoints)
│   ├── class-apollo-native-newsletter.php   # Newsletter completa (1415 linhas!)
│   ├── class-cena-rio-submissions.php       # CENA-RIO (745 linhas)
│   ├── class-interesse-ranking.php          # Ranking de "interesses" (fav!)
│   ├── class-user-dashboard-interesse.php   # Dashboard de "interesses"
│   └── class-user-stats-widget.php          # Stats com "likes" (wow!)
└── assets/
    ├── css/apollo-search.css
    └── js/apollo-search.js
```

### 2.4 apollo-admin (L7 — Admin)

**Papel:** CPanel administrativo unificado (SPA com tabs).

**O que FAZ:**
- 1 menu page WP-admin (`apollo` com ícone dashicons-superhero-alt)
- REST: `/settings`, `/settings/{slug}`, `/settings/{slug}/schema`, `/registry`, `/registry/refresh`, `/settings/export`, `/settings/import` + extras não documentados (`/toolbar`, `/errorlog`, `/preferences`)
- Template `dashboard.php` com 40+ seções parciais em 6 grupos (system, identity, events, social, email, admin)
- Registry de 27 plugins hardcoded com info de status, version, admin_url

**Árvore-chave:**
```
apollo-admin/
├── apollo-admin.php
├── src/
│   ├── Plugin.php              # Bootstrap + enqueue
│   ├── AdminPage.php           # Menu registration
│   ├── Registry.php            # 27 plugins hardcoded info
│   ├── Settings.php            # Options CRUD
│   └── Rest/SettingsController.php  # 7+ REST endpoints
└── templates/
    ├── dashboard.php           # SPA CPanel (1 template)
    └── partials/
        ├── topbar.php, sidebar.php, footer.php, styles.php
        └── sections/           # 40 seções em 6 grupos
            ├── admin/          # overview, users, moderate, membership, notify, spreadsheet
            ├── email/          # settings, templates, subscribers, logger, stats, rss, tools, workflows, unsub
            ├── events/         # general, calendar, single, maps, social, theme, repeat, custom, eventcard, eventtop
            ├── identity/       # login, users, membership
            ├── social/         # activity, chat, groups, notif, reactions
            └── system/         # core, security, seo, templates, pwa, stats, content
```

---

## PARTE 3 — TODOS OS PROBLEMAS E ERROS ENCONTRADOS

### 🔴 CRÍTICOS (Bloqueiam deploy / Vulnerabilidades de segurança)

| # | Plugin | Problema | Arquivo | Impacto |
|---|--------|----------|---------|---------|
| C1 | **apollo-templates** | `create-page.php` acessível publicamente sem auth, executa `wp_insert_post()` via `require wp-load.php` | templates/create-page.php | **Qualquer um pode criar pages no WP** |
| C2 | **apollo-shortcodes** | REST `POST /shortcodes/render` executa `do_shortcode()` arbitrário sem sanitização — aceita qualquer shortcode do sistema | includes/class-plugin.php | **RCE potencial via shortcodes maliciosos** |
| C3 | **apollo-shortcodes** | Rotas REST `/shortcodes` e `/shortcodes/{tag}` registradas DUAS vezes (Plugin + Registry) com formatos diferentes | class-plugin.php + class-apollo-shortcode-registry.php | **Conflito REST — resposta imprevisível** |
| C4 | **apollo-admin** + **apollo-core** | Rota REST `/registry` registrada por AMBOS plugins com permissões diferentes (`admin` vs `__return_true`) | HealthController + SettingsController | **Dados admin expostos publicamente** |
| C5 | **apollo-shortcodes** | CPT slug `event_listing` usado em Cena-Rio Submissions vs `event` no registry | class-cena-rio-submissions.php | **Posts criados com CPT errado — não aparecem nos queries** |
| C6 | **apollo-core** | `composer.json` PSR-4 mapping `"Apollo\\Core\\": "src/Core/"` está ERRADO — deveria ser `"Apollo\\Core\\": "src/"` | composer.json | **Classes fora de Core/ não carregam via Composer** |
| C7 | **apollo-templates** | `$_SERVER['REQUEST_URI']` não sanitizado em routing | includes/pages.php | **Possível injection no routing path** |

### 🟡 ALTOS (Funcionalidade quebrada / Non-compliance com registry)

| # | Plugin | Problema | Arquivo | Impacto |
|---|--------|----------|---------|---------|
| H1 | **apollo-templates** | Front page logged exibe `page-mural.php`, mas `pages-layout.json` especifica que o center panel logged é `explore` | includes/mural-router.php | **Divergência spec vs implementação** |
| H2 | **apollo-templates** | 3 filtros `template_include` na prioridade 99 (pages.php + mural-router.php) — ordem depende do `require_once` | apollo-templates.php | **Resultado imprevisível** |
| H3 | **apollo-templates** | Label "Locais" e URL `/locais` nos defaults da navbar — TERMO PROIBIDO (deveria ser `loc`/`gps`) | includes/class-navbar-settings.php | **Violação de naming rules** |
| H4 | **apollo-shortcodes** | 6 de 10 classes usam namespace `Apollo_Core` ou global — deveria ser `Apollo\Shortcode` | includes/*.php | **Conflito de namespace com apollo-core** |
| H5 | **apollo-shortcodes** | AJAX handler `ajax_user_sounds` em `class-interesse-ranking.php` sem `check_ajax_referer()` | class-interesse-ranking.php | **Violação NON-NEGOTIABLE de segurança** |
| H6 | **apollo-templates** | Templates `event/calendar.php` e `event/form.php` referenciados por shortcodes mas NÃO existem | templates/event/ | **Shortcodes renderizam vazio** |
| H7 | **apollo-templates** | `apollo_suggest_event` AJAX handler que cria posts `event` — deveria estar em `apollo-events` | templates/template-parts/new-home/ | **Responsabilidade fora do plugin** |
| H8 | **Pipeline** | Dois padrões de rendering (`template_redirect+exit` vs `template_include+return`) causam inconsistência: plugins que usam exit() bypassam `wp_head`/`wp_footer` se o template não os inclui | Múltiplos plugins | **Headers/footers inconsistentes** |
| H9 | **apollo-core** | `apollo_log_audit()` desabilitada com `return;` — sistema de auditoria inoperante | includes/functions.php | **Zero audit trail** |
| H10 | **apollo-admin** | Registry hardcoded de 27 plugins diverge do workspace real e do apollo-registry.json | src/Registry.php | **Dashboard mostra info incorreta** |

### 🟠 MÉDIOS (Termos proibidos / Duplicidades / Code smells)

| # | Plugin | Problema | Detalhes |
|---|--------|----------|---------|
| M1 | **apollo-shortcodes** | Termo proibido `venue` — 6 ocorrências em class-cena-rio-submissions.php | `'venue' =>`, `name="event_venue"`, `_event_venue_name` |
| M2 | **apollo-shortcodes** | Termo proibido `local` — 4 ocorrências | `event_local`, `_event_local_name`, label "Local do Evento" |
| M3 | **apollo-shortcodes** | Termo proibido `interesse`/`interest` — nomes de classes inteiros | class-interesse-ranking.php, class-user-dashboard-interesse.php, meta keys `_user_interested_events` |
| M4 | **apollo-shortcodes** | Termo proibido `likes`/`heart` — em user stats widget | `ri-heart-line`, `$stats['likes_received']`, label "Likes" |
| M5 | **apollo-admin** | ~45+ ocorrências de termos proibidos: "location", "comment", "bookmark", "like", "venue", "heart" | Espalhados nos 40 partials de seções |
| M6 | **apollo-templates** | Meta key `user_location` com termo proibido | Navbar settings |
| M7 | **apollo-templates** + **apollo-classifieds** | `/classificados` (apollo-templates) vs `/marketplace` (apollo-classifieds) — dois templates para conceito similar | page-classificados.php vs classifieds-page.php |
| M8 | **apollo-templates** | 4 versões de navbar (navbar.v1.php, navbar-old-backup.php, new-home/navbar.php, home/ parts) — 3 são dead code | templates/template-parts/ |
| M9 | **apollo-templates** | Templates `auth/` duplicam funcionalidade do apollo-login (login-form, register-form, header, footer, quiz) | templates/auth/ |
| M10 | **apollo-shortcodes** | Newsletter completa (1415 linhas! DB tables, campaigns, subscribers, admin) deveria estar em `apollo-email` | class-apollo-native-newsletter.php |
| M11 | **apollo-shortcodes** | Global search API (8 endpoints REST) deveria estar em `apollo-core` | class-apollo-search-controller.php |
| M12 | **apollo-shortcodes** | CENA-RIO submissions deveria estar em `apollo-events` ou `apollo-cult` | class-cena-rio-submissions.php |
| M13 | **apollo-shortcodes** | User stats widget deveria estar em `apollo-statistics` | class-user-stats-widget.php |
| M14 | **apollo-shortcodes** | Interesse ranking deveria estar em `apollo-fav` | class-interesse-ranking.php |
| M15 | **apollo-shortcodes** | Newsletter subscribe REST sem rate limiting — abuso por bots | class-apollo-native-newsletter.php |
| M16 | **apollo-core** | Classes `Plugin_Name` (boilerplate WPPB) + `Apollo_Core_Admin` nunca instanciadas — código morto que registra menus admin duplicados | includes/ + admin/ |
| M17 | **apollo-core** | `ActivationHandler` verifica PHP 7.4 + WP 5.8, mas sistema requer PHP 8.1 + WP 6.4 | src/Activation.php |
| M18 | **apollo-shortcodes** | Mesma inconsistência de version check: PHP 7.4 / WP 5.8 no código, 8.1 / 6.4 no header | src/Activation.php |
| M19 | **apollo-core** | Dois `Activation.php` em paths diferentes (`src/` e `src/Core/`) — confusão de namespace | src/ |
| M20 | **apollo-core** | `HealthController` E `RegistryController` ambos registram `GET /registry` | src/API/ |
| M21 | **CDN** | Inconsistência de carregamento: `core.js` vs `core.min.js`, com/sem query string `?v=1.0.0` | Múltiplos plugins |

### 🔵 BAIXOS (Debt técnico / Melhorias)

| # | Plugin | Problema |
|---|--------|----------|
| L1 | **apollo-shortcodes** | Text domain errado em 5 classes (`apollo-core` em vez de `apollo-shortcodes`) |
| L2 | **apollo-shortcodes** | Assets vazios (shortcodes.css + shortcodes.js) enfileirados sem conteúdo |
| L3 | **apollo-shortcodes** | Admin menu duplicado: Plugin + Registry ambos registram páginas admin |
| L4 | **apollo-templates** | `@package Apollo_Core` errado em page-sobre.php (deveria ser `Apollo_Templates`) |
| L5 | **apollo-templates** | Diretórios vazios: docs/, pages/ |
| L6 | **apollo-templates** | Arquivo PHP dentro de assets/js/ |
| L7 | **apollo-admin** | composer.json com placeholders genéricos nunca preenchidos |
| L8 | **apollo-admin** | Assets CSS/JS existem mas não são carregados (tudo inline no dashboard) |
| L9 | **apollo-admin** | Import de settings sem validação de schema |
| L10 | **apollo-admin** | 4 rotas REST não documentadas no pages-rest.json: `/toolbar`, `/errorlog`, `/preferences`, `/preferences/{key}` |
| L11 | **apollo-admin** | 7 submenus admin definidos no registry mas não implementados (CPanel é SPA) |
| L12 | **apollo-core** | Guard `WPINC` em vez de `ABSPATH` no header — inconsistente com ecossistema |
| L13 | **apollo-templates** | `$_POST['pass']` sem `wp_unslash()` |

---

## PARTE 4 — DIVERGÊNCIAS ENTRE INVENTORY E CÓDIGO REAL

### 4.1 pages-rest.json vs Código Real

| Divergência | Fonte diz | Código Real faz |
|------------|-----------|-----------------|
| `/locs` como URL de archive | APOLLO_ALL_ROUTES.json | CPT `local` rewrite usa `/gps` (correto); `/locs` aparece só no REST |
| `/loc/{slug}` como URL single | APOLLO_ALL_ROUTES.json | CPT `local` single usa `/gps/{slug}` |
| apollo-core pages (classificados, editar/{cpt}) | pages-rest.json diz "apollo-core" | Código real: implementado por **apollo-templates** (pages.php + FrontendRouter) |
| `/feed` como página standalone | APOLLO_ALL_ROUTES.json | É 301 redirect para `/explore` (não página) |
| `/mural` como página standalone | APOLLO_ALL_ROUTES.json | É 301 redirect para `/explore` (não página) |
| mural-router logged = explore | pages-layout.json: `"center": "explore"` | Código serve `page-mural.php` (não explore.php) |
| apollo-shortcodes: `/search/*` 8 endpoints | pages-rest.json atribui a apollo-shortcodes | Correto mas deveria ser apollo-core por responsabilidade |

### 4.2 apollo-registry.json vs Código Real

| Divergência | Registry diz | Código Real |
|------------|-------------|-------------|
| apollo-shortcodes shortcodes | Não lista nenhum | 5 executáveis + 24 documentais |
| apollo-wow: `apollo_wow_chart` | NÃO no registry | Implementado em Plugin.php L147 |
| apollo-mod: `/mod/report` | NÃO no registry | Implementado em Plugin.php L119 |
| apollo-coauthor: `/coauthors/search` | NÃO no registry | Implementado |
| apollo-admin: `/toolbar`, `/errorlog`, `/preferences` | NÃO no registry | Implementados |
| apollo-statistics: `/stats/health`, `/stats/trend`, `/stats/chart` | NÃO no registry | Implementados |
| apollo-sign: `signature-pad.php`, `admin-signatures.php` | NÃO no registry | Implementados |

---

## PARTE 5 — SUGESTÕES DE FIX INTELIGENTE

### 🚨 Fixes URGENTES (Pré-deploy)

| # | Fix | Como resolver | Prioridade |
|---|-----|---------------|------------|
| F1 | **DELETAR `create-page.php`** | Remover `apollo-templates/templates/create-page.php` — vulnerabilidade de criação de páginas sem auth | IMEDIATO |
| F2 | **Sanitizar shortcodes/render** | Em `apollo-shortcodes/includes/class-plugin.php`, adicionar whitelist de shortcodes permitidos em `rest_render_shortcode()` — bloquear execução de shortcodes arbitrários | IMEDIATO |
| F3 | **Deduplicar rota /shortcodes** | Remover registro duplicado em `class-apollo-shortcode-registry.php` — manter apenas o de `class-plugin.php` | IMEDIATO |
| F4 | **Resolver conflito /registry** | Em `apollo-core/src/API/HealthController.php`, remover o registro de `/registry` — manter APENAS o de `RegistryController.php`. Em apollo-admin, garantir que usa rota diferente (ex: `/admin/registry`) | IMEDIATO |
| F5 | **Corrigir CPT slug cena-rio** | Em `class-cena-rio-submissions.php`, trocar `event_listing` por `event` (constante `APOLLO_CPT_EVENT`) | IMEDIATO |
| F6 | **Adicionar nonce no AJAX** | Em `class-interesse-ranking.php`, adicionar `check_ajax_referer('apollo_interesse_nonce')` no handler `ajax_user_sounds` | IMEDIATO |
| F7 | **Sanitizar REQUEST_URI** | Em `apollo-templates/includes/pages.php`, usar `sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']))` | IMEDIATO |

### 🔧 Fixes ESTRUTURAIS (Sprint de refactor)

| # | Fix | Como resolver | Esforço |
|---|-----|---------------|---------|
| F8 | **Unificar padrão de rendering** | Todos os plugins devem usar `template_include` com `return $path` em vez de `template_redirect + exit`. Criar trait `Apollo\Core\Traits\TemplateLoader` com helper `serve_template($path)` que seta `is_404=false`, `status_header(200)` e retorna o path | MÉDIO |
| F9 | **Resolver dualidade mural/explore** | Opção A: `mural-router.php` serve `explore.php` (de apollo-social) em vez de `page-mural.php`. Opção B: Unificar `page-mural.php` e `explore.php` em um único template. Alinhar com pages-layout.json | MÉDIO |
| F10 | **Mover responsabilidades do apollo-shortcodes** | Newsletter → apollo-email; CENA-RIO → apollo-cult; Global search → apollo-core; User stats → apollo-statistics; Interesse ranking → apollo-fav. Manter em apollo-shortcodes APENAS: registry, /shortcodes REST, shortcode render | ALTO |
| F11 | **Corrigir namespaces** | Todas as 6 classes em `includes/` de apollo-shortcodes: mudar de `Apollo_Core` para `Apollo\Shortcode\` e converter para PSR-4 | MÉDIO |
| F12 | **Eliminar termos proibidos** | Buscar e substituir em TODOS os plugins: `venue → loc`, `local → loc` (em meta keys/labels), `interesse → fav`, `likes → wows`, `heart → wow`, `bookmark → fav`, `comment → depoimento` (em labels), `location → loc` | ALTO |
| F13 | **Limpar dead code do apollo-core** | Remover: `includes/class-plugin-name.php`, `class-plugin-name-activator.php`, `class-plugin-name-deactivator.php`, `class-plugin-name-i18n.php`, `class-plugin-name-loader.php`, `admin/class-apollo-core-admin.php`. Consolidar `src/Activation.php` com `src/Core/Activation.php` | BAIXO |
| F14 | **Corrigir composer.json** | Em apollo-core: mudar `"Apollo\\Core\\": "src/Core/"` para `"Apollo\\Core\\": "src/"` | BAIXO |
| F15 | **Corrigir version checks** | Em apollo-core e apollo-shortcodes: mudar `PHP 7.4 + WP 5.8` para `PHP 8.1 + WP 6.4` nos Activation.php | BAIXO |
| F16 | **Eliminar duplicata auth templates** | Remover `apollo-templates/templates/auth/` — funcionalidade pertence exclusivamente a `apollo-login` | BAIXO |
| F17 | **Eliminar navbars mortas** | Manter apenas `navbar.v1.php`. Remover: `navbar-old-backup.php`, navbars redundantes em home/ e new-home/ | BAIXO |
| F18 | **Padronizar CDN** | Definir constante única `APOLLO_CDN_CORE_JS` em `apollo-core/config/constants.php`. Todos os templates devem usar `<?php echo esc_url(APOLLO_CDN_CORE_JS); ?>` — nunca hardcoded. Decidir: `core.js` ou `core.min.js` (usar `core.min.js` em produção) | BAIXO |
| F19 | **Resolver /classificados vs /marketplace** | Decidir: unificar em um único URL + template ou manter ambos com propósitos distintos. Se unificar, `apollo-classifieds/classifieds-page.php` serve `/classificados` e remove `page-classificados.php` do apollo-templates | MÉDIO |
| F20 | **Atualizar apollo-registry.json** | Adicionar todas as divergências listadas na Parte 4.2 — shortcodes de apollo-shortcodes, rotas extras de apollo-admin/mod/wow/coauthor/statistics, templates extras de apollo-sign | BAIXO |
| F21 | **Ativar audit log** | Em `apollo-core/includes/functions.php`, remover o `return;` de `apollo_log_audit()` e implementar o write na tabela `apollo_audit_log` | MÉDIO |
| F22 | **Documentar rotas REST extras** | Adicionar no pages-rest.json: `/toolbar`, `/errorlog`, `/preferences`, `/preferences/{key}` (apollo-admin), e as rotas de `/stats/health|trend|chart` (apollo-statistics) | BAIXO |

### 📋 Ordem Recomendada de Execução

```
Sprint 0 — EMERGÊNCIA (1 dia)
├─ F1: Deletar create-page.php
├─ F2: Sanitizar shortcodes/render
├─ F5: Corrigir CPT slug cena-rio
├─ F6: Adicionar nonce AJAX
└─ F7: Sanitizar REQUEST_URI

Sprint 1 — CONFLITOS (2-3 dias)
├─ F3: Deduplicar rota /shortcodes
├─ F4: Resolver conflito /registry
├─ F14: Corrigir composer.json
├─ F15: Corrigir version checks
└─ F18: Padronizar CDN

Sprint 2 — REFACTOR NAMING (3-5 dias)
├─ F12: Eliminar termos proibidos
├─ F11: Corrigir namespaces
├─ F13: Limpar dead code
├─ F16: Eliminar auth templates duplicados
└─ F17: Eliminar navbars mortas

Sprint 3 — ARQUITETURA (5-7 dias)
├─ F8: Unificar padrão de rendering
├─ F9: Resolver dualidade mural/explore
├─ F10: Mover responsabilidades do shortcodes
├─ F19: Resolver /classificados vs /marketplace
└─ F21: Ativar audit log

Sprint 4 — DOCUMENTAÇÃO (1 dia)
├─ F20: Atualizar apollo-registry.json
└─ F22: Documentar rotas REST extras
```

---

## PARTE 6 — RESUMO EXECUTIVO

| Métrica | Valor |
|---------|-------|
| Problemas CRÍTICOS | **7** |
| Problemas ALTOS | **10** |
| Problemas MÉDIOS | **21** |
| Problemas BAIXOS | **13** |
| **Total de issues** | **51** |
| Termos proibidos encontrados | **55+ ocorrências em 3 plugins** |
| Rotas REST conflitantes | **3 rotas (registry, shortcodes, shortcodes/{tag})** |
| Templates missing referenciados | **2 (event/calendar.php, event/form.php)** |
| Dead code files | **6+ arquivos no apollo-core + 3 navbars mortas** |
| Divergências registry vs código | **12 documentadas** |
| Plugins com responsabilidades fora do escopo | **1 (apollo-shortcodes contém 6 features de outros plugins)** |

**Veredicto:** O sistema NÃO está pronto para deploy. Os 7 issues críticos (C1-C7) devem ser resolvidos ANTES de qualquer release. O Sprint 0 (1 dia) resolve as vulnerabilidades de segurança. Sprint 1 (2-3 dias) resolve os conflitos REST. Após esses dois sprints, o sistema pode ir para staging com os issues médios/baixos em backlog.

---

_Gerado em 25/02/2026 — Auditoria completa de 4 plugins + cross-reference com 8 arquivos de inventory_
