<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Seed authoritative educational due diligence guides.
     */
    public function run(): void
    {
        $fnbCat = Category::where('slug', 'food-beverage')->first();
        $retailCat = Category::where('slug', 'retail-fmcg')->first();

        $articles = [
            [
                'title' => 'Franchise Due Diligence Checklist: 7 Essential Verification Checks Before Investing in India',
                'slug' => 'franchise-due-diligence-checklist-india',
                'category_id' => null,
                'author_name' => 'Franchise Dekho Research Desk',
                'author_title' => 'Legal & Regulatory Franchise Research Desk',
                'reading_time_minutes' => 8,
                'excerpt' => 'A rigorous, 7-point legal and operational due diligence checklist for Indian entrepreneurs evaluating franchise opportunities, before committing capital or signing binding agreements.',
                'content' => "Investing in a franchise business in India is a significant commercial commitment. Unlike the United States (which enforces FTC Rule 436) or Australia (Franchising Code of Conduct), India currently lacks a single dedicated federal franchising act. Instead, Indian franchise relationships are governed by a complex intersection of the Indian Contract Act 1872, the Specific Relief Act 1963, the Competition Act 2002, the Trade Marks Act 1999, and the Consumer Protection Act 2019.

Before executing a franchise agreement or remitting an earnest token deposit, every prospective investor must conduct rigorous, independent verification across these seven non-negotiable dimensions:

1. Corporate Entity Validation via MCA 21
Verify that the franchisor operates as a bona fide registered entity (Private Limited Company, Public Limited Company, or Limited Liability Partnership). Using the Ministry of Corporate Affairs (MCA) portal, confirm the Corporate Identification Number (CIN), active status, authorized share capital, and director identities (DIN). Check for director disqualifications under Section 164(2) of the Companies Act 2013 and inspect registered charges or hypothecated assets.

2. Intellectual Property & Trademark Verification on IP India
A franchise business fundamentally licenses a brand's intellectual property. Always verify the brand name and logo on the Trade Marks Registry (ipindiaonline.gov.in). Confirm whether the trademark is 'Registered' (Word Mark & Device Mark in applicable classes such as Class 43 for food services or Class 35 for retail) or merely 'Objected'/'Opposed'. If the trademark is owned by an individual promoter rather than the contracting company, inspect the registered Trademark License Agreement between the owner and the franchisor.

3. Itemized Capital Expenditure vs. Working Capital Reserves
Promotional brochures routinely quote attractive 'Turnkey Setup Costs' while omitting mandatory operational pre-requisites. Require a line-by-line itemization including: civil interior works, electrical HVAC installations, statutory security deposits (electricity board, landlord), initial inventory buffer, and point-of-sale software licenses. Additionally, calculate a mandatory 6-month operational working capital reserve to sustain staff payroll, lease rental, and utilities before reaching cash-flow breakeven.

4. Territorial Exclusivity & Encroachment Protection
Examine the geographical exclusivity clause in the draft contract. Does the franchisor grant an exclusive territory radius (e.g., 3 km in Tier 1 cities or 5 km in Tier 2 cities), or do they retain unrestricted rights to license additional units or launch cloud kitchens nearby? Crucially, verify whether the franchisor sells direct-to-consumer online or via quick-commerce platforms in your assigned territory, which could cannibalize retail outlet revenue.

5. Supply Chain Mandates & Markup Transparency
Most franchise models require franchisees to purchase raw materials, proprietary ingredients, or merchandise exclusively from designated centralized vendors. Scrutinize the pricing mechanism: are supply costs pegged to transparent wholesale indices, or does the franchisor embed high, non-negotiable profit margins on mandatory consumable goods?

6. Termination, Exit, and Liquidated Damages Clauses
Review default remedies and dispute resolution clauses carefully. Notice periods for cure of breach should be bilateral and reasonable (minimum 30 days). Be alert to onerous non-compete clauses that attempt to prohibit you from running any business in the industry for years after contract expiration—such post-term restrictions are often held void under Section 27 of the Indian Contract Act 1872 unless strictly limited to protecting proprietary trade secrets.

7. Grievance History and Existing Franchisee Audits
Never rely exclusively on testimonials arranged by franchisor sales representatives. Visit at least three existing operating franchise units unannounced. Speak directly with on-ground unit operators regarding actual daily footfall, brand marketing support, supply chain consistency, and real-world payback timelines.",
                'sources_and_evidence' => "1. Ministry of Corporate Affairs (MCA21 Portal) — Companies Act 2013 & LLP Act 2008.
2. Controller General of Patents, Designs and Trade Marks (IP India) — Trade Marks Act 1999 (Classes 35, 43, 41).
3. Indian Contract Act 1872 — Section 27 (Agreement in restraint of trade void) and Section 73 (Compensation for breach).
4. Consumer Protection Act 2019 & E-Commerce Rules — Prohibiting deceptive marketing and misleading business representations.",
                'disclaimer' => "This checklist is published by Franchise Dekho for educational and research due diligence. It does not constitute legal representation or chartered financial certification. Franchise seekers should obtain independent legal and financial counsel before executing binding contracts.",
                'is_published' => true,
                'published_at' => now()->subDays(15),
                'last_verified_at' => now()->subDays(2),
                'meta_title' => 'Franchise Due Diligence Checklist: 7 Essential Verification Checks in India',
                'meta_description' => 'A complete 7-point due diligence checklist for evaluating franchise opportunities in India. Entity validation, trademark search, CapEx breakdown, and legal protections.',
            ],
            [
                'title' => 'Master Franchise vs Unit Franchise: Capital Requirements and Territorial Rights Explained',
                'slug' => 'master-franchise-vs-unit-franchise-india',
                'category_id' => null,
                'author_name' => 'Franchise Dekho Research Desk',
                'author_title' => 'Commercial Structuring Specialist',
                'reading_time_minutes' => 6,
                'excerpt' => 'Compare master franchise rights with single-unit operations. Learn the differences in upfront fee structures, sub-franchising commissions, and territorial performance milestones.',
                'content' => "When exploring franchise expansion in India, entrepreneurs frequently encounter two distinct licensing structures: Unit Franchise and Master Franchise (Area Development). Understanding the differences in capital intensity, legal liability, and territorial commitments is critical before entering negotiations.

Unit Franchise Structure: Operational Focus
A Unit Franchise grants the investor rights to operate a single physical outlet at a specified commercial address. 
- Capital Outlay: Typically ₹5 Lakh to ₹40 Lakh depending on sector format.
- Operational Role: Hands-on daily management, localized inventory control, customer service, and store staff oversight.
- Sub-franchising Rights: Strictly zero. The unit operator cannot license the brand to third parties or open subsidiary branches without a separate commercial agreement.
- Revenue Model: Direct customer retail transactions minus cost of goods sold (COGS), operating expenses, and franchisor royalties.

Master Franchise Structure: Regional Scaler
A Master Franchise (or Area Representative Agreement) grants exclusive commercial rights across an entire metropolitan area, state, or geographic zone (e.g., NCR, Western Maharashtra, or South India).
- Capital Outlay: Typically ₹50 Lakh to ₹5 Crore+, covering territory license fees, central warehouse infrastructure, and corporate training hubs.
- Operational Role: Regional brand stewardship. The master franchisee acts as an intermediary franchisor, recruiting, vetting, training, and inspecting single-unit franchisees within their assigned territory.
- Sub-franchising Rights: Full authority to sub-license unit franchises, collect upfront franchisee fees, and split ongoing monthly royalties with the parent brand (typically 50/50 or 60/40 splits).
- Development Milestones: Mandatory minimum store opening schedules (e.g., 10 outlets in Year 1, 25 outlets in Year 3). Failure to meet performance targets can result in territory shrinkage or revocation of exclusivity under Indian contract law.",
                'sources_and_evidence' => "1. Indian Contract Act 1872 — Agency and Principal-Intermediary Rights (Sections 182-238).
2. Reserve Bank of India (RBI) & FEMA Guidelines — Inward/Outward royalty remittances for multinational master franchise agreements.
3. Competition Act 2002 — Section 3 regarding exclusive supply and distribution agreements.",
                'disclaimer' => "Master franchise agreements involve significant corporate legal obligations and performance milestones. Independent commercial and legal review is strongly recommended.",
                'is_published' => true,
                'published_at' => now()->subDays(25),
                'last_verified_at' => now()->subDays(5),
                'meta_title' => 'Master Franchise vs Unit Franchise in India: Differences & Capital Outlay',
                'meta_description' => 'Understand the critical differences between unit franchises and master area franchise agreements in India. Capital requirements, territory rights, and royalty splits.',
            ],
            [
                'title' => 'Understanding Franchise Fees, Royalties, and GST (SAC 998311) Under Indian Law',
                'slug' => 'franchise-fees-royalties-and-gst-india',
                'category_id' => null,
                'author_name' => 'Franchise Dekho Research Desk',
                'author_title' => 'Taxation & Regulatory Advisory Desk',
                'reading_time_minutes' => 7,
                'excerpt' => 'A comprehensive guide to statutory GST taxation on franchise fees and royalties under SAC 998311, Place of Supply rules, and Input Tax Credit (ITC) eligibility for franchisees.',
                'content' => "One of the most common financial discrepancies encountered by first-time franchise buyers in India is the treatment of Goods and Services Tax (GST) on franchise fees and ongoing royalties. Failing to factor in the 18% statutory tax component can distort investment projections and working capital assumptions.

1. Classification Under GST: Service Accounting Code (SAC 998311)
In the Indian GST tariff schedule, franchise consultancy, licensing of business models, brand know-how transfers, and verification services fall under SAC 998311 ('Management Consulting and Business Management Services / Franchise Licensing').
- Applicable Rate: Standard 18% GST.
- Upfront Franchise Fee: Charged on the initial lump-sum franchise licensing fee. A quoted ₹10,00,000 fee will require an actual invoice payment of ₹11,80,000 including 18% GST.
- Ongoing Monthly Royalties: Charged on the regular brand fee (e.g., 6% of gross sales or flat monthly retainer). Each monthly invoice attracts 18% GST.

2. Place of Supply (POS) and Tax Split Rules
The type of GST levied depends strictly on the Place of Supply rules under the Integrated Goods and Services Tax (IGST) Act 2017:
- Intra-State Transactions: When the franchisor's billing entity and the franchisee's registered operational unit are located within the same state (e.g., Haryana to Haryana), the tax is split equally into 9% CGST (Central Goods and Services Tax) + 9% SGST (State Goods and Services Tax).
- Inter-State Transactions: When the franchisor operates in Haryana and the franchisee operates in Maharashtra, Delhi, Karnataka, or Uttar Pradesh, the entire 18% is billed as IGST (Integrated Goods and Services Tax).

3. Input Tax Credit (ITC) Eligibility for Franchisees
Franchisees registered as taxable persons under GST can claim full Input Tax Credit (ITC) for the 18% GST paid on franchise fees and monthly royalties, provided the services are used in furtherance of taxable business activities (e.g., retail store sales or restaurant services). To claim ITC, ensure:
- The franchisor issues a valid statutory GST Tax Invoice under Section 31 of the CGST Act.
- The invoice includes your correct 15-digit GSTIN, business name, and address.
- The franchisor files their GSTR-1 return, reflecting the invoice in your GSTR-2B statement.
Note: If your business format operates under a composite scheme or 5% restaurant GST without ITC, the 18% GST on franchise fees becomes a non-creditable business cost.",
                'sources_and_evidence' => "1. Central Goods and Services Tax (CGST) Act 2017 — Section 9 & Section 16 (Eligibility for Input Tax Credit).
2. Integrated Goods and Services Tax (IGST) Act 2017 — Section 7 & Section 12 (Place of Supply of Services).
3. Central Board of Indirect Taxes and Customs (CBIC) — Tariff Schedule for Service Accounting Code (SAC) 998311.",
                'disclaimer' => "Tax laws and GST circulars are subject to periodic amendment by the GST Council. Franchisees should consult their Chartered Accountant regarding specific ITC eligibility and tax accounting treatment.",
                'is_published' => true,
                'published_at' => now()->subDays(30),
                'last_verified_at' => now()->subDays(3),
                'meta_title' => 'Franchise Fees, Royalties, and GST SAC 998311 Under Indian Law',
                'meta_description' => 'Complete guide to GST on franchise fees and royalties in India under SAC 998311. Intra-state vs inter-state tax split, input tax credit (ITC), and invoice compliances.',
            ],
            [
                'title' => 'Commercial Leases & Zoning Laws for Retail & F&B Franchises in India',
                'slug' => 'commercial-leases-and-zoning-for-franchises-india',
                'category_id' => $fnbCat?->id,
                'author_name' => 'Franchise Dekho Research Desk',
                'author_title' => 'Real Estate & Municipal Compliance Desk',
                'reading_time_minutes' => 7,
                'excerpt' => 'Avoid costly eviction or sealing actions. Essential guide to municipal commercial zoning, Shop & Establishment Act licensing, lease lock-ins, and CAM charges for Indian retail franchisees.',
                'content' => "Securing a prime high-street or mall location is pivotal for retail and food & beverage (QSR) franchise success. However, signing a standard commercial lease agreement without verifying local municipal land-use permits and structural NOCs can lead to sudden municipal sealing, punitive fines, or operational closure.

1. Municipal Commercial Zoning and Master Plan Conformity
Under local municipal corporation regulations (e.g., MCD in Delhi, BMC in Mumbai, GMDA in Gurugram), commercial activities are strictly restricted to designated commercial corridors or authorized mixed-use streets upon payment of applicable conversion and parking charges.
Before signing a lease:
- Inspect the property's Sanctioned Building Plan and Occupancy Certificate (OC). Operating in a building lacking an OC is illegal and invites eviction.
- Confirm whether the specific street has been notified for commercial use under the relevant city Master Plan.
- Verify whether the premises are subject to pending demolition orders, unauthorized floor constructions, or basement usage violations.

2. F&B Specific Licensing Requirements
Food and beverage outlets require specialized infrastructure and statutory clearances before commencing commercial trade:
- FSSAI License: Food Safety and Standards Authority of India registration or state license.
- Fire Safety NOC: Mandated for premises above prescribed square footage or seating capacities. Requires compliant emergency exit doors, fire suppression sprinklers, and dedicated fire extinguishers.
- Health Trade License (HTL): Issued by the local municipal corporation confirming hygienic water supply, sanitary grease traps, and adequate sewage connectivity.
- Environmental Clearance: State Pollution Control Board consent to establish and operate, particularly for effluent discharge and commercial kitchen exhaust chimneys.

3. Commercial Lease Negotiation: 4 Clauses Franchisees Must Scrutinize
- Lock-in Periods: Franchisors often demand 5-year franchise contracts, while landlords demand 3-year lock-in periods with steep penalties for early termination. Ensure your lease lock-in period provides exit relief if the franchisor goes into liquidation or breaches territory rights.
- Common Area Maintenance (CAM) Charges: Clarify whether quoted rent includes CAM, air conditioning, and power backup costs, or whether CAM is billed separately as a variable expense.
- Fit-Out Rent-Free Period: Negotiate a 30 to 60 day rent-free fit-out period during which your civil interior, plumbing, and branding works can be completed before rental accruals commence.
- Force Majeure and Re-instatement: Clearly define business interruption protections (pandemics, municipal sealing drives, infrastructure works) and limit restoration obligations to return the premises in 'normal wear-and-tear' condition.",
                'sources_and_evidence' => "1. Transfer of Property Act 1882 — Leases of Immovable Property (Sections 105-117).
2. Food Safety and Standards Act 2006 (FSSAI) — Licensing & Registration of Food Businesses.
3. State Shop and Commercial Establishments Acts — Working hours, employee registers, and registration compliances.
4. Municipal Corporation Building Bye-Laws & Fire Safety Codes.",
                'disclaimer' => "Municipal commercial laws vary significantly across Indian states and union territories. Conduct physical title inspections and municipal verification before signing commercial lease agreements.",
                'is_published' => true,
                'published_at' => now()->subDays(40),
                'last_verified_at' => now()->subDays(4),
                'meta_title' => 'Commercial Leases and Municipal Zoning for Franchises in India',
                'meta_description' => 'Essential legal guide to commercial leasing, municipal zoning laws, FSSAI licenses, and Shop & Establishment registrations for retail and F&B franchisees in India.',
            ],
        ];

        foreach ($articles as $art) {
            Article::updateOrCreate(
                ['slug' => $art['slug']],
                $art
            );
        }
    }
}
