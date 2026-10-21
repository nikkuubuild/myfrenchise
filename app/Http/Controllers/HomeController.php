<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Opportunity;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected \App\Services\SeoService $seoService
    ) {}

    /**
     * Display the editorial homepage with live verified statistics and categories.
     */
    public function index(): View
    {
        $categories = Category::active()
            ->ordered()
            ->withCount(['opportunities' => fn ($q) => $q->published()])
            ->get();

        $featuredOpportunities = Opportunity::published()
            ->with(['company', 'category', 'locations'])
            ->where('is_featured', true)
            ->latest('last_verified_at')
            ->take(3)
            ->get();

        // If no featured explicitly tagged, grab latest verified
        if ($featuredOpportunities->isEmpty()) {
            $featuredOpportunities = Opportunity::published()
                ->with(['company', 'category', 'locations'])
                ->latest('last_verified_at')
                ->take(3)
                ->get();
        }

        $stats = [
            'total_opportunities' => Opportunity::published()->count(),
            'total_companies' => Company::verified()->count(),
            'total_categories' => $categories->count(),
        ];

        $schema = [
            $this->seoService->organizationSchema(),
            $this->seoService->webSiteSchema(),
        ];

        return view('welcome', compact('categories', 'featuredOpportunities', 'stats', 'schema'));
    }
}
