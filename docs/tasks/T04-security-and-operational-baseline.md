# Task T04 --- Security and Operational Baseline

## Outcome

Establish the baseline security configurations, HTTP security headers, rate limiters, private storage boundaries, log rotation, and emergency recovery runbooks for Franchise Dekho in accordance with `docs/SECURITY_PRIVACY.md` and Hostinger shared hosting constraints.

## Scope

### Included

- Implemented `App\Http\Middleware\SecurityHeadersMiddleware` enforcing `X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`, `Referrer-Policy`, `Permissions-Policy`, and conditional HSTS.
- Configured dedicated rate limiters for `enquiry` (5/min per IP) and `auth` (5/min per IP) in `AppServiceProvider`.
- Configured daily log rotation with 14-day retention in `.env.example` and `config/logging.php`.
- Enforced private filesystem boundaries by disabling automatic file serving (`serve => false`) on the `local` private disk in `config/filesystems.php`.
- Enhanced `public/.htaccess` rewrite rules to block direct access to `.env`, `.sqlite`, `.log`, `.md`, and backup files.
- Documented emergency incident, disaster recovery, and Hostinger sibling directory layout in `docs/OPERATIONAL_RECOVERY_RUNBOOK.md`.
- Implemented and verified automated security tests (`SecurityBaselineTest`).

### Excluded

- Customer OTP SMS provider integration (deferred to Task T09/T13).
- Payment gateway webhook signature verification (Task T13).
- Role-based ACL policy implementations (Task T06/T10).

## Acceptance criteria

- [x] Enterprise security headers sent on all web responses.
- [x] Rate limiting configured for sensitive routes.
- [x] Private storage isolation confirmed; no automatic file serving for private storage.
- [x] `.htaccess` blocks direct file downloads of hidden/config files.
- [x] Recovery runbook covers database backup/restore, rollback, and unpublish workflows.
- [x] Automated security tests pass.

## Inspection findings

- **Relevant files/routes/components:**
  - `bootstrap/app.php`
  - `app/Http/Middleware/SecurityHeadersMiddleware.php`
  - `app/Providers/AppServiceProvider.php`
  - `config/filesystems.php`
  - `config/logging.php`
  - `public/.htaccess`
  - `tests/Feature/SecurityBaselineTest.php`
  - `docs/OPERATIONAL_RECOVERY_RUNBOOK.md`
- **Hosting constraints:**
  - Sibling directory deployment (`myfranchise` next to `public_html`) prevents web access to `.env` and application storage.
  - Daily log rotation prevents file exhaustion on shared hosting storage quotas.

## Plan

1. Create and register `SecurityHeadersMiddleware`.
2. Configure rate limiting rules in `AppServiceProvider`.
3. Harden `config/filesystems.php` and `public/.htaccess`.
4. Configure logging retention in environment files.
5. Create operational runbook for backups and incident response.
6. Write feature tests and verify.

## Risks and controls

- **Framing/Clickjacking:** Prevented with `X-Frame-Options: SAMEORIGIN`.
- **MIME Confusion:** Prevented with `X-Content-Type-Options: nosniff`.
- **Information Leaks:** Private storage routes disabled; stack traces hidden when `APP_DEBUG=false`.

## Verification

- **Automated tests:**
  - `php artisan test` -> 6 passed (27 assertions).
    - `SecurityBaselineTest::test_security_headers_are_present_on_web_requests` (Passed)
    - `SecurityBaselineTest::test_enquiry_rate_limiter_is_configured` (Passed)
    - `SecurityBaselineTest::test_auth_rate_limiter_is_configured` (Passed)
- **Results:**
  - All security checks passed.

## Review

- **Duplication review:** No duplicate middleware or overlapping route definitions.
- **Documentation updated:**
  - Created `docs/tasks/T04-security-and-operational-baseline.md`.
  - Created `docs/OPERATIONAL_RECOVERY_RUNBOOK.md`.
  - Updated `docs/IMPLEMENTATION_PLAN.md`.

## Status

Complete

## Commit

Pending commit.
