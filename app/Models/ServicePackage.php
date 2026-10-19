<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'target_audience',
        'summary',
        'description',
        'inclusions',
        'exclusions',
        'cancellation_policy',
        'base_price_paise',
        'tax_rate_percentage',
        'sac_code',
        'is_active',
    ];

    protected $casts = [
        'inclusions' => 'array',
        'exclusions' => 'array',
        'base_price_paise' => 'integer',
        'tax_rate_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getFormattedBasePriceAttribute(): string
    {
        return '₹'.number_format($this->base_price_paise / 100, 2);
    }

    public function calculateTaxAmount(int $basePaise): int
    {
        return (int) round(($basePaise * (float) $this->tax_rate_percentage) / 100);
    }

    public function calculateTotalAmount(int $basePaise): int
    {
        return $basePaise + $this->calculateTaxAmount($basePaise);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '₹'.number_format($this->calculateTotalAmount($this->base_price_paise) / 100, 2);
    }
}
