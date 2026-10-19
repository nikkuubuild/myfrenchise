# Skill: Engineering Workflow

## When to use

For every implementation, bug fix, refactor, deployment change or
repository task in Franchise Dekho.

## Procedure

1.  Translate the request into a narrow outcome and acceptance criteria.
2.  Inspect the repository and related routes, views, models, services,
    migrations, styles and tests.
3.  If the repository is empty or access is missing, report that fact
    and do not claim implementation.
4.  Check relevant project docs and hosting compatibility.
5.  Propose the smallest safe plan and identify affected files.
6.  Implement only the current task.
7.  Run targeted tests, then broader relevant checks.
8.  Fix regressions caused by the change.
9.  Review for duplication, unnecessary dependencies, security issues
    and design-system drift.
10. Update documentation and report exact results.
11. Commit only if authorized repository access is available.

## Output format

-   Understanding and acceptance criteria.
-   Inspection findings.
-   Plan.
-   Files changed.
-   Tests and results.
-   Security/privacy review.
-   Duplication review.
-   Remaining risks.
-   Commit/deployment status, only if verified.

## Stop conditions

Stop and ask for a decision when: - A choice affects payment, legal
terms, privacy, security or irreversible data changes. - The host cannot
support the selected architecture safely. - Requirements conflict or
acceptance criteria are missing. - A task would alter protected Owner
functionality or unrelated modules. - Evidence is insufficient to
publish an opportunity.
