<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class DocumentConsent extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_GRANTED = 'granted';
    public const STATUS_REVOKED = 'revoked';

    protected $fillable = [
        'enquiry_id',
        'user_id',
        'opportunity_id',
        'requested_by_user_id',
        'scope_summary',
        'included_document_ids',
        'status',
        'granted_at',
        'revoked_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'included_document_ids' => 'array',
        'granted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    /**
     * Retrieve the Document models explicitly included in this consent.
     */
    public function includedDocuments(): Collection
    {
        $ids = $this->included_document_ids ?? [];
        if (empty($ids)) {
            return new Collection();
        }

        return Document::whereIn('id', $ids)->get();
    }

    /**
     * Check if consent is granted and active.
     */
    public function isGranted(): bool
    {
        return $this->status === self::STATUS_GRANTED && ! is_null($this->granted_at);
    }

    /**
     * Check if consent is pending customer action.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if consent has been revoked.
     */
    public function isRevoked(): bool
    {
        return $this->status === self::STATUS_REVOKED;
    }

    /**
     * Grant explicit consent.
     */
    public function grant(User $customer, Request $request): void
    {
        $this->update([
            'status' => self::STATUS_GRANTED,
            'granted_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Revoke previously granted consent.
     */
    public function revoke(User $customer, Request $request): void
    {
        $this->update([
            'status' => self::STATUS_REVOKED,
            'revoked_at' => now(),
        ]);
    }
}
