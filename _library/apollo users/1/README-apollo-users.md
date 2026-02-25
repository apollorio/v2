# Apollo Users v2.0.0

**Users: Profiles, Ratings, Depoimentos, Account Management, Author Protection**

## Architecture

```
apollo-users/
├── apollo-users.php              ← Main plugin (routes, autoloader, activation)
├── composer.json                 ← PSR-4: Apollo\Users\
├── uninstall.php                 ← Full cleanup on uninstall
│
├── includes/
│   ├── constants.php             ← Tables, rating categories, sections
│   └── functions.php             ← Helpers (avatar, cover, profile URL, etc.)
│
├── src/
│   ├── Plugin.php                ← Singleton, shortcodes, REST init
│   ├── Activation.php            ← DB tables creation (4 tables)
│   ├── Deactivation.php          ← Transient cleanup
│   │
│   ├── API/
│   │   ├── UsersController.php   ← /users, /users/{id}, /users/search, /users/radar
│   │   ├── ProfileController.php ← /profile/avatar, /profile/cover, /profile/views
│   │   └── RatingController.php  ← /users/{id}/ratings, /users/{id}/depoimentos
│   │
│   └── Components/
│       ├── ProfileHandler.php    ← AJAX: avatar/cover upload, profile update
│       ├── UserFields.php        ← Admin profile fields, sanitization
│       ├── AuthorProtection.php  ← Block ?author=N, sitemap, RSS, REST
│       ├── RatingHandler.php     ← User-to-user ratings (sexy/legal/confiável)
│       ├── DepoimentoHandler.php ← WP Comments as testimonials
│       └── AccountHandler.php    ← Password change, privacy, account deletion
│
├── templates/
│   ├── single-profile.php        ← /id/{username} (2/3 + 1/3 grid)
│   ├── minha-conta.php           ← /minha-conta (account management)
│   ├── edit-profile.php          ← /editar-perfil (form)
│   ├── user-radar.php            ← /radar (directory)
│   ├── profile-private.php       ← Privacy wall
│   ├── profile-login-required.php← Login gate
│   └── parts/                    ← Template partials
│
└── assets/
    ├── css/
    │   ├── profile.css           ← Profile page (Apollo Design System)
    │   ├── account.css           ← Account page
    │   ├── edit-profile.css      ← Edit profile page
    │   └── radar.css             ← Radar page
    └── js/
        ├── profile.js            ← Ratings, depoimentos, feed tabs
        └── account.js            ← Account forms AJAX
```

## Routes

| Route | Template | Auth | Description |
|-------|----------|------|-------------|
| `/id/{username}` | single-profile.php | No | Public profile page |
| `/perfil/{username}` | 301 redirect | No | Alias → `/id/{username}` |
| `/minha-conta` | minha-conta.php | Yes | Account settings |
| `/minha-conta/{section}` | minha-conta.php | Yes | account/change-password/privacy/delete-account |
| `/editar-perfil` | edit-profile.php | Yes | Edit profile form |
| `/radar` | user-radar.php | No | User directory |

## Database Tables

| Table | Purpose |
|-------|---------|
| `apollo_matchmaking` | User preferences for matchmaking |
| `apollo_user_fields` | Custom field storage |
| `apollo_profile_views` | Profile view tracking |
| `apollo_user_ratings` | User-to-user ratings (voter_id, target_id, category, score) |

## Rating System

- **Categories**: Sexy, Legal, Confiável (configurable in constants.php)
- **Scale**: 0-3 per category (rendered as 3 emoji icons)
- **Constraint**: One vote per user per category per target (UNIQUE KEY)
- **Storage**: `apollo_user_ratings` table with `REPLACE INTO` for upsert
- **Display**: Average shown to visitors, own votes shown to voter

## Depoimentos

- **Backend**: WordPress `$comment` system
- **comment_type**: `apollo_depoimento`
- **comment_parent**: Target user ID
- **Constraint**: One depoimento per user per target
- **Deletion**: Author, target user, or admin can delete
- **Exclusion**: Automatically excluded from regular WP comment queries

## Visibility Rules

| Data | Own Profile | Visitor Profile |
|------|-------------|-----------------|
| Avatar, Name, Handle | ✅ | ✅ |
| Bio, Location, Sounds | ✅ | ✅ |
| Edit/Settings buttons | ✅ | ❌ |
| Performance stats | ✅ | ❌ |
| Email | ✅ | Privacy setting |
| Rating emojis (interactive) | ❌ | ✅ (logged in) |
| Rating averages | ✅ | ✅ |
| Depoimentos | ✅ | ✅ |
| Depoimento form | ❌ | ✅ (logged in) |

## Dependencies

- **apollo-core** (sound taxonomy)
- **apollo-login** (auth routes, user creation)

## REST Endpoints

All under `apollo/v1/`:

- `GET /users` — Directory (paginated)
- `GET /users/me` — Current user
- `PUT /users/me` — Update current user
- `GET /users/{id}` — User by ID
- `GET /users/{username}` — User by username
- `GET /users/{id}/ratings` — Get ratings
- `POST /users/{id}/ratings` — Submit rating
- `GET /users/{id}/depoimentos` — Get depoimentos
- `POST /users/{id}/depoimentos` — Submit depoimento
- `POST /profile/avatar` — Upload avatar
- `DELETE /profile/avatar` — Delete avatar
- `POST /profile/cover` — Upload cover
- `DELETE /profile/cover` — Delete cover
- `GET /profile/views` — Who viewed me
