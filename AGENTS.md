# Repository-wide instructions for coding agents and contributors

## Project context

Build Franchise Dekho: an India-wide, managed business-opportunity
discovery and application-coordination platform. The intended stack is
Laravel/PHP, MySQL/MariaDB and server-rendered Blade with semantic HTML,
CSS and lightweight JavaScript. Target hosting is Hostinger shared
hosting. Visual direction is editorial sophistication with enterprise
credibility.

## Mandatory workflow

For every task, follow this sequence: 1. **Understand** --- restate the
task, user outcome, constraints and acceptance criteria. 2. **Inspect**
--- inspect relevant files, routes, components, models, migrations,
services, tests, styles and similar functionality. If the repository is
empty, state that and verify environment constraints. 3. **Plan** ---
propose the smallest coherent change and identify risks. 4.
**Implement** --- change only what the task requires. 5. **Test** ---
run appropriate automated and manual checks. 6. **Fix** --- resolve
failures introduced by the change. 7. **Review for duplication** ---
reuse existing patterns; remove unnecessary duplication introduced by
the task. 8. **Commit** --- commit only when repository access and Git
permissions are available and the task is reviewed.

One task at a time. Do not silently bundle unrelated roadmap tasks.

## Reuse and scope

-   Inspect before creating. Prefer extending existing sound code over
    duplicating it.
-   Reuse established routes, components, services, policies, validation
    patterns and design tokens.
-   Do not perform unrelated redesigns, broad refactors or dependency
    upgrades.
-   Do not modify Owner functionality or other existing protected
    functionality unless explicitly required; explain any unavoidable
    impact before changing it.
-   Do not add a framework or package without a documented reason.
-   Keep changes compatible with the actual hosting plan and supported
    PHP/Laravel versions.

## Security and privacy

-   Enforce authorization on the server; hiding a button is not access
    control.
-   Validate all untrusted input; use framework query bindings/ORM
    rather than concatenated SQL.
-   Escape output by default and use CSRF protection on state-changing
    web forms.
-   Protect sessions, uploads, downloads, webhooks, logs and secrets.
-   Never log passwords, OTP values, session tokens, API secrets,
    payment credentials or full sensitive documents.
-   Never expose `.env`, source files, backups, logs, private storage or
    the application root over the web.
-   Record consent and authorization for customer document sharing and
    application submission.
-   Avoid collecting personal data without a defined purpose, lawful
    basis, retention period and access controls.

## Product integrity

-   A listing cannot become public solely because an AI score is high.
-   Publication requires the approved verification workflow and current
    evidence.
-   Do not invent company authorization, fees, territory availability,
    investment ranges, outcomes, reviews or success rates.
-   Distinguish company-provided information from independently verified
    information.
-   No guaranteed approval, income, profit or business success claims.
-   AI must not independently approve opportunities, submit
    applications, disclose customer data or authorize payments.

## Design quality

-   Do not create generic AI-template UI: avoid repetitive card grids,
    gratuitous gradients, decorative shapes, excessive shadows and
    unnecessary glassmorphism.
-   Follow `docs/DESIGN_POLICY.md` and `designs/ART_DIRECTION.md`.
-   Prioritize typography, hierarchy, spacing, content clarity,
    accessibility, responsive behavior and purposeful interaction.
-   Use existing tokens and components; don't create one-off styles when
    a reusable pattern is appropriate.
-   Target WCAG 2.2 AA, subject to practical testing.

## Testing and reporting

-   Add or update tests for behavior changed.
-   Test permissions and negative cases, not only happy paths.
-   State exact commands run and outcomes. Clearly distinguish tests not
    run from tests that failed.
-   Never claim code was implemented, tests passed, a commit was created
    or deployment succeeded unless verified.
-   Update docs when behavior or architecture decisions change.

## Completion checklist

-   Acceptance criteria met.
-   Scope remains limited.
-   Relevant tests run and failures fixed or documented.
-   Security, privacy, accessibility and responsive behavior considered.
-   Duplication review completed.
-   Docs updated when needed.
-   Changes reviewed and committed only when possible.
