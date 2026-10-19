# Technical Architecture

## 1. Baseline

-   Hosting target: Hostinger shared hosting.
-   Backend: Laravel/PHP.
-   Database: MySQL/MariaDB.
-   UI: Blade-rendered HTML, CSS and lightweight JavaScript.
-   Architecture: modular monolith.
-   Public pages: server-rendered and indexable where appropriate.
-   Deployment: Git/SSH/Composer where the actual plan supports them.

This is a proposed architecture. Confirm current host capabilities
before choosing exact Laravel/PHP versions or deployment commands.

## 2. Compatibility gate

Before installation, record: - Hostinger plan and server details. - PHP
CLI and web PHP versions. - Required PHP extensions. - Composer version
and dependency-install capability. - SSH/Git access and deployment
method. - MySQL/MariaDB version, database limits and connection
settings. - Cron/scheduled-task availability. - Public document-root
control. - Writable storage/cache/log directories. - TLS/HTTPS, backups,
file and database size limits. - Outbound network restrictions and
supported email/SMS integration paths.

Choose a Laravel release compatible with all required PHP extensions and
host capabilities. Do not downgrade security or expose private
directories to make a release work. If shared hosting cannot safely
support a requirement, document the limitation and propose a compatible
alternative before proceeding.

## 3. Application boundaries

Suggested logical areas: - Identity and access. - Geography and customer
preferences. - Companies and opportunities. - Verification and
publication. - Customer assessment and matching. - Shortlists and
comparisons. - Managed-service cases and applications. - Documents and
consent. - Tasks and communications. - Billing and payment events. -
Notifications. - Content and SEO. - Audit, complaints and system
settings.

Keep these as clear namespaces/services within one Laravel application
initially; do not split into separate deployable services without
measured need.

## 4. Laravel conventions

-   Routes define HTTP entry points.
-   Form Request classes validate complex inputs.
-   Controllers remain thin and delegate business decisions.
-   Policies/Gates enforce authorization.
-   Services/domain classes hold reusable business workflows.
-   Eloquent models and migrations define persistence.
-   Jobs/queues only where the hosting plan supports reliable execution;
    synchronous or cron-backed fallback must be deliberate.
-   Blade layouts/components implement shared UI patterns.
-   Feature tests cover routes and end-to-end business rules.
-   Unit tests cover isolated rules where valuable.

## 5. Suggested directory structure

Use Laravel's standard structure. Add domain-specific directories only
when needed. Example: - `app/Http/Controllers` - `app/Http/Requests` -
`app/Models` - `app/Policies` - `app/Services` - `app/Notifications` -
`database/migrations` - `resources/views/layouts` -
`resources/views/components` - `resources/views/pages` -
`resources/css` - `resources/js` - `routes/web.php` - `tests/Feature` -
`tests/Unit`

Do not create all planned models/tables at once. Implement schema per
task.

## 6. Public/private boundaries

-   Only Laravel's `public/` directory should be web-accessible.
-   `.env`, source code, logs, backups, private documents and storage
    must not be served publicly.
-   Private files should be stored outside the public web root where
    possible and delivered through an authorized controller or signed
    temporary URL strategy.
-   Never trust a client-supplied file path or case ID; verify
    authorization server-side.
-   Separate public opportunity fields from internal verification notes
    and evidence.

## 7. Environment and configuration

-   Keep `.env` local/server-only and out of Git.
-   Commit a sanitized `.env.example` with variable names, not real
    secrets.
-   Use different credentials and configuration for
    local/staging/production.
-   Never put secrets in front-end JavaScript, committed config,
    screenshots or logs.
-   Use production-safe error display and restricted logs.

## 8. Database

-   Use migrations and foreign keys for schema evolution.
-   Define appropriate unique constraints and indexes based on real
    queries.
-   Use transactions for multi-step operations such as application
    status changes and payment state updates.
-   Use idempotency for webhook and repeatable external events.
-   Avoid hard deletion of important audit/business records; define
    retention and deletion/anonymization rules with legal review.
-   Use seeders only for non-sensitive sample/reference data; never seed
    production with fake public opportunities.

## 9. External integrations

Integrations are separate adapters/services behind configuration. Add
them only when selected: - SMS/OTP provider. - Email provider. - Payment
gateway. - IP geolocation database/API. - Analytics/consent tooling. -
AI model/API.

Provide timeouts, error handling, safe retries, redacted logs and a
manual fallback for operationally critical steps.

## 10. Observability and operations

-   Log request/correlation IDs and business events without secrets.
-   Track failures in OTP, payments, notifications and uploads.
-   Provide admin-visible status and retry/manual-resolution workflows
    where appropriate.
-   Schedule backups and test restore procedures.
-   Confirm cron and queue behavior on the actual host.
-   Document deploy, rollback and migration procedures before production
    launch.

## 11. Architectural decision record (ADR)

For consequential choices, add an ADR in `docs/decisions/`: - Context
and problem. - Options considered. - Decision. - Consequences and
trade-offs. - Date and status. Do not treat unverified assumptions as
final decisions.
