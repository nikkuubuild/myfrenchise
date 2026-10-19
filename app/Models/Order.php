<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_number',
        'user_id',
        'service_package_id',
        'enquiry_id',
        'currency',
        'subtotal_paise',
        'tax_amount_paise',
        'total_amount_paise',
        'status',
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_state',
        'billing_city',
        'billing_gstin',
        'paid_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal_paise' => 'integer',
        'tax_amount_paise' => 'integer',
        'total_amount_paise' => 'integer',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class)->latest();
    }

    public function latestTransaction(): HasOne
    {
        return $this->hasOne(PaymentTransaction::class)->latestOfMany();
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class)->latest();
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    public function getFormattedTotalAttribute(): string
    {
        return '₹'.number_format($this->total_amount_paise / 100, 2);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return '₹'.number_format($this->subtotal_paise / 100, 2);
    }

    public function getFormattedTaxAttribute(): string
    {
        return '₹'.number_format($this->tax_amount_paise / 100, 2);
    }

    /**
     * Generate unique Order Number: FD-ORD-YYYYMM-XXXX
     */
    public static function generateOrderNumber(): string
    {
        do {
            $prefix = 'FD-ORD-'.date('Ym').'-';
            $random = strtoupper(Str::random(4));
            $number = $prefix.$random;
        } while (self::where('order_number', $number)->exists());

        return $number;
    }
}
