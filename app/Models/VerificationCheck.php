<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'verification_case_id',
        'check_type',
        'title',
        'is_passed',
        'is_mandatory',
        'evidence_source',
        'evidence_file_path',
        'checked_at',
        'checked_by_id',
        'reviewer_notes',
    ];

    protected $casts = [
        'is_passed' => 'boolean',
        'is_mandatory' => 'boolean',
        'checked_at' => 'datetime',
    ];

    /**
     * Associated verification case.
     */
    public function verificationCase(): BelongsTo
    {
        return $this->belongsTo(VerificationCase::class);
    }

    /**
     * Reviewer who performed this check.
     */
    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by_id');
    }
}
