# Apollo Classifieds

**Marketplace system for Apollo ecosystem** — tickets, accommodations, general classifieds with mandatory disclaimer modal.

## Features

- **Ticket/Repasse Cards**: Event tickets with user info, event details, price, and barcode aesthetic
- **Accommodation Cards**: Lodging listings with images, ratings, locations, pricing
- **Mandatory Disclaimer Modal**: Legal protection — users MUST check "I'm AWARE!" before accessing chat
- **GSAP Animations**: Scroll-triggered reveal effects
- **Apollo Design System**: Luxury monochrome palette with Space Grotesk typography

## File Structure

```
apollo-classifieds/
├── apollo-classifieds.php          # Main plugin file
├── src/
│   └── Plugin.php                  # Core plugin class
├── templates/
│   ├── classifieds-page.php        # Main marketplace template
│   └── parts/
│       ├── page-header.php         # Page title/header
│       ├── info-box.php            # Security info box
│       ├── section-header.php      # Section headers with icons/counts
│       ├── filters-row.php         # Filter pills
│       ├── card-ticket.php         # Ticket/repasse card
│       ├── card-accommodation.php  # Accommodation card
│       └── modal-disclaimer.php    # Legal disclaimer modal
├── assets/
│   ├── css/
│   │   └── classifieds.css         # Complete design system
│   └── js/
│       └── classifieds.js          # Modal logic + AJAX chat init
└── README.md
```

## Usage

### Display Classifieds Page

1. Create a page with slug `/marketplace`
2. Plugin auto-redirects to custom template
3. Or use shortcode: `[apollo_classifieds]` (TODO)

### Template Parts

Reusable components:

```php
// Section header
get_template_part('wp-content/plugins/apollo-classifieds/templates/parts/section-header', null, array(
    'icon' => 'ri-ticket-2-fill',
    'title' => 'Repasses',
    'count' => '12 Active'
));

// Ticket card (inside WP_Query loop)
while ($query->have_posts()) : $query->the_post();
    get_template_part('wp-content/plugins/apollo-classifieds/templates/parts/card-ticket');
endwhile;
```

### Custom Post Type

Register `classified` CPT with meta fields:

- `_classified_type`: `ticket` | `accommodation` | `general`
- `_event_title`: Event name (for tickets)
- `_event_date`: Event date
- `_event_location`: Venue/section
- `_price`: Price in BRL
- `_location`: Location (for accommodation)
- `_rating`: Star rating
- `_badge`: Badge text (e.g., "Superhost", "Novo")

## Modal Flow (CRITICAL)

1. User clicks `.btn-open-modal` on any card
2. Modal opens with legal disclaimer
3. Checkbox `#modal-consent-check` MUST be checked
4. Only then, `#btn-proceed-chat` becomes `.active`
5. AJAX call to `apollo_classifieds_init_chat`
6. Redirect to `apollo-chat` thread

## AJAX Endpoint

```javascript
$.ajax({
    url: apolloClassifieds.ajaxUrl,
    data: {
        action: 'apollo_classifieds_init_chat',
        nonce: apolloClassifieds.nonce,
        user_id: 123,
        classified_id: 456
    }
});
```

Response: `{ chat_url: '/chat/789' }`

## Dependencies

- `apollo-core`: CPT registry fallback
- `apollo-chat`: Optional (creates chat threads)
- Apollo CDN: `https://cdn.apollo.rio.br/v1.0.0/core.min.js`
- GSAP 3.12.2
- jQuery 3.7.1

## Forbidden Terms

Per Apollo registry:
- ❌ venue/local/location → ✅ `loc`
- ❌ like/heart → ✅ `wow`
- ❌ bookmark → ✅ `fav`

## Version

**1.0.0** — Initial modular structure
