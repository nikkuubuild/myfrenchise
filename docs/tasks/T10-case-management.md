# Task T10 --- Case Management

## Outcome

Build and deploy the operational Case Management System for staff (case managers and administrators) to coordinate customer enquiries, manage assignments, enforce checklists, track status history, and maintain strict customer visibility boundaries:
1. Staff Case Management Pipeline (`/admin/cases`) with multi-faceted filtering (Stage, Priority, Assigned Specialist, Text Search) and operational KPI metrics (Total Cases, Unassigned Intake, My Active Cases, Urgent Escalations).
2. Comprehensive Case Workspace (`/admin/cases/{id}`) featuring:
   - Applicant Profile & Criteria Dossier with investment capacity, location, commercial space status, and verified consent details (IP address and timestamp).
   - Operational Action Checklist with inline task creation, assignment, and completion toggles.
   - Activity & Communication Timeline recording status history, internal staff notes, call logs, and customer milestone updates.
   - Stage progression control with mandatory/optional audit comments across 9 defined stages.
   - Specialist assignment and reassignment control.
   - Priority and follow-up scheduling.
3. Strict Customer Visibility Boundary:
   - Internal staff notes, confidential credit evaluations, and private commentary are strictly inaccessible to customer views or APIs.
   - Customer-visible milestone updates appear directly in the Customer Dashboard (`/dashboard`) under their specific coordinated application.
4. Role-based server-side authorization: Customers attempting to access `/admin/cases*` receive HTTP 403 Forbidden.

## Scope

### Included

- Migrations:
  - `2026_10_19_000013_add_case_management_fields_to_enquiries_table.php`: Adds `assigned_to_user_id`, `assigned_at`, `priority`, `next_follow_up_at`, `stage`.
  - `2026_10_19_000014_create_case_management_tables.php`: Creates `case_status_histories`, `case_tasks`, and `case_communications`.
- Models:
  - `App\Models\CaseStatusHistory`: Audit log of from/to stage transitions and comments.
  - `App\Models\CaseTask`: Checklist action items with assignee, completion status, and timestamps.
  - `App\Models\CaseCommunication`: Notes, call logs, and customer updates with `customerVisible` and `internalOnly` scopes.
  - Updated `App\Models\Enquiry`: Added case management relationships (`assignedCaseManager`, `statusHistories`, `tasks`, `communications`, `customerVisibleUpdates`).
  - Updated `App\Models\User`: Added `assignedCases` relationship.
- Service:
  - `App\Services\CaseManagementService`: Coordinates case assignments, standard task checklist seeding, stage transitions, communication logging with boundary enforcement, task toggles, and follow-up schedules.
- Controller:
  - `App\Http\Controllers\Admin\CaseController`: `index`, `show`, `assign`, `updateStage`, `storeCommunication`, `storeTask`, `toggleTask`, `updateFollowUp`.
  - Updated `App\Http\Controllers\CustomerDashboardController`: Eager-loads assigned specialist and customer-visible updates.
- Views:
  - `resources/views/admin/cases/index.blade.php`: Case pipeline queue.
  - `resources/views/admin/cases/show.blade.php`: Case workspace with dossier, checklist, and communications timeline.
  - `resources/views/customer/dashboard.blade.php`: Enhanced to render assigned specialist name and public milestone updates while keeping internal notes 100% hidden.
- Routes:
  - Role-gated routes under `/admin/cases` protected by `auth` and `role:case_manager,admin`.
- Automated feature tests in `tests/Feature/CaseManagementTest.php` (9 tests, 42 assertions).

### Excluded

- Private document upload vault and encrypted file storage (Task T11).
- Outbound SMS notifications and email delivery providers (Task T13).

## Acceptance criteria

- [x] Staff pipeline allows filtering by stage, priority, specialist assignment, and keyword search.
- [x] Case assignment records timestamp and transitions unassigned cases to `intake_assessment`.
- [x] Case assignment automatically seeds baseline operational checklist tasks if none exist.
- [x] Case stage transitions record audit trail with user identity and comment.
- [x] Action checklist items can be created and toggled completed/incomplete.
- [x] Internal staff notes are strictly hidden from customer view (`is_customer_visible = false`).
- [x] Customer milestone updates are displayed in customer dashboard (`is_customer_visible = true`).
- [x] Customers receive HTTP 403 Forbidden when accessing `/admin/cases*`.
- [x] 100% test pass rate across all 54 automated tests in the application.

## Inspection findings

- **Privacy & Visibility Boundary:** Verified that `case_communications` uses an explicit boolean `is_customer_visible` and that `customerVisibleUpdates` on the `Enquiry` model strictly filters by `is_customer_visible = true`. Tested negative cases ensuring internal notes do not leak to customer views.
- **Audit Trails:** Confirmed that every assignment and status change records the acting user ID, from/to states, and comments for accountability.

## Testing evidence

Executed commands:
```bash
php artisan test tests/Feature/CaseManagementTest.php
php artisan test
```

Results:
```
PASS  Tests\Feature\CaseManagementTest
✓ guests are redirected to login from staff cases index                                                    1.10s  
✓ customers receive 403 forbidden from staff cases                                                         0.12s  
✓ case managers and admins can view cases pipeline                                                         0.64s  
✓ case managers can view case workspace                                                                    0.91s  
✓ case assignment updates assignee and seeds checklist                                                     0.05s  
✓ case stage transition updates stage and audit history                                                    0.02s  
✓ creating and toggling checklist tasks                                                                    0.03s  
✓ strict customer visibility boundary                                                                      1.27s  
✓ case priority and followup update                                                                        0.03s  

Tests: 54 passed (230 assertions)
```

## Duplication review

- Reused existing layout (`x-layout.app`) and UI badge (`x-ui.badge`) components.
- Maintained clean separation between domain logic in `CaseManagementService` and HTTP requests in `CaseController`.
