# Task T08 --- Customer Assessment and Matching Engine

## Outcome

Build and deploy an explainable, deterministic opportunity matching engine and self-service assessment wizard that computes compatibility between entrepreneur capital/geographic profile and audited franchisor disclosures:
1. Hard qualification filters strictly enforcing verified publication, maximum capital feasibility, and state/territory intake availability.
2. 5-factor weighted fit score (0-100) covering Budget (30%), Space (20%), Location tier (20%), Sector alignment (15%), and Experience (15%).
3. Fully transparent, human-readable match reasons alongside critical unknown caveats and regulatory disclaimers (no guarantee of success or approval).
4. Interactive 3-step assessment intake form (`/assessment`) and instant results display (`/assessment/match`) requiring zero upfront phone collection to view matches.

## Scope

### Included

- Matching Service `App\Services\AssessmentMatchingService`:
  - Hard filters (verification freshness, budget plausibility, state presence).
  - 5-factor fit score calculation with point breakdown.
  - Transparent itemized reasons and caveats generator.
- Assessment Controller `App\Http\Controllers\AssessmentController`:
  - `index`: Renders self-service assessment form with active categories.
  - `match`: Validates inputs and returns ranked match compatibility list.
- Views:
  - `resources/views/assessment/index.blade.php`: Clean editorial multi-section assessment form.
  - `resources/views/assessment/results.blade.php`: Results display with fit score badges, itemized reasons, caveats, and disclaimers.
- Web routes:
  - `GET /assessment` (`assessment.index`)
  - `POST /assessment/match` (`assessment.match`)
- Automated feature tests in `tests/Feature/AssessmentMatchingTest.php` (7 tests, 22 assertions).

### Excluded

- Customer user account persistence (Task T09).
- Case manager manual reassessment and internal notes (Task T10).
- Payment for premium assisted consultations (Task T13).

## Acceptance criteria

- [x] Hard filters strictly exclude unverified or draft opportunities regardless of capital compatibility.
- [x] Hard filters strictly exclude opportunities where minimum CapEx exceeds user budget.
- [x] Hard filters strictly exclude opportunities with no active territory availability in user's state.
- [x] Fit scores calculate deterministically and provide itemized positive factors and caveats.
- [x] Clear regulatory alerts emphasize that fit scores represent objective criteria matching, not an approval or earnings guarantee.
- [x] 100% test pass rate across all 30 test cases in the test suite.

## Inspection findings

- **Data Models:** Verified that `Opportunity::published()`, `locations`, `financialBreakdowns`, `requirements`, and `category` relationships provide complete structured data for deterministic rule evaluation.
- **Regulatory Guardrails:** Confirmed adherence to `AGENTS.md` and `docs/PRD.md` rules barring AI/algorithmic guarantees of business success, approvals, or earnings.

## Implementation details

- **Service Architecture:** Implemented `AssessmentMatchingService` cleanly separated from HTTP controller to enable reuse in customer dashboards and staff case management.
- **Budget Ranges:** Mapped user-facing intervals (`under-5l`, `5l-15l`, `15l-30l`, `30l-plus`) to strict numeric INR bounds for comparison with audited `min_investment` and `max_investment`.

## Testing evidence

Executed commands:
```bash
php artisan test tests/Feature/AssessmentMatchingTest.php
php artisan test
```

Results:
```
PASS  Tests\Feature\AssessmentMatchingTest
✓ assessment index renders with sectors                                                                    0.72s  
✓ assessment validation fails with missing mandatory inputs                                                0.03s  
✓ matching engine strictly excludes unverified opportunities                                               0.02s  
✓ matching engine excludes opportunities exceeding budget                                                  0.03s  
✓ matching engine excludes opportunities outside target state                                              0.02s  
✓ matching engine ranks and explains fit                                                                   0.03s  
✓ http assessment matching submission displays results and disclaimer                                      0.03s  

Tests: 30 passed (137 assertions)
```

## Duplication review

- Reused existing Blade components (`x-layout.app`, `x-ui.badge`, `x-ui.button`, `x-ui.alert`).
- Reused `Opportunity::formatInr()` utility for consistent Indian rupee presentation.
- Maintained clean separation between domain matching logic and HTTP presentation layer.
