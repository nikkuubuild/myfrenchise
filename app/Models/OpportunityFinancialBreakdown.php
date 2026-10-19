<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpportunityFinancialBreakdown extends Model
{
    use HasFactory;

    protected $fillable = [
        'opportunity_id',
        'item_name',
        'amount',
        'is_estimate',
        'is_refundable',
        'source_evidence',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'integer',
        'is_estimate' => 'boolean',
        'is_refundable' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the associated opportunity.
     */
    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }
}
