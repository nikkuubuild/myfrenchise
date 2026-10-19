<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PaymentTransaction extends Model
{
    use HasFactory;

    public const STATUS_INITIATED = 'initiated';
    public const STATUS_CAPTURED = 'captured';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    public const GATEWAY_RAZORPAY = 'razorpay';
    public const GATEWAY_CASHFREE = 'cashfree';
    public const GATEWAY_MOCK = 'mock_gateway';

    protected $fillable = [
        'transaction_reference',
        'order_id',
        'gateway',
        'gateway_order_id',
        'gateway_payment_id',
        'gateway_signature',
        'amount_paise',
        'currency',
        'payment_method',
        'status',
        'failure_reason',
        'metadata',
    ];

    protected $casts = [
        'amount_paise' => 'integer',
        'metadata' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isCaptured(): bool
    {
        return $this->status === self::STATUS_CAPTURED;
    }

    public function getFormattedAmountAttribute(): string
    {
        return '₹'.number_format($this->amount_paise / 100, 2);
    }

    /**
     * Generate unique Transaction Reference: FD-TXN-YYYYMM-XXXX
     */
    public static function generateReference(): string
    {
        do {
            $prefix = 'FD-TXN-'.date('Ym').'-';
            $random = strtoupper(Str::random(4));
            $reference = $prefix.$random;
        } while (self::where('transaction_reference', $reference)->exists());

        return $reference;
    }
}
