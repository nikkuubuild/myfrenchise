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

### T03 --- Laravel application foundation (P0)

-   Install compatible Laravel release.
-   Configure environment example, database, routes, shared layout,
    error handling and baseline tests.
-   Deliverable: app boots locally/in target environment and basic
    checks pass.

### T04 --- Security and operational baseline (P0)

-   Configure safe production errors, sessions, CSRF, rate limits,
    secrets, storage boundaries, logs, backups and deployment notes.
-   Deliverable: reviewed security baseline and recovery procedure.

## Phase 1 --- Public MVP

### T05 --- Core opportunity data model (P1)

Create only required companies, opportunities, categories, locations,
costs, requirements and publication state. Add provenance and
constraints.

### T06 --- Verification and publication workflow (P1)

Create verification cases/checks/evidence, reviewer approvals, re-review
dates, suspend/unpublish behavior and audit events. Publication requires
approved evidence.

### T07 --- Public website and opportunity discovery (P1)

Build homepage, category/search, opportunity detail and managed-service
enquiry using the approved design system and server-rendered
SEO-friendly pages.

### T08 --- Customer assessment and matching (P1)

Build progressive assessment, hard eligibility filters, configurable
ranking and explainable match reasons. No score bypasses verification.

### T09 --- Customer authentication (P1)

Implement secure account/session behavior. Add OTP only after provider
and applicable messaging requirements are verified.

## Phase 2 --- Managed service

### T10 --- Case management (P1)

Implement case ownership, tasks, status history, communication log,
customer visibility boundaries and next-action UX.

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
