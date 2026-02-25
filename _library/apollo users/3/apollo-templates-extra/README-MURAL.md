# Apollo Mural — Logged-in Dashboard

**Package:** `apollo-templates-extra` (overlay onto `apollo-templates`)

## What This Is

The **Mural** is the personalized dashboard shown to logged-in users when they visit the home page. Guests see the public landing page (`page-home.php`); authenticated users see the mural with their greeting, weather, sound tags, favorited events, upcoming events, and classifieds.

## Architecture

```
Weather Video (full-width cinematic bar, TOP)
        ↓
Greeting ("Boa tarde, Rafael!" + location + next event alert)
        ↓
News Ticker (airport-style scrolling)
        ↓
My Sounds (tag pills from user preferences)
        ↓
My Favorites (favorited events grid)
        ↓
All Upcoming Events (next 30 days)
        ↓
Marketplace (hosting + tickets)
```

## File Map

```
apollo-templates/
├── includes/
│   └── mural-router.php          ← NEW — template routing hook
├── templates/
│   ├── page-mural.php            ← NEW — main mural template
│   └── template-parts/
│       └── mural/
│           ├── weather-hero.php   ← Weather video card (full-width)
│           ├── greeting.php       ← Personalized hello + alert
│           ├── ticker.php         ← News ticker
│           ├── sounds.php         ← Sound preference pills
│           ├── favorites.php      ← Favorited events grid
│           ├── upcoming.php       ← All upcoming events
│           └── classifieds.php    ← Marketplace 2-column
└── assets/
    ├── css/
    │   └── mural.css              ← NEW — full mural stylesheet
    └── js/
        └── mural.js               ← NEW — scroll reveal, ticker
```

## Integration Steps

### 1. Copy files into apollo-templates

```bash
# From the plugin root
cp -r apollo-templates-extra/* apollo-templates/
```

### 2. Register the router

Edit `apollo-templates.php` or `includes/functions.php` — add this line:

```php
// After the autoloader / includes section:
require_once APOLLO_TEMPLATES_DIR . 'includes/mural-router.php';
```

### 3. Verify routing

- **Guest** → visits `/` → sees `page-home.php` (public landing)
- **Logged-in** → visits `/` → sees `page-mural.php` (mural dashboard)

### 4. Flush permalinks

Settings → Permalinks → Save (no changes needed, just flush)

## Data Sources

| Section | Source | Meta Key / Query |
|---------|--------|------------------|
| Greeting name | `_apollo_social_name` or `display_name` | user meta |
| Location | `user_location` | user meta |
| Sound tags | `_apollo_sound_preferences` | user meta → taxonomy terms |
| Favorites | `_apollo_favorite_events` | user meta → `apollo_event` posts |
| Upcoming | `apollo_event` CPT | `_apollo_event_date >= today` |
| Classifieds | `apollo_classified` CPT | `classified_type` taxonomy |
| Weather | Filter hooks | `apollo_mural_weather_*` |
| Ticker | Filter hook | `apollo_mural_ticker_items` |

## Customization Hooks

### Filters

```php
// Weather data (connect to real API later)
add_filter( 'apollo_mural_weather_temp', fn() => '32°' );
add_filter( 'apollo_mural_weather_condition', fn() => 'Clear' );
add_filter( 'apollo_mural_weather_icon', fn() => 'ri-sun-fill' );
add_filter( 'apollo_mural_weather_location', fn() => 'Ipanema' );
add_filter( 'apollo_mural_weather_video', fn() => 'YOUR_VIDEO_ID' );

// Ticker items (dynamic from DB, API, etc.)
add_filter( 'apollo_mural_ticker_items', function( $items ) {
    // Add dynamic items
    $items[] = 'NEW: CARNIVAL LINEUP ANNOUNCED';
    return $items;
});
```

### Actions

```php
// After all mural content
add_action( 'apollo_after_mural_content', function() {
    // Add custom sections, modals, etc.
});
```

## Design Notes

- Weather video is **full-width at the top** (not beside the greeting)
- Uses `clamp()` for responsive height (200px → 360px)
- Greeting appears BELOW the weather card
- Ticker duplicates items for seamless infinite loop
- Event cards have grayscale → color hover effect
- Classifieds slide right on hover
- Apollo Design System tokens throughout
