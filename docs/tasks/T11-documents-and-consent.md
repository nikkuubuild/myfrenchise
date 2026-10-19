# Task T11 --- Documents and Consent

## Outcome

Build and deploy private document storage, file validation, access logs, and explicit customer consent workflows for document sharing and application submission:
1. Secure, non-public document vault for confidential applicant files (Identity, Financial Statements, Commercial Premises Lease/Title, Brand Disclosures).
2. Strict server-side file validation (PDF, PNG, JPEG only; max 10 MB limit) with randomized UUID filenames to prevent path traversal and executable execution.
3. Non-negotiable authorization checks and comprehensive `document_access_logs` recording every download/access with timestamp, IP address, and user agent.
4. Auditable document-sharing consent workflow (`document_consents`):
   - Staff can compile disclosure packs and submit consent requests to applicants.
   - Customers review the exact list of documents and scope summary on their portal.
   - Customers explicitly grant or revoke consent with cryptographic/session proof (timestamp and IP address).
   - Zero external sharing is permitted without an active, customer-granted consent record.
5. Customer Case Vault interface (`/customer/cases/{id}/documents`) and staff workspace integration with verification status controls (Pending, Verified, Rejected).

## Scope

### Included

- Migrations:
  - `2026_10_19_000015_create_documents_and_consents_tables.php`: Creates `documents`, `document_access_logs`, and `document_consents`.
- Models:
  - `App\Models\Document`: Storage helpers, file format checks, and relations.
  - `App\Models\DocumentAccessLog`: Immutable audit trail for document access.
  - `App\Models\DocumentConsent`: Multi-document sharing authorizations with `grant()` and `revoke()`.
  - Updated `App\Models\Enquiry`: Added `documents()`, `consents()`, and `activeConsents()` relations.
- Service:
  - `App\Services\DocumentService`: Private uploads to `local` disk, authorized downloads with access logging, staff verification, and consent request/grant/revoke workflows.
- Controllers:
  - `App\Http\Controllers\Admin\CaseDocumentController`: Staff document upload, verification, and consent request creation.
  - `App\Http\Controllers\Customer\CaseDocumentController`: Customer document vault, upload, download, and explicit consent grant/revoke actions.
- Views:
  - `resources/views/customer/cases/documents.blade.php`: Customer vault and consent authorization screen.
  - Updated `resources/views/admin/cases/show.blade.php`: Integrated document table, verification actions, and consent pack builder.
  - Updated `resources/views/customer/dashboard.blade.php`: Added document vault links and pending authorization notification badges.
- Routes:
  - Customer routes: `customer.cases.documents.show`, `store`, `download`, `consents.grant`, `consents.revoke`.
  - Staff routes: `admin.cases.documents.store`, `download`, `verify`, `consents.request`.
- Automated feature tests in `tests/Feature/DocumentAndConsentTest.php` (8 tests, 34 assertions).

### Excluded

- Third-party e-Sign or Aadhaar biometric authentication gateway (Task T13 / future release).
- Legal policy disclosure reviews (Task T12).

## Acceptance criteria

- [x] File uploads strictly enforce allowlist of extensions and MIME types (`pdf`, `png`, `jpg`, `jpeg`) and 10 MB size limit.
- [x] Files are stored strictly outside the web root on the private `local` disk (`storage/app/private`).
- [x] Filenames are randomized UUIDs to prevent directory traversal attacks.
- [x] Every document download creates a `document_access_logs` entry.
- [x] Customers receive HTTP 403 Forbidden when attempting to view or download documents belonging to other customers (IDOR prevention).
- [x] Explicit consent records require customer affirmative action, recording IP address and timestamp.
- [x] Customers can revoke previously granted consent at any time.
- [x] 100% test pass rate across all 62 automated tests in the test suite.

## Inspection findings

- **Filesystem Security:** Verified that `config/filesystems.php` configures `local` with `'root' => storage_path('app/private')` and `'serve' => false`. Files cannot be accessed directly via static HTTP URL.
- **Privacy by Design:** In accordance with `docs/SECURITY_PRIVACY.md` Section 5, documents require purpose-specific customer authorization prior to brand disclosure.

## Testing evidence

Executed commands:
```bash
php artisan test tests/Feature/DocumentAndConsentTest.php
php artisan test
```

Results:
```
PASS  Tests\Feature\DocumentAndConsentTest
✓ customer can view own case documents and consents                                                        0.05s  
✓ customer cannot view documents of another customer                                                       0.03s  
✓ customer can upload valid document to private storage                                                    0.07s  
✓ document upload rejects unpermitted mime types                                                           0.03s  
✓ customer can download own document and access is logged                                                  0.10s  
✓ customer cannot download document belonging to other customer                                            0.04s  
✓ case manager can verify and reject case document                                                         0.05s  
✓ consent request and grant workflow                                                                       0.07s  

Tests: 62 passed (264 assertions)
```

## Duplication review

- Reused existing layout (`x-layout.app`) and alert (`x-ui.alert`) components.
- Maintained centralized download authorization in `DocumentService::downloadDocument()`.
