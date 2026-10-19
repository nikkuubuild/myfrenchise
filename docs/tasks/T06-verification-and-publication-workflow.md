# Task T06 --- Verification and Publication Workflow

## Outcome

Implement the verification workflow ensuring no opportunity is published without verified evidence across the 8 standard audit checkpoints (MCA entity registration, official contact authority, brand trademark/franchisor mandate, active status, territory availability, itemized fee schedules, infrastructure requirements, and contract caveats). Enforce append-only audit events, re-review dates, and emergency suspension controls.

## Scope

### Included

- Relational migrations for:
  - `verification_cases`: Tracks opportunity audits, reviewer assignment, decision, validity window, and re-review dates.
  - `verification_checks`: Tracks 8 mandatory checkpoints with provenance citations, reviewer sign-offs, and secure evidence paths.
  - `verification_audit_events`: Append-only audit history logging state transitions, actor ID, IP address, and incident reasons.
- Eloquent models: `VerificationCase`, `VerificationCheck`, and `VerificationAuditEvent`.
- Relations on `Opportunity`: `verificationCases()`, `latestVerificationCase()`.
- Domain service: `App\Services\VerificationService` implementing `createCase()`, `passCheck()`, `approveAndPublish()`, `suspendOpportunity()`, and `rejectCase()`.
- Feature tests in `tests/Feature/VerificationWorkflowTest.php` asserting strict check enforcement, suspension hiding, and audit event creation.

### Excluded

- Admin web UI forms and evidence upload interfaces (scheduled for Task T10 / T16).
- Customer portal document vault and consent tokens (Task T11).

## Acceptance criteria

- [x] Schema enforces relational links between opportunities, companies, checks, and audit logs.
- [x] Publication is strictly prevented if any mandatory check is incomplete.
- [x] Approving verification stamps `last_verified_at`, sets `verification_valid_until`, marks opportunity `published`, and logs audit events.
- [x] Emergency suspension immediately revokes public visibility.
- [x] 100% automated test pass rate for verification logic.

## Inspection findings

- **Relevant files/routes/components:**
  - `database/migrations/2026_10_19_000007_create_verification_cases_table.php`
  - `database/migrations/2026_10_19_000008_create_verification_checks_table.php`
  - `database/migrations/2026_10_19_000009_create_verification_audit_events_table.php`
  - `app/Models/VerificationCase.php`, `VerificationCheck.php`, `VerificationAuditEvent.php`
  - `app/Services/VerificationService.php`
  - `tests/Feature/VerificationWorkflowTest.php`
- **Existing patterns to reuse:**
  - 8 standard check types defined in `skills/opportunity-verification/SKILL.md`.
  - Append-only audit structure per `docs/SECURITY_PRIVACY.md`.

## Plan

1. Migrate verification cases, checks, and audit events.
2. Build Eloquent models with relations.
3. Build `VerificationService` with strict guardrails.
4. Implement automated feature tests.
5. Record task completion and update implementation plan.

## Risks and controls

- **Premature / Bogus Publication:** The service throws `DomainException` if all mandatory checks are not marked as passed.
- **Audit Tampering:** Audit events table is append-only (`const UPDATED_AT = null`).
- **Dispute Response:** `suspendOpportunity()` method allows instant revocation of public visibility without data loss.

## Verification

- **Automated tests:**
  - `php artisan test` -> 15 passed (70 assertions).
    - `test_create_case_initializes_standard_checks` (Passed)
    - `test_cannot_publish_when_mandatory_checks_are_incomplete` (Passed)
    - `test_passing_all_mandatory_checks_approves_and_publishes_opportunity` (Passed)
    - `test_suspend_opportunity_hides_it_from_catalog_and_logs_audit` (Passed)
- **Results:**
  - 100% pass rate.

## Review

- **Duplication review:** Reused existing Opportunity model scopes and relations.
- **Documentation updated:**
  - Created `docs/tasks/T06-verification-and-publication-workflow.md`.
  - Updated `docs/IMPLEMENTATION_PLAN.md`.

## Status

Complete

## Commit

Pending commit.
