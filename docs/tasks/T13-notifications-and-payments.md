# Task T13 --- Notifications and Payments

## Outcome

Deploy an India-adapted, secure, server-verified, idempotent notifications and payment processing architecture:
1. **Commercial Fee Catalogue & Transparent Pricing**:
   - `ServicePackage` catalogue with itemized base pricing, 18% GST calculation (SAC 998311), itemized service inclusions, non-negotiable limitations/exclusions, and cancellation terms.
   - Transparent customer checkout screen (`/checkout/{slug}`) requiring explicit statutory declaration that platform fees cover research/coordination and do not guarantee brand licensing or turnover.
2. **Cryptographic Payment Signature Verification**:
   - `PaymentService` enforces server-side HMAC SHA256 signature verification (`hash_equals`) on gateway transaction callbacks.
   - Non-negotiable rule enforced: **Browser redirect alone never marks payment as successful**.
   - Zero storage of raw card numbers, CVV, or UPI PINs.
3. **Statutory Indian GST Tax Invoicing**:
   - Automated `InvoiceService` generating sequential GST tax invoices (`FD-INV-YYYYMM-XXXX`) upon verified payment.
   - Indian GST tax breakdown logic:
     - Intra-state (Haryana buyer): 9% CGST + 9% SGST.
     - Inter-state (outside Haryana): 18% IGST.
   - Formal GST tax invoice template with SAC code `998311`, seller Haryana GSTIN (`06AAAAF0000A1Z5`), place of supply, and customer GSTIN for B2B input tax credit.
4. **Idempotent Gateway Webhook Processing**:
   - Webhook controller (`/webhooks/razorpay`) with CSRF exemption, HMAC SHA256 webhook signature verification, and deduplication guard in `webhook_events` table.
   - Duplicate or replayed webhook events return HTTP 200 `duplicate_ignored` without creating duplicate invoices or credit.
5. **Multi-Channel Service Notifications**:
   - Multi-channel notification service (`mail`, `sms_dlt`, `in_app`) and audit trail in `notification_logs`.
   - Transactional dispatch for:
     - Payment receipt and GST tax invoice generation.
     - Case assigned and stage transition notices.
     - Document sharing consent requests.
     - Statutory grievance findings publication.
6. **Staff Billing & Authorized Refunds**:
   - Staff revenue and billing pipeline at `/admin/billing` (restricted to `case_manager` and `admin`).
   - Staff-authorized refund workflow with audit trail and reason recording. AI is explicitly prohibited from authorizing refunds.

## Scope

### Included

- Migrations:
  - `2026_10_19_000017_create_commerce_tables.php`: Creates `service_packages`, `orders`, `payment_transactions`, `invoices`, `refunds`, and `webhook_events`.
  - `2026_10_19_000018_create_notification_logs_table.php`: Creates `notification_logs`.
- Models:
  - `App\Models\ServicePackage`: Service catalog, deliverables, exclusions, and tax calculations.
  - `App\Models\Order`: Order status constants, unique generator (`FD-ORD-YYYYMM-XXXX`), and relations.
  - `App\Models\PaymentTransaction`: Transaction reference (`FD-TXN-YYYYMM-XXXX`), gateway tokens, and capture states.
  - `App\Models\Invoice`: GST tax invoice with intra-state CGST/SGST vs inter-state IGST calculations.
  - `App\Models\Refund`: Staff-authorized refunds with audit tracking.
  - `App\Models\WebhookEvent`: Gateway event log with idempotency check (`isDuplicate`).
  - `App\Models\NotificationLog`: Multi-channel notification delivery log with DLT template tracking.
  - Updated `App\Models\User`: Added `orders()` and `notificationLogs()` relations.
- Services:
  - `App\Services\InvoiceService`: Automated GST tax invoice generator.
  - `App\Services\NotificationService`: Multi-channel transactional notifications with DLT template logging.
  - `App\Services\PaymentService`: Order creation, transaction initiation, server-side signature capture, idempotent webhook handling, and staff-authorized refund execution.
- Controllers:
  - `App\Http\Controllers\CheckoutController`: Transparent pricing overview, order submission, hosted simulation, and callback.
  - `App\Http\Controllers\Customer\BillingController`: Customer orders list, order detail, and tax invoice viewer.
  - `App\Http\Controllers\Admin\BillingController`: Staff revenue pipeline, order auditing, and refund authorization.
  - `App\Http\Controllers\WebhookController`: Signed webhook receiver.
- Views:
  - `resources/views/pages/checkout.blade.php`: Transparent price itemization, deliverables, exclusions, and billing details form.
  - `resources/views/pages/payment-gateway.blade.php`: Hosted payment simulation with HMAC SHA256 authorization.
  - `resources/views/customer/billing/index.blade.php`: Customer order history and tax invoice links.
  - `resources/views/customer/billing/order.blade.php`: Order details, transaction history, and invoice download.
  - `resources/views/customer/billing/invoice.blade.php`: Statutory Indian GST tax invoice template.
  - `resources/views/admin/billing/index.blade.php`: Staff revenue and billing pipeline.
  - `resources/views/admin/billing/show.blade.php`: Staff order review and authorized refund form.
  - Updated `resources/views/customer/dashboard.blade.php`: Added Billing & Invoices navigation.
  - Updated `resources/views/admin/cases/index.blade.php`: Added Billing & Revenue navigation.
- Seeders:
  - `database/seeders/ServicePackageSeeder.php`: Reference packages for due diligence and brand onboarding.
- Automated feature tests in `tests/Feature/NotificationAndPaymentTest.php` (9 tests, 61 assertions). Total suite: 80 tests (403 assertions).

### Excluded

- Live production merchant bank account activation (requires production merchant credentials).
- SEO structured data and sitemap generator (Task T14).

## Acceptance criteria

- [x] Transparent pricing displays base amount, 18% GST, deliverables, limitations, and cancellation terms before payment.
- [x] Server-side signature checks reject forged/tampered payment callbacks without charging or marking orders as paid.
- [x] Valid payment capture marks order as paid, sets `paid_at`, and generates a statutory Indian GST Tax Invoice.
- [x] Invoicing enforces Indian GST rules: intra-state (HR) splits into CGST/SGST; inter-state uses IGST.
- [x] Webhook processing verifies cryptographic signatures server-side and deduplicates events idempotently.
- [x] Replaying webhooks does not create duplicate invoices or duplicate status transitions.
- [x] No raw card numbers, CVVs, or UPI PINs are stored in the database or logs.
- [x] IDOR protection prevents customers from viewing invoices or orders belonging to other customers.
- [x] Staff can audit orders and authorize refunds with recorded reasons; AI cannot authorize refunds.
- [x] Transactional notifications are logged with delivery channels and DLT template references.
- [x] 100% test pass rate across all 80 tests in the test suite.

## Verification

- `php artisan test --filter=NotificationAndPaymentTest`: 9 passed, 61 assertions.
- `php artisan test`: 80 passed, 403 assertions.
- `npm run build`: Compiled frontend assets in 4.15s without errors.
- `php artisan db:seed --class=ServicePackageSeeder`: Seeded cleanly.
