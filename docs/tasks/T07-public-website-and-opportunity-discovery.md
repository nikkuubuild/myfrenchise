# Task T07 --- Public Website and Opportunity Discovery

## Outcome

Build and deploy the complete public discovery experience for Franchise Dekho conforming to the editorial design tokens, server-rendered SEO architecture, and non-negotiable verification and regulatory caveats:
1. Dynamic Homepage with live audited categories, featured opportunities, and platform metrics.
2. Filterable Opportunity Catalog (`/opportunities`) with sector, budget, and state filters.
3. Comprehensive Opportunity Detail page (`/opportunities/{slug}`) with legal CIN metadata, itemized financial schedules with provenance citations, and mandatory caveats.
4. Progressive 3-step Needs Assessment enquiry flow (`/enquiry`) with mandatory explicit data-sharing consent.
5. Confirmation receipt screen (`/enquiry/confirmed`) with unique tracking reference ID (`#FD-CASE-XXXX`).
6. Core governance and policy pages (`/how-it-works`, `/verification-policy`, `/grievance-and-fraud`, `/privacy-policy`, `/terms-of-service`).

## Scope

### Included

- Migration for `enquiries` table tracking customer criteria, contact details, consent timestamps, and reference IDs.
- Model `App\Models\Enquiry` with reference code generator.
- Form request validator `App\Http\Requests\StoreEnquiryRequest` requiring mandatory explicit consent.
- Controllers:
  - `HomeController`
  - `OpportunityController` (`index`, `show`)
  - `EnquiryController` (`create`, `store`, `confirmed`)
  - `PageController`
- Blade components:
  - `components.opportunity.card`
  - `components.opportunity.disclosure-table`
- Views:
  - `welcome.blade.php`
  - `opportunities/index.blade.php`
  - `opportunities/show.blade.php`
  - `enquiry/create.blade.php`
  - `enquiry/confirmed.blade.php`
  - `pages/how-it-works.blade.php`
  - `pages/verification-policy.blade.php`
  - `pages/grievance.blade.php`
  - `pages/privacy.blade.php`
  - `pages/terms.blade.php`
- Automated feature tests in `tests/Feature/PublicDiscoveryTest.php`.

### Excluded

- Customer OTP SMS verification gateway (Task T09).
- Automated customer suitability ranking engine (Task T08).
- Online payment transactions and checkout (Task T13).

## Acceptance criteria

- [x] Homepage dynamically renders active categories and featured opportunities.
- [x] Opportunity Catalog allows multi-facet filtering (Sector, Budget, State) and sorting.
- [x] Draft and unverified opportunities are hidden from catalog and return 404 on direct detail route.
- [x] Opportunity Detail shows legal company profile, itemized CapEx breakdown, and legal caveats.
- [x] Needs assessment strictly requires explicit consent before recording enquiry.
- [x] All policy and governance routes return 200.
- [x] 100% test pass rate across 23 automated tests.

## Inspection findings

- **Relevant files/routes/components:**
  - `routes/web.php`
  - `app/Http/Controllers/HomeController.php`, `OpportunityController.php`, `EnquiryController.php`, `PageController.php`
  - `resources/views/opportunities/index.blade.php`, `opportunities/show.blade.php`
  - `resources/views/enquiry/create.blade.php`, `enquiry/confirmed.blade.php`
  - `tests/Feature/PublicDiscoveryTest.php`
- **Hosting constraints:**
  - Clean server-rendered Blade templates with zero client-side framework overhead.
  - Rate limiting applied to enquiry submissions (5 requests/minute).

## Plan

1. Create migration and model for customer enquiries.
2. Build controllers and form request validation.
3. Build opportunity card and disclosure table components.
4. Implement all catalog, detail, enquiry, and policy Blade views.
5. Compile production frontend assets via Vite.
6. Write feature test suite and verify.

## Risks and controls

- **Privacy & Telemarketing Risk:** Platform explicitly avoids lead-selling; customer enquiry records explicit consent and timestamps.
- **Unverified Listings:** Scope filtering strictly requires verified badges and active validity windows.
- **Regulatory Transparency:** Every opportunity and layout view clearly states that approval authority remains solely with the brand.

## Verification

- **Automated tests:**
  - `php artisan test` -> 23 passed (115 assertions).
    - `PublicDiscoveryTest::test_homepage_loads_dynamic_categories_and_verified_opportunities` (Passed)
    - `PublicDiscoveryTest::test_opportunity_catalog_filtering` (Passed)
    - `PublicDiscoveryTest::test_draft_opportunities_are_hidden_from_catalog` (Passed)
    - `PublicDiscoveryTest::test_opportunity_detail_page_renders_disclosures` (Passed)
    - `PublicDiscoveryTest::test_unverified_opportunity_detail_returns_404_for_guest` (Passed)
    - `PublicDiscoveryTest::test_enquiry_requires_mandatory_consent` (Passed)
    - `PublicDiscoveryTest::test_valid_enquiry_creates_record_and_redirects` (Passed)
    - `PublicDiscoveryTest::test_policy_pages_load_successfully` (Passed)
- **Results:**
  - All tests passed.

## Review

- **Duplication review:** Reused existing layout, UI badges, buttons, alerts, and models.
- **Documentation updated:**
  - Created `docs/tasks/T07-public-website-and-opportunity-discovery.md`.
  - Updated `docs/IMPLEMENTATION_PLAN.md`.

## Status

Complete

## Commit

Pending commit.
