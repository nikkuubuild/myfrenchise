<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\OpportunityFinancialBreakdown;
use App\Models\OpportunityLocation;
use App\Models\OpportunityRequirement;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with audited reference categories and initial verified sample.
     */
    public function run(): void
    {
        // 1. Seed Core Reference Categories
        $categories = [
            [
                'name' => 'Food & Beverage (QSR)',
                'slug' => 'food-beverage',
                'description' => 'Beverage kiosks, quick-service restaurant formats, and bakery setups with documented equipment schedules.',
                'icon' => 'utensils',
                'sort_order' => 1,
            ],
            [
                'name' => 'Retail & FMCG Distribution',
                'slug' => 'retail-fmcg',
                'description' => 'Exclusive territory dealerships, grocery franchises, and consumer electronics distribution networks.',
                'icon' => 'shopping-cart',
                'sort_order' => 2,
            ],
            [
                'name' => 'Courier & Logistics Hubs',
                'slug' => 'logistics',
                'description' => 'Last-mile delivery centers and express parcel distribution franchises with institutional logistics partners.',
                'icon' => 'truck',
                'sort_order' => 3,
            ],
            [
                'name' => 'Education & Skill Institutes',
                'slug' => 'education',
                'description' => 'Pre-schools, competitive test prep centers, and vocational training institutes with audited curriculum licensing.',
                'icon' => 'academic-cap',
                'sort_order' => 4,
            ],
            [
                'name' => 'Healthcare & Diagnostics',
                'slug' => 'healthcare',
                'description' => 'Pathology sample collection points, retail pharmacies, and dental clinic franchises with medical regulatory backing.',
                'icon' => 'heart',
                'sort_order' => 5,
            ],
            [
                'name' => 'Banking CSP & Financial Outlets',
                'slug' => 'banking-csp',
                'description' => 'Customer Service Points and payment kiosks verified against institutional bank authorization letters.',
                'icon' => 'building-bank',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $catData) {
            Category::updateOrCreate(['slug' => $catData['slug']], $catData);
        }

        // 2. Seed Default Admin User
        User::updateOrCreate(
            ['email' => 'admin@franchisedekho.in'],
            [
                'name' => 'Franchise Dekho Admin',
                'password' => bcrypt('AdminPassword123!'),
            ]
        );

        // 3. Seed Verified Sample Company & Opportunity (Chai Point Express)
        $fnbCategory = Category::where('slug', 'food-beverage')->first();

        $company = Company::updateOrCreate(
            ['slug' => 'mountain-trail-foods'],
            [
                'legal_name' => 'Mountain Trail Foods Private Limited',
                'brand_name' => 'Chai Point',
                'cin' => 'U55101KA2008PTC047123',
                'gstin' => '29AABCM1234F1Z8',
                'pan' => 'AABCM1234F',
                'registered_address' => 'Indiranagar, Bengaluru, Karnataka 560038',
                'operational_state' => 'Karnataka',
                'official_website' => 'https://chaipoint.com',
                'official_email' => 'franchise@chaipoint.com',
                'official_phone' => '+918040001234',
                'incorporation_year' => 2008,
                'status' => 'verified',
            ]
        );

        $opportunity = Opportunity::updateOrCreate(
            ['slug' => 'chai-point-express'],
            [
                'company_id' => $company->id,
                'category_id' => $fnbCategory->id,
                'title' => 'Chai Point Express Kiosk',
                'summary' => 'Compact QSR beverage and snacks format designed for high footfall commercial hubs, metro stations, and tech parks.',
                'description' => 'Chai Point Express operates on a semi-automated brewing dispenser model with centralized raw material supply. All equipment specifications, franchise agreements, and trademark certifications have been reviewed.',
                'business_model' => 'FOFO',
                'min_investment' => 850000,
                'max_investment' => 1200000,
                'franchise_fee' => 250000,
                'royalty_percentage' => 6.00,
                'min_space_sqft' => 150,
                'max_space_sqft' => 300,
                'min_term_years' => 5,
                'publication_status' => 'published',
                'is_featured' => true,
                'published_at' => now()->subDays(10),
                'last_verified_at' => now()->subDays(3),
                'verification_valid_until' => now()->addDays(177),
                'disclaimer' => 'Approval authority remains solely with Mountain Trail Foods Pvt. Ltd. Turnover and profit margins vary depending on high street footfall and operator execution.',
            ]
        );

        // Financial Breakdowns
        OpportunityFinancialBreakdown::where('opportunity_id', $opportunity->id)->delete();
        $financials = [
            ['item_name' => 'Initial Franchise Fee', 'amount' => 250000, 'is_estimate' => false, 'is_refundable' => false, 'source_evidence' => 'Clause 3.1 Franchise Agreement', 'sort_order' => 1],
            ['item_name' => 'Dispensing Equipment & Machinery', 'amount' => 350000, 'is_estimate' => true, 'is_refundable' => false, 'source_evidence' => 'Vendor Rate Card 2026', 'sort_order' => 2],
            ['item_name' => 'Interior Fit-out, Branding & Signage', 'amount' => 200000, 'is_estimate' => true, 'is_refundable' => false, 'source_evidence' => 'Standard Architect Blueprint', 'sort_order' => 3],
            ['item_name' => 'Initial Tea Leaves, Packaging & Consumables', 'amount' => 100000, 'is_estimate' => false, 'is_refundable' => false, 'source_evidence' => 'Initial Supply Invoice Schedule', 'sort_order' => 4],
            ['item_name' => 'Security Deposit (Refundable on Term End)', 'amount' => 100000, 'is_estimate' => false, 'is_refundable' => true, 'source_evidence' => 'Clause 7 Agreement', 'sort_order' => 5],
        ];
        foreach ($financials as $fin) {
            $fin['opportunity_id'] = $opportunity->id;
            OpportunityFinancialBreakdown::create($fin);
        }

        // Available Locations
        OpportunityLocation::where('opportunity_id', $opportunity->id)->delete();
        $locations = [
            ['state_code' => 'UP', 'state_name' => 'Uttar Pradesh', 'district' => 'Lucknow', 'territory_tier' => 'Tier 2', 'is_exclusive' => true, 'status' => 'available'],
            ['state_code' => 'UP', 'state_name' => 'Uttar Pradesh', 'district' => 'Kanpur', 'territory_tier' => 'Tier 2', 'is_exclusive' => true, 'status' => 'available'],
            ['state_code' => 'DL', 'state_name' => 'Delhi NCR', 'district' => 'Noida', 'territory_tier' => 'Tier 1', 'is_exclusive' => true, 'status' => 'available'],
            ['state_code' => 'RJ', 'state_name' => 'Rajasthan', 'district' => 'Jaipur', 'territory_tier' => 'Tier 2', 'is_exclusive' => false, 'status' => 'available'],
        ];
        foreach ($locations as $loc) {
            $loc['opportunity_id'] = $opportunity->id;
            OpportunityLocation::create($loc);
        }

        // Requirements
        OpportunityRequirement::where('opportunity_id', $opportunity->id)->delete();
        $requirements = [
            ['requirement_type' => 'space', 'title' => 'Minimum 150 Sq. Ft. Carpet Area', 'description' => 'Ground floor corner retail shop or food court kiosk with prominent frontage.', 'is_mandatory' => true],
            ['requirement_type' => 'power', 'title' => 'Commercial 5 kW Three-Phase Connection', 'description' => 'Dedicated power supply for induction boilers and automated tea brewing dispensers.', 'is_mandatory' => true],
            ['requirement_type' => 'water', 'title' => 'Dedicated Potable Water & Drainage Line', 'description' => 'Food safety and municipal sanitation compliance requirement.', 'is_mandatory' => true],
            ['requirement_type' => 'experience', 'title' => 'Full-time Onsite Operating Partner', 'description' => 'Owner-operator preferred; prior retail/hospitality experience advantageous.', 'is_mandatory' => false],
        ];
        foreach ($requirements as $req) {
            $req['opportunity_id'] = $opportunity->id;
            OpportunityRequirement::create($req);
        }
    }
}
