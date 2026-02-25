# 🏗️ APOLLO ULTRA-MODULAR SKELETON

> **30 Plugins (workspace atual)** | Cada um = UMA responsabilidade | Conectados via apollo-core

## 🧩 WORKSPACE SNAPSHOT (SYNC)

Plugins atualmente presentes em `wp-content/plugins` (pastas `apollo-*`):

`apollo-admin`, `apollo-adverts`, `apollo-chat`, `apollo-classifieds`, `apollo-coauthor`, `apollo-comment`, `apollo-core`, `apollo-dashboard`, `apollo-djs`, `apollo-docs`, `apollo-email`, `apollo-events`, `apollo-fav`, `apollo-gestor`, `apollo-groups`, `apollo-loc`, `apollo-login`, `apollo-membership`, `apollo-mod`, `apollo-notif`, `apollo-runtime`, `apollo-seo`, `apollo-sheets`, `apollo-shortcodes`, `apollo-sign`, `apollo-social`, `apollo-statistics`, `apollo-templates`, `apollo-users`, `apollo-wow`

---

## 📦 ARQUITETURA DE CAMADAS

```
L0 FOUNDATION ─────────────────────────────────────────────
│
├── apollo-core/                    # REQUIRED - Base de tudo
│   ├── apollo-core.php
│   ├── includes/
│   │   ├── class-loader.php        # Autoloader PSR-4
│   │   ├── class-bridge.php        # Inter-plugin communication
│   │   ├── class-identifiers.php   # Registry reader
│   │   └── class-utilities.php     # Helpers
│   ├── src/
│   │   ├── Hooks/                  # Sistema de hooks
│   │   ├── API/                    # Base REST
│   │   └── Utilities/              # Funções comuns
│   └── assets/
│       └── shared/                 # CSS/JS compartilhado
│
L1 AUTH ───────────────────────────────────────────────────
│
├── apollo-login/                   # Login + Register + Games
│   ├── apollo-login.php
│   ├── includes/
│   │   ├── class-auth.php
│   │   ├── class-register.php
│   │   ├── class-miniquiz.php      # Quiz de ética/respeito
│   │   └── class-minisimon.php     # Jogo Simon
│   ├── templates/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── quiz-form.php
│   └── assets/
│       ├── css/
│       └── js/
│           └── simon-game.js
│
├── apollo-users/                   # Roles + Profile (/id/username)
│   ├── apollo-users.php
│   ├── includes/
│   │   ├── class-roles.php         # 5 WP roles only
│   │   ├── class-capabilities.php
│   │   ├── class-profile.php       # /id/username
│   │   ├── class-preferences.php
│   │   └── class-matchmaking.php
│   ├── templates/
│   │   └── profile/
│   │       ├── single-profile.php
│   │       └── edit-profile.php
│   └── assets/
│
L2 CONTENT (CPTs) ─────────────────────────────────────────
│
├── apollo-events/                  # Events CPT
│   ├── apollo-events.php
│   ├── includes/
│   │   ├── class-cpt-event.php     # Registra CPT 'event'
│   │   ├── class-meta-event.php    # Meta boxes
│   │   └── class-query-event.php   # Custom queries
│   ├── templates/
│   │   ├── archive-event.php       # /eventos
│   │   └── single-event.php        # /evento/{id}
│   └── assets/
│
├── apollo-djs/                     # DJs CPT (shared)
│   ├── apollo-djs.php
│   ├── includes/
│   │   ├── class-cpt-dj.php
│   │   └── class-meta-dj.php
│   ├── templates/
│   │   ├── archive-dj.php          # /djs
│   │   └── single-dj.php           # /dj/{id}
│   └── assets/
│
├── apollo-loc/                     # Locations CPT (NEVER 'venue')
│   ├── apollo-loc.php
│   ├── includes/
│   │   ├── class-cpt-loc.php
│   │   ├── class-meta-loc.php
│   │   └── class-geocoding.php
│   ├── templates/
│   │   ├── archive-loc.php         # /locs
│   │   └── single-loc.php          # /loc/{id}
│   └── assets/
│
├── apollo-classifieds/             # Ads/Marketplace
│   ├── apollo-classifieds.php
│   ├── includes/
│   │   ├── class-cpt-classified.php
│   │   └── class-meta-classified.php
│   ├── templates/
│   │   ├── archive-classified.php  # /anuncios
│   │   └── single-classified.php   # /anuncio/{id}
│   └── assets/
│
├── apollo-suppliers/               # Suppliers (industry-only)
│   ├── apollo-suppliers.php
│   ├── includes/
│   │   ├── class-cpt-supplier.php
│   │   └── class-access-control.php
│   ├── templates/
│   │   ├── archive-supplier.php    # /fornecedores
│   │   └── single-supplier.php     # /fornecedor/{id}
│   └── assets/
│
L3 SOCIAL ─────────────────────────────────────────────────
│
├── apollo-fav/                     # Universal Favorites (NEVER 'interesse')
│   ├── apollo-fav.php
│   ├── includes/
│   │   ├── class-fav-handler.php   # Toggle fav
│   │   ├── class-fav-list.php      # User's favs list
│   │   └── class-fav-button.php    # Render button
│   └── assets/
│       └── js/
│           └── fav-toggle.js       # AJAX toggle
│
├── apollo-wow/                     # Reactions (NEVER 'like')
│   ├── apollo-wow.php
│   ├── includes/
│   │   ├── class-wow-handler.php
│   │   ├── class-wow-types.php     # Reaction types
│   │   └── class-wow-render.php
│   └── assets/
│       └── js/
│           └── wow-reactions.js
│
├── apollo-comment/                 # Depoimentos (NEVER 'comment')
│   ├── apollo-comment.php
│   ├── includes/
│   │   ├── class-depoimento.php
│   │   └── class-comment-labels.php # Relabel system
│   └── templates/
│       └── depoimento-form.php
│
├── apollo-social/                  # Feed/Activity
│   ├── apollo-social.php
│   ├── includes/
│   │   ├── class-feed.php
│   │   ├── class-activity.php
│   │   └── class-connections.php
│   ├── templates/
│   │   └── feed/
│   │       ├── feed-main.php
│   │       └── activity-item.php
│   └── assets/
│
├── apollo-groups/                  # Núcleo + Comuna
│   ├── apollo-groups.php
│   ├── includes/
│   │   ├── class-group.php
│   │   ├── class-nucleo.php        # Private teams
│   │   ├── class-comuna.php        # Public communities
│   │   └── class-membership.php
│   ├── templates/
│   │   ├── group-single.php
│   │   └── group-list.php
│   └── assets/
│
L4 COMMUNICATION ──────────────────────────────────────────
│
├── apollo-notif/                   # In-app Notifications
│   ├── apollo-notif.php
│   ├── includes/
│   │   ├── class-notification.php
│   │   ├── class-notif-prefs.php
│   │   └── class-notif-badge.php
│   ├── templates/
│   │   └── notif-dropdown.php
│   └── assets/
│       └── js/
│           └── notif-realtime.js
│
├── apollo-email/                   # Email System (SEPARATE from notif)
│   ├── apollo-email.php
│   ├── includes/
│   │   ├── class-email-sender.php
│   │   ├── class-email-queue.php
│   │   ├── class-email-template.php
│   │   └── class-email-log.php
│   ├── templates/
│   │   └── emails/
│   │       ├── welcome.php
│   │       ├── password-reset.php
│   │       └── notification.php
│   └── assets/
│
L5 DOCUMENTS ──────────────────────────────────────────────
│
├── apollo-docs/                    # Document Management
│   ├── apollo-docs.php
│   ├── includes/
│   │   ├── class-cpt-document.php
│   │   ├── class-doc-library.php
│   │   └── class-doc-folders.php
│   ├── templates/
│   │   └── document-viewer.php
│   └── assets/
│
├── apollo-sign/                    # Digital Signatures
│   ├── apollo-sign.php
│   ├── includes/
│   │   ├── class-signature.php
│   │   ├── class-signature-pad.php
│   │   └── class-signature-audit.php
│   ├── templates/
│   │   └── signature-pad.php
│   └── assets/
│       └── js/
│           └── signature-pad.js    # Canvas signature
│
├── apollo-journal/                 # Magazine & Editorial Platform
│   ├── apollo-journal.php
│   ├── src/
│   │   ├── Plugin.php              # Singleton orchestrator
│   │   ├── NREP.php                # Auto-coding NREP.YYYY-NNN
│   │   ├── Shortcodes.php          # [apollo_journal], [apollo_journal_marquee], [apollo_journal_card]
│   │   ├── Admin.php               # Settings, columns, dashboard widget
│   │   ├── Activation.php
│   │   ├── Deactivation.php
│   │   └── API/
│   │       └── PostsController.php # GET /journal/posts
│   ├── templates/
│   │   ├── archive-journal.php     # Magazine archive (Canvas)
│   │   └── parts/
│   │       └── news-grid.php       # Embeddable news grid widget
│   ├── assets/
│   │   ├── css/journal.css
│   │   └── js/journal.js
│   ├── includes/
│   │   ├── constants.php
│   │   └── functions.php
│   ├── registry.json
│   └── composer.json
│
L6 FRONTEND ───────────────────────────────────────────────
│
├── apollo-shortcodes/              # ALL Shortcodes
│   ├── apollo-shortcodes.php
│   ├── includes/
│   │   ├── class-shortcode-registry.php
│   │   └── shortcodes/
│   │       ├── event-list.php      # [apollo_events]
│   │       ├── event-calendar.php  # [apollo_calendar]
│   │       ├── dj-list.php         # [apollo_djs]
│   │       ├── loc-map.php         # [apollo_map]
│   │       └── ...
│   └── assets/
│
├── apollo-templates/               # Page Builder + Calendar Types
│   ├── apollo-templates.php
│   ├── includes/
│   │   ├── class-page-builder.php
│   │   ├── class-canvas.php
│   │   └── class-template-loader.php
│   ├── templates/
│   │   ├── calendars/
│   │   │   ├── calendar-type-01.php
│   │   │   ├── calendar-type-02.php
│   │   │   └── calendar-type-03.php
│   │   └── layouts/
│   │       ├── full-width.php
│   │       └── sidebar.php
│   └── assets/
│
├── apollo-dashboard/               # User Dashboard
│   ├── apollo-dashboard.php
│   ├── includes/
│   │   ├── class-dashboard.php
│   │   ├── class-dashboard-widgets.php
│   │   └── class-dashboard-settings.php
│   ├── templates/
│   │   └── dashboard/
│   │       ├── main.php
│   │       ├── sidebar.php
│   │       └── widgets/
│   └── assets/
│
├── apollo-hub/                     # Linktree-style Public Page
│   ├── apollo-hub.php
│   ├── includes/
│   │   ├── class-hub-page.php
│   │   └── class-hub-builder.php
│   ├── templates/
│   │   └── hub/
│   │       └── single-hub.php      # /hub/{username}
│   └── assets/
│
L7 ADMIN ──────────────────────────────────────────────────
│
├── apollo-coauthor/                # Co-Author System
│   ├── apollo-coauthor.php
│   ├── includes/
│   │   ├── class-coauthor.php
│   │   ├── class-coauthor-meta.php
│   │   └── class-coauthor-permissions.php
│   └── assets/
│
├── apollo-mod/                     # Moderation Tabs
│   ├── apollo-mod.php
│   ├── includes/
│   │   ├── class-mod-queue.php
│   │   ├── class-mod-tabs.php
│   │   └── class-mod-actions.php
│   ├── templates/
│   │   └── admin/
│   │       └── mod-dashboard.php
│   └── assets/
│
├── apollo-admin/                   # MANDATORY Unified Admin Panel
│   ├── apollo-admin.php
│   ├── includes/
│   │   ├── class-admin-panel.php
│   │   ├── class-admin-tabs.php    # Tab for EACH plugin
│   │   └── class-settings-hub.php
│   ├── templates/
│   │   └── admin/
│   │       ├── main-panel.php
│   │       └── tabs/
│   │           ├── tab-core.php
│   │           ├── tab-event.php
│   │           ├── tab-social.php
│   │           └── ...
│   └── assets/
│
├── apollo-statistics/              # ALL Analytics
│   ├── apollo-statistics.php
│   ├── includes/
│   │   ├── class-analytics.php
│   │   ├── class-stats-events.php
│   │   ├── class-stats-users.php
│   │   └── class-stats-content.php
│   ├── templates/
│   │   └── admin/
│   │       └── stats-dashboard.php
│   └── assets/
│
L8 INDUSTRY ───────────────────────────────────────────────
│
├── apollo-cult/                    # Industry Area (NEVER 'cena-rio')
│   ├── apollo-cult.php
│   ├── includes/
│   │   ├── class-cult-access.php   # Restrict to industry
│   │   ├── class-cult-calendar.php # Save-the-dates
│   │   └── class-cult-tools.php
│   ├── templates/
│   │   └── cult/
│   │       ├── main.php
│   │       └── calendar.php
│   └── assets/
│
L9 PWA ────────────────────────────────────────────────────
│
└── apollo-pwa/                     # PWA + Offline
    ├── apollo-pwa.php
    ├── includes/
    │   ├── class-pwa.php
    │   ├── class-service-worker.php
    │   ├── class-manifest.php
    │   └── class-offline.php
    ├── templates/
    │   └── offline.php
    └── assets/
        ├── manifest.json
        └── js/
            └── service-worker.js
```

