<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Refund extends Model
{
    use HasFactory;

    public const STATUS_REQUESTED = 'requested';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'refund_reference',
        'order_id',
        'payment_transaction_id',
        'amount_paise',
        'reason',
        'status',
        'gateway_refund_id',
        'approved_by_user_id',
        'processed_at',
    ];

    protected $casts = [
        'amount_paise' => 'integer',
        'processed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function getFormattedAmountAttribute(): string
    {
        return '₹'.number_format($this->amount_paise / 100, 2);
    }

    /**
     * Generate unique Refund Reference: FD-REF-YYYYMM-XXXX
     */
    public static function generateReference(): string
    {
        do {
            $prefix = 'FD-REF-'.date('Ym').'-';
            $random = strtoupper(Str::random(4));
            $reference = $prefix.$random;
        } while (self::where('refund_reference', $reference)->exists());

        return $reference;
    }
}
