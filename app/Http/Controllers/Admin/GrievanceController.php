<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grievance;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GrievanceController extends Controller
{
    /**
     * Display the statutory grievance queue and fraud reporting records.
     */
    public function index(Request $request): View
    {
        $query = Grievance::with(['opportunity.company', 'assignedOfficer'])->latest();

        // Status filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Search query
        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('tracking_code', 'like', "%{$q}%")
                    ->orWhere('full_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%");
            });
        }

        $grievances = $query->paginate(20)->withQueryString();

        $metrics = [
            'total' => Grievance::count(),
            'received' => Grievance::where('status', Grievance::STATUS_RECEIVED)->count(),
            'under_investigation' => Grievance::where('status', Grievance::STATUS_UNDER_INVESTIGATION)->count(),
            'resolved' => Grievance::where('status', Grievance::STATUS_RESOLVED)->count(),
            'urgent' => Grievance::where('priority', 'urgent')->whereIn('status', [Grievance::STATUS_RECEIVED, Grievance::STATUS_UNDER_INVESTIGATION])->count(),
        ];

        return view('admin.grievances.index', [
            'grievances' => $grievances,
            'metrics' => $metrics,
            'categories' => Grievance::CATEGORIES,
        ]);
    }

    /**
     * Display full dossier for a specific grievance.
     */
    public function show(Grievance $grievance): View
    {
        $grievance->load(['opportunity.company', 'assignedOfficer']);

        $officers = User::whereIn('role', [User::ROLE_CASE_MANAGER, User::ROLE_ADMIN])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.grievances.show', [
            'grievance' => $grievance,
            'officers' => $officers,
            'categories' => Grievance::CATEGORIES,
            'reporterTypes' => Grievance::REPORTER_TYPES,
        ]);
    }

    /**
     * Assign grievance to a designated officer.
     */
    public function assign(Request $request, Grievance $grievance): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_to_user_id' => ['required', 'exists:users,id'],
        ]);

        $grievance->assigned_to_user_id = $validated['assigned_to_user_id'];
        if ($grievance->status === Grievance::STATUS_RECEIVED) {
            $grievance->status = Grievance::STATUS_UNDER_INVESTIGATION;
        }
        $grievance->save();

        return redirect()->route('admin.grievances.show', $grievance)
            ->with('status', 'Grievance successfully assigned to designated officer.');
    }

    /**
     * Update investigation status.
     */
    public function updateStatus(Request $request, Grievance $grievance): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', [
                Grievance::STATUS_RECEIVED,
                Grievance::STATUS_UNDER_INVESTIGATION,
                Grievance::STATUS_RESOLVED,
                Grievance::STATUS_REJECTED,
            ])],
            'priority' => ['nullable', 'string', 'in:normal,high,urgent'],
        ]);

        $grievance->status = $validated['status'];
        if (! empty($validated['priority'])) {
            $grievance->priority = $validated['priority'];
        }
        $grievance->save();

        return redirect()->route('admin.grievances.show', $grievance)
            ->with('status', 'Grievance status updated to: '.ucwords(str_replace('_', ' ', $grievance->status)));
    }

    /**
     * Formally resolve or close the grievance report with findings.
     */
    public function resolve(Request $request, Grievance $grievance): RedirectResponse
    {
        $validated = $request->validate([
            'resolution_notes' => ['required', 'string', 'min:10', 'max:5000'],
            'outcome' => ['required', 'string', 'in:'.implode(',', [Grievance::STATUS_RESOLVED, Grievance::STATUS_REJECTED])],
        ]);

        $grievance->resolution_notes = $validated['resolution_notes'];
        $grievance->status = $validated['outcome'];
        $grievance->resolved_at = now();
        $grievance->save();

        return redirect()->route('admin.grievances.show', $grievance)
            ->with('status', 'Grievance marked as '.ucwords($grievance->status).' with official resolution findings recorded.');
    }
}
