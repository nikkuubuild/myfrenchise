# Implementation Plan and Task Register

## Workflow

One task at a time: Understand → Inspect → Plan → Implement → Test → Fix
→ Review for duplication → Commit. Do not begin the next task until the
current task is reviewed and its outcome is reported.

## Phase 0 --- Readiness and foundations

### T01 --- Repository and hosting readiness (P0) [COMPLETED]

-   Verify actual repository state and access: Staged documentation pack verified, Git configured.
-   Check Hostinger plan, PHP/web and CLI versions, extensions,
    Composer, SSH/Git, database version, cron, writable directories,
    document-root control, TLS and deployment path: PHP 8.2.30 CLI, Composer 2.9.3, Node v22.13.1, SQLite/MySQL verified.
-   Decide compatible Laravel release and safe deployment layout: Laravel 11.x selected with isolated private root deployment.
-   Deliverable: Environment report in `docs/tasks/T01-environment-readiness.md` and decision record in `docs/decisions/ADR-001-laravel-release-and-hosting-layout.md`.
-   Acceptance: Complete. All baseline assumptions verified.

### T02 --- Product and design baseline (P0) [COMPLETED]

-   Review PRD, design policy and art direction: Completed against editorial standard.
-   Define sitemap, user journeys, initial wireframes and design tokens: Defined in `designs/DESIGN_TOKENS.md`, `designs/SITEMAP_AND_JOURNEYS.md`, and `designs/SCREEN_SPECIFICATIONS.md`.
-   Deliverable: Approved first-screen specifications, component inventory, and task record in `docs/tasks/T02-product-and-design-baseline.md`.
-   Acceptance: Complete. Design system tokens and screen specifications ready for implementation.

### T03 --- Laravel application foundation (P0) [COMPLETED]

-   Install compatible Laravel release: Laravel 11.57.0 on PHP 8.2.30 installed with Composer.
-   Configure environment example, database, routes, shared layout, error handling and baseline tests: Configured with SQLite/MySQL, Vite + Tailwind design tokens, editorial header/footer, UI components, and `/health` route.
-   Deliverable: App boots locally and baseline tests pass (`Tests: 3 passed (12 assertions)`). Task record in `docs/tasks/T03-laravel-application-foundation.md`.
-   Acceptance: Complete. Foundation ready for feature development.

### T04 --- Security and operational baseline (P0) [COMPLETED]

-   Configure safe production errors, sessions, CSRF, rate limits, secrets, storage boundaries, logs, backups and deployment notes: Enforced `SecurityHeadersMiddleware`, rate limiters, storage isolation, and `.htaccess` file blocks.
-   Deliverable: Reviewed security baseline, runbook in `docs/OPERATIONAL_RECOVERY_RUNBOOK.md`, and task record in `docs/tasks/T04-security-and-operational-baseline.md`.
-   Acceptance: Complete. Automated security tests pass (`Tests: 6 passed (27 assertions)`).

## Phase 1 --- Public MVP

### T05 --- Core opportunity data model (P1) [COMPLETED]

Create only required companies, opportunities, categories, locations,
costs, requirements and publication state. Add provenance and
constraints: 6 relational tables migrated, Eloquent models built with scopes (`published`, `active`, `verified`, `inBudget`), factories, and database seeders created.
Deliverable: Migrations, models, seeders, test suite in `tests/Feature/OpportunityDataModelTest.php`, and task record in `docs/tasks/T05-core-opportunity-data-model.md`.
Acceptance: Complete. 100% test pass rate (`Tests: 11 passed (48 assertions)`).

### T06 --- Verification and publication workflow (P1) [COMPLETED]

Create verification cases/checks/evidence, reviewer approvals, re-review
dates, suspend/unpublish behavior and audit events. Publication requires
approved evidence: Migrations for cases, checks, and audit events, Eloquent models, `VerificationService` with guardrails, and feature tests in `tests/Feature/VerificationWorkflowTest.php`.
Deliverable: Service, models, migrations, tests, and task record in `docs/tasks/T06-verification-and-publication-workflow.md`.
Acceptance: Complete. 100% test pass rate (`Tests: 15 passed (70 assertions)`).

### T07 --- Public website and opportunity discovery (P1) [COMPLETED]

Build homepage, category/search, opportunity detail and managed-service
enquiry using the approved design system and server-rendered
SEO-friendly pages: Built `HomeController`, `OpportunityController`, `EnquiryController`, `PageController`, catalog filtering, opportunity profile with itemized CapEx, consented enquiry flow, and policy pages.
Deliverable: Views, controllers, components, tests in `tests/Feature/PublicDiscoveryTest.php`, and task record in `docs/tasks/T07-public-website-and-opportunity-discovery.md`.
Acceptance: Complete. 100% test pass rate across 23 automated tests (`Tests: 23 passed (115 assertions)`).

