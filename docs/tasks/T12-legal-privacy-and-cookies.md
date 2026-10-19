# Task T12 --- Legal, Privacy, and Cookies

## Outcome

Deploy counsel-reviewed statutory policies, DPDPA 2023 compliance controls, accessible cookie consent banner, grievance redressal and fraud reporting workflows, and customer data subject rights (export and erasure):
1. **Statutory Policies**:
   - Comprehensive Privacy Notice tailored to the Digital Personal Data Protection Act (DPDPA) 2023, establishing Franchise Dekho's role as Data Fiduciary, data minimization, and absence of lead selling.
   - Terms of Service & Advisory Disclaimer establishing non-negotiable platform limitations, independent verification boundaries, no profit guarantees, fee transparency, and Indian jurisdiction.
   - Cookie Policy outlining zero unsolicited tracking, no third-party surveillance pixels, and transparent first-party cookie inventory (`laravel_session`, `XSRF-TOKEN`, `fd_cookie_consent`).
2. **Accessible Cookie Consent UX**:
   - Lightweight, non-intrusive cookie consent banner (`<x-layout.cookie-banner />`) docked at the bottom of the viewport.
   - Respects essential vs optional preferences, stores user choice in first-party storage, and loads zero external advertising trackers.
3. **Statutory Grievance Redressal & Fraud Reporting**:
   - Official designation and publication of Chief Compliance & Grievance Officer details under IT (Intermediary Guidelines) Rules, 2021 and Consumer Protection (E-Commerce) Rules, 2020.
   - Statutory SLAs: 24-hour formal acknowledgment SLA and 15 working days resolution SLA.
   - Public interactive intake form (`POST /grievance-and-fraud`) generating immutable tracking codes (`#FD-GRV-YYYYMM-XXXX`) for reporting fee fraud, unauthorized brand impersonation, and privacy violations.
   - Public tracking lookup portal (`/grievance-and-fraud/track`) with email verification and milestone timeline.
   - Staff grievance queue and dossier review workspace at `/admin/grievances` for Case Managers and Admins.
4. **Data Principal Rights (DPDPA 2023)**:
   - Self-service machine-readable personal data export (`GET /customer/data-export`) streaming structured JSON archives of personal profile, commercial enquiries, vault document metadata, and third-party sharing consent logs.
   - Account erasure and identity anonymization workflow (`/customer/data-deletion`) requiring current password verification, revoking all active sharing consents, wiping personal identifiers, and terminating session.
5. **Automated Data Retention Scrubbing**:
   - Artisan command `php artisan privacy:scrub-expired` to scrub and anonymize orphaned guest enquiries older than the retention threshold (180 days) and minimize IP address retention on resolved grievances.

## Scope

### Included

- Migrations:
  - `2026_10_19_000016_create_grievances_table.php`: Stores formal grievances, tracking codes, categories, SLAs, evidence summaries, assignment, and statutory resolution findings.
- Models:
  - `App\Models\Grievance`: Category and reporter type constants, relations to `Opportunity`, `Company`, and assigned officer, and unique tracking code generator (`FD-GRV-YYYYMM-XXXX`).
- Controllers:
  - `App\Http\Controllers\GrievanceController`: Public grievance intake, confirmation receipt, and tracking lookup.
  - `App\Http\Controllers\Admin\GrievanceController`: Staff investigation queue, docket review, officer assignment, and statutory resolution orders.
  - `App\Http\Controllers\Customer\DataSubjectController`: Machine-readable personal data export and password-authenticated account erasure.
  - Updated `App\Http\Controllers\PageController`: Added `cookiePolicy()`.
- Artisan Commands:
  - `App\Console\Commands\ScrubExpiredPrivacyData`: Automated data scrubbing command (`privacy:scrub-expired`).
- Views & Components:
  - `resources/views/components/layout/cookie-banner.blade.php`: Accessible cookie consent banner with client-side persistence.
  - `resources/views/pages/cookie-policy.blade.php`: Complete first-party cookie audit table and zero-tracking pledge.
  - Updated `resources/views/pages/privacy.blade.php`: Detailed DPDPA 2023 disclosures, Data Principal rights, and self-service portal links.
  - Updated `resources/views/pages/terms.blade.php`: Comprehensive statutory limitations, fee transparency, and governing law.
  - Updated `resources/views/pages/grievance.blade.php`: Statutory Grievance Officer disclosures and structured public intake form.
  - `resources/views/pages/grievance-confirmed.blade.php`: Formal acknowledgment receipt with tracking code and SLA reminders.
  - `resources/views/pages/grievance-track.blade.php`: Confidential status lookup and milestone timeline.
  - `resources/views/admin/grievances/index.blade.php`: Staff investigation queue with SLA countdowns and filters.
  - `resources/views/admin/grievances/show.blade.php`: Staff complaint dossier, officer assignment, and resolution findings order.
  - `resources/views/customer/data-deletion.blade.php`: DPDPA right to erasure confirmation with password authentication.
  - Updated `resources/views/customer/dashboard.blade.php`: Added Data Principal Rights panel.
  - Updated `resources/views/admin/cases/index.blade.php`: Added navigation link to Grievance & Fraud Queue.
  - Updated `resources/views/components/layout/footer.blade.php`: Added Cookie Policy link.
  - Updated `resources/views/components/layout/app.blade.php`: Injected cookie banner.
- Routes:
  - Public routes: `/grievance-and-fraud`, `/grievance-and-fraud/confirmed`, `/grievance-and-fraud/track`, `/cookie-policy`.
  - Customer routes: `/customer/data-export`, `/customer/data-deletion` (GET/POST).
  - Admin routes: `/admin/grievances*` (under `role:case_manager,admin`).
- Automated feature tests in `tests/Feature/LegalPrivacyAndCookiesTest.php` (9 tests, 78 assertions). Total suite: 71 tests (342 assertions).

### Excluded

- In-browser legal document electronic signing engine (Aadhaar e-Sign / DigiLocker integration) — future release.
- Multilingual statutory disclosures in regional vernacular languages (T14 / future release).

## Acceptance criteria

- [x] Privacy notice incorporates all mandatory DPDPA 2023 provisions (Data Fiduciary role, Data Principal rights, Grievance Officer).
- [x] Terms of service explicitly state the absence of earnings or approval guarantees and clarify verification boundaries.
- [x] Cookie policy itemizes all first-party cookies with durations and explicitly notes absence of third-party advertising trackers.
- [x] Cookie consent banner respects user choices and stores preferences locally without blocking site rendering.
- [x] Grievance submission assigns unique tracking code and strictly enforces statutory declaration acknowledgment.
- [x] Public can look up complaint progress and statutory resolution findings with tracking code and matching email.
- [x] Staff queue enforces role-based access (`case_manager` and `admin` only), tracks 15-day resolution SLAs, and records official resolution findings.
- [x] Customers can download a complete, machine-readable JSON archive of personal data.
- [x] Customers can permanently erase and anonymize account with password verification and consent revocation.
- [x] Automated artisan command `privacy:scrub-expired` runs cleanly and anonymizes expired unlinked records.
- [x] 100% test pass rate across all 71 tests in the application suite.

## Verification

- `php artisan test --filter=LegalPrivacyAndCookiesTest`: 9 passed, 78 assertions.
- `php artisan test`: 71 passed, 342 assertions.
- `npm run build`: Compiled frontend assets in 4.19s without errors.
- `php artisan privacy:scrub-expired`: Successfully scanned and executed without errors.
