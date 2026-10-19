<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseCommunication;
use App\Models\CaseTask;
use App\Models\Enquiry;
use App\Models\User;
use App\Services\CaseManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseController extends Controller
{
    protected CaseManagementService $caseService;

    public function __construct(CaseManagementService $caseService)
    {
        $this->caseService = $caseService;
    }

    /**
     * Display the operational case management pipeline.
     */
    public function index(Request $request): View
    {
        $query = Enquiry::with(['opportunity.company', 'assignedCaseManager', 'tasks']);

        // Filter by stage
        if ($request->filled('stage')) {
            $query->where('stage', $request->input('stage'));
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        // Filter by assignment
        if ($request->input('assigned') === 'unassigned') {
            $query->whereNull('assigned_to_user_id');
        } elseif ($request->input('assigned') === 'me') {
            $query->where('assigned_to_user_id', $request->user()->id);
        } elseif ($request->filled('assigned_to')) {
            $query->where('assigned_to_user_id', $request->input('assigned_to'));
        }

        // Search by reference or customer name / contact
        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('reference_code', 'like', "%{$term}%")
                    ->orWhere('full_name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            });
        }

        $enquiries = $query->latest()->paginate(15)->withQueryString();

        $metrics = [
            'total_cases' => Enquiry::count(),
            'unassigned' => Enquiry::whereNull('assigned_to_user_id')->count(),
            'my_cases' => Enquiry::where('assigned_to_user_id', $request->user()->id)->count(),
            'urgent' => Enquiry::where('priority', 'urgent')->whereNotIn('stage', ['closed_won', 'closed_lost'])->count(),
        ];

        $caseManagers = User::whereIn('role', [User::ROLE_CASE_MANAGER, User::ROLE_ADMIN])
            ->where('is_active', true)
            ->get();

        return view('admin.cases.index', [
            'enquiries' => $enquiries,
            'metrics' => $metrics,
            'stages' => CaseManagementService::STAGES,
            'caseManagers' => $caseManagers,
        ]);
    }

    /**
     * Show comprehensive case workspace and dossiers.
     */
    public function show(Enquiry $enquiry): View
    {
        $enquiry->load([
            'assignedCaseManager',
            'opportunity.company',
            'opportunity.category',
            'user',
            'tasks.assignee',
            'tasks.completedBy',
            'statusHistories.user',
            'communications.author',
        ]);

        $caseManagers = User::whereIn('role', [User::ROLE_CASE_MANAGER, User::ROLE_ADMIN])
            ->where('is_active', true)
            ->get();

        return view('admin.cases.show', [
            'enquiry' => $enquiry,
            'stages' => CaseManagementService::STAGES,
            'caseManagers' => $caseManagers,
        ]);
    }

    /**
     * Assign or reassign case manager.
     */
    public function assign(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_to_user_id' => ['required', 'exists:users,id'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $caseManager = User::findOrFail($validated['assigned_to_user_id']);

        $this->caseService->assignCase($enquiry, $caseManager, $request->user(), $validated['comment'] ?? null);

        return back()->with('status', "Case assigned successfully to {$caseManager->name}.");
    }

    /**
     * Transition stage with audit comment.
     */
    public function updateStage(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'stage' => ['required', 'string', 'in:'.implode(',', array_keys(CaseManagementService::STAGES))],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->caseService->transitionStage($enquiry, $validated['stage'], $validated['comment'] ?? null, $request->user());

        return back()->with('status', 'Case stage updated to: '.CaseManagementService::STAGES[$validated['stage']]);
    }

    /**
     * Log communication or note with boundary enforcement.
     */
    public function storeCommunication(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:'.implode(',', [
                CaseCommunication::TYPE_INTERNAL_NOTE,
                CaseCommunication::TYPE_CALL_LOG,
                CaseCommunication::TYPE_CUSTOMER_UPDATE,
                CaseCommunication::TYPE_BRAND_COMMUNICATION,
            ])],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'is_customer_visible' => ['nullable', 'boolean'],
        ]);

        $this->caseService->logCommunication($enquiry, $validated, $request->user());

        $visibilityNotice = ($validated['type'] === CaseCommunication::TYPE_CUSTOMER_UPDATE || ! empty($validated['is_customer_visible']))
            ? 'Customer update published to applicant portal.'
            : 'Internal confidential note logged.';

        return back()->with('status', $visibilityNotice);
    }

    /**
     * Create action task on checklist.
     */
    public function storeTask(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $this->caseService->createTask($enquiry, $validated, $request->user());

        return back()->with('status', 'Operational action item added to checklist.');
    }

    /**
     * Toggle checklist item state.
     */
    public function toggleTask(Request $request, CaseTask $task): RedirectResponse
    {
        $this->caseService->toggleTask($task, $request->user());

        return back()->with('status', 'Task checklist status updated.');
    }

    /**
     * Update priority and follow-up date.
     */
    public function updateFollowUp(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'priority' => ['required', 'in:normal,high,urgent'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $this->caseService->updateFollowUp($enquiry, $validated['next_follow_up_at'], $validated['priority'], $request->user());

        return back()->with('status', 'Priority and follow-up schedule updated.');
    }
}
