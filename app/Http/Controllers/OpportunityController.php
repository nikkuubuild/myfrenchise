<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OpportunityController extends Controller
{
    public function __construct(
        protected \App\Services\SeoService $seoService
    ) {}

    /**
     * Display a filterable catalog of verified business opportunities.
     */
    public function index(Request $request): View
    {
        $categories = Category::active()->ordered()->get();

        $query = Opportunity::published()
            ->with(['company', 'category', 'locations']);

        // Filter by Sector / Category
        if ($request->filled('sector')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('sector'));
            });
        }

        // Filter by Investment Outlay Range
        if ($request->filled('budget')) {
            match ($request->input('budget')) {
                'under-5l' => $query->where('min_investment', '<=', 500000),
                '5l-15l' => $query->where('max_investment', '>=', 500000)->where('min_investment', '<=', 1500000),
                '15l-30l' => $query->where('max_investment', '>=', 1500000)->where('min_investment', '<=', 3000000),
                '30l-plus' => $query->where('max_investment', '>=', 3000000),
                default => null,
            };
        }

        // Filter by Target State
        if ($request->filled('state')) {
            $query->whereHas('locations', function ($q) use ($request) {
                $q->where('state_code', $request->input('state'));
            });
        }

        // Text Search
        if ($request->filled('q')) {
            $search = '%'.$request->input('q').'%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('summary', 'like', $search)
                    ->orWhereHas('company', fn ($cq) => $cq->where('brand_name', 'like', $search));
            });
        }

        // Sorting
        match ($request->input('sort')) {
            'investment_asc' => $query->orderBy('min_investment', 'asc'),
            'investment_desc' => $query->orderBy('max_investment', 'desc'),
            default => $query->orderBy('last_verified_at', 'desc'),
        };

        $opportunities = $query->paginate(9)->withQueryString();

        $selectedFilters = [
            'sector' => $request->input('sector'),
            'budget' => $request->input('budget'),
            'state' => $request->input('state'),
            'q' => $request->input('q'),
            'sort' => $request->input('sort', 'recently_verified'),
        ];

        return view('opportunities.index', compact('opportunities', 'categories', 'selectedFilters'));
    }

    /**
     * Display comprehensive disclosures for a single verified opportunity.
     */
    public function show(string $slug): View
    {
        $opportunity = Opportunity::where('slug', $slug)
            ->with(['company', 'category', 'financialBreakdowns', 'locations', 'requirements'])
            ->firstOrFail();

        // Enforce access control: unverified or unpublished opportunities cannot be viewed publicly
        if (! $opportunity->isVerified() && ! auth()->check()) {
            abort(404, 'This opportunity is not currently approved for publication.');
        }

        $schema = $this->seoService->opportunitySchema($opportunity);

        return view('opportunities.show', compact('opportunity', 'schema'));
    }
}