---

## 🔗 DEPENDENCY GRAPH

```
                          ┌─────────────────┐
                          │   apollo-pwa    │ L9
                          └────────┬────────┘
                                   │
              ┌────────────────────┴────────────────────┐
              │                                         │
     ┌────────▼────────┐                      ┌────────▼────────┐
     │apollo-templates │ L6                   │   apollo-cult   │ L8
     └────────┬────────┘                      └────────┬────────┘
              │                                        │
     ┌────────▼────────┐                      ┌────────▼────────┐
    │apollo-shortcodes│ L6                   │apollo-suppliers │ L2
     └────────┬────────┘                      └────────┬────────┘
              │                                        │
              └────────────────────┬───────────────────┘
                                   │
┌──────────┬──────────┬───────────┼───────────┬──────────┬──────────┐
│          │          │           │           │          │          │
▼          ▼          ▼           ▼           ▼          ▼          ▼
apollo-    apollo-    apollo-     apollo-     apollo-    apollo-    apollo-
events     djs        loc         social      groups     email      notif
(L2)       (L2)       (L2)        (L3)        (L3)       (L4)       (L4)
│          │          │           │           │          │          │
│          │          │           └─────┬─────┘          │          │
│          │          │                 │                │          │
│          │          │        ┌────────▼────────┐       │          │
│          │          │        │  apollo-users   │ L1    │          │
│          │          │        └────────┬────────┘       │          │
│          │          │                 │                │          │
│          │          │        ┌────────▼────────┐       │          │
│          │          │        │  apollo-login   │ L1    │          │
│          │          │        └────────┬────────┘       │          │
│          │          │                 │                │          │
└──────────┴──────────┴─────────────────┼────────────────┴──────────┘
                                        │
                               ┌────────▼────────┐
                               │   apollo-core   │ L0 REQUIRED
                               └─────────────────┘
```

