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
    ];

    protected $casts = [
        'consent_given' => 'boolean',
        'consent_recorded_at' => 'datetime',
        'carpet_area_sqft' => 'integer',
    ];

    /**
     * Associated customer account (if authenticated or registered).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Associated target opportunity (if initiated from an opportunity profile).
     */
    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
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
