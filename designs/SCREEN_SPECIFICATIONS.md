# Screen Specifications and Component Inventory

**Platform:** Franchise Dekho  
**Visual Style:** Editorial Sophistication + Enterprise Credibility  
**Target:** Server-rendered Blade components with semantic HTML, accessible CSS, and minimal vanilla JS.

---

## 1. Screen 1: Homepage (`/`)

### 1.1 Structural Layout
```
+--------------------------------------------------------------------------+
|  [Logo: Franchise Dekho]      Explore | Sectors | How It Works | [Contact] |
+--------------------------------------------------------------------------+
|  Warm Paper Surface (--color-surface-paper)                              |
|  KICKER: India's Managed Business Opportunity Discovery                  |
|  HERO HEADLINE:                                                          |
|  "Pursue Verified Business Opportunities with Coordinated Support."     |
|                                                                          |
|  SUBTITLE:                                                               |
|  Franchise Dekho pairs independent opportunity research with dedicated   |
|  application coordination. We verify facts, not marketing promises.      |
|                                                                          |
|  [ FILTER BAR ]                                                          |
|  [ Sector: All ▼ ] [ Budget: ₹5L - ₹15L ▼ ] [ State: All ▼ ] [ Search ] |
+--------------------------------------------------------------------------+
|  SECTION: CURATED SECTORS (6 Distinct Sectors with Active Audits)        |
|  [ Retail & FMCG ]  [ Food & Beverage ]  [ Education & Training ]        |
|  [ Courier & Logistics ] [ Healthcare & Diagnostic ] [ CSP & Banking ]   |
+--------------------------------------------------------------------------+
|  SECTION: HOW MANAGED SERVICE WORKS (Not just a directory)               |
|  1. Rigorous Verification   2. Transparent Fit   3. Consented Coordination|
|  MCA, Trademark, Financial   Hard budget & space  Zero unsolicited shares|
|  audits before publishing    feasibility checks   or mass spam           |
+--------------------------------------------------------------------------+
|  SECTION: VERIFICATION TRANSPARENCY & LEGAL CAVEAT                       |
|  "Approval authority remains solely with the brand/institution.          |
|   Franchise Dekho does not guarantee profits, approval, or success."     |
+--------------------------------------------------------------------------+
|  FOOTER: Navigation, Verification Policy, Grievance, Legal Disclaimers   |
+--------------------------------------------------------------------------+
```

### 1.2 Key Behavioral Specifications
- **Search Filter Bar:** Server-rendered form submitting via `GET /opportunities` with selected query parameters. Fully functional with keyboard and without JavaScript.
- **Visual Restraint:** No auto-scrolling carousels, no floating decorative emojis, no fake trust badges ("100% Guaranteed Success").
- **Clear Kicker:** Subtle brass accent rule `--color-accent-brass` or ink muted eyebrow styling.

---

## 2. Screen 2: Opportunity Catalog & Search (`/opportunities`)

### 2.1 Structural Layout
```
+--------------------------------------------------------------------------+
|  [Filter Sidebar / Top Bar]                                               |
|  Category: [x] Food & Beverage  [ ] Retail  [ ] Logistics                 |
|  Total Investment:  [ Min: ₹2 Lakh ] to [ Max: ₹15 Lakh ]                |
|  Space Available:   [ Any ▼ ]                                             |
|  State / Territory: [ All India / Select State ▼ ]                        |
|  [ Apply Filters ]  [ Reset Filters ]                                     |
+--------------------------------------------------------------------------+
|  RESULTS HEADER: "Showing 24 Verified Opportunities"                      |
|  SORT: [ Recently Verified ▼ | Investment: Low to High | Space Needed ]  |
+--------------------------------------------------------------------------+
|  OPPORTUNITY CARD / ROW                                                   |
|  +----------------------------------------------------------------------+ |
|  | BRAND NAME: Chai Point Express           [ VERIFIED: 14 Oct 2026 ]   | |
|  | Sector: Food & Beverage (QSR) | Model: Franchise-Owned Franchise-Op  | |
|  |                                                                      | |
|  | Total Outlay:       Space Required:      Territory:                  | |
|  | ₹8,50,000 - ₹12,00,000    150 - 300 sq ft     North India (Tier 2/3)  | |
|  |                                                                      | |
|  | Verified Components: Includes Franchise Fee, Equipment, Fit-out.     | |
|  | Royalty: 6% gross monthly | Agreement Period: 5 Years                | |
|  |                                                                      | |
|  | [ View Full Disclosures ]          [ Request Assisted Application ]  | |
|  +----------------------------------------------------------------------+ |
+--------------------------------------------------------------------------+
|  PAGINATION: [ < Previous ]  Page 1 of 3  [ Next > ]                     |
+--------------------------------------------------------------------------+
```

### 2.2 Component Details
- **Verification Status Tag:** Distinct pill with checkmark icon, `--color-status-verified-text`, and `--color-status-verified-bg`. Shows exact date of latest audit.
- **Cost Clarity:** Displays explicit range with components listed, avoiding misleading "Starts at just ₹10k" gimmicks.
- **Empty State:** If filters return 0 results, shows actionable advice to expand budget or location, plus an option to submit a Custom Needs Assessment.

---

## 3. Screen 3: Opportunity Detail (`/opportunities/:slug`)

