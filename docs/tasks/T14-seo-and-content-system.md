# Task T14 — SEO and Content System (P1)

## Executive Summary
Implemented technical SEO architecture, dynamic XML sitemaps, robots crawl controls, dynamic metadata with canonical URLs, schema.org structured data, sector landing pages, an authoritative educational Due Diligence Knowledge Hub, and staff editorial workflow with content freshness tracking for **Franchise Dekho**.

---

## 1. Scope and Requirements Delivered

### 1.1 Technical SEO & Crawl Controls
- **Robots Directives & Crawl Policy (`/robots.txt` & `public/robots.txt`):**
  - Explicit directives for standard web crawlers (`User-agent: *`) allowing public discovery routes and disallowing private areas (`/admin/`, `/customer/`, `/checkout/`, `/webhooks/`, `/health`, `/enquiry/confirmed`, `/grievance-and-fraud/confirmed`, `/assessment/match`).
  - Explicit policies for AI search bots (`GPTBot`, `ClaudeBot`, `PerplexityBot`) allowing canonical content while restricting private portals. Restricted bulk web scrapers (`CCBot`).
  - Dynamically links to canonical `Sitemap: https://franchisedekho.com/sitemap.xml`.
  - Added strict `<meta name="robots" content="noindex, nofollow">` to all customer dashboards, admin panels, checkout, and authentication views.

### 1.2 Dynamic XML Sitemap (`/sitemap.xml`)
- Built `SitemapController` returning `application/xml; charset=UTF-8` with standard sitemap protocol schema (`http://www.sitemaps.org/schemas/sitemap/0.9`).
- Dynamically indexes:
  - Static core pages (Homepage `/`, `/opportunities`, `/assessment`, `/guides`, `/how-it-works`, `/verification-policy`, `/grievance-and-fraud`, `/privacy-policy`, `/terms-of-service`, `/cookie-policy`).
  - Sector category landing pages (`/sectors/{slug}`).
  - Only published, verified business opportunities (`/opportunities/{slug}`) with accurate `<lastmod>` timestamps from audit verification dates.
  - Published editorial guides (`/guides/{slug}`) with `<lastmod>`.
- Excludes all private/admin routes and draft/unverified listings.

### 1.3 Canonical URLs, OpenGraph, and Twitter Cards
- Updated `components/layout/app.blade.php`:
  - Enforced dynamic `<link rel="canonical" href="...">`.
  - Standard OpenGraph tags (`og:site_name`, `og:locale`, `og:type`, `og:title`, `og:description`, `og:url`, `og:image`).
  - Twitter Card metadata (`twitter:card`, `twitter:title`, `twitter:description`).

### 1.4 Schema.org Structured Data (JSON-LD)
Built `app/Services/SeoService.php` to generate standard, ethical Schema.org markup strictly adhering to visible page content:
- **`Organization`**: Legal entity `Franchise Dekho Technologies LLP`, address in Gurugram, Haryana, official contact points (Customer Support & Grievance Officer).
- **`WebSite`**: With standard `SearchAction` entry point.
- **`BreadcrumbList`**: Automatically generated via accessible `<x-ui.breadcrumbs>` UI component on catalog, sectors, opportunity profiles, articles, and policy pages.
- **`Service` (Franchise Opportunity)**: Valid schema with provider, description, itemized investment range in `INR` (`AggregateOffer`), `areaServed: India`, and `last_verified_at` modified date. **Zero fabricated reviews or artificial rating stars.**
- **`Article`**: Structured data for educational guides with `headline`, `author`, `publisher`, `datePublished`, and `dateModified`.
- **`CollectionPage` / `ItemList`**: Structured data for sector landing pages.

### 1.5 Sector-Specific Landing Pages (`/sectors/{slug}`)
- Built `SectorController` and view `resources/views/sectors/show.blade.php`.
- Surfaces industry benchmarks (typical commercial space, breakeven horizon, active verified counts).
- Embeds Indian statutory licensing context per domain (e.g. FSSAI for QSR, CDSCO & Pharmacy Council for Healthcare, Carriage by Road Act & GST e-way bills for Logistics, RTE & Municipal codes for Education).
- Displays verified opportunities with filter shortcuts and canonical tags.

### 1.6 Authoritative Editorial Knowledge Hub & Content Freshness (`/guides` & `/admin/articles`)
- Created `articles` table migration and `Article` model with freshness helper (`isFresh()` checking verification within 180-day SLA).
- Public Knowledge Hub:
  - `/guides`: Filterable index with reading time, author credential, and last verified dates.
  - `/guides/{slug}`: Article page with breadcrumbs, statutory citations box, and non-negotiable legal disclaimer.
- Seeded 4 substantive, legally rigorous guides:
  1. *Franchise Due Diligence Checklist: 7 Essential Verification Checks Before Investing in India*
  2. *Master Franchise vs Unit Franchise: Capital Requirements and Territorial Rights Explained*
  3. *Understanding Franchise Fees, Royalties, and GST (SAC 998311) Under Indian Law*
  4. *Commercial Leases & Zoning Laws for Retail & F&B Franchises in India*
- Staff Admin Editorial Queue:
  - `/admin/articles`: Table displaying title, author, publication status, freshness status, and instant "Re-verify Today" button (`POST /admin/articles/{article}/verify`).
  - `/admin/articles/create` & `edit`: Authoring form with validation and SEO meta overrides.

---

## 2. Test Verification & Suite Results

Feature test `tests/Feature/SeoAndContentTest.php` covers 10 test cases:
1. `test_xml_sitemap_returns_valid_xml_with_canonical_urls` — Passed
2. `test_xml_sitemap_excludes_unverified_opportunities_and_private_routes` — Passed
3. `test_robots_txt_returns_proper_directives_and_sitemap_url` — Passed
4. `test_meta_tags_canonical_and_opengraph_render_accurately` — Passed
5. `test_private_views_render_noindex_nofollow_directive` — Passed
6. `test_structured_data_organization_and_website_rendered_on_homepage` — Passed
7. `test_opportunity_detail_renders_valid_service_schema_and_breadcrumbs` — Passed
8. `test_sector_landing_page_renders_regulatory_guidance_and_opportunities` — Passed
9. `test_article_knowledge_hub_and_detail_render_statutory_sources` — Passed
10. `test_admin_can_manage_articles_and_update_content_freshness` — Passed

### Full Suite Run:
- Total tests: **90 passed (493 assertions)**
- Duration: 15.95s
- Regressions: **0**
- Frontend Assets: Clean production build with Vite in 1.50s
