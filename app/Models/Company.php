<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'legal_name',
        'brand_name',
        'slug',
        'cin',
        'gstin',
        'pan',
        'registered_address',
        'operational_state',
        'official_website',
        'official_email',
        'official_phone',
        'incorporation_year',
        'status',
    ];

    protected $casts = [
        'incorporation_year' => 'integer',
    ];

    /**
     * Get opportunities belonging to this company.
     */
    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    /**
     * Scope for verified companies.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'verified');
    }

    /**
     * Check if company has approved verification status.
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }
}
