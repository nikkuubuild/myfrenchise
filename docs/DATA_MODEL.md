# Conceptual Data Model

This document defines logical entities for planning only. It is not a
migration plan. Add schema incrementally when the related feature is
approved.

## Core identity and access

-   `users`: account identity and status.
-   `roles`, `permissions`, role/permission assignments: least-privilege
    access.
-   `sessions` and security/login events: only appropriate metadata;
    never secrets.
-   `consents`: user, purpose, notice/version, action, timestamp and
    withdrawal where relevant.

## Geography

-   `states`, `districts`, `subdistricts`, `localities`, `pincodes` as
    required by chosen data sources.
-   `user_location_preferences`: user-selected operating locations.
-   Do not create an unnecessarily deep administrative hierarchy before
    source data and use cases are confirmed.

## Companies and opportunities

-   `companies`: legal/operating identity and approved public details.
-   `company_contacts`: controlled contact data and provenance.
-   `categories`.
-   `opportunities`: name, description, lifecycle status and
    public/private fields.
-   `opportunity_locations` and/or `territory_availability`.
-   `opportunity_requirements`: eligibility, infrastructure, licenses
    and documentation.
-   `opportunity_financials`: cost components, ranges, source, date and
    uncertainty.
-   `opportunity_documents`: metadata and access rules, not public file
    paths.

## Verification

-   `verification_cases`.
-   `verification_checks`: check type, outcome, reviewer, date and
    validity.
-   `verification_evidence`: source, retrieval date, secure location and
    access policy.
-   `verification_approvals` and status history.
-   `risk_incidents` and suspension/unpublication reasons.

## Customer and matching

-   `customer_profiles` and `assessments`.
-   `customer_preferences`.
-   `matches` or recommendation snapshots with explainable reason codes
    and model/version.
-   `shortlists` and shortlist items.

## Managed cases

-   `service_cases`: customer, scope, status, owner and timestamps.
-   `case_assignments`.
-   `tasks` and task/status history.
-   `applications` and application/status history.
-   `customer_documents`, consented sharing records and document access
    logs.
-   `communications` with provider/message references and redaction.
-   `support_tickets` and complaint records.

## Commerce

-   `service_catalogue`, `orders`, `payment_transactions`,
    `webhook_events`, `invoices`, `refunds`.
-   Store provider references, amounts, currency, state and necessary
    metadata.
-   Never store raw card numbers, CVV, UPI PIN or payment authentication
    secrets.
-   Make webhook processing idempotent and verify signatures
    server-side.

## Content and operations

-   `content_pages`, `articles`, `redirects`, SEO metadata and
    publication history.
-   `notifications` and delivery outcomes.
-   `audit_logs` for sensitive staff and system actions.
-   `settings` with strict separation of secrets from ordinary settings.

## Modeling rules

-   Record provenance and dates for material business claims.
-   Separate public data from private evidence and internal notes.
-   Use foreign keys, constraints and indexes based on actual access
    patterns.
-   Define status transitions explicitly; do not accept arbitrary status
    strings from clients.
-   Keep audit events append-oriented where practical.
-   Define retention, deletion and anonymization with legal review.
-   Use transactions for multi-record business changes.
