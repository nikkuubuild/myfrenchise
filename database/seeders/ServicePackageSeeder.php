<?php

namespace Database\Seeders;

use App\Models\ServicePackage;
use Illuminate\Database\Seeder;

class ServicePackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Managed Legal Due Diligence & Application Dossier',
                'slug' => 'commercial-due-diligence',
                'target_audience' => 'customer',
                'summary' => 'Independent statutory verification of corporate records and human-coordinated franchise application presentation.',
                'description' => 'Designed for serious business investors and prospective franchisees who require independent legal verification before remitting brand fees.',
                'inclusions' => [
                    'Independent MCA / ROC corporate registration and directorship check',
                    'GSTIN active status and filing regularity audit',
                    'Trademark registry title and class ownership check',
                    'Dedicated Case Manager document compilation and review',
                    'Consented submission package delivery to verified brand franchisor',
                ],
                'exclusions' => [
                    'Does not guarantee franchisor application approval or territory allocation',
                    'Does not guarantee commercial turnover, profits, or business success',
                    'Does not include franchisor license or franchise agreement fees',
                ],
                'cancellation_policy' => 'Full refund available if cancelled within 24 hours prior to case manager assignment. Non-refundable once primary corporate and registry investigations commence.',
                'base_price_paise' => 499900, // ₹4,999.00
                'tax_rate_percentage' => 18.00,
                'sac_code' => '998311',
                'is_active' => true,
            ],
            [
                'name' => 'Brand Expansion Onboarding & Verification Audit',
                'slug' => 'brand-verification-listing',
                'target_audience' => 'company',
                'summary' => 'Comprehensive statutory compliance verification and verified platform listing for growing franchisors.',
                'description' => 'For legitimate Indian enterprises seeking serious franchise applicants through independent evidentiary verification.',
                'inclusions' => [
                    'Formal 5-point independent statutory verification audit',
                    'Verified platform badge and catalog listing upon review approval',
                    'Human-screened entrepreneur applicant coordination',
                    'Dedicated case pipeline and document vault access',
                ],
                'exclusions' => [
                    'Does not guarantee minimum application volume or financial closure',
                    'Does not endorse franchisor operational management or profitability',
                ],
                'cancellation_policy' => 'Non-refundable once verification research docket is formally registered.',
                'base_price_paise' => 2499900, // ₹24,999.00
                'tax_rate_percentage' => 18.00,
                'sac_code' => '998311',
                'is_active' => true,
            ],
        ];

        foreach ($packages as $pkg) {
            ServicePackage::updateOrCreate(['slug' => $pkg['slug']], $pkg);
        }
    }
}
