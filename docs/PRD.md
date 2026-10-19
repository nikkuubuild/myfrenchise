# Product Requirements Document (PRD)

**Product:** Franchise Dekho\
**Version:** 1.0 baseline\
**Status:** Proposed; validate through product discovery\
**Geography:** India-wide\
**Delivery model:** Managed business-opportunity service

## 1. Product vision

Help aspiring and existing entrepreneurs identify, evaluate and pursue
business opportunities with less confusion, better evidence and
coordinated support. Franchise Dekho combines opportunity discovery with
a human-led managed service.

It is not just a directory, lead-selling page or promise of guaranteed
business success.

## 2. Problem statement

Prospective business owners may struggle to establish whether an
opportunity is legitimate, understand the real investment and
eligibility requirements, confirm territory availability, compare
alternatives, prepare documents and coordinate applications. Information
can be incomplete, stale or presented as marketing claims.

Franchise Dekho should make the process more transparent and help
customers move from discovery to an informed, documented application.

## 3. Target users

-   First-time entrepreneurs exploring business options.
-   Existing business owners seeking a new franchise, dealership,
    distributorship or service partnership.
-   Customers comparing opportunities against budget, location,
    experience and infrastructure.
-   Internal researchers and verification reviewers.
-   Case managers coordinating enquiries, documents and applications.
-   Administrators responsible for access, publication, audit and
    complaints.
-   Future company/partner users, subject to a later scoped release.

## 4. Scope

### MVP

1.  Public homepage and service explanation.
2.  Opportunity category pages and searchable/filterable opportunity
    listings.
3.  Opportunity detail pages with investment, eligibility,
    location/territory, evidence status and last-verified date where
    known.
4.  Managed-service enquiry and needs-assessment flow.
5.  Admin-managed companies and opportunities.
6.  Verification review and controlled publication workflow.
7.  Customer case record, assignment, tasks, status history and
    communications log.
8.  Responsive, accessible UI and server-rendered SEO-friendly public
    pages.
9.  Privacy notice, terms, verification policy, fee/refund disclosure,
    grievance and fraud-reporting routes before public operation.
10. Basic security, audit logs, backup and operational readiness.

### Later releases

-   OTP/SMS login after provider and Indian messaging compliance checks.
-   Advanced recommendation ranking and comparison.
-   Customer portal, secure document uploads and consented sharing.
-   Payments, invoices and refunds.
-   Company/partner portal.
-   Email/SMS notifications and scheduled follow-ups.
-   Content publishing, structured data and analytics.
-   AI-assisted discovery grounded in approved opportunity data.
-   Additional automation and integrations.

## 5. Opportunity categories

-   Franchise opportunities: food, retail, education, salon, fitness and
    other eligible sectors.
-   Banking/financial services: CSP/Business Correspondent and
    payment-service outlets only when institutional authorization and
    applicable requirements are verified.
-   Distributorship and dealership: FMCG, electronics, agriculture,
    automotive, building materials and industrial categories.
-   Agencies and service partnerships: courier, travel, telecom,
    authorized service centres and regulated services subject to
    sector-specific requirements.
-   Manufacturing, food processing, packaging, reselling and contract
    manufacturing.
-   Business acquisition or other opportunities only after a separate
    scope and verification policy are approved.

Category labels are not evidence that an opportunity is authorized or
suitable.

## 6. Managed-service workflow

1.  Visitor discovers an opportunity or requests guidance.
2.  Platform explains its role, scope, fee basis and limitations.
3.  Customer submits needs such as budget range, preferred operating
    location, experience and sector preferences.
4.  System applies hard eligibility and publication filters before
    ranking.
5.  Operations team checks relevant company/opportunity evidence and
    availability.
6.  Customer receives a shortlist with rationale, evidence status,
    caveats and last-verified dates.
7.  Customer explicitly approves each relevant document share or
    application submission.
8.  Case manager coordinates communication, checklist, tasks and
    follow-ups.
9.  Provider/company makes its own approval and commercial decisions.
10. Platform records status, communications, outcomes, complaints and
    next steps.

## 7. Opportunity publication policy

An opportunity must not be published until an authorized reviewer
confirms the required checks. At minimum, verify: - Company legal
identity and appropriate official contact route. - Whether the company
or authorized representative is entitled to offer this specific
opportunity. - Whether the specific opportunity is active and territory
availability is supported by evidence. - Investment/fee components,
source and date; mark unknown amounts as unknown rather than guessing. -
Eligibility, infrastructure, licence and documentation requirements as
provided or independently confirmed. - Application process,
agreement/contract access and material caveats. - Evidence freshness,
reviewer identity, decision and review/expiry date.

