<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_code',
        'user_id',
        'assigned_to_user_id',
        'assigned_at',
        'opportunity_id',
        'full_name',
        'phone',
        'email',
        'target_state',
        'target_city',
        'budget_range',
        'preferred_timeline',
        'commercial_space_status',
        'carpet_area_sqft',
        'business_experience',
        'notes',
        'consent_given',
        'consent_recorded_at',
        'ip_address',
        'status',
        'priority',
        'next_follow_up_at',
        'stage',
    ];

    protected $casts = [
        'consent_given' => 'boolean',
        'consent_recorded_at' => 'datetime',
        'carpet_area_sqft' => 'integer',
        'assigned_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
    ];

    /**
     * Associated customer account (if authenticated or registered).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Assigned case manager (staff member).
     */
    public function assignedCaseManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Associated target opportunity (if initiated from an opportunity profile).
     */
    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    /**
     * Case status transition audit trail.
     */
    public function statusHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CaseStatusHistory::class)->latest();
    }

    /**
     * Operational checklist action tasks.
     */
    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CaseTask::class)->orderBy('is_completed')->orderBy('due_date');
    }

    /**
     * Case notes, call logs, and customer updates.
     */
    public function communications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CaseCommunication::class)->latest();
    }

    /**
     * Updates explicitly marked as visible to the customer.
     */
    public function customerVisibleUpdates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CaseCommunication::class)->where('is_customer_visible', true)->latest();
    }

    /**
     * Check if case is assigned to a manager.
     */
    public function isAssigned(): bool
    {
        return ! is_null($this->assigned_to_user_id);
    }

    /**
     * Confidential customer and brand documents associated with this case.
     */
    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Document::class)->latest();
    }

    /**
     * Auditable customer document-sharing consent records.
     */
    public function consents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DocumentConsent::class)->latest();
    }

    /**
     * Active granted consent records for brand submission.
     */
    public function activeConsents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DocumentConsent::class)->where('status', DocumentConsent::STATUS_GRANTED)->latest();
    }

    /**
     * Generate unique Case Reference Code: FD-CASE-YYYYMM-XXXX
     */
    public static function generateReferenceCode(): string
    {
        do {
            $prefix = 'FD-CASE-'.date('Ym').'-';
            $random = strtoupper(Str::random(4));
            $code = $prefix.$random;
        } while (self::where('reference_code', $code)->exists());

        return $code;
    }
}
