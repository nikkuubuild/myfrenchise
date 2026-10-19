<?php

namespace App\Http\Controllers;

use App\Models\Grievance;
use App\Models\Opportunity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GrievanceController extends Controller
{
    /**
     * Show grievance redressal disclosures and filing intake form.
     */
    public function index(Request $request): View
    {
        $targetOpportunity = null;
        if ($request->filled('opportunity')) {
            $targetOpportunity = Opportunity::where('slug', $request->input('opportunity'))
                ->orWhere('id', $request->input('opportunity'))
                ->first();
        }

        return view('pages.grievance', [
            'categories' => Grievance::CATEGORIES,
            'reporterTypes' => Grievance::REPORTER_TYPES,
            'targetOpportunity' => $targetOpportunity,
        ]);
    }

    /**
     * Submit a formal grievance or fraud report.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reporter_type' => ['required', 'string', 'in:'.implode(',', array_keys(Grievance::REPORTER_TYPES))],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\s\-]{10,15}$/'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(Grievance::CATEGORIES))],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'evidence_summary' => ['nullable', 'string', 'max:2000'],
            'opportunity_id' => ['nullable', 'exists:opportunities,id'],
            'declaration' => ['accepted'],
        ], [
            'declaration.accepted' => 'You must declare that the submitted information is true and accurate to the best of your knowledge.',
        ]);

        $opportunity = ! empty($validated['opportunity_id']) ? Opportunity::find($validated['opportunity_id']) : null;

        $grievance = Grievance::create([
            'tracking_code' => Grievance::generateTrackingCode(),
            'reporter_type' => $validated['reporter_type'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'opportunity_id' => $opportunity?->id,
            'company_id' => $opportunity?->company_id,
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'evidence_summary' => $validated['evidence_summary'] ?? null,
            'status' => Grievance::STATUS_RECEIVED,
            'priority' => in_array($validated['category'], ['fee_fraud', 'unauthorized_impersonation']) ? 'urgent' : 'normal',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('grievance.confirmed')
            ->with('grievance_tracking_code', $grievance->tracking_code)
            ->with('status', 'Your grievance has been formally logged and assigned tracking code '.$grievance->tracking_code);
    }

    /**
     * Display filing confirmation receipt.
     */
    public function confirmed(): View
    {
        $trackingCode = session('grievance_tracking_code', 'FD-GRV-ACTIVE');

        return view('pages.grievance-confirmed', compact('trackingCode'));
    }

    /**
     * Public status tracking by complaint code and email.
     */
    public function track(Request $request): View
    {
        $grievance = null;

        if ($request->filled(['tracking_code', 'email'])) {
            $grievance = Grievance::where('tracking_code', strtoupper(trim($request->input('tracking_code'))))
                ->where('email', strtolower(trim($request->input('email'))))
                ->first();
        }

        return view('pages.grievance-track', [
            'grievance' => $grievance,
            'searched' => $request->filled(['tracking_code', 'email']),
            'categories' => Grievance::CATEGORIES,
        ]);
    }
}
