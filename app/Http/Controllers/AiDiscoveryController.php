<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\AiDiscoveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiDiscoveryController extends Controller
{
    public function __construct(
        protected AiDiscoveryService $aiService
    ) {}

    /**
     * Display the AI Discovery Assistant interface with prompt suggestions.
     */
    public function index(Request $request): View
    {
        $categories = Category::active()->ordered()->get();
        $initialQuery = $request->input('q');
        $result = null;

        if ($request->filled('q')) {
            $result = $this->aiService->ask($initialQuery);
        }

        $suggestedPrompts = [
            'Food & Beverage kiosks under ₹10 Lakh in UP',
            'Courier & Logistics hubs in Delhi NCR',
            'Healthcare sample collection center under ₹15 Lakh',
            'Franchise fees, royalties, and GST SAC 998311 rules',
            'Retail dealerships with space > 500 Sq Ft',
        ];

        return view('assistant.index', compact('categories', 'suggestedPrompts', 'initialQuery', 'result'));
    }

    /**
     * Process an interactive inquiry through the grounded AI engine.
     */
    public function query(Request $request): JsonResponse|View
    {
        $validated = $request->validate([
            'query' => 'required|string|min:3|max:500',
        ]);

        $result = $this->aiService->ask($validated['query']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($result);
        }

        $categories = Category::active()->ordered()->get();
        $suggestedPrompts = [
            'Food & Beverage kiosks under ₹10 Lakh in UP',
            'Courier & Logistics hubs in Delhi NCR',
            'Healthcare sample collection center under ₹15 Lakh',
            'Franchise fees, royalties, and GST SAC 998311 rules',
            'Retail dealerships with space > 500 Sq Ft',
        ];
        $initialQuery = $validated['query'];

        return view('assistant.index', compact('categories', 'suggestedPrompts', 'initialQuery', 'result'));
    }
}
