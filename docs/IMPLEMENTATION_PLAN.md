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

### T11 --- Documents and consent (P1)

Private uploads/downloads, file validation, access logs and explicit
consent for document sharing/application submission.

### T12 --- Legal, privacy and cookies (P1)

Implement counsel-reviewed policies, consent/choice UX, retention
workflows, grievance/fraud routes and data request handling.

## Phase 3 --- Integrations and growth

### T13 --- Notifications and payments (P2)

Select providers; implement delivery outcomes, webhook signature checks,
idempotency, billing, refunds and transparent fees.

### T14 --- SEO and content system (P1)

Sitemaps, metadata, canonical URLs, structured data where valid, crawl
controls, editorial workflow and content freshness.

### T15 --- AI-assisted discovery (P2)

Only after verified structured data exists. Ground answers in approved
records, show source/last-verified dates, handle uncertainty and require
human review for consequential actions.

### T16 --- Production readiness (P0 before public launch)

Security/permission tests, accessibility, performance, SEO validation,
backup restore, monitoring, incident/complaint handling, legal review
and release/rollback checklist.

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
