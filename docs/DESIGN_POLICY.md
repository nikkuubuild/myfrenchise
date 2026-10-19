# UI/UX Design Policy

## 1. Standard

The product must feel deliberately art-directed by an experienced
product design team: sophisticated, clear, credible and commercially
useful. It must not resemble a generic AI-generated startup template.

The standard is not "more decoration." It is strong hierarchy,
purposeful composition, precise spacing, readable content, coherent
behavior and restraint.

## 2. Principles

1.  **Trust before conversion.** Explain what the platform does, what
    has been verified, what remains unknown and who makes the final
    decision.
2.  **Information before ornament.** Costs, eligibility, territory,
    evidence and next steps must be easy to find.
3.  **Editorial hierarchy.** Use typography, grid, alignment and
    whitespace to guide attention.
4.  **Purposeful variation.** Different content deserves different
    layouts; avoid putting every section inside the same rounded card.
5.  **Progressive disclosure.** Ask only for information needed at the
    current step.
6.  **Consistency without monotony.** Reuse components and tokens while
    adapting page composition to the task.
7.  **Accessible by default.** Target WCAG 2.2 AA, including keyboard,
    focus, contrast, labels, errors and reduced motion.
8.  **Performance is design.** Optimize images, avoid unnecessary
    JavaScript and keep interactions usable on lower-end phones and slow
    networks.
9.  **Mobile is a first-class layout.** Design and test actual mobile
    flows, not just scaled-down desktop pages.
10. **Evidence-led iteration.** Review with users and analytics; do not
    rely on aesthetic preference alone.

## 3. Anti-patterns

Avoid: - Repeated 3/4-card grids for every section. - Gradients used
only to make a page look "modern." - Random floating blobs, decorative
pills and gratuitous animations. - Excessive drop shadows, glassmorphism
and oversized corner radii. - Icons used as decoration instead of
meaning. - Fake testimonials, unsupported badges, invented ratings or
fake scarcity. - Tiny low-contrast metadata. - Dense forms that ask for
all information upfront. - AI-generated imagery that misrepresents real
businesses. - Visual indicators that imply an unverified opportunity is
trustworthy. - Buttons that look clickable but have no real behavior.

## 4. Page design intent

### Homepage

-   One clear proposition and primary action.
-   Useful discovery entry point (sector, budget and location).
-   Curated category navigation and a concise explanation of the managed
    process.
-   Evidence-based trust section, useful guides and transparent
    limitations.
-   Avoid hero carousels and stacks of redundant promotional cards.

### Category and search

-   Clear page title, useful introductory content and crawlable links.
-   Filters with understandable selected states and a visible reset.
-   Results count and useful sorting.
-   Distinguish unavailable/unknown from zero.
-   Accessible empty, loading and error states.

### Opportunity detail

-   Company/opportunity identity and current publication/verification
    status.
-   Investment range and cost components with source/date when known.
-   Eligibility, infrastructure, location/territory and application
    requirements.
-   Evidence and last-verified date.
-   Clear caveats, fee disclosure and a single dominant next step.
-   Keep internal verification evidence private.

### Assessment/enquiry

-   Ask only necessary questions, explain why sensitive fields are
    requested and show progress.
-   Preserve inputs on recoverable errors.
-   Provide clear validation and a human-help route.
-   Do not imply a match score guarantees approval.

### Customer dashboard

-   Lead with current status, next action, pending documents and
    case-manager contact route.
-   Use plain-language status labels and timeline history.
-   Avoid exposing internal notes or another customer's data.

### Admin operations

-   Optimize for accuracy, throughput and auditability.
-   Use dense tables when they improve comparison; support search,
    filters and clear statuses.
-   Make risky actions explicit, permission-gated and auditable.
-   Never let a single unreviewed AI result publish a listing.

### Legal and payment

-   Readable text hierarchy, clear section navigation and version/date.
-   Display total price, taxes, payer, inclusions, exclusions and
    refund/cancellation terms before payment.
-   No preselected optional consent.

## 5. Proposed design tokens

Initial proposal to validate in prototypes: - Ink navy: `#172B3A` - Warm
paper: `#F6F3ED` - Muted green: `#49685B` - Soft brass accent:
`#D7B77A` - Neutral ink, muted text, borders, surfaces and semantic
status colors must be separately defined and contrast-tested.

Do not use colour alone to communicate state. Never use the accent
palette where contrast fails. Tokens are starting points, not a
requirement to preserve a colour that fails usability testing.

### Typography

-   Choose a distinctive display typeface for selected headlines and a
    highly legible text/UI typeface.
-   Check font licensing, Hindi glyph coverage, numerals, weight
    availability and performance.
-   Use a consistent scale; avoid arbitrary font sizes.
-   Use line lengths suitable for long-form reading.
-   Test Hindi and English text expansion and mixed-script content.

### Spacing and layout

-   Define a spacing scale and responsive grid.
-   Align content edges intentionally.
-   Use whitespace to group information; do not create blank space
    accidentally.
-   Use consistent form heights, focus treatment and button hierarchy.
-   Corner radii and shadows are used selectively, not everywhere.

## 6. Interaction states

Every interactive component must specify: - Default. - Hover (where
relevant). - Keyboard focus. - Active/selected. - Disabled. - Loading. -
Empty. - Success. - Error and recovery.

Respect reduced-motion preferences. Avoid animation that blocks the
user's next action.

## 7. Design review questions

-   Is the primary user task obvious within seconds?
-   Is the most important information visually dominant?
-   Does the page still look strong without shadows, gradients and
    decoration?
-   Is each component necessary?
-   Are verified facts distinguishable from company claims and unknowns?
-   Does the mobile layout have its own intentional hierarchy?
-   Can the full journey be completed with keyboard and
    screen-reader-compatible semantics?
-   Are errors specific and recoverable?
-   Is this a reusable pattern or an unnecessary one-off?
