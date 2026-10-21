<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Opportunity;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SectorController extends Controller
{
    public function __construct(
        protected SeoService $seoService
    ) {}

    /**
     * Display sector-focused landing page with regulatory context and verified opportunities.
     */
    public function show(string $slug): View
    {
        $category = Category::active()->where('slug', $slug)->firstOrFail();

        $opportunities = Opportunity::published()
            ->where('category_id', $category->id)
            ->with(['company', 'locations'])
            ->orderBy('last_verified_at', 'desc')
            ->paginate(9);

        // Curated regulatory and operational context per Indian industry domain
        $regulatoryGuidance = match ($category->slug) {
            'food-beverage' => [
                'acts' => ['Food Safety and Standards Act (FSSAI) 2006', 'State Fire Safety NOC', 'Shop & Establishment Act'],
                'key_check' => 'Ensure brand holds valid Central/State FSSAI license and provides written equipment schedules and recipe standardization protocols.',
                'typical_space' => '150 - 1,200 Sq. Ft.',
                'typical_breakeven' => '12 - 24 Months',
            ],
            'healthcare' => [
                'acts' => ['Drugs and Cosmetics Act 1940', 'Pharmacy Practice Regulations', 'Bio-Medical Waste Management Rules 2016'],
                'key_check' => 'Verify registered pharmacist/pathologist staffing requirements and brand cold-chain logistics certifications.',
                'typical_space' => '250 - 1,500 Sq. Ft.',
                'typical_breakeven' => '18 - 36 Months',
            ],
            'logistics' => [
                'acts' => ['Carriage by Road Act 2007', 'Central Motor Vehicles Rules', 'GST E-Way Bill Compliances'],
                'key_check' => 'Confirm daily parcel volume commitments, route exclusivity clauses, and security deposit refund conditions.',
                'typical_space' => '400 - 2,500 Sq. Ft.',
                'typical_breakeven' => '9 - 18 Months',
            ],
            'retail-fmcg' => [
                'acts' => ['Legal Metrology Act 2009', 'Packaged Commodities Rules', 'State Shop & Commercial Establishment Act'],
                'key_check' => 'Check stock replenishment frequency, inventory return policies for damaged/expired goods, and territorial radius locks.',
                'typical_space' => '300 - 3,000 Sq. Ft.',
                'typical_breakeven' => '12 - 28 Months',
            ],
            'education' => [
                'acts' => ['Right to Education (RTE) Compliance', 'Municipal Building & Safety Codes', 'Copyright/Curriculum Licensing Terms'],
                'key_check' => 'Review intellectual property licensing terms, annual franchise royalty slabs, and teacher training support SLAs.',
                'typical_space' => '1,200 - 5,000 Sq. Ft.',
                'typical_breakeven' => '24 - 36 Months',
            ],
            default => [
                'acts' => ['Companies Act 2013 / LLP Act 2008', 'Shop & Establishment Act', 'GST Act (SAC 998311)'],
                'key_check' => 'Perform corporate registration audits, trademark verification on IP India portal, and audited financial disclosure checks.',
                'typical_space' => '200 - 1,500 Sq. Ft.',
                'typical_breakeven' => '12 - 24 Months',
            ],
        };

        $schema = $this->seoService->sectorSchema($category, $opportunities->items());

        return view('sectors.show', compact('category', 'opportunities', 'regulatoryGuidance', 'schema'));
    }
}
