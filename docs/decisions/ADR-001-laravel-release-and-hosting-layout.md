# ADR-001: Laravel 11.x Selection and Hostinger Shared Hosting Deployment Layout

- Status: Accepted
- Date: 2026-10-19
- Decision owner: Technical Lead / Core Agent

## Context

Franchise Dekho is an India-wide managed business-opportunity discovery platform.
The target production host is Hostinger shared hosting running Apache/LiteSpeed on CloudLinux.
The local development environment has been audited and contains PHP 8.2.30, Composer 2.9.3, Node.js v22.13.1, and Git 2.48.1.
We need to select the appropriate framework version and define a secure hosting deployment directory layout before creating application files.

## Options considered

### Framework Version

1. **Laravel 10.x:**
   - *Benefits:* Mature ecosystem, widely used. Requires PHP >= 8.1 <= 8.3.
   - *Costs:* Older structure with more boilerplate files; active bugfix support window is winding down.
2. **Laravel 11.x (Selected):**
   - *Benefits:* Requires PHP >= 8.2 (satisfied by both local PHP 8.2.30 and Hostinger PHP 8.2/8.3). Streamlined directory structure, native SQLite support for rapid and isolated automated testing, improved rate limiting, modern Blade features, and extended active support lifecycle.
   - *Costs:* Strict minimum requirement of PHP 8.2 (already met).
3. **Plain PHP / Custom Framework:**
   - *Benefits:* Minimal footprint.
   - *Costs:* High maintenance, lacks out-of-the-box CSRF protection, secure authentication, session handling, query binding, and robust validation tooling required by the security policy.

### Hosting Directory Layout

1. **Option A: Entire application in `public_html` with `.htaccess` rewrites:**
   - *Costs:* Dangerous on shared hosting. If `.htaccess` is bypassed, misconfigured, or overwritten, `.env`, SQLite files, source code, and application logs become publicly downloadable.
2. **Option B: Isolated application root outside `public_html` (Selected):**
   - Place application core files in a private directory (e.g. `~/myfranchise/` or `~/laravel_app/` above `public_html`).
   - Only expose the `public/` directory via `public_html` (either by configuring document root in hPanel, using a symbolic link, or routing through `public_html/index.php` pointing to the external bootstrap path).
   - *Benefits:* Immune to web-accessible source code leaks. Guarantees that private storage, logs, and environment configurations remain inaccessible via HTTP.

## Decision

1. Adopt **Laravel 11.x** as the core backend framework.
2. Implement **Option B** for hosting deployment: strictly separate web-accessible assets (`public/`) from application core code and environment configurations (`.env`, `storage/`, `bootstrap/`).
3. Use **SQLite** for local development and rapid automated test execution, while maintaining full compatibility with **MySQL 8.0 / MariaDB** for production on Hostinger.

## Consequences

- **Positive:**
  - Modern, secure defaults with zero risk of `.env` or internal file leaks on shared hosting.
  - Development tests can run in-memory or on local SQLite without needing a local MySQL server running.
  - Clear architectural boundaries aligned with `docs/ARCHITECTURE.md` and `docs/SECURITY_PRIVACY.md`.
- **Negative/trade-offs:**
  - Deployment to Hostinger requires placing files into two directory locations or configuring the document root in hPanel.
- **Security/privacy:**
  - Prevents directory traversal and exposure of sensitive credentials, meeting non-negotiable security requirements.
- **Hosting/operations:**
  - Hostinger hPanel allows setting PHP version to 8.2 or 8.3 and supports standard Composer/cron execution.
- **Migration/rollback:**
  - Standard Laravel migrations and rollback procedures apply.

## Revisit when

Revisit if Hostinger infrastructure changes to VPS/Docker containers or if PHP version upgrades beyond 8.3 are required.
