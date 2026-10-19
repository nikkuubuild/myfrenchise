# Franchise Dekho

**Product:** India-wide managed business-opportunity discovery and
application-coordination platform\
**Status:** Documentation baseline --- implementation has not started\
**Hosting target:** Hostinger shared hosting\
**Backend:** Laravel / PHP\
**Database:** MySQL or MariaDB\
**Frontend direction:** Server-rendered Laravel Blade, semantic HTML,
CSS and lightweight JavaScript\
**Design direction:** Editorial sophistication + enterprise credibility

## What Franchise Dekho does

Franchise Dekho helps people discover, evaluate and pursue business
opportunities. It is not merely a directory. Its managed service can
include needs assessment, opportunity research, verification, shortlist
recommendations, communication with companies, document coordination,
application tracking and onboarding coordination.

The relevant company, bank, institution, regulator or opportunity
provider retains its own approval authority. Franchise Dekho must not
guarantee acceptance, earnings, profitability, financing or business
success.

## Repository status

The repository has been described by the project owner as 100% empty.
This documentation is a proposed starting baseline, not evidence that
any application code has been created, tested or deployed.

## Start here

1.  Read `docs/PRD.md`.
2.  Read `docs/DESIGN_POLICY.md` and `designs/ART_DIRECTION.md`.
3.  Read `docs/ARCHITECTURE.md`.
4.  Read `docs/SECURITY_PRIVACY.md`.
5.  Read `docs/SEO_CONTENT.md` and `docs/LEGAL_REQUIREMENTS.md`.
6.  Follow `AGENTS.md` and `skills/engineering-workflow/SKILL.md`.
7.  Begin with Task T01 in `docs/IMPLEMENTATION_PLAN.md`: verify
    repository and Hostinger compatibility before installing or coding.

## Documentation map

-   `AGENTS.md` --- mandatory repository-wide coding and agent rules.
-   `docs/PRD.md` --- product scope, users, journeys and requirements.
-   `docs/ARCHITECTURE.md` --- proposed application architecture and
    hosting constraints.
-   `docs/DESIGN_POLICY.md` --- design principles and quality bar.
-   `designs/ART_DIRECTION.md` --- visual direction and design tokens
    proposal.
-   `docs/DATA_MODEL.md` --- conceptual data model; not yet migrations.
-   `docs/SECURITY_PRIVACY.md` --- security, privacy, identity and
    document handling.
-   `docs/SEO_CONTENT.md` --- technical SEO, content and AI-search
    readiness.
-   `docs/LEGAL_REQUIREMENTS.md` --- legal/policy inventory requiring
    qualified review.
-   `docs/QA_ACCEPTANCE.md` --- test strategy and definition of done.
-   `docs/IMPLEMENTATION_PLAN.md` --- ordered, one-task-at-a-time
    roadmap.
-   `skills/engineering-workflow/SKILL.md` --- reusable engineering
    workflow.
-   `skills/ui-ux-review/SKILL.md` --- design review checklist.
-   `skills/opportunity-verification/SKILL.md` --- verification and
    publication process.
-   `templates/` --- reusable templates for tasks, decisions and
    verification evidence.

## Non-negotiable principles

-   Do not publish an opportunity until the company and the specific
    opportunity have passed the required verification checks.
-   Do not invent fees, investment amounts, earnings, territories,
    approvals, testimonials or verification evidence.
-   Distinguish company-provided claims from independently checked
    facts.
-   Obtain explicit, recorded customer consent before sharing documents
    or submitting an application on their behalf.
-   Protect customer documents and secrets; never commit production
    credentials.
-   Prefer a modular Laravel monolith over premature microservices.
-   Reuse existing code and design conventions if any are later added;
    do not duplicate components.
-   Complete one task at a time: Understand → Inspect → Plan → Implement
    → Test → Fix → Review for duplication → Commit.
-   Report what was actually tested. Never claim a deployment, test,
    audit or implementation happened when it did not.

## Initial setup note

Before selecting the Laravel version, inspect the exact Hostinger plan
for PHP version, required extensions, SSH/Composer access, cron support,
document-root control, database version, storage limits and deployment
options. Never expose the Laravel project root as the public web root;
only the `public/` directory should be web-accessible.

## Product and legal caveat

This repository documents a product plan, not legal advice or a
compliance certification. Qualified Indian legal, tax, privacy and
sector-specific review is required before launch.