### T08 --- Customer assessment and matching (P1) [COMPLETED]

Build progressive assessment, hard eligibility filters, configurable
ranking and explainable match reasons. No score bypasses verification: Built `AssessmentMatchingService` with 3 hard filters and 5 weighted factors (budget, space, location, sector, experience), `AssessmentController`, intake & results views, and automated tests.
Deliverable: Service, controller, views, tests in `tests/Feature/AssessmentMatchingTest.php`, and task record in `docs/tasks/T08-customer-assessment-and-matching.md`.
Acceptance: Complete. 100% test pass rate across 30 automated tests (`Tests: 30 passed (137 assertions)`).

### T09 --- Customer authentication (P1) [COMPLETED]

Implement secure account/session behavior. Add OTP only after provider
and applicable messaging requirements are verified: Built user roles (`customer`, `case_manager`, `verification_officer`, `admin`), `RegisteredUserController`, `AuthenticatedSessionController`, `PasswordResetController`, `CustomerDashboardController`, `EnsureUserRole` middleware, linked enquiries, and authenticated layout.
Deliverable: Migrations, controllers, middleware, views, tests in `tests/Feature/AuthenticationTest.php`, and task record in `docs/tasks/T09-customer-authentication.md`.
Acceptance: Complete. 100% test pass rate across 45 automated tests (`Tests: 45 passed (188 assertions)`).

## Phase 2 --- Managed service

### T10 --- Case management (P1) [COMPLETED]

Implement case ownership, tasks, status history, communication log,
customer visibility boundaries and next-action UX: Built `CaseManagementService`, `Admin\CaseController`, models (`CaseStatusHistory`, `CaseTask`, `CaseCommunication`), migrations, pipeline view, case workspace with dossier & checklist, and strict customer visibility boundary protections.
Deliverable: Models, migrations, service, controller, views, tests in `tests/Feature/CaseManagementTest.php`, and task record in `docs/tasks/T10-case-management.md`.
Acceptance: Complete. 100% test pass rate across 54 automated tests (`Tests: 54 passed (230 assertions)`).

### T11 --- Documents and consent (P1) [COMPLETED]

Private uploads/downloads, file validation, access logs and explicit
consent for document sharing/application submission: Built `DocumentService`, `Admin\CaseDocumentController`, `Customer\CaseDocumentController`, models (`Document`, `DocumentAccessLog`, `DocumentConsent`), migrations, private storage on local disk, access logging, and customer consent grant/revoke workflows.
Deliverable: Models, migrations, service, controllers, views, tests in `tests/Feature/DocumentAndConsentTest.php`, and task record in `docs/tasks/T11-documents-and-consent.md`.
Acceptance: Complete. 100% test pass rate across 62 automated tests (`Tests: 62 passed (264 assertions)`).

### T12 --- Legal, privacy and cookies (P1) [COMPLETED]

Implement counsel-reviewed policies, consent/choice UX, retention
workflows, grievance/fraud routes and data request handling:
- Deployed statutory DPDPA 2023 Privacy Notice, Terms of Service & Advisory Disclaimer, and transparent Cookie Policy.
- Built non-intrusive, accessible cookie consent banner with client-side persistence and zero external advertising trackers.
- Implemented formal Grievance Redressal & Fraud Reporting system with designated Grievance Officer disclosures (IT Rules 2021), 24h acknowledgment and 15-day resolution SLAs, unique complaint tracking codes (`#FD-GRV-YYYYMM-XXXX`), public tracking lookup, and staff review queue at `/admin/grievances`.
- Implemented Data Principal Rights: machine-readable JSON personal data export (`/customer/data-export`) and self-service account erasure & anonymization (`/customer/data-deletion`) with password authentication.
- Implemented automated data retention command `php artisan privacy:scrub-expired`.
Deliverable: Migrations, models, controllers, views, console command, feature tests in `tests/Feature/LegalPrivacyAndCookiesTest.php`, and task record in `docs/tasks/T12-legal-privacy-and-cookies.md`.
Acceptance: Complete. 100% test pass rate across all 71 tests in the application suite (`Tests: 71 passed (342 assertions)`).

## Phase 3 --- Integrations and growth

### T13 --- Notifications and payments (P2) [COMPLETED]

Select providers; implement delivery outcomes, webhook signature checks,
idempotency, billing, refunds and transparent fees:
- Implemented transparent Service Package catalogue with itemized pricing, deliverables, exclusions, and cancellation policies.
- Built server-side HMAC SHA256 payment signature verification (`PaymentService`) enforcing rule that browser redirects alone never mark payments as successful.
- Implemented automated statutory Indian GST Tax Invoice generation (`InvoiceService`) with intra-state (CGST+SGST) vs inter-state (IGST) tax split and SAC 998311 code.
- Implemented idempotent gateway webhook processor (`WebhookController`) with signature verification and replay deduplication (`webhook_events`).
- Built multi-channel transactional notification service (`NotificationService`) logging delivery across email, SMS/DLT, and in-app channels with regulatory DLT template IDs.
- Built customer checkout, order tracking, and GST invoice download interfaces, and staff billing review queue with authorized refunds.
Deliverable: Migrations, models, services, controllers, views, seeders, feature tests in `tests/Feature/NotificationAndPaymentTest.php`, and task record in `docs/tasks/T13-notifications-and-payments.md`.
Acceptance: Complete. 100% test pass rate across all 80 tests in the application suite (`Tests: 80 passed (403 assertions)`).

