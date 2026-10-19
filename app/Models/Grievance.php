<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Grievance extends Model
{
    use HasFactory;

    public const STATUS_RECEIVED = 'received';
    public const STATUS_UNDER_INVESTIGATION = 'under_investigation';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_REJECTED = 'rejected';

    public const CATEGORIES = [
        'unauthorized_impersonation' => 'Unauthorized Brand Impersonation / Fake Broker',
        'misleading_financials' => 'Misleading Financials or Profit Guarantees',
        'fee_fraud' => 'Demanding Unauthorized Cash Fees or Unofficial Charges',
        'consent_violation' => 'Privacy or Data-Sharing Consent Violation',
        'service_complaint' => 'Managed Service Coordination Grievance',
        'other' => 'Other Legal or Governance Dispute',
    ];

    public const REPORTER_TYPES = [
        'entrepreneur' => 'Prospective Entrepreneur / Franchisee',
        'brand_owner' => 'Brand Owner / Franchisor Representative',
        'consumer' => 'Consumer / General Public',
        'whistleblower' => 'Industry Whistleblower / Former Employee',
    ];

    protected $fillable = [
        'tracking_code',
        'reporter_type',
        'full_name',
        'email',
        'phone',
        'opportunity_id',
        'company_id',
        'category',
        'subject',
        'description',
        'evidence_summary',
        'status',
        'priority',
        'assigned_to_user_id',
        'resolution_notes',
        'resolved_at',
        'ip_address',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function assignedOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    public function isUnderInvestigation(): bool
    {
        return $this->status === self::STATUS_UNDER_INVESTIGATION;
    }

    /**
     * Generate unique tracking code: FD-GRV-YYYYMM-XXXX
     */
    public static function generateTrackingCode(): string
    {
        do {
            $prefix = 'FD-GRV-'.date('Ym').'-';
            $random = strtoupper(Str::random(4));
            $code = $prefix.$random;
        } while (self::where('tracking_code', $code)->exists());

        return $code;
    }
}
