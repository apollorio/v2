# Apollo Classifieds — Technical Documentation

## System Architecture

### Overview
Apollo Classifieds é um sistema marketplace modular para o ecossistema Apollo, projetado para lidar com **tickets** (repasses de ingressos), **acomodações**, e **classificados gerais**. A característica crítica é o **modal de disclaimer obrigatório** que protege legalmente a plataforma antes de conectar usuários via chat.

---

## Critical Flow (Legal Protection)

```
User clicks "Contact" button on card
        ↓
Modal opens with legal disclaimer
        ↓
User MUST check "I'm AWARE!" checkbox
        ↓
Button "INICIAR CHAT" becomes active (.active class)
        ↓
AJAX call to apollo_classifieds_init_chat
        ↓
Response: { chat_url: '/chat/789' }
        ↓
Redirect to apollo-chat thread
```

**NEVER** allow direct chat access without going through modal flow.

---

## File Structure & Components

### 1. Main Plugin Files

#### `apollo-classifieds.php`
- Plugin header + constants
- PSR-4 autoloader for `Apollo\Classifieds\` namespace
- Bootstrap via `plugins_loaded` hook
- Activation/deactivation hooks (flush rewrite rules)

#### `src/Plugin.php`
- Core plugin class
- `enqueue_assets()`: CDN, GSAP, CSS, JS with localization
- `register_cpt()`: Fallback CPT registration if apollo-core not active
- `template_redirect()`: Redirects `/marketplace` to custom template
- `ajax_init_chat()`: AJAX handler for chat initialization

---

### 2. Templates

#### Main Template: `templates/classifieds-page.php`
- Canvas template (uses `get_header()` and `get_footer()`)
- Loads Apollo CDN in `<head>`
- Queries classifieds by type (`ticket`, `accommodation`)
- Uses `get_template_part()` for all components
- MUST include modal at bottom

#### Template Parts (Modular Components)

| File | Purpose | Args |
|------|---------|------|
| `parts/page-header.php` | Masthead title "MARKET::PLACE" | None |
| `parts/info-box.php` | Security warning box with shield icon | None |
| `parts/section-header.php` | Section title + icon + count | `icon`, `title`, `count` |
| `parts/filters-row.php` | Filter pills (Todos, Categories, etc.) | `filters` (array) |
| `parts/card-ticket.php` | Ticket/repasse card component | Post loop context |
| `parts/card-accommodation.php` | Accommodation card component | Post loop context |
| `parts/modal-disclaimer.php` | Legal disclaimer modal | None |

---

### 3. Assets

#### `assets/css/classifieds.css` (630 lines)
Complete design system with:
- **CSS Variables**: Apollo Core palette (`--primary`, `--black-1`, `--surface`, etc.)
- **Typography**: Space Grotesk (main), Space Mono (mono), Shrikhand (fun), Lato (ticket)
- **Layout Utils**: `.container`, responsive grid (1→2→3→4 columns)
- **Components**: Ticket cards, accommodation cards, modal
- **Animations**: `.reveal-up` with GSAP triggers

**Key Sections:**
- Lines 1-60: CSS variables (`:root`)
- Lines 190-380: Ticket card (top, rip, bottom sections)
- Lines 430-520: Accommodation card
- Lines 540-630: Modal (warning header, consent checkbox, buttons)

#### `assets/js/classifieds.js`
JavaScript interactions:
- **GSAP ScrollTrigger**: Reveals `.reveal-up` elements on scroll
- **Modal Logic**:
  - Open: `.btn-open-modal` click → modal.addClass('open')
  - Checkbox: `#modal-consent-check` change → toggle `.active` on proceed button
  - Proceed: AJAX to `apollo_classifieds_init_chat` → redirect to chat
- **Filters**: Optional category filtering (TODO)