---

## 📁 STANDARD FILE STRUCTURE (per plugin)

```
apollo-{plugin}/
├── apollo-{plugin}.php           # Main plugin file
├── composer.json                 # Dependencies
├── uninstall.php                # Cleanup on uninstall
├── includes/
│   ├── class-{main}.php         # Main class
│   ├── class-{feature}.php      # Feature classes
│   └── ...
├── src/                          # PSR-4 Namespace classes
│   ├── Admin/
│   ├── Public/
│   └── REST/
├── templates/
│   └── ...
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── public.css
│   └── js/
│       ├── admin.js
│       └── public.js
└── languages/
    └── apollo-{plugin}.pot
```

---

## ⚠️ FORBIDDEN TERMS CHECKLIST

| ❌ FORBIDDEN     | ✅ USE INSTEAD |
| ---------------- | -------------- |
| venue            | loc            |
| local (CPT)      | loc            |
| location         | loc            |
| interesse        | fav            |
| interessado      | fav            |
| interest         | fav            |
| bookmark         | fav            |
| like             | wow            |
| heart            | wow            |
| reaction         | wow            |
| comment (system) | depoimento     |
| review           | depoimento     |
| cena-rio         | cult           |
| cenario          | cult           |
| cena_rio         | cult           |
| /user/username   | /id/username   |

---

## 🚀 ACTIVATION ORDER

1. **apollo-core** (FIRST - sempre)
2. **apollo-login** + **apollo-users** (auth layer)
3. **apollo-events** + **apollo-djs** + **apollo-loc** (content CPTs)
4. **apollo-fav** + **apollo-wow** + **apollo-comment** (interaction)
5. **apollo-social** + **apollo-groups** (social)
6. **apollo-notif** + **apollo-email** (communication)
7. **apollo-shortcodes** + **apollo-templates** (frontend)
8. **apollo-dashboard** + **apollo-hub** (user areas)
9. **apollo-admin** + **apollo-mod** + **apollo-statistics** (admin)
10. **apollo-cult** + **apollo-suppliers** (industry)
11. **apollo-pwa** (LAST)

---

_Generated: 2026-02-18 | Apollo Ecosystem (workspace sync)_
