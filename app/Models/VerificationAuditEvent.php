<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationAuditEvent extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'verification_case_id',
        'user_id',
        'event',
        'from_status',
        'to_status',
        'reason',
        'ip_address',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Associated verification case.
     */
    public function verificationCase(): BelongsTo
    {
        return $this->belongsTo(VerificationCase::class);
    }

    /**
     * Actor / user responsible for event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
