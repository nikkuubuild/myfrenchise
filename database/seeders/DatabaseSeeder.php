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
     * Seed the application's database with audited reference categories, staff accounts,
     * and verified sample opportunities across Indian business sectors.
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
                'is_active' => true,
            ],
            [
                'name' => 'Retail & FMCG Distribution',
                'slug' => 'retail-fmcg',
                'description' => 'Exclusive territory dealerships, grocery franchises, and consumer electronics distribution networks.',
                'icon' => 'shopping-cart',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Courier & Logistics Hubs',
                'slug' => 'logistics',
                'description' => 'Last-mile delivery centers and express parcel distribution franchises with institutional logistics partners.',
                'icon' => 'truck',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Education & Skill Institutes',
                'slug' => 'education',
                'description' => 'Pre-schools, competitive test prep centers, and vocational training institutes with audited curriculum licensing.',
                'icon' => 'academic-cap',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Healthcare & Diagnostics',
                'slug' => 'healthcare',
                'description' => 'Pathology sample collection points, retail pharmacies, and dental clinic franchises with medical regulatory backing.',
                'icon' => 'heart',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Banking CSP & Financial Outlets',
                'slug' => 'banking-csp',
                'description' => 'Customer Service Points and payment kiosks verified against institutional bank authorization letters.',
                'icon' => 'building-bank',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $catData) {
            Category::updateOrCreate(['slug' => $catData['slug']], $catData);
        }

        // 2. Seed Default Staff Accounts
        User::updateOrCreate(
            ['email' => 'admin@franchisedekho.in'],
            [
                'name' => 'Franchise Dekho Admin',
                'password' => bcrypt('AdminPassword123!'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@franchisedekho.in'],
            [
                'name' => 'Franchise Dekho Coordinator',
                'password' => bcrypt('ManagerPassword123!'),
                'role' => User::ROLE_CASE_MANAGER,
                'is_active' => true,
            ]
        );

        // 3. Seed Verified Sample Opportunity 1: Chai Point Express (Food & Beverage)
        $fnbCategory = Category::where('slug', 'food-beverage')->first();

        $companyChai = Company::updateOrCreate(
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

        $oppChai = Opportunity::updateOrCreate(
            ['slug' => 'chai-point-express'],
            [
                'company_id' => $companyChai->id,
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

        OpportunityFinancialBreakdown::where('opportunity_id', $oppChai->id)->delete();
        $chaiFinancials = [
            ['item_name' => 'Initial Franchise Fee', 'amount' => 250000, 'is_estimate' => false, 'is_refundable' => false, 'source_evidence' => 'Clause 3.1 Franchise Agreement', 'sort_order' => 1],
            ['item_name' => 'Dispensing Equipment & Machinery', 'amount' => 350000, 'is_estimate' => true, 'is_refundable' => false, 'source_evidence' => 'Vendor Rate Card 2026', 'sort_order' => 2],
            ['item_name' => 'Interior Fit-out, Branding & Signage', 'amount' => 200000, 'is_estimate' => true, 'is_refundable' => false, 'source_evidence' => 'Standard Architect Blueprint', 'sort_order' => 3],
            ['item_name' => 'Initial Tea Leaves, Packaging & Consumables', 'amount' => 100000, 'is_estimate' => false, 'is_refundable' => false, 'source_evidence' => 'Initial Supply Invoice Schedule', 'sort_order' => 4],
            ['item_name' => 'Security Deposit (Refundable on Term End)', 'amount' => 100000, 'is_estimate' => false, 'is_refundable' => true, 'source_evidence' => 'Clause 7 Agreement', 'sort_order' => 5],
        ];
        foreach ($chaiFinancials as $fin) {
            $fin['opportunity_id'] = $oppChai->id;
            OpportunityFinancialBreakdown::create($fin);
        }

        OpportunityLocation::where('opportunity_id', $oppChai->id)->delete();
        $chaiLocations = [
            ['state_code' => 'UP', 'state_name' => 'Uttar Pradesh', 'district' => 'Lucknow', 'territory_tier' => 'Tier 2', 'is_exclusive' => true, 'status' => 'available'],
            ['state_code' => 'UP', 'state_name' => 'Uttar Pradesh', 'district' => 'Kanpur', 'territory_tier' => 'Tier 2', 'is_exclusive' => true, 'status' => 'available'],
            ['state_code' => 'DL', 'state_name' => 'Delhi NCR', 'district' => 'Noida', 'territory_tier' => 'Tier 1', 'is_exclusive' => true, 'status' => 'available'],
            ['state_code' => 'RJ', 'state_name' => 'Rajasthan', 'district' => 'Jaipur', 'territory_tier' => 'Tier 2', 'is_exclusive' => false, 'status' => 'available'],
        ];
        foreach ($chaiLocations as $loc) {
            $loc['opportunity_id'] = $oppChai->id;
            OpportunityLocation::create($loc);
        }

        OpportunityRequirement::where('opportunity_id', $oppChai->id)->delete();
        $chaiRequirements = [
            ['requirement_type' => 'space', 'title' => 'Minimum 150 Sq. Ft. Carpet Area', 'description' => 'Ground floor corner retail shop or food court kiosk with prominent frontage.', 'is_mandatory' => true],
            ['requirement_type' => 'power', 'title' => 'Commercial 5 kW Three-Phase Connection', 'description' => 'Dedicated power supply for induction boilers and automated tea brewing dispensers.', 'is_mandatory' => true],
            ['requirement_type' => 'water', 'title' => 'Dedicated Potable Water & Drainage Line', 'description' => 'Food safety and municipal sanitation compliance requirement.', 'is_mandatory' => true],
            ['requirement_type' => 'experience', 'title' => 'Full-time Onsite Operating Partner', 'description' => 'Owner-operator preferred; prior retail/hospitality experience advantageous.', 'is_mandatory' => false],
        ];
        foreach ($chaiRequirements as $req) {
            $req['opportunity_id'] = $oppChai->id;
            OpportunityRequirement::create($req);
        }

        // 4. Seed Verified Sample Opportunity 2: Delhivery Express Logistics Hub (Logistics)
        $logisticsCategory = Category::where('slug', 'logistics')->first();
        if ($logisticsCategory) {
            $companyDelhivery = Company::updateOrCreate(
                ['slug' => 'delhivery-limited'],
                [
                    'legal_name' => 'Delhivery Limited',
                    'brand_name' => 'Delhivery Express',
                    'cin' => 'L63090DL2011PLC221234',
                    'gstin' => '07AABCD1234F1Z5',
                    'pan' => 'AABCD1234F',
                    'registered_address' => 'Plot 5, Sector 44, Gurugram, Haryana 122002',
                    'operational_state' => 'Haryana',
                    'official_website' => 'https://delhivery.com',
                    'official_email' => 'partner@delhivery.com',
                    'official_phone' => '+911244005000',
                    'incorporation_year' => 2011,
                    'status' => 'verified',
                ]
            );

            $oppDelhivery = Opportunity::updateOrCreate(
                ['slug' => 'delhivery-express-hub'],
                [
                    'company_id' => $companyDelhivery->id,
                    'category_id' => $logisticsCategory->id,
                    'title' => 'Delhivery Express Logistics Partner Hub',
                    'summary' => 'Authorized last-mile parcel booking and delivery distribution hub for tier-2/3 commercial corridors.',
                    'description' => 'Delhivery Partner Program allows independent commercial operators to manage territorial package distribution with automated barcode sorting, handheld terminals, and ERP connectivity.',
                    'business_model' => 'FOFO',
                    'min_investment' => 450000,
                    'max_investment' => 750000,
                    'franchise_fee' => 100000,
                    'royalty_percentage' => 0.00,
                    'min_space_sqft' => 300,
                    'max_space_sqft' => 600,
                    'min_term_years' => 3,
                    'publication_status' => 'published',
                    'is_featured' => true,
                    'published_at' => now()->subDays(8),
                    'last_verified_at' => now()->subDays(2),
                    'verification_valid_until' => now()->addDays(178),
                    'disclaimer' => 'Territory allocation is subject to commercial route viability audit and vehicle feeder connectivity.',
                ]
            );

            OpportunityFinancialBreakdown::where('opportunity_id', $oppDelhivery->id)->delete();
            $delhiveryFinancials = [
                ['item_name' => 'Partner Onboarding Fee', 'amount' => 100000, 'is_estimate' => false, 'is_refundable' => false, 'source_evidence' => 'Partner Schedule A', 'sort_order' => 1],
                ['item_name' => 'Security Deposit (Interest Free)', 'amount' => 150000, 'is_estimate' => false, 'is_refundable' => true, 'source_evidence' => 'Partner Agreement Cl 5', 'sort_order' => 2],
                ['item_name' => 'Barcode Scanners & Thermal Printers', 'amount' => 80000, 'is_estimate' => true, 'is_refundable' => false, 'source_evidence' => 'Hardware Specification Sheet', 'sort_order' => 3],
                ['item_name' => 'Hub Branding & Office Setup', 'amount' => 120000, 'is_estimate' => true, 'is_refundable' => false, 'source_evidence' => 'Standard Fit-out Manual', 'sort_order' => 4],
            ];
            foreach ($delhiveryFinancials as $fin) {
                $fin['opportunity_id'] = $oppDelhivery->id;
                OpportunityFinancialBreakdown::create($fin);
            }

            OpportunityLocation::where('opportunity_id', $oppDelhivery->id)->delete();
            $delhiveryLocations = [
                ['state_code' => 'UP', 'state_name' => 'Uttar Pradesh', 'district' => 'Varanasi', 'territory_tier' => 'Tier 2', 'is_exclusive' => true, 'status' => 'available'],
                ['state_code' => 'MH', 'state_name' => 'Maharashtra', 'district' => 'Nagpur', 'territory_tier' => 'Tier 2', 'is_exclusive' => true, 'status' => 'available'],
                ['state_code' => 'HR', 'state_name' => 'Haryana', 'district' => 'Karnal', 'territory_tier' => 'Tier 3', 'is_exclusive' => true, 'status' => 'available'],
            ];
            foreach ($delhiveryLocations as $loc) {
                $loc['opportunity_id'] = $oppDelhivery->id;
                OpportunityLocation::create($loc);
            }

            OpportunityRequirement::where('opportunity_id', $oppDelhivery->id)->delete();
            $delhiveryRequirements = [
                ['requirement_type' => 'space', 'title' => 'Minimum 300 Sq. Ft. Commercial Storage', 'description' => 'Shutter-access ground floor unit with loading dock access.', 'is_mandatory' => true],
                ['requirement_type' => 'power', 'title' => 'Uninterrupted Broadband & Inverter Backup', 'description' => 'Real-time sync requirement for package delivery tracking.', 'is_mandatory' => true],
                ['requirement_type' => 'experience', 'title' => 'Fleet Coordination Capability', 'description' => 'Ability to oversee 3-5 delivery riders with two-wheelers.', 'is_mandatory' => true],
            ];
            foreach ($delhiveryRequirements as $req) {
                $req['opportunity_id'] = $oppDelhivery->id;
                OpportunityRequirement::create($req);
            }
        }

        // 5. Seed Verified Sample Opportunity 3: Apollo Diagnostics (Healthcare)
        $healthcareCategory = Category::where('slug', 'healthcare')->first();
        if ($healthcareCategory) {
            $companyApollo = Company::updateOrCreate(
                ['slug' => 'apollo-health-lifestyle'],
                [
                    'legal_name' => 'Apollo Health and Lifestyle Limited',
                    'brand_name' => 'Apollo Diagnostics',
                    'cin' => 'U85110TG2000PLC035345',
                    'gstin' => '36AAACA1234F1Z2',
                    'pan' => 'AAACA1234F',
                    'registered_address' => 'Jubilee Hills, Hyderabad, Telangana 500033',
                    'operational_state' => 'Telangana',
                    'official_website' => 'https://apollodiagnostics.in',
                    'official_email' => 'franchise@apollodiagnostics.in',
                    'official_phone' => '+914044442222',
                    'incorporation_year' => 2000,
                    'status' => 'verified',
                ]
            );

            $oppApollo = Opportunity::updateOrCreate(
                ['slug' => 'apollo-diagnostics-collection-centre'],
                [
                    'company_id' => $companyApollo->id,
                    'category_id' => $healthcareCategory->id,
                    'title' => 'Apollo Diagnostics Sample Collection Centre',
                    'summary' => 'NABL-accredited pathology sample collection franchise with centralized hub lab testing.',
                    'description' => 'Franchisee operates a certified phlebotomy collection centre. Blood, urine, and pathology samples are cold-chain transported to Apollo regional reference laboratories for testing and digital report dispatch.',
                    'business_model' => 'FOFO',
                    'min_investment' => 350000,
                    'max_investment' => 600000,
                    'franchise_fee' => 150000,
                    'royalty_percentage' => 0.00,
                    'min_space_sqft' => 200,
                    'max_space_sqft' => 400,
                    'min_term_years' => 5,
                    'publication_status' => 'published',
                    'is_featured' => true,
                    'published_at' => now()->subDays(6),
                    'last_verified_at' => now()->subDays(1),
                    'verification_valid_until' => now()->addDays(179),
                    'disclaimer' => 'Phlebotomist must hold DMLT or BMLT medical certification registered under State Paramedical Council.',
                ]
            );

            OpportunityFinancialBreakdown::where('opportunity_id', $oppApollo->id)->delete();
            $apolloFinancials = [
                ['item_name' => 'Franchise Authorization Fee', 'amount' => 150000, 'is_estimate' => false, 'is_refundable' => false, 'source_evidence' => 'Franchise Master Agreement', 'sort_order' => 1],
                ['item_name' => 'Centrifuge & Cold Chain Refrigerator', 'amount' => 90000, 'is_estimate' => true, 'is_refundable' => false, 'source_evidence' => 'Medical Equipment Schedule', 'sort_order' => 2],
                ['item_name' => 'Clinic Signage & Sterile Phlebotomy Cubicle', 'amount' => 110000, 'is_estimate' => true, 'is_refundable' => false, 'source_evidence' => 'Standard Clinic Layout Spec', 'sort_order' => 3],
            ];
            foreach ($apolloFinancials as $fin) {
                $fin['opportunity_id'] = $oppApollo->id;
                OpportunityFinancialBreakdown::create($fin);
            }

            OpportunityLocation::where('opportunity_id', $oppApollo->id)->delete();
            $apolloLocations = [
                ['state_code' => 'DL', 'state_name' => 'Delhi NCR', 'district' => 'Dwarka', 'territory_tier' => 'Tier 1', 'is_exclusive' => true, 'status' => 'available'],
                ['state_code' => 'UP', 'state_name' => 'Uttar Pradesh', 'district' => 'Ghaziabad', 'territory_tier' => 'Tier 2', 'is_exclusive' => true, 'status' => 'available'],
                ['state_code' => 'KA', 'state_name' => 'Karnataka', 'district' => 'Mysuru', 'territory_tier' => 'Tier 2', 'is_exclusive' => true, 'status' => 'available'],
            ];
            foreach ($apolloLocations as $loc) {
                $loc['opportunity_id'] = $oppApollo->id;
                OpportunityLocation::create($loc);
            }

            OpportunityRequirement::where('opportunity_id', $oppApollo->id)->delete();
            $apolloRequirements = [
                ['requirement_type' => 'space', 'title' => 'Ground Floor Clinical Footprint (200+ sq ft)', 'description' => 'Hygienic, wheelchair accessible ground floor premises on a prominent residential-commercial road.', 'is_mandatory' => true],
                ['requirement_type' => 'experience', 'title' => 'Certified Phlebotomist (DMLT / BMLT)', 'description' => 'Mandatory qualified healthcare staff under Clinical Establishments Act.', 'is_mandatory' => true],
                ['requirement_type' => 'power', 'title' => '24x7 Power Backup for Cold Storage', 'description' => 'Biological samples must be preserved between 2°C and 8°C prior to courier collection.', 'is_mandatory' => true],
            ];
            foreach ($apolloRequirements as $req) {
                $req['opportunity_id'] = $oppApollo->id;
                OpportunityRequirement::create($req);
            }
        }

        // 6. Seed Service Packages for Transparent Commerce
        $this->call(ServicePackageSeeder::class);

        // 7. Seed Authoritative Due Diligence Guides
        $this->call(ArticleSeeder::class);
    }
}
