<?php

namespace App\Services;

use App\Models\Opportunity;
use App\Models\User;
use App\Models\VerificationAuditEvent;
use App\Models\VerificationCase;
use App\Models\VerificationCheck;
use DomainException;
use Illuminate\Support\Facades\DB;

class VerificationService
{
    /**
     * Standard mandatory verification check definitions per SKILL.md.
     */
    public const STANDARD_CHECKS = [
        'company_identity' => 'Ministry of Corporate Affairs (MCA) / RoC legal entity registration and CIN validation.',
        'contact_authority' => 'Official domain email and representative signing authority confirmation.',
        'opportunity_authorization' => 'Trademark registration, brand ownership certificate, or franchisor agreement.',
        'opportunity_status' => 'Confirmation of active opportunity intake and non-dormant operations.',
        'territory_availability' => 'Documented state and district territory availability verification.',
        'financial_disclosures' => 'Itemized fee schedule, CapEx setup costs, and royalty percentage provenance.',
        'infrastructure_licence' => 'Commercial premises carpet area, power load, and sector regulatory compliance.',
        'contract_terms' => 'Franchise agreement draft review, renewal tenure, and material caveats.',
    ];

    /**
     * Initialize a new verification case for an opportunity with all standard checks.
     */
    public function createCase(Opportunity $opportunity, ?User $reviewer = null, ?string $notes = null): VerificationCase
    {
        return DB::transaction(function () use ($opportunity, $reviewer, $notes) {
            $case = VerificationCase::create([
                'opportunity_id' => $opportunity->id,
                'company_id' => $opportunity->company_id,
                'assigned_reviewer_id' => $reviewer?->id,
                'status' => 'under_review',
                'internal_notes' => $notes,
            ]);

            foreach (self::STANDARD_CHECKS as $type => $title) {
                VerificationCheck::create([
                    'verification_case_id' => $case->id,
                    'check_type' => $type,
                    'title' => $title,
                    'is_passed' => false,
                    'is_mandatory' => true,
                    'evidence_source' => 'Pending audit',
                ]);
            }

            VerificationAuditEvent::create([
                'verification_case_id' => $case->id,
                'user_id' => $reviewer?->id,
                'event' => 'case_created',
                'from_status' => null,
                'to_status' => 'under_review',
                'reason' => 'Initiated verification case for opportunity audit.',
            ]);

            return $case;
        });
    }

    /**
     * Record a passed verification check with evidence provenance.
     */
    public function passCheck(
        VerificationCheck $check,
        User $reviewer,
        string $evidenceSource,
        ?string $evidencePath = null,
        ?string $notes = null
    ): VerificationCheck {
        return DB::transaction(function () use ($check, $reviewer, $evidenceSource, $evidencePath, $notes) {
            $check->update([
                'is_passed' => true,
                'evidence_source' => $evidenceSource,
                'evidence_file_path' => $evidencePath,
                'checked_at' => now(),
                'checked_by_id' => $reviewer->id,
                'reviewer_notes' => $notes,
            ]);

            VerificationAuditEvent::create([
                'verification_case_id' => $check->verification_case_id,
                'user_id' => $reviewer->id,
                'event' => 'check_passed',
                'from_status' => $check->verificationCase->status,
                'to_status' => $check->verificationCase->status,
                'reason' => "Verified check [{$check->check_type}]: {$evidenceSource}",
            ]);

            return $check;
        });
    }

    /**
     * Approve verification case and publish opportunity.
     * Enforces that ALL mandatory checks must be passed.
     */
    public function approveAndPublish(
        VerificationCase $case,
        User $reviewer,
        int $validityDays = 180,
        ?string $notes = null
    ): bool {
        return DB::transaction(function () use ($case, $reviewer, $validityDays, $notes) {
            // Strict rule: check that all mandatory checks are passed
            if (! $case->areMandatoryChecksPassed()) {
                throw new DomainException('Cannot approve verification: all mandatory checks must be confirmed with evidence before publication.');
            }

            $validUntil = now()->addDays($validityDays);
            $fromStatus = $case->status;

            $case->update([
                'status' => 'approved',
                'decision' => 'approve',
                'decision_reason' => $notes ?? 'All mandatory verification criteria satisfied with verified evidence.',
                'approved_at' => now(),
                'review_due_at' => $validUntil,
            ]);

            // Update Opportunity to Published
            $opportunity = $case->opportunity;
            $opportunity->update([
                'publication_status' => 'published',
                'published_at' => $opportunity->published_at ?? now(),
                'last_verified_at' => now(),
                'verification_valid_until' => $validUntil,
            ]);

            // Mark company as verified
            $opportunity->company->update(['status' => 'verified']);

            // Record audit events
            VerificationAuditEvent::create([
                'verification_case_id' => $case->id,
                'user_id' => $reviewer->id,
                'event' => 'case_approved',
                'from_status' => $fromStatus,
                'to_status' => 'approved',
                'reason' => $notes ?? 'Verification approved by authorized reviewer.',
                'metadata' => [
                    'validity_days' => $validityDays,
                    'valid_until' => $validUntil->toIso8601String(),
                ],
            ]);

            VerificationAuditEvent::create([
                'verification_case_id' => $case->id,
                'user_id' => $reviewer->id,
                'event' => 'opportunity_published',
                'from_status' => $fromStatus,
                'to_status' => 'published',
                'reason' => "Opportunity [{$opportunity->title}] published to directory.",
            ]);

            return true;
        });
    }

    /**
     * Immediately suspend an opportunity and revoke public visibility.
     */
    public function suspendOpportunity(Opportunity $opportunity, User $actor, string $reason): void
    {
        DB::transaction(function () use ($opportunity, $actor, $reason) {
            $fromStatus = $opportunity->publication_status;

            $opportunity->update([
                'publication_status' => 'suspended',
            ]);

            $latestCase = $opportunity->latestVerificationCase;
            if ($latestCase) {
                $latestCase->update([
                    'status' => 'suspended',
                    'decision' => 'suspend',
                    'decision_reason' => $reason,
                ]);

                VerificationAuditEvent::create([
                    'verification_case_id' => $latestCase->id,
                    'user_id' => $actor->id,
                    'event' => 'opportunity_suspended',
                    'from_status' => $fromStatus,
                    'to_status' => 'suspended',
                    'reason' => $reason,
                ]);
            }
        });
    }

    /**
     * Reject a verification case.
     */
    public function rejectCase(VerificationCase $case, User $reviewer, string $reason): void
    {
        DB::transaction(function () use ($case, $reviewer, $reason) {
            $fromStatus = $case->status;

            $case->update([
                'status' => 'rejected',
                'decision' => 'reject',
                'decision_reason' => $reason,
            ]);

            $case->opportunity->update([
                'publication_status' => 'draft',
            ]);

            VerificationAuditEvent::create([
                'verification_case_id' => $case->id,
                'user_id' => $reviewer->id,
                'event' => 'case_rejected',
                'from_status' => $fromStatus,
                'to_status' => 'rejected',
                'reason' => $reason,
            ]);
        });
    }
}
