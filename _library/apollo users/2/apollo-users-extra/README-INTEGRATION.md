# Apollo Users — Profile Page Extra Content

## 🎯 Target Plugin: `apollo-users`

All files in this ZIP go into **`wp-content/plugins/apollo-users/`**.

---

## 📂 File Map — What Goes Where in VS Code

```
apollo-users/                         ← YOUR EXISTING PLUGIN
│
├── src/
│   ├── Activation.php                ← REPLACE (adds apollo_user_ratings table)
│   └── Components/
│       ├── RatingHandler.php         ← NEW FILE (anonymous voting system)
│       └── DepoimentoHandler.php     ← NEW FILE (testimonials via WP comments)
│
├── templates/
│   ├── single-profile.php            ← REPLACE (new hero + 2/3+1/3 grid layout)
│   └── parts/
│       ├── profile-hero.php          ← NEW FILE (cover image section)
│       ├── profile-card.php          ← NEW FILE (left column: avatar, bio, player)
│       ├── profile-sidebar.php       ← NEW FILE (right column: ratings, stats, pubs)
│       ├── profile-feed.php          ← REPLACE (updated feed with tab filtering)
│       └── profile-depoimentos.php   ← REPLACE (updated 2-col grid depoimentos)
│
├── assets/
│   ├── css/
│   │   └── profile.css               ← REPLACE (complete redesign matching HTML)
│   └── js/
│       └── profile.js                ← REPLACE (SC player, ratings, depo, admin)
│
└── includes/
    └── constants.php                  ← REPLACE (adds APOLLO_USERS_TABLE_RATINGS)
```

---

## ⚡ Integration Steps

### 1. Backup
```bash
cp -r wp-content/plugins/apollo-users wp-content/plugins/apollo-users.bak
```

### 2. Copy Files
Extract this ZIP and overlay on top of `apollo-users/`:
```bash
cp -r apollo-users-extra/* wp-content/plugins/apollo-users/
```

### 3. Register New Components in Plugin.php
Open `src/Plugin.php` and add these two lines inside `init_components()`:

```php
private function init_components(): void {
    new Components\ProfileHandler();
    new Components\UserFields();
    new Components\AuthorProtection();
    new Components\RatingHandler();       // ← ADD THIS
    new Components\DepoimentoHandler();   // ← ADD THIS
}
```

### 4. Create Ratings Table
Deactivate and reactivate the plugin in WP Admin → Plugins, OR run:
```php
// In a one-time script or WP-CLI:
\Apollo\Users\Activation::activate();
```

### 5. Flush Rewrite Rules
Visit WP Admin → Settings → Permalinks and click "Save Changes".

---

## 🗳️ Rating System

### How It Works
- **3 categories**: Sexy (heart), Legal (smile), Confiável (cube)
- **3 icons each**: Score 0–3 per category
- **One vote per user per category per target** (UNIQUE KEY constraint)
- **Anonymous**: Public visitors see aggregated averages only, never who voted
- **Click to vote**: Logged-in users click emoji icons, AJAX upsert

### Admin Controls
- Admins see a shield icon (🛡) on the ratings card
- Click opens a modal showing ALL voters with their scores
- Admin can **adjust** any vote via dropdown (0–3)
- Admin can **delete** any vote (for hate attack removal)
- Uses separate `apollo_admin_rating_nonce` for security

### Database
```sql
CREATE TABLE wp_apollo_user_ratings (
    id          BIGINT UNSIGNED AUTO_INCREMENT,
    voter_id    BIGINT UNSIGNED NOT NULL,
    target_id   BIGINT UNSIGNED NOT NULL,
    category    VARCHAR(50) NOT NULL,
    score       TINYINT UNSIGNED DEFAULT 0,
    created_at  DATETIME,
    updated_at  DATETIME,
    PRIMARY KEY (id),
    UNIQUE KEY unique_vote (voter_id, target_id, category)
);
```

---

## 📄 Template Structure

```
┌──────────────────────────────────────────────┐
│  HERO MEDIA (cover image, 94vw)              │  ← profile-hero.php
├───────────────────────┬──────────────────────┤
│  PROFILE CARD (66%)   │  SIDEBAR (34%)       │
│                       │                      │
│  • Avatar             │  • Ratings card      │  ← profile-card.php
│  • Name + Tags        │    (heart/smile/cube)│  ← profile-sidebar.php
│  • @handle            │  • Stats card        │
│  • Bio                │    (own user only)   │
│  • Hub link           │  • Pubs list         │
│  • Sound tags         │    (flat list, no    │
│  • SoundCloud player  │     sub-cards)       │
│  • Member since       │                      │
├───────────────────────┴──────────────────────┤
│  FEED SECTION                                │  ← profile-feed.php
│  [All] [Events] [Classifieds]                │
│  2-column card grid with hover overlays      │
├──────────────────────────────────────────────┤
│  DEPOIMENTOS                                 │  ← profile-depoimentos.php
│  Divider → Header → Submit form → 2-col grid │
└──────────────────────────────────────────────┘
```

---

## 🔑 User Meta Keys Used

| Key | Purpose |
|-----|---------|
| `_apollo_social_name` | Display name |
| `_apollo_bio` | Bio text |
| `_apollo_soundcloud_url` | **NEW** — SoundCloud track URL for profile player |
| `_apollo_sound_preferences` | Array of taxonomy term IDs |
| `_apollo_nucleos` | Array of núcleo tag strings |
| `_apollo_privacy_profile` | public / members / private |
| `_apollo_profile_views` | View counter |
| `_apollo_membership` | Membership type |
| `_apollo_favorites_count` | Favorites counter |
| `_apollo_unique_visits` | Unique visit counter |

---

## ⚠️ Notes

- **Pubs are flat lists** — no sub-cards inside sidebar card, avoids padding/margin issues
- **Stats card is own-user-only** — visitors never see performance stats
- **SoundCloud player** requires `_apollo_soundcloud_url` user meta to be set
- **Dark mode** is supported via existing Apollo CDN CSS variables
- **No jQuery dependency** — pure vanilla JS
- **apollo-login** and **apollo-templates** remain untouched
