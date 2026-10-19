<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VerificationCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'opportunity_id',
        'company_id',
        'assigned_reviewer_id',
        'status',
        'decision',
        'decision_reason',
        'internal_notes',
        'review_due_at',
        'approved_at',
    ];

    protected $casts = [
        'review_due_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Associated opportunity.
     */
    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    /**
     * Associated company.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Assigned verification officer / reviewer.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_reviewer_id');
    }

    /**
     * Individual verification audit checks.
     */
    public function checks(): HasMany
    {
        return $this->hasMany(VerificationCheck::class);
    }

    /**
     * Append-only audit history log.
     */
    public function auditEvents(): HasMany
    {
        return $this->hasMany(VerificationAuditEvent::class)->orderBy('created_at', 'desc');
    }

    /**
     * Check if all mandatory verification checks are confirmed passed.
     */
    public function areMandatoryChecksPassed(): bool
    {
        $mandatoryChecks = $this->checks()->where('is_mandatory', true)->get();

        if ($mandatoryChecks->isEmpty()) {
            return false;
        }

        return $mandatoryChecks->every(fn (VerificationCheck $check) => $check->is_passed === true);
    }

    /**
     * Check if case is actively approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved' && $this->approved_at !== null;
    }
}
