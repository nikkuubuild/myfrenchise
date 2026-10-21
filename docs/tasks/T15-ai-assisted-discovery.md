# Task T15 — AI-Assisted Discovery (P2)

## Executive Summary
Built an AI-assisted business opportunity discovery and due diligence guidance system for **Franchise Dekho** ([`/assistant`](file:///C:/Users/NIKKU/Desktop/myfranchise/resources/views/assistant/index.blade.php)). The discovery engine is strictly grounded in approved, verified database records (`Opportunity::published()`), cites source legal entities, CINs, and verification audit dates (`last_verified_at`), transparently discloses missing physical evidence, and enforces non-negotiable governance safety guardrails prohibiting payment authorization, automatic application approvals, and fraudulent earnings guarantees.

---

## 1. Scope and Features Delivered

### 1.1 Strict Evidentiary Grounding
- **`AiDiscoveryService` ([`app/Services/AiDiscoveryService.php`](file:///C:/Users/NIKKU/Desktop/myfranchise/app/Services/AiDiscoveryService.php)):**
  - Parses natural language inquiries to extract budget ranges (e.g. "under ₹10 Lakh"), target state codes (`UP`, `DL`, `MH`, etc.), business sectors (`food-beverage`, `logistics`, `healthcare`, `retail-fmcg`, `education`), and carpet space requirements.
  - Queries ONLY published and audit-verified listings via `Opportunity::published()->with(['company', 'category', 'financialBreakdowns', 'locations', 'requirements'])`.
  - Strictly excludes draft, unverified, suspended, or unapproved records.
  - Transparent negative result handling: when no approved listings meet the user's constraints, the engine explicitly acknowledges zero matches and explains the absence rather than hallucinating fictitious brands.

### 1.2 Non-Negotiable Safety & Governance Guardrails
Enforced programmatic guardrails intercepting prohibited intents before query synthesis:
1. **Payment & Financial Transactions Prohibited:**
   - Detects payment intents ("charge my card", "pay now", "transfer money").
   - Blocks transaction and returns statutory notice: AI cannot authorize payments or accept card details; all payments occur through the statutory 18% GST checkout flow.
2. **Automatic Application Approvals Prohibited:**
   - Detects approval requests ("approve my application", "grant me franchise").
   - Explains that approval authority rests exclusively with the brand's corporate leadership and cannot be delegated to an AI model.
3. **Guaranteed Returns & Profit Promises Prohibited:**
   - Detects earnings guarantees ("guaranteed 30% monthly profit", "zero risk", "guaranteed turnover").
   - Firmly rejects earnings claims, citing the Consumer Protection Act 2019 provisions against misleading advertisements.

### 1.3 Transparency, Uncertainty & Source Citations
- **Attribution:** Lists operating company legal name, corporate CIN (from MCA21), audited CapEx outlay range in INR, and exact `last_verified_at` audit date for every cited opportunity.
- **Uncertainty Disclosures:** Transparently discloses required physical verifications that cannot be confirmed digitally (e.g. 3-phase commercial electricity load at landlord premises, municipal Shop & Establishment zoning, local radial buffer locks).
- **Statutory Framework Citations:** Automatically attaches governing legal references (e.g. Ministry of Corporate Affairs MCA21, Trade Marks Registry IP India, Food Safety and Standards Act FSSAI, GST SAC 998311).
- **Mandatory Non-Negotiable AI Notice Banner:**
  > "AI Discovery & Regulatory Notice: Responses are synthesized strictly from Franchise Dekho's approved, verified company and opportunity records. Franchise Dekho AI does not guarantee financial returns, loan approvals, or commercial turnover. The parent franchisor retains sole authority over franchisee selection. Applications require human case manager coordination and customer consent."

### 1.4 Interactive Discovery Interface & Rate Limiting
- **Controller & Routes:**
  - `GET /assistant`: Renders discovery workspace with exploration prompts and trust pillars.
  - `POST /assistant/query`: Processes inquiries with server-side rate limiting (`throttle:ai_discovery` configured to 15 queries/min per IP in `AppServiceProvider.php`). Supports both server-rendered Blade forms and async JSON API payloads.
- **UI Design ([`resources/views/assistant/index.blade.php`](file:///C:/Users/NIKKU/Desktop/myfranchise/resources/views/assistant/index.blade.php)):**
  - Follows editorial warm paper / ink navy art direction.
  - Features exploration prompt chips, structured answer dossier, opportunity cards with "View Disclosures" and "Apply" CTAs, and statutory citation boxes.
  - Linked across global navigation in [`header.blade.php`](file:///C:/Users/NIKKU/Desktop/myfranchise/resources/views/components/layout/header.blade.php), [`footer.blade.php`](file:///C:/Users/NIKKU/Desktop/myfranchise/resources/views/components/layout/footer.blade.php), and [`SitemapController.php`](file:///C:/Users/NIKKU/Desktop/myfranchise/app/Http/Controllers/SitemapController.php).

---

## 2. Test Verification & Suite Results

Feature test `tests/Feature/AiDiscoveryTest.php` covers 9 distinct test scenarios:
1. `test_ai_assistant_page_renders_with_trust_pillars_and_suggested_prompts` — Passed
2. `test_ai_discovery_answers_grounded_in_published_verified_opportunities` — Passed
3. `test_unverified_and_draft_opportunities_are_strictly_excluded_from_ai_discovery` — Passed
4. `test_ai_assistant_acknowledges_uncertainty_when_no_verified_records_match` — Passed
5. `test_ai_guardrail_refuses_payment_and_transaction_authorization` — Passed
6. `test_ai_guardrail_refuses_automatic_application_approval` — Passed
7. `test_ai_guardrail_refuses_guaranteed_profit_and_roi_promises` — Passed
8. `test_ai_query_via_json_api_returns_structured_payload` — Passed
9. `test_ai_discovery_handles_regulatory_due_diligence_queries_with_statutory_citations` — Passed

### Full Test Suite:
- Total tests: **99 passed (546 assertions)**
- Duration: 5.37s
- Regressions: **0**
- Frontend Assets: Clean production build with Vite in 1.86s
