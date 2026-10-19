<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpportunityLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'opportunity_id',
        'state_code',
        'state_name',
        'district',
        'territory_tier',
        'is_exclusive',
        'status',
    ];

    protected $casts = [
        'is_exclusive' => 'boolean',
    ];

    /**
     * Get the associated opportunity.
     */
    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    /**
     * Scope for available locations.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }
}
