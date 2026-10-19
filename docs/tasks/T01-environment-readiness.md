# Task T01 --- Repository and Hosting Readiness

## Outcome

Establish verified baseline for repository status, local development environment, Hostinger shared hosting constraints, deployment layout, and framework version selection before starting application implementation.

## Scope

### Included

- Verify repository state, Git configuration, and remote access.
- Audit local PHP version, CLI extensions, Composer, Node.js, and database drivers.
- Document Hostinger shared hosting capabilities, restrictions, and security requirements.
- Formulate and record Architectural Decision Record ADR-001 (Laravel release selection and deployment directory layout).
- Verify pre-conditions for Task T02 (Product & Design baseline) and Task T03 (Laravel foundation).

### Excluded

- Installing application framework code (deferred to Task T03).
- Provisioning remote Hostinger infrastructure (requires production deployment phase).
- Implementing UI components or database models.

## Acceptance criteria

- [x] Repository state, Git config, and remotes inspected and reported.
- [x] Local PHP version, Composer, Node.js, and required extensions audited.
- [x] Hostinger shared hosting environment constraints and web root protection documented.
- [x] Architectural Decision Record ADR-001 produced detailing Laravel 11.x selection and deployment layout.
- [x] No unverified environment assumptions remain blocking T02/T03.

## Inspection findings

- **Relevant files/routes/components:**
  - `README.md`, `AGENTS.md`, `docs/ARCHITECTURE.md`, `docs/IMPLEMENTATION_PLAN.md`.
- **Existing patterns to reuse:**
  - `templates/TASK_TEMPLATE.md` for task tracking.
  - `templates/DECISION_RECORD_TEMPLATE.md` for architectural decision records.
- **Dependencies:**
  - PHP: `8.2.30` CLI (NTS x64) verified.
  - Composer: `2.9.3` verified via `php C:\php\composer`.
  - Node.js: `v22.13.1` verified.
  - Git: `2.48.1.windows.1` verified.
  - Active PHP extensions: `bcmath`, `curl`, `dom`, `fileinfo`, `filter`, `gd`, `hash`, `iconv`, `intl`, `json`, `libxml`, `mbstring`, `mysqli`, `mysqlnd`, `openssl`, `pcre`, `PDO`, `pdo_mysql`, `pdo_sqlite`, `session`, `SimpleXML`, `sodium`, `sqlite3`, `tokenizer`, `xml`, `xmlreader`, `xmlwriter`, `zip`, `zlib`.
- **Hosting constraints (Hostinger Shared):**
  - Web root isolation: Only Laravel's `public/` directory must be accessible via HTTP/web.
  - PHP version compatibility: Hostinger hPanel supports PHP 8.2 and 8.3.
  - Background processes: Long-running daemons are not reliably supported; background work must run via cron-scheduled `artisan schedule:run` or synchronous execution.
  - Database: MySQL 8.0 / MariaDB 10.x.

## Plan

1. Document complete environment findings in task record.
2. Draft ADR-001 proposing Laravel 11.x and safe directory separation for Hostinger.
3. Update `docs/IMPLEMENTATION_PLAN.md` tracking progress.
4. Prepare repository for Task T02 and T03.

## Risks and controls

- **Security/privacy:** Direct exposure of `.env` or application root on shared hosting must be prevented by deployment layout.
- **Data migration:** None for T01; database connection parameters will be configured strictly through environment variables.
- **UX/accessibility:** N/A for environment audit.
- **Deployment/rollback:** Hostinger deployment layout established before any codebase installation to avoid refactoring later.

## Verification

- **Automated checks:**
  - `php -v` -> PHP 8.2.30.
  - `php C:\php\composer --version` -> Composer 2.9.3.
  - `node -v` -> v22.13.1.
  - `php -m` -> All core Laravel extensions present.
  - `git status` -> Clean branch detection and staged documentation baseline.
- **Manual checks:**
  - Verified extension support for SQLite (local fast testing) and MySQL (production database).
  - Verified Hostinger directory structure requirements.
- **Results:**
  - All prerequisite tools exist and meet Laravel 11.x requirements.

## Review

- **Duplication review:** No duplicate files or redundant configurations created; followed existing template structure.
- **Documentation updated:**
  - Created `docs/tasks/T01-environment-readiness.md`.
  - Created `docs/decisions/ADR-001-laravel-release-and-hosting-layout.md`.
  - Updated `docs/IMPLEMENTATION_PLAN.md` with T01 completion status.
- **Remaining issues:**
  - Specific domain name and FTP/SSH credentials for the live Hostinger account will be needed when ready to deploy.

## Status

Complete

## Commit

Pending initial repository commit.
