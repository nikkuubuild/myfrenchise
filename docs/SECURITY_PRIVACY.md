# Security and Privacy Requirements

This is an engineering checklist, not a security certification or legal
opinion.

## 1. Threat model

Assets include customer identity/contact data, investment preferences,
applications, documents, company verification evidence, staff accounts,
payment events, credentials and audit history. Threats include account
takeover, unauthorized document access, forged opportunities, privilege
escalation, injection, CSRF, malicious uploads, webhook replay, spam,
insider misuse and accidental exposure through logs/backups.

## 2. Authentication and sessions

-   Use supported Laravel authentication/session mechanisms.
-   Hash passwords with framework-supported password hashing.
-   Rotate session IDs after authentication and privilege changes.
-   Use secure, HttpOnly and appropriate SameSite cookies; require HTTPS
    in production.
-   Rate-limit login, OTP requests and verification attempts.
-   OTPs must be short-lived, single-use, attempt-limited and stored
    safely (e.g. hashed where practical).
-   Do not log OTP contents, passwords, session IDs or reset tokens.
-   Staff/admin accounts require stronger protections; consider MFA.
-   Provide session revocation and account recovery with abuse controls.

## 3. Authorization

-   Enforce policies on every protected read and write.
-   Verify access to a specific case/document, not just whether a user
    is logged in.
-   Test horizontal privilege escalation and IDOR cases.
-   Restrict internal verification notes, evidence, audit records and
    customer documents to authorized roles.
-   Sensitive actions require an audit event and, where appropriate,
    explicit confirmation.

## 4. Input/output and web security

-   Validate server-side with allowlists and explicit rules.
-   Use Eloquent/query bindings; never concatenate untrusted input into
    SQL.
-   Escape output by default; sanitize any intentionally supported rich
    text.
-   Protect state-changing requests with CSRF tokens.
-   Add sensible rate limits and request-size limits.
-   Set secure headers and production error handling.
-   Avoid leaking stack traces, SQL details or environment variables.
-   Review dependencies and update securely.

## 5. File handling

-   Store private documents outside public web access.
-   Allowlist necessary extensions and MIME types; verify actual content
    where possible.
-   Generate server-side filenames; never trust user filenames or paths.
-   Apply size limits, access controls and malware scanning where
    available.
-   Serve through authorization checks and log access to sensitive
    documents.
-   Prevent executable uploads and path traversal.
-   Obtain purpose-specific customer consent before sharing documents
    externally.

## 6. OTP/SMS and communications

-   Select provider only after testing delivery and operational
    reliability.
-   Confirm applicable Indian telecom/DLT registration, sender/header
    and template requirements with provider and qualified advisers.
-   Do not scrape or read the user's SMS inbox.
-   Redact phone numbers where full numbers are unnecessary in logs.
-   Separate marketing consent from necessary service communication
    where applicable.
-   Use idempotency and delivery-status handling.

## 7. Payments

-   Use a reputable gateway and hosted/secure payment flow where
    suitable.
-   Verify webhook signatures and event authenticity server-side.
-   Handle retries idempotently; reconcile payment state with provider
    records.
-   Never trust a browser redirect as proof of payment.
-   Never store raw card data, CVV or UPI PIN.
-   Record payer, purpose, amount, tax, status, provider reference and
    refund state.
-   AI must not authorize or execute a payment without a separately
    authorized, explicit user action and server-side controls.

## 8. Privacy by design

For each data field, document purpose, necessity, access, sharing,
retention and deletion/anonymization approach. Collect the minimum
needed. Explain the managed-service process and document-sharing
purpose. Obtain and record appropriate consent/authorization before
sharing customer documents or submitting applications.

IP-derived location is approximate. Offer manual selection and avoid
exact-address claims. Avoid retaining raw IP data longer than
operational/security needs justify.

## 9. Cookies and analytics

-   Classify essential, functional, analytics and marketing
    technologies.
-   Provide clear notices and appropriate choice/consent controls based
    on applicable requirements.
-   Do not treat continued browsing as blanket consent.
-   Record notice/version and choice where needed.
-   Provide a way to change/withdraw choices.
-   Avoid loading non-essential trackers before required consent.

## 10. Logs and audit

Log appropriate event metadata: actor, event, target, timestamp,
outcome, correlation/reference ID and relevant changes. Do not log
secrets or entire sensitive documents. Restrict log access and define
retention. Protect audit logs from casual modification. Log staff
viewing/sharing of sensitive records where proportionate.

## 11. Backups and deployment

-   Keep backups access-controlled and encrypted where supported.
-   Test restore procedures.
-   Keep secrets outside source control.
-   Restrict writable directories.
-   Protect `.env`, logs, SQL dumps, archives and source files from web
    access.
-   Use least-privilege database credentials.
-   Have a rollback plan for deployments and migrations.
-   Remove debug mode and verbose errors in production.

## 12. Testing

Use OWASP ASVS as a planning checklist and test relevant risks: broken
access control, injection, XSS, CSRF, session management, unsafe
uploads, secrets exposure, rate limiting, webhook forgery/replay and
sensitive-data leakage. Record limitations of the hosting environment
and any tests that cannot be performed.
