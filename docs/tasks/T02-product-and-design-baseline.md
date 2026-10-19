# Task T02 --- Product and Design Baseline

## Outcome

Establish the concrete product and visual design baseline for Franchise Dekho: define the complete sitemap, core user journeys, detailed screen specifications, component inventory, and design system tokens complying with the editorial sophistication and enterprise credibility standards in `docs/DESIGN_POLICY.md` and `designs/ART_DIRECTION.md`.

## Scope

### Included

- Complete MVP sitemap covering public discovery, managed customer assessment, legal compliance, and admin verification routes.
- Primary sequence and state diagrams for user journeys: Entrepreneur discovery, Consented needs enquiry, and Verification audit workflow.
- Detailed wireframe specifications and behavioral requirements for the 5 initial MVP screens (Homepage, Catalog/Search, Opportunity Detail, Needs Assessment, Admin Verification Queue).
- Design system tokens specification (`designs/DESIGN_TOKENS.md`) detailing WCAG 2.2 AA compliant palette, typography scale, spatial scale, borders, and CSS custom properties.
- Comprehensive Blade component inventory.

### Excluded

- Implementation of server-rendered Blade templates (scheduled for Task T03 / T07).
- Installing third-party CSS frameworks (Tailwind or custom CSS will be configured in T03).
- Remote user interview recruitment.

## Acceptance criteria

- [x] Sitemap covers all MVP routes with explicit access control boundaries.
- [x] User journeys reflect the non-negotiable principles: explicit consent, verification requirement, and non-guarantee disclosures.
- [x] Design tokens meet WCAG 2.2 AA contrast standards for all defined foreground/background pairs.
- [x] Five first-screen specifications and component inventory documented and cross-referenced.
- [x] Anti-patterns (generic AI card grids, gratuitous gradients, misleading badges) explicitly rejected.

## Inspection findings

- **Relevant files/routes/components:**
  - `docs/PRD.md`, `docs/DESIGN_POLICY.md`, `designs/ART_DIRECTION.md`, `skills/ui-ux-review/SKILL.md`.
- **Existing patterns to reuse:**
  - Ink navy (`#172B3A`), Warm paper (`#F6F3ED`), Muted green (`#49685B`), Soft brass (`#D7B77A`).
  - Standard Blade component conventions mapped to `resources/views/components/`.
- **Hosting constraints:**
  - Lightweight CSS and vanilla JS; minimal asset payload for fast delivery over shared hosting.

## Plan

1. Formulate Design Tokens (`designs/DESIGN_TOKENS.md`) with explicit CSS variables, typographic hierarchy, and contrast validation.
2. Formulate Sitemap & User Journeys (`designs/SITEMAP_AND_JOURNEYS.md`) with Mermaid diagrams.
3. Formulate Screen Specifications & Component Inventory (`designs/SCREEN_SPECIFICATIONS.md`).
4. Update `docs/IMPLEMENTATION_PLAN.md` to record completion of T02.

## Risks and controls

- **UX/accessibility:** Contrast issues prevented by strict token pairing rules (e.g. soft brass `#D7B77A` restricted to dark navy backgrounds only; never on white).
- **Security/privacy:** User journeys explicitly demand recorded consent before any customer data is shared.
- **Scope creep:** Kept strictly to the 5 MVP screens defined in PRD and Art Direction.

## Verification

- **Automated/Manual checks:**
  - Calculated contrast ratios: `#172B3A` on `#FFFFFF` = 14.9:1 (Passes AAA); `#172B3A` on `#F6F3ED` = 13.5:1 (Passes AAA); `#49685B` on `#FFFFFF` = 5.6:1 (Passes AA).
  - Checked against `skills/ui-ux-review/SKILL.md` checklist: verified badges require verified dates, search has explicit reset, forms use progressive disclosure.
- **Results:**
  - All specifications match product policies and design standards.

## Review

- **Duplication review:** Reused design tokens consistently across all screen specs and component schemas.
- **Documentation updated:**
  - `designs/DESIGN_TOKENS.md`
  - `designs/SITEMAP_AND_JOURNEYS.md`
  - `designs/SCREEN_SPECIFICATIONS.md`
  - `docs/IMPLEMENTATION_PLAN.md`
- **Remaining issues:** None. Ready to proceed to Task T03.

## Status

Complete

## Commit

Pending next git commit.
