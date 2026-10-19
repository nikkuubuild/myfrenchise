<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnquiryRequest;
use App\Models\Enquiry;
use App\Models\Opportunity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    /**
     * Show the progressive needs assessment and managed-service enquiry form.
     */
    public function create(Request $request): View
    {
        $targetOpportunity = null;

        if ($request->filled('opportunity')) {
            $identifier = $request->input('opportunity');
            $targetOpportunity = Opportunity::published()
                ->where('slug', $identifier)
                ->orWhere('id', $identifier)
                ->first();
        }

        return view('enquiry.create', compact('targetOpportunity'));
    }

    /**
     * Validate and record a consented customer enquiry.
     */
    public function store(StoreEnquiryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $enquiry = Enquiry::create([
            'reference_code' => Enquiry::generateReferenceCode(),
            'opportunity_id' => $validated['opportunity_id'] ?? null,
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'target_state' => $validated['target_state'],
            'target_city' => $validated['target_city'],
            'budget_range' => $validated['budget_range'],
            'preferred_timeline' => $validated['preferred_timeline'],
            'commercial_space_status' => $validated['commercial_space_status'],
            'carpet_area_sqft' => $validated['carpet_area_sqft'] ?? null,
            'business_experience' => $validated['business_experience'],
            'notes' => $validated['notes'] ?? null,
            'consent_given' => true,
            'consent_recorded_at' => now(),
            'ip_address' => $request->ip(),
            'status' => 'new',
        ]);

        return redirect()
            ->route('enquiry.confirmed')
            ->with('enquiry_reference', $enquiry->reference_code);
    }

    /**
     * Display the confirmation receipt and case manager next steps.
     */
    public function confirmed(Request $request): View
    {
        $referenceCode = session('enquiry_reference', 'FD-CASE-ACTIVE');

        return view('enquiry.confirmed', compact('referenceCode'));
    }
}
