# Apollo Plugin Boilerplate

## How to Create a New Apollo Plugin

### Quick Start

1. Copy the `apollo-{slug}` folder
2. Rename folder to `apollo-yourplugin`
3. Find and replace these placeholders:

| Placeholder     | Replace With                     | Example                        |
| --------------- | -------------------------------- | ------------------------------ |
| `{slug}`        | Plugin slug (lowercase, hyphens) | `users`                        |
| `{Namespace}`   | PHP Namespace (PascalCase)       | `Users`                        |
| `{Name}`        | Display name                     | `Users`                        |
| `{CONST}`       | Constant prefix (UPPERCASE)      | `USERS`                        |
| `{Description}` | Plugin description               | `User profiles and management` |

### Example for apollo-users:

```bash
# Find and replace:
{slug} → users
{Namespace} → Users
{Name} → Users
{CONST} → USERS
{Description} → Users: Roles, Capabilities, Profile page, Preferences, Matchmaking
```

### File Naming

After replacement, rename files:

- `apollo-{slug}.php` → `apollo-users.php`
- `assets/css/{slug}.css` → `assets/css/users.css`
- `assets/js/{slug}.js` → `assets/js/users.js`

### Structure

```
apollo-{slug}/
├── apollo-{slug}.php          # Main plugin file
├── composer.json              # PSR-4 autoload config
├── uninstall.php              # Cleanup on delete
├── README.md                  # Documentation
│
├── includes/
│   ├── constants.php          # Plugin constants
│   └── functions.php          # Helper functions
│
├── src/                       # PSR-4 Classes
│   ├── Plugin.php             # Main singleton
│   ├── Activation.php         # Activation handler
│   ├── Deactivation.php       # Deactivation handler
│   ├── API/                   # REST Controllers
│   └── Components/            # Feature classes
│
├── assets/
│   ├── css/
│   └── js/
│
├── templates/                 # PHP templates
│
└── languages/                 # i18n files
```

### Connection to Apollo Core

All Apollo plugins:

1. Check for `apollo-core` on load
2. Use `Apollo\{Namespace}` namespace
3. Register with priority > 10 on `plugins_loaded`
4. Access core via `apollo_core()` helper
5. Use shared taxonomies as BRIDGE (sounds, seasons)

### Registry Compliance

Check `_inventory/apollo-registry.json` for:

- Required tables
- Meta keys
- REST endpoints
- Virtual pages
- Shortcodes
