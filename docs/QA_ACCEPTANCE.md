# QA Strategy and Definition of Done

## 1. Test layers

-   Unit: isolated matching, status-transition, validation and fee
    calculations.
-   Feature/integration: routes, database persistence, policies,
    notifications and service workflows.
-   Authorization: role matrix, case isolation, document access and
    admin-only actions.
-   Security: validation, CSRF, XSS, injection, session, upload and
    webhook cases.
-   UI: desktop/mobile layout, forms, focus, errors, loading, empty and
    success states.
-   Accessibility: keyboard navigation, semantic labels, contrast, focus
    visibility and reduced motion.
-   SEO: metadata, canonical URLs, sitemap, robots, structured data and
    HTTP statuses.
-   Operational: backup/restore, cron, deployment, logging, error pages
    and rollback.
-   Regression: existing approved behavior remains intact.

## 2. Minimum critical test scenarios

### Opportunities

-   Draft opportunity is not publicly visible.
-   Failed, expired or missing required verification blocks publication.
-   Only authorized reviewers can approve/suspend.
-   Public pages do not expose internal evidence or notes.
-   Stale opportunity can be suspended and no longer recommended.

### Matching

-   Hard filters run before ranking.
-   Ineligible, unpublished or unavailable opportunities cannot be
    recommended.
-   Unknown data is not silently treated as a positive match.
-   Explanations reflect actual matching rules and do not imply a
    success probability.

### Customer cases

-   A customer cannot read another customer's case or documents by
    changing an ID.
-   Document sharing requires appropriate recorded authorization.
-   Case status changes follow allowed transitions and are audited.
-   Internal notes are hidden from customers.

### Authentication and payments

-   OTP expires, cannot be reused and is rate-limited.
-   Secrets are not written to logs.
-   Forged/replayed payment webhooks do not cause duplicate or false
    payment state.
-   Browser redirect alone cannot mark payment as successful.

### Content and privacy

-   Private pages are not indexed.
-   Non-essential analytics respect applicable consent requirements.
-   Privacy/cookie choices can be updated where required.
-   Public claims show source/status/date when the design requires them.

## 3. UI review

Check: - Hierarchy and primary action. - Spacing and alignment. -
Typography and contrast. - Responsive behavior at representative
widths. - Keyboard focus and form labels. - Realistic long Hindi/English
content. - Loading, empty, error and success states. - No decorative
component that distracts from the task. - No fake content, invented
statistics or unsupported trust badges.

## 4. Definition of done

A task is done only when: - Acceptance criteria are met. - Relevant
tests are run and pass, or limitations/failures are explicitly
recorded. - Security, privacy and authorization are reviewed. - UI
behavior and accessibility are checked where relevant. - No unrelated
changes are bundled. - Duplication review is complete. - Documentation
is updated. - Changes are reviewed and committed if repository
access/permissions permit.

## 5. Reporting template

-   Task ID and scope.
-   Files changed.
-   Behavior delivered.
-   Commands/tests run and exact outcomes.
-   Manual checks performed.
-   Known issues or untested areas.
-   Security/privacy considerations.
-   Duplication review.
-   Commit hash if a commit was actually created.
-   Deployment status only if deployment was actually verified.
