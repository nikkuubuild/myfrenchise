<?php

namespace App\Services;

use App\Models\CaseCommunication;
use App\Models\CaseStatusHistory;
use App\Models\CaseTask;
use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CaseManagementService
{
    public const STAGES = [
        'new' => 'New Unassigned Intake',
        'intake_assessment' => 'Preliminary Intake Assessment',
        'shortlist_prepared' => 'Opportunity Shortlist Prepared',
        'consent_pending' => 'Consent & Disclosure Pending',
        'submitted_to_brand' => 'Application Delivered to Brand',
        'brand_interview' => 'Franchisor Interview / Due Diligence',
        'contract_review' => 'Legal Contract / Agreement Review',
        'closed_won' => 'Closed — Opportunity Finalized',
        'closed_lost' => 'Closed — Withdrawn / Unsuccessful',
    ];

    /**
     * Assign a case manager to an enquiry and seed standard checklist items.
     */
    public function assignCase(Enquiry $enquiry, User $caseManager, User $actor, ?string $comment = null): Enquiry
    {
        return DB::transaction(function () use ($enquiry, $caseManager, $actor, $comment) {
            $prevAssignee = $enquiry->assignedCaseManager?->name ?? 'Unassigned';

            $enquiry->update([
                'assigned_to_user_id' => $caseManager->id,
                'assigned_at' => now(),
                'stage' => $enquiry->stage === 'new' ? 'intake_assessment' : $enquiry->stage,
                'status' => $enquiry->status === 'new' ? 'shortlisted' : $enquiry->status,
            ]);

            // Record status history if stage changed
            CaseStatusHistory::create([
                'enquiry_id' => $enquiry->id,
                'user_id' => $actor->id,
                'from_status' => "Assigned ({$prevAssignee})",
                'to_status' => "Assigned to {$caseManager->name}",
                'comment' => $comment ?? "Case claimed/assigned to {$caseManager->name}.",
            ]);

            // Seed standard checklist if case has zero tasks
            if ($enquiry->tasks()->count() === 0) {
                $this->seedStandardChecklist($enquiry, $caseManager);
            }

            return $enquiry->fresh(['assignedCaseManager', 'tasks', 'statusHistories']);
        });
    }

    /**
     * Transition a case to a new stage with audit history.
     */
    public function transitionStage(Enquiry $enquiry, string $newStage, ?string $comment, User $actor): Enquiry
    {
        if (! array_key_exists($newStage, self::STAGES)) {
            throw new InvalidArgumentException("Invalid case stage [{$newStage}].");
        }

        return DB::transaction(function () use ($enquiry, $newStage, $comment, $actor) {
            $oldStage = $enquiry->stage;

            $statusMapping = match ($newStage) {
                'new' => 'new',
                'intake_assessment', 'shortlist_prepared' => 'shortlisted',
                'consent_pending' => 'consented',
                'submitted_to_brand', 'brand_interview', 'contract_review' => 'submitted_to_brand',
                'closed_won', 'closed_lost' => 'closed',
                default => $enquiry->status,
            };

            $enquiry->update([
                'stage' => $newStage,
                'status' => $statusMapping,
            ]);

            CaseStatusHistory::create([
                'enquiry_id' => $enquiry->id,
                'user_id' => $actor->id,
                'from_status' => self::STAGES[$oldStage] ?? $oldStage,
                'to_status' => self::STAGES[$newStage],
                'comment' => $comment,
            ]);

            return $enquiry->fresh();
        });
    }

    /**
     * Log a communication or note respecting strict customer visibility boundaries.
     */
    public function logCommunication(Enquiry $enquiry, array $data, User $actor): CaseCommunication
    {
        $type = $data['type'] ?? CaseCommunication::TYPE_INTERNAL_NOTE;

        // Non-negotiable boundary: internal notes are NEVER customer-visible
        $isCustomerVisible = match ($type) {
            CaseCommunication::TYPE_INTERNAL_NOTE => false,
            CaseCommunication::TYPE_CUSTOMER_UPDATE => true,
            default => (bool) ($data['is_customer_visible'] ?? false),
        };

        return CaseCommunication::create([
            'enquiry_id' => $enquiry->id,
            'user_id' => $actor->id,
            'type' => $type,
            'is_customer_visible' => $isCustomerVisible,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
        ]);
    }

    /**
     * Add a task to the case checklist.
     */
    public function createTask(Enquiry $enquiry, array $data, User $actor): CaseTask
    {
        return CaseTask::create([
            'enquiry_id' => $enquiry->id,
            'assigned_to_user_id' => $data['assigned_to_user_id'] ?? $enquiry->assigned_to_user_id ?? $actor->id,
            'title' => $data['title'],
            'due_date' => $data['due_date'] ?? null,
            'is_completed' => false,
        ]);
    }

    /**
     * Toggle task completion.
     */
    public function toggleTask(CaseTask $task, User $actor): CaseTask
    {
        if ($task->is_completed) {
            $task->markIncomplete();
        } else {
            $task->markCompleted($actor);
        }

        return $task->fresh();
    }

    /**
     * Update case priority and follow-up schedule.
     */
    public function updateFollowUp(Enquiry $enquiry, ?string $nextFollowUpAt, string $priority, User $actor): Enquiry
    {
        $enquiry->update([
            'priority' => $priority,
            'next_follow_up_at' => $nextFollowUpAt ? \Carbon\Carbon::parse($nextFollowUpAt) : null,
        ]);

        return $enquiry->fresh();
    }

    /**
     * Seed baseline operational tasks for new case management intake.
     */
    protected function seedStandardChecklist(Enquiry $enquiry, User $caseManager): void
    {
        $standardTasks = [
            'Conduct introductory intake call to confirm entrepreneur capital and location readiness',
            'Verify territory expansion status with target brand / franchisor',
            'Assess commercial premises availability or footfall suitability in target city',
            'Obtain explicit written consent form for brand disclosure package',
        ];

        foreach ($standardTasks as $index => $title) {
            CaseTask::create([
                'enquiry_id' => $enquiry->id,
                'assigned_to_user_id' => $caseManager->id,
                'title' => $title,
                'due_date' => now()->addDays(2 * ($index + 1))->toDateString(),
                'is_completed' => false,
            ]);
        }
    }
}