**Critical Code:**
```javascript
// Checkbox toggle (MUST be checked to unlock chat)
consentCheckbox.on('change', function() {
    if ($(this).is(':checked')) {
        proceedButton.addClass('active');
    } else {
        proceedButton.removeClass('active');
    }
});

// AJAX chat initialization
proceedButton.on('click', function() {
    if (!$(this).hasClass('active')) {
        return; // DO NOTHING if not active
    }
    
    $.ajax({
        url: apolloClassifieds.ajaxUrl,
        data: {
            action: 'apollo_classifieds_init_chat',
            nonce: apolloClassifieds.nonce,
            user_id: userId,
            classified_id: classifiedId
        },
        success: function(response) {
            window.location.href = response.data.chat_url;
        }
    });
});
```

---

## Custom Post Type: `classified`

### Meta Fields

| Meta Key | Type | Usage | Example |
|----------|------|-------|---------|
| `_classified_type` | string | `ticket` \| `accommodation` \| `general` | `ticket` |
| `_event_title` | string | Event name (tickets only) | `Sunset Theory` |
| `_event_date` | string | Event date/time | `24 Fev • 23:00` |
| `_event_location` | string | Venue/section | `Pista Premium` |
| `_price` | int | Price in BRL (no cents) | `149` |
| `_location` | string | Location (accommodations) | `Lapa, Rio de Janeiro` |
| `_rating` | float | Star rating | `4.8` |
| `_badge` | string | Badge text | `Superhost`, `Novo` |

### Query Examples

```php
// Get all tickets
$tickets = new WP_Query(array(
    'post_type' => 'classified',
    'meta_query' => array(
        array(
            'key' => '_classified_type',
            'value' => 'ticket'
        )
    )
));

// Get accommodations in Lapa
$lapa_accom = new WP_Query(array(
    'post_type' => 'classified',
    'meta_query' => array(
        array('key' => '_classified_type', 'value' => 'accommodation'),
        array('key' => '_location', 'value' => 'Lapa', 'compare' => 'LIKE')
    )
));
```

---

## AJAX Endpoints

### `apollo_classifieds_init_chat`

**Purpose**: Create chat thread after user accepts disclaimer

**Method**: `POST`

**Hook**: `wp_ajax_apollo_classifieds_init_chat` (logged-in users only)

**Parameters**:
- `nonce`: Nonce verification (`apollo_classifieds_nonce`)
- `user_id`: Target user ID (classified author)
- `classified_id`: Classified post ID

**Response**:
```json
{
    "success": true,
    "data": {
        "chat_url": "/chat/123"
    }
}
```

**Flow**:
1. Check nonce + user logged in
2. Validate `user_id` and `classified_id`
3. Check if `apollo-chat` plugin active
   - If YES: Call `apollo_chat_create_thread()` with context
   - If NO: Fallback to user profile URL
4. Return chat URL

---

## Design System Reference

### Colors
```css
--primary: #f45f00;           /* Apollo orange */
--accent-violet: #651FFF;     /* Violet accent */
--black-1: #121214;           /* Near black */
--gray-8: #bfbfc1;            /* Light gray */
--bg: #ffffff;                /* White background */
--surface: #fafafa;           /* Off-white surface */
--border: #e4e4e7;            /* Border gray */
```

### Typography
```css
--ff-main: "Space Grotesk";   /* Headings, UI */
--ff-mono: "Space Mono";      /* Dates, counts, codes */
--ff-fun: "Shrikhand";        /* Display titles */
--ff-ticket: "Lato";          /* Ticket card content */
```

### Layout
```css
--radius: 18px;               /* Card border radius */
--radius-sm: 10px;            /* Button radius */
--ease-lux: cubic-bezier(0.16, 1, 0.3, 1); /* Smooth easing */
```

---

## Integration Patterns

