<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    /**
     * Display the authenticated customer's dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $enquiryQuery = $user->enquiries()
            ->with([
                'opportunity.company',
                'opportunity.category',
                'assignedCaseManager',
                'customerVisibleUpdates.author',
                'consents',
            ])
            ->latest();

        $enquiries = $enquiryQuery->get();

        $metrics = [
            'total_enquiries' => $enquiries->count(),
            'active_cases' => $enquiries->whereIn('status', ['new', 'shortlisted', 'consented', 'submitted_to_brand'])->count(),
            'submitted_to_brands' => $enquiries->where('status', 'submitted_to_brand')->count(),
        ];

        return view('customer.dashboard', [
            'user' => $user,
            'enquiries' => $enquiries,
            'metrics' => $metrics,
        ]);
    }
}
