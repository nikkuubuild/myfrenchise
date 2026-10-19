<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'enquiry_id',
        'assigned_to_user_id',
        'title',
        'due_date',
        'is_completed',
        'completed_at',
        'completed_by_user_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by_user_id');
    }

    /**
     * Mark task completed by given user.
     */
    public function markCompleted(User $user): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'completed_by_user_id' => $user->id,
        ]);
    }

    /**
     * Mark task as pending / incomplete.
     */
    public function markIncomplete(): void
    {
        $this->update([
            'is_completed' => false,
            'completed_at' => null,
            'completed_by_user_id' => null,
        ]);
    }
}
