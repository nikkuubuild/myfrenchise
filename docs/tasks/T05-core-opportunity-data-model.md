# Task T05 --- Core Opportunity Data Model

## Outcome

Establish the core relational schema, Eloquent models, model factories, relationships, scopes, and database seeders for categories, companies, opportunities, itemized financial breakdowns, territory availability, and operational requirements. Ensure strict data provenance, explicit publication states, and verified timestamp tracking.

## Scope

### Included

- Migrations for:
  - `categories`: Sector classifications with sorting and active flags.
  - `companies`: Legal entity identity (CIN, GSTIN, PAN, incorporation year, status).
  - `opportunities`: Core business opportunities with INR investment ranges, business model, space mandates, verification validity timestamps, and publication states (`draft`, `under_review`, `published`, `suspended`).
  - `opportunity_financial_breakdowns`: Itemized outlay components with provenance citation and estimate/refundable flags.
  - `opportunity_locations`: Geographic territory availability with state codes, district, and exclusivity indicators.
  - `opportunity_requirements`: Operational mandates (space, power, water, experience) with mandatory flags.
- Eloquent models with typed properties, relationships, and custom query scopes (`active`, `verified`, `published`, `inBudget`).
- Indian Rupee currency formatting helper (`Opportunity::formatInr()`).
- Complete model factories for automated testing and test data generation.
- Database seeder (`DatabaseSeeder`) with 6 audited reference categories and verified sample opportunity (*Chai Point Express*).
- Comprehensive automated feature tests (`OpportunityDataModelTest`).

### Excluded

- Verification case management and audit log persistence (Task T06).
- Public catalog UI rendering and search filters (Task T07).
- Customer scoring and assessment matching engine (Task T08).

## Acceptance criteria

- [x] Schema enforces foreign key constraints and indexed lookup paths.
- [x] Publication states require explicit verified timestamps before appearing in `published` scope.
- [x] Financial components maintain provenance citations (source evidence and dates).
- [x] Model factories exist for all entities.
- [x] Reference sectors seeded matching PRD specifications.
- [x] 100% test coverage for model relationships, query scopes, and INR formatting.

## Inspection findings

- **Relevant files/routes/components:**
  - `database/migrations/2026_10_19_000001_create_categories_table.php`
  - `database/migrations/2026_10_19_000002_create_companies_table.php`
  - `database/migrations/2026_10_19_000003_create_opportunities_table.php`
  - `database/migrations/2026_10_19_000004_create_opportunity_financial_breakdowns_table.php`
  - `database/migrations/2026_10_19_000005_create_opportunity_locations_table.php`
  - `database/migrations/2026_10_19_000006_create_opportunity_requirements_table.php`
  - `app/Models/Category.php`, `Company.php`, `Opportunity.php`, `OpportunityFinancialBreakdown.php`, `OpportunityLocation.php`, `OpportunityRequirement.php`
  - `database/seeders/DatabaseSeeder.php`
  - `tests/Feature/OpportunityDataModelTest.php`
- **Hosting constraints:**
  - Foreign key and indexed schema strictly compatible with MySQL 8.0, MariaDB 10.x, and SQLite.

## Plan

1. Create migration files for the 6 core entities.
2. Build Eloquent models with relationships, casts, and scopes.
3. Build model factories.
4. Update `DatabaseSeeder` with 6 PRD categories and initial sample opportunity.
5. Create and run feature tests in `tests/Feature/OpportunityDataModelTest.php`.
6. Update implementation tracking documentation.

## Risks and controls

- **Data Integrity:** Foreign keys use `cascadeOnDelete` for child components (financials, locations, requirements) and `restrictOnDelete` for categories to prevent orphaned opportunities.
- **Premature Disclosure:** `published` query scope strictly enforces that `publication_status = 'published'`, `last_verified_at IS NOT NULL`, and `verification_valid_until >= NOW()`.
- **Currency Representation:** Whole rupee integers used for INR amounts to avoid floating point rounding inaccuracies.

## Verification

- **Automated tests:**
  - `php artisan migrate` -> 6 migrations executed successfully.
  - `php artisan db:seed` -> Categories and sample opportunity seeded.
  - `php artisan test` -> 11 passed (48 assertions) in 0.85s.
    - `OpportunityDataModelTest::test_category_creation_and_scope` (Passed)
    - `OpportunityDataModelTest::test_company_creation_and_verified_scope` (Passed)
    - `OpportunityDataModelTest::test_opportunity_creation_with_financials_locations_and_requirements` (Passed)
    - `OpportunityDataModelTest::test_published_scope_requires_verification_and_published_status` (Passed)
    - `OpportunityDataModelTest::test_in_budget_scope_filters_correctly` (Passed)
- **Results:**
  - All tests passed.

## Review

- **Duplication review:** Reused standard Laravel conventions; no duplicate database columns or overlapping models.
- **Documentation updated:**
  - Created `docs/tasks/T05-core-opportunity-data-model.md`.
  - Updated `docs/IMPLEMENTATION_PLAN.md`.

## Status

Complete

## Commit

Pending commit.