### T14 --- SEO and content system (P1) [COMPLETED]

Sitemaps, metadata, canonical URLs, structured data where valid, crawl
controls, editorial workflow and content freshness:
- Implemented standard crawl controls (`/robots.txt`) with search/AI bot directives and strict `noindex, nofollow` on non-public routes.
- Built dynamic XML sitemap (`/sitemap.xml`) indexing verified opportunities, sector landings, and published guides with `<lastmod>` audit dates.
- Implemented canonical URLs, OpenGraph, Twitter card metadata, and Schema.org structured data (`Organization`, `WebSite`, `BreadcrumbList`, `Service`, `Article`).
- Built sector-specific landing pages (`/sectors/{slug}`) with Indian regulatory licensing context and verified listings.
- Created authoritative Due Diligence Knowledge Hub (`/guides` & `/guides/{slug}`) with statutory citations, disclaimer, and seeded guides.
- Built staff editorial management queue (`/admin/articles`) with 180-day content freshness tracking and re-verification actions.
Deliverable: Models, services, controllers, views, seeder, feature tests in `tests/Feature/SeoAndContentTest.php`, and task record in `docs/tasks/T14-seo-and-content-system.md`.
Acceptance: Complete. 100% test pass rate across all 90 tests in the application suite (`Tests: 90 passed (493 assertions)`).

### T15 --- AI-assisted discovery (P2) [COMPLETED]

Only after verified structured data exists. Ground answers in approved
records, show source/last-verified dates, handle uncertainty and require
human review for consequential actions:
- Implemented `AiDiscoveryService` strictly grounding responses in approved, verified records (`Opportunity::published()`).
- Added non-negotiable safety guardrails preventing AI payment authorization, automated application approvals, and fraudulent earnings promises.
- Surfaced verifiable attribution (company legal entity, MCA CIN, itemized CapEx, audit date) and transparently disclosed missing physical evidence.
- Built interactive discovery workspace (`/assistant` & `/assistant/query`) with prompt chips and rate limiting (`throttle:ai_discovery`).
- Added mandatory non-negotiable AI governance and regulatory notices.
Deliverable: Service, controller, view, feature tests in `tests/Feature/AiDiscoveryTest.php`, and task record in `docs/tasks/T15-ai-assisted-discovery.md`.
Acceptance: Complete. 100% test pass rate across all 99 tests in the application suite (`Tests: 99 passed (546 assertions)`).

### T16 --- Production readiness (P0 before public launch) [COMPLETED]

Security/permission tests, accessibility, performance, SEO validation,
backup restore, monitoring, incident/complaint handling, legal review
and release/rollback checklist:
- Engineered custom editorial error templates (403, 404, 419, 429, 500, 503) with zero information or stack trace leakage.
- Hardened Apache web server configuration in `public/.htaccess` (HTTPS redirection, `mod_headers` security headers defense-in-depth, `mod_expires` caching, dotfile/directory protection).
- Updated `.env.example` with sanitized production configuration covering Razorpay, AI discovery, database, and secure cookie parameters.
- Implemented automated pre-flight diagnostic command `php artisan app:production-check` (`app/Console/Commands/ProductionCheckCommand.php`).
- Authored authoritative Hostinger shared hosting release runbook and fast rollback procedure in `docs/RELEASE_CHECKLIST.md`.
Deliverable: Views, `.htaccess`, `.env.example`, Artisan command, runbook, feature tests in `tests/Feature/ProductionReadinessTest.php`, and task record in `docs/tasks/T16-production-readiness.md`.
Acceptance: Complete. 100% test pass rate across all 110 tests in the application suite (`Tests: 110 passed (591 assertions)`).

## Dependencies

-   T01 before selecting versions or deployment instructions.
-   T02 before large-scale UI implementation.
-   T03 before application features.
-   T05 and T06 before public opportunity publication.
-   T10/T11 before handling sensitive customer applications/documents.
-   T12 and relevant legal review before production collection/charging.
-   T13 only after provider, pricing and refund decisions.
-   T15 only after trustworthy, current structured data and guardrails
    exist.

## Change control

Any new request should identify task ID, scope, acceptance criteria,
affected files/modules, risks and tests. Reprioritize explicitly rather
than silently mixing unrelated tasks.
