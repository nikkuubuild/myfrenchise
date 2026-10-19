<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\AssessmentMatchingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    protected AssessmentMatchingService $matchingService;

    public function __construct(AssessmentMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * Show the interactive assessment form.
     */
    public function index(): View
    {
        $categories = Category::active()->ordered()->get();

        return view('assessment.index', compact('categories'));
    }

    /**
     * Compute and display ranked opportunity matches.
     */
    public function match(Request $request): View
    {
        $validated = $request->validate([
            'budget_range' => ['required', 'string', 'in:under-5l,5l-15l,15l-30l,30l-plus'],
            'state' => ['required', 'string', 'max:10'],
            'district' => ['nullable', 'string', 'max:100'],
            'carpet_area_sqft' => ['nullable', 'integer', 'min:50', 'max:50000'],
            'sector' => ['nullable', 'string', 'exists:categories,slug'],
            'experience' => ['nullable', 'string', 'in:first_time,existing_retail,corporate_professional,other'],
        ]);

        $matches = $this->matchingService->match($validated);

        return view('assessment.results', [
            'matches' => $matches,
            'criteria' => $validated,
        ]);
    }
}
