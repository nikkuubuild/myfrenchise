# Task T09 --- Customer Authentication and Role-Based Portals

## Outcome

Build and deploy secure customer and staff authentication, session management, and role-based portal access:
1. Customer self-service registration (`/register`) with Indian mobile validation, location preferences, strong password rules, and mandatory terms & privacy consent.
2. Secure session-based authentication (`/login`) with rate limiting (`throttle:auth`), session fixation protection (`session()->regenerate()`), and inactive account blocking.
3. Self-service password recovery flow (`/forgot-password`, `/reset-password`).
4. Customer Dashboard (`/dashboard`, `/customer/dashboard`) displaying user's submitted enquiries, real-time case review statuses, allocated capital, and assigned specialist notes.
5. Automatic association of prior guest enquiries when an entrepreneur registers with the same email.
6. Role-based access control middleware (`EnsureUserRole`) protecting sensitive staff routes with 403 Forbidden.
7. Responsive navigation header dynamically rendering authenticated user status and secure sign-out.

## Scope

### Included

- Migrations:
  - `2026_10_19_000011_add_role_and_profile_to_users_table.php`: Adds `role`, `phone`, `state`, `city`, `is_active`, `last_login_at`.
  - `2026_10_19_000012_add_user_id_to_enquiries_table.php`: Adds `user_id` foreign key.
- Models:
  - `App\Models\User`: Role constants (`ROLE_CUSTOMER`, `ROLE_CASE_MANAGER`, `ROLE_VERIFICATION_OFFICER`, `ROLE_ADMIN`), role helpers (`isCustomer()`, `isCaseManager()`, `isVerificationOfficer()`, `isAdmin()`, `isStaff()`), and `enquiries()` relation.
  - `App\Models\Enquiry`: Added `user_id` fillable and `user()` relation.
- Factory:
  - `Database\Factories\UserFactory`: Added role states (`customer`, `caseManager`, `verificationOfficer`, `admin`, `deactivated`).
- Controllers:
  - `App\Http\Controllers\Auth\RegisteredUserController`
  - `App\Http\Controllers\Auth\AuthenticatedSessionController`
  - `App\Http\Controllers\Auth\PasswordResetController`
  - `App\Http\Controllers\CustomerDashboardController`
  - `App\Http\Controllers\EnquiryController`: Associates authenticated user on submission.
- Middleware:
  - `App\Http\Middleware\EnsureUserRole`: Checks user authentication, account active state, and permitted roles; registered with alias `role` in `bootstrap/app.php`.
- Views:
  - `resources/views/auth/login.blade.php`
  - `resources/views/auth/register.blade.php`
  - `resources/views/auth/forgot-password.blade.php`
  - `resources/views/auth/reset-password.blade.php`
  - `resources/views/customer/dashboard.blade.php`
  - `resources/views/components/layout/header.blade.php`: Updated with dynamic auth links.
- Routes:
  - Guest routes with rate limiting: `login`, `register`, `forgot-password`, `reset-password`.
  - Auth routes: `logout`, `dashboard`, `customer.dashboard`.
- Automated feature tests in `tests/Feature/AuthenticationTest.php` (15 tests, 51 assertions).

### Excluded

- SMS OTP gateway integration (deferred until provider contract and Indian DLT regulatory templates are verified per PRD).
- Internal case management dashboard (Task T10).
- Encrypted document storage and upload vault (Task T11).

## Acceptance criteria

- [x] Registration requires valid 10-digit phone, state, city, strong password, and mandatory terms consent.
- [x] Registration logs customer in and rotates session ID.
- [x] Login is rate-limited and validates active status. Deactivated users cannot log in.
- [x] Prior guest enquiries matching email are automatically linked upon registration.
- [x] Logged-in users submitting new enquiries have their `user_id` automatically assigned.
- [x] Customer dashboard shows user's enquiries and prevents viewing other users' records (IDOR protection).
- [x] Role middleware blocks unauthorized roles with 403 Forbidden.
- [x] 100% test pass rate across all 45 automated tests in the test suite.

## Inspection findings

- **Session Security:** Adhered strictly to `docs/SECURITY_PRIVACY.md` by regenerating session tokens on login and logout, invalidating sessions on deactivation, and enforcing password complexity.
- **Privacy by Design:** Preserved privacy rules: passwords and sensitive tokens are hidden from serialization and never exposed in views.

## Testing evidence

Executed commands:
```bash
php artisan test tests/Feature/AuthenticationTest.php
php artisan test
```

Results:
```
PASS  Tests\Feature\AuthenticationTest
✓ login screen can be rendered                                                                             0.81s  
✓ users can authenticate using the login screen                                                            0.10s  
✓ users can not authenticate with invalid password                                                         0.24s  
✓ deactivated users cannot authenticate                                                                    0.04s  
✓ registration screen can be rendered                                                                      0.02s  
✓ new users can register as customers with valid details and consent                                       0.03s  
✓ registration fails if mandatory terms not accepted                                                       0.02s  
✓ registration fails with weak password                                                                    0.02s  
✓ prior guest enquiries are linked to user upon registration                                               0.02s  
✓ enquiry submission associates logged in user id                                                          0.03s  
✓ authenticated users can log out                                                                          0.02s  
✓ guests are redirected to login when accessing dashboard                                                  0.02s  
✓ customer can view own dashboard and enquiries                                                            0.04s  
✓ customer cannot view enquiries belonging to other customers                                              0.04s  
✓ role middleware enforces role restrictions                                                               0.07s  

Tests: 45 passed (188 assertions)
```

## Duplication review

- Reused existing application components (`x-layout.app`, `x-ui.badge`, `x-ui.button`, `x-ui.alert`).
- Reused `throttle:auth` configured in `AppServiceProvider`.
- Followed standard Laravel authentication conventions without introducing heavyweight external dependencies.
