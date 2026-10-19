<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    public const SELLER_STATE = 'HR';
    public const SELLER_LEGAL_NAME = 'Franchise Dekho Platform Operations';
    public const SELLER_ADDRESS = 'DLF Cyber City, Tower B, Level 8, Gurugram, Haryana 122002';
    public const SELLER_GSTIN = '06AAAAF0000A1Z5'; // Compliant Haryana GSTIN format

    protected $fillable = [
        'invoice_number',
        'order_id',
        'invoice_date',
        'financial_year',
        'subtotal_paise',
        'cgst_paise',
        'sgst_paise',
        'igst_paise',
        'total_paise',
        'customer_state',
        'seller_state',
        'is_interstate',
        'sac_code',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal_paise' => 'integer',
        'cgst_paise' => 'integer',
        'sgst_paise' => 'integer',
        'igst_paise' => 'integer',
        'total_paise' => 'integer',
        'is_interstate' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '₹'.number_format($this->total_paise / 100, 2);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return '₹'.number_format($this->subtotal_paise / 100, 2);
    }

    public function getFormattedCgstAttribute(): string
    {
        return '₹'.number_format($this->cgst_paise / 100, 2);
    }

    public function getFormattedSgstAttribute(): string
    {
        return '₹'.number_format($this->sgst_paise / 100, 2);
    }

    public function getFormattedIgstAttribute(): string
    {
        return '₹'.number_format($this->igst_paise / 100, 2);
    }

    /**
     * Generate unique Invoice Number: FD-INV-YYYYMM-XXXX
     */
    public static function generateInvoiceNumber(): string
    {
        do {
            $prefix = 'FD-INV-'.date('Ym').'-';
            $random = strtoupper(Str::random(4));
            $number = $prefix.$random;
        } while (self::where('invoice_number', $number)->exists());

        return $number;
    }
}