Keep evidence private where necessary. Suspend or unpublish stale,
disputed or potentially fraudulent listings pending review. A badge must
never imply that profit, approval or success is guaranteed.

## 8. Matching and recommendations

### Hard filters first

-   Opportunity is approved for publication and within its verification
    validity period.
-   Territory is available for the requested location, where territory
    restrictions apply.
-   Investment fit is plausible based on known customer budget and
    disclosed cost range.
-   Required eligibility and infrastructure constraints are met.
-   Mandatory sector/licence constraints are not known to be unmet.

### Ranking second

Initial illustrative weights for a later configurable ranking model: -
Budget fit: 30% - Eligibility fit: 20% - Location/territory fit: 15% -
Sector and preference fit: 15% - Infrastructure fit: 10% - Experience
and support fit: 10%

These weights require testing and must not override hard filters or
verification failures. Show understandable reasons and disclose material
unknowns. Do not present a score as a success probability.

## 9. Location

-   Use IP geolocation only for approximate, optional location
    suggestions.
-   Explain that VPNs, mobile networks and shared IPs can be inaccurate.
-   Provide manual location selection and allow customers to choose or
    save an operating location.
-   Never infer an exact home address from IP.
-   Store only what is needed and disclose purposes and retention.

## 10. Authentication and accounts

-   Use secure framework-supported sessions and server-side
    authorization.
-   OTP must be short-lived, single-use, attempt-limited and
    rate-limited.
-   Never log OTP values or ask to read a user's SMS inbox.
-   Select a provider after verifying pricing, delivery, operational
    support and applicable Indian DLT/template/header requirements.
-   Protect staff/admin accounts with stronger access controls and
    appropriate multi-factor authentication.
-   Log security events without storing secrets.

## 11. Payments and revenue

Revenue payer remains undecided. Design configuration and disclosures so
the product can support: - Customer-paid managed-service fees. -
Company-paid sourcing/partner fees. - A transparent hybrid model.

Before charging, clearly show payer, amount, taxes, inclusions,
exclusions, refund/cancellation rules and whether fees are paid to
Franchise Dekho or directly to a provider. Never hide conflicts of
interest. Payment provider integration must verify signed/server-side
webhooks and idempotency. Do not store raw card details, CVV or UPI PIN.
AI must not authorize payment.

## 12. Roles and access

Initial roles to validate: - Anonymous visitor. - Customer. -
Researcher. - Verification reviewer. - Case manager. -
Support/complaints operator. - Administrator.

Apply least privilege and server-side policies. A user should see only
the cases and documents necessary for their role. Sensitive
administrative actions must be auditable.

## 13. Non-functional requirements

-   Server-rendered public pages with crawlable links and unique
    metadata.
-   Mobile-first responsive UI and useful Hindi/English experience.
-   Target WCAG 2.2 AA.
-   Secure defaults, validation, CSRF protection, output escaping, rate
    limiting and prepared queries/ORM.
-   Private document storage and access-controlled downloads.
-   Operational logging without secrets, backup/restore process and
    error monitoring appropriate to shared hosting.
-   Graceful behavior under low bandwidth; avoid unnecessary large JS
    bundles and heavy animation.
-   Performance targets should be measured on the actual deployment and
    set after a baseline.
-   Maintainable Laravel conventions and automated tests for critical
    flows.

## 14. Success measures

Instrument only with appropriate notice and privacy controls. Candidate
measures: - Qualified enquiry completion rate. - Time from enquiry to
first human response. - Percentage of published listings with current
verification evidence. - Stale-listing detection and resolution time. -
Customer understanding of fees and next steps. - Application stage
completion and drop-off. - Complaint rate, fraud reports and resolution
time. - Page performance, accessibility defects and technical SEO
health.

Do not publish success rates or testimonials unless evidence and
methodology support them.

## 15. Out of scope for initial MVP

-   Guaranteed returns, approvals, funding or franchise awards.
-   Automatic publication solely based on AI output.
-   Publicly exposing customer documents or case notes.
-   Unverified banking/financial schemes.
-   Microservices or complex infrastructure before need is proven.
-   AI making legal, eligibility, payment or approval decisions without
    human review.

## 16. Key open decisions

-   Exact Hostinger plan and available PHP/extensions/Composer/cron
    features.
-   Domain and deployment directory.
-   OTP/email/payment providers and budgets.
-   Which language(s) are required at launch.
-   Initial fee model and refund policy.
-   Staff roles and approval matrix.
-   Data retention periods and legal review.
-   Initial opportunity sources and evidence standards.
-   Analytics and consent-management approach.