### 3.1 Structural Layout
```
+--------------------------------------------------------------------------+
|  BREADCRUMB: Home > Food & Beverage > Chai Point Express                 |
+--------------------------------------------------------------------------+
|  HEADER PANEL (Warm Paper Background)                                    |
|  Brand: Chai Point Express                                               |
|  Legal Entity: Mountain Trail Foods Pvt. Ltd. (CIN Verified)             |
|  [ ✓ Verified Opportunity: Audited on 14 Oct 2026 | Validity: 180 Days ]|
+--------------------------------------------------------------------------+
|  SECTION: INVESTMENT BREAKDOWN (Comprehensive Disclosure Table)          |
|  Item                              Disclosed Amount    Source/Evidence   |
|  ----------------------------------------------------------------------  |
|  Initial Franchise Fee             ₹2,50,000 + GST     Brand Agreement   |
|  Equipment & Machinery Setup       ₹3,50,000 approx    Vendor Schedule   |
|  Interior Fit-out & Signage        ₹2,00,000 approx    Architect Spec    |
|  Initial Inventory & Packaging     ₹1,00,000           Order Sheet       |
|  Refundable Security Deposit       ₹1,00,000           Agreement Clause 4|
|  ----------------------------------------------------------------------  |
|  TOTAL ESTIMATED OUTLAY            ₹10,00,000 (Excl. Working Capital)    |
|  Ongoing Royalty / Marketing Fee:  6% on Monthly Gross Revenue           |
+--------------------------------------------------------------------------+
|  SECTION: ELIGIBILITY & INFRASTRUCTURE MANDATES                          |
|  • Minimum Carpet Area: 150 sq. ft. on High Footfall Street / Mall       |
|  • Power Load: 5 kW commercial three-phase connection                    |
|  • Water & Drainage: Mandatory dedicated commercial supply              |
|  • Operator Commitment: Full-time on-site management preferred          |
+--------------------------------------------------------------------------+
|  SECTION: TERRITORY & EXPANSION STATUS                                   |
|  • Available Regions: Uttar Pradesh, Rajasthan, Madhya Pradesh (Tier 2/3)|
|  • Exclusivity: 1.5 km radius territorial protection                     |
+--------------------------------------------------------------------------+
|  CAVEAT & DISCLAIMER BOX (Informational Notice)                          |
|  "Franchise Dekho verifies legal entity existence and published term      |
|   schedules. Actual revenue depends on local trade, footfall, and        |
|   operational execution. No profits or earnings are guaranteed."        |
+--------------------------------------------------------------------------+
|  STICKY / DOMINANT CTA:                                                  |
|  [ Request Managed Application Support ]  [ Download Information Brief ] |
+--------------------------------------------------------------------------+
```

---

## 4. Screen 4: Needs Assessment & Managed Enquiry (`/enquiry`)

### 4.1 Progressive Form Steps
1. **Step 1: Financial & Readiness Constraints**
   - Investment Budget Range (`< ₹5L`, `₹5L - ₹10L`, `₹10L - ₹25L`, `₹25L - ₹50L`, `₹50L+`)
   - Source of Funds (Self-funded, Bank Loan Required, Partnership)
   - Preferred Timeline to Launch (Immediate / 1-3 months / 3-6 months)
2. **Step 2: Location & Space Profile**
   - Operating State & City / District
   - Property Status (Commercial Own, Commercial Leased, No Space Yet)
   - Available Carpet Area (in Sq. Ft.)
3. **Step 3: Applicant Identity & Consents**
   - Full Legal Name
   - Verified Mobile Phone Number (with SMS notice)
   - Email Address
   - Business Experience Summary (Dropdown: First-time, Retail/F&B Operator, Corporate Executive)
   - **MANDATORY CONSENT CHECKBOX:**
     > "[x] I authorize Franchise Dekho case managers to assess my profile, discuss suitability, and share my application only with brands I explicitly approve in writing."

---

## 5. Screen 5: Admin Verification Queue (`/admin/verifications`)

### 5.1 Operational Verification Table
- **Columns:**
  - Case # / Date Submitted
  - Brand & Company Name
  - Sector
  - MCA Registration / CIN Status (Checked / Flagged / Unchecked)
  - Trademark / Authorization Evidence (Uploaded Document Link)
  - Auditor Assigned
  - Action (Audit Now / Request Clarification / Approve / Reject)
- **Detail View:**
  - Split-screen comparison: Left panel shows brand-submitted data; right panel shows verified registry lookup and document preview.
  - Required sign-off checklist before "Publish" button is enabled.

---

## 6. Complete Component Inventory

| Component Name | Category | Blade View Path | Props / Variants |
| :--- | :--- | :--- | :--- |
| `layout.app` | Layout | `components/layout/app.blade.php` | `title`, `metaDescription`, `breadcrumbs` |
| `layout.header`| Layout | `components/layout/header.blade.php`| `activeNav`, `user` |
| `layout.footer`| Layout | `components/layout/footer.blade.php`| Disclaimers, Grievance contact, Copyright |
| `ui.badge` | Presentation | `components/ui/badge.blade.php` | `status` (verified, pending, caveat, rejected) |
| `ui.button` | Action | `components/ui/button.blade.php` | `variant` (primary, secondary, ghost, danger), `type`, `size` |
| `ui.input` | Form | `components/ui/input.blade.php` | `name`, `label`, `type`, `error`, `required`, `helper` |
| `ui.select` | Form | `components/ui/select.blade.php` | `name`, `label`, `options`, `selected`, `error` |
| `ui.checkbox`| Form | `components/ui/checkbox.blade.php` | `name`, `label`, `checked`, `required` |
| `ui.alert` | Feedback | `components/ui/alert.blade.php` | `type` (info, warning, danger, success), `title` |
| `opportunity.card` | Domain | `components/opportunity/card.blade.php` | `opportunity` (Model instance) |
| `opportunity.disclosure-table` | Domain | `components/opportunity/disclosure-table.blade.php` | `costs`, `terms` |
