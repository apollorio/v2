# Apollo Login

**Auth plugin for the Apollo ecosystem.** Standalone with apollo-core dependency for taxonomy data.

## Architecture

```
apollo-login/
├── apollo-login.php              # Main plugin (PSR-4 autoloader, dependency check)
├── composer.json                  # Namespace: Apollo\Login
├── uninstall.php                  # Clean removal of all data
├── includes/
│   ├── constants.php              # All meta keys, table names, page slugs
│   └── functions.php              # Helper functions (CPF validation, etc.)
├── src/
│   ├── Plugin.php                 # Singleton, rewrite rules, virtual pages
│   ├── Activation.php             # 4 tables, default options, rewrite flush
│   ├── Deactivation.php           # Cron/transient cleanup
│   ├── Auth/
│   │   ├── Login.php              # AJAX login w/ rate limiting + attempt logging
│   │   ├── Register.php           # 7-step registration, wp_insert_user, meta save
│   │   ├── PasswordReset.php      # Token-based reset with email
│   │   └── EmailVerification.php  # Token verify via link + AJAX
│   ├── Security/
│   │   ├── RateLimiter.php        # IP-based transient rate limiting
│   │   └── WPHideGhost.php        # 4-layer URL protection (no core mods)
│   ├── Quiz/
│   │   └── QuizHandler.php        # 4-stage aptitude test AJAX handlers
│   ├── API/
│   │   ├── AuthController.php     # 9 REST endpoints under /apollo/v1/auth/*
│   │   ├── QuizController.php     # Quiz + Simon REST endpoints
│   │   └── SecurityController.php # Admin-only security endpoints
│   ├── Shortcodes/
│   │   └── ShortcodeHandler.php   # 6 shortcodes per registry
│   └── Templates/
│       └── BlankCanvas.php        # Zero-theme-conflict asset loading
├── templates/
│   ├── login.php                  # Blank canvas: /acesso
│   ├── register.php               # Blank canvas: /registre
│   ├── password-reset.php         # Blank canvas: /reset
│   ├── verify-email.php           # Blank canvas: /verificar-email
│   └── parts/
│       ├── header.php             # Apollo::Rio branding
│       ├── footer.php             # Node info + copyright
│       ├── login-form.php         # Login w/ identity auto-detection
│       ├── register-form.php      # Multi-step w/ dynamic sound chips
│       ├── lockout-overlay.php    # Security lockout UI
│       └── aptitude-quiz.php      # 4-stage quiz overlay
└── assets/
    ├── css/login.css              # Full terminal UI styles
    └── js/login.js                # Auth logic, quiz, Simon, reactions
```

## Registry Compliance

- **Namespace:** `Apollo\Login`
- **Constants:** `APOLLO_LOGIN_*`
- **Tables:** `apollo_quiz_results`, `apollo_simon_scores`, `apollo_login_attempts`, `apollo_url_rewrites`
- **Meta keys:** 16 user meta keys per registry (`_apollo_social_name`, `_apollo_instagram`, etc.)
- **REST:** 15 endpoints under `apollo/v1/auth/*`, `apollo/v1/quiz/*`, `apollo/v1/simon/*`, `apollo/v1/security/*`
- **Pages:** `/acesso`, `/registre`, `/sair`, `/reset`, `/verificar-email`
- **Shortcodes:** `[apollo_login]`, `[apollo_register]`, `[apollo_quiz]`, `[apollo_simon]`, `[apollo_password_reset]`, `[apollo_verify_email]`

## apollo-core Integration

Sound taxonomy terms are fetched from `sound` GLOBAL BRIDGE taxonomy (registered by apollo-core). The register form dynamically populates chip-select UI with these terms for matchmaking data.

## Security Features

- WP Hide Ghost: 4-layer protection (wp-login.php → 404, wp-admin → 404, URL filtering, real 404 responses)
- Rate limiting: IP-based with configurable max attempts and lockout duration
- Login attempt logging to `apollo_login_attempts` table
- Nonce verification on all forms and AJAX handlers
- Honeypot anti-spam on registration
- Password hashing via `wp_insert_user` / `wp_set_password`
- Secure token generation for email verification and password reset
