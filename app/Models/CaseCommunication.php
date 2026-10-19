<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseCommunication extends Model
{
    use HasFactory;

    public const TYPE_INTERNAL_NOTE = 'internal_note';
    public const TYPE_CALL_LOG = 'call_log';
    public const TYPE_CUSTOMER_UPDATE = 'customer_update';
    public const TYPE_BRAND_COMMUNICATION = 'brand_communication';

    protected $fillable = [
        'enquiry_id',
        'user_id',
        'type',
        'is_customer_visible',
        'subject',
        'message',
    ];

    protected $casts = [
        'is_customer_visible' => 'boolean',
    ];

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope to records visible to the customer on their portal.
     */
    public function scopeCustomerVisible(Builder $query): Builder
    {
        return $query->where('is_customer_visible', true);
    }

    /**
     * Scope to internal staff-only confidential records.
     */
    public function scopeInternalOnly(Builder $query): Builder
    {
        return $query->where('is_customer_visible', false);
    }
}