### 1. Custom Page Template
```php
<?php
/* Template Name: Only Tickets Page */
get_header();
?>

<main class="container">
    <?php 
    get_template_part('wp-content/plugins/apollo-classifieds/templates/parts/info-box');
    
    $query = new WP_Query(['post_type' => 'classified', 'meta_key' => '_classified_type', 'meta_value' => 'ticket']);
    
    if ($query->have_posts()) :
        get_template_part('wp-content/plugins/apollo-classifieds/templates/parts/section-header', null, [
            'icon' => 'ri-ticket-2-fill',
            'title' => 'Ingressos',
            'count' => $query->found_posts
        ]);
        
        echo '<div class="grid-layout">';
        while ($query->have_posts()) : $query->the_post();
            get_template_part('wp-content/plugins/apollo-classifieds/templates/parts/card-ticket');
        endwhile;
        echo '</div>';
    endif;
    
    get_template_part('wp-content/plugins/apollo-classifieds/templates/parts/modal-disclaimer');
    ?>
</main>

<?php get_footer(); ?>
```

### 2. Shortcode
```php
[apollo_tickets limit="6" event="Sunset Theory"]
```

### 3. REST API
```bash
GET /wp-json/apollo/v1/classifieds/ticket
```

---

## Security Checklist

- [x] Nonce verification on AJAX calls
- [x] User authentication check (`is_user_logged_in()`)
- [x] Sanitize inputs (`absint()`, `sanitize_text_field()`)
- [x] Escape outputs (`esc_html()`, `esc_url()`, `esc_attr()`)
- [x] Capability checks (`current_user_can()`)
- [x] Legal disclaimer REQUIRED before chat access
- [x] Data validation (user_id, classified_id exist)

---

## Dependencies

### Required
- **WordPress**: 6.4+
- **PHP**: 8.1+
- **Apollo CDN**: `https://cdn.apollo.rio.br/v1.0.0/core.min.js`
- **jQuery**: 3.7.1 (via CDN)
- **GSAP**: 3.12.2 (via CDN)

### Optional
- **apollo-core**: CPT registry (plugin has fallback)
- **apollo-chat**: Chat thread creation (fallback to profile URL)

---

## Testing Checklist

### Manual Tests
1. Activate plugin
2. Create page with slug `/marketplace`
3. Visit page → should auto-load custom template
4. Run demo data generator: `WP Admin → Classifieds → Demo Data`
5. Click any "Contact" button
6. Verify modal opens
7. Try clicking "INICIAR CHAT" without checkbox → should do nothing
8. Check checkbox → button should become orange
9. Click button → should redirect to chat/profile

### Browser Console Tests
```javascript
// Check if classifieds.js loaded
typeof apolloClassifieds

// Check modal state
$('#apollo-classifieds-modal').hasClass('open')

// Force open modal (debug)
$('#apollo-classifieds-modal').addClass('open')
```

---

## Common Issues & Fixes

### Issue: Modal doesn't open
**Fix**: Ensure `classifieds.js` is enqueued. Check browser console for errors.

### Issue: Button stays disabled after checking
**Fix**: Verify checkbox ID is `modal-consent-check` and button ID is `btn-proceed-chat`.

### Issue: Cards not rendering
**Fix**: Check WP_Query is returning posts. Verify meta fields exist with `get_post_meta()`.

### Issue: GSAP animations not working
**Fix**: Ensure GSAP + ScrollTrigger scripts are loaded. Check console for `gsap is not defined`.

---

## Forbidden Terms (Apollo Registry Compliance)

Per `apollo-registry.json`:
- ❌ `venue/local/location` → ✅ `loc`
- ❌ `like/heart` → ✅ `wow`
- ❌ `bookmark/interesse` → ✅ `fav`
- ❌ `comment/review` → ✅ `depoimento`

---

## Version History

### 1.0.0 (2026-02-14)
- Initial modular structure
- Ticket + accommodation cards
- Mandatory disclaimer modal
- GSAP scroll animations
- Demo data generator
- Integration examples
- Complete design system

---

## License

Proprietary — Apollo Team © 2026
