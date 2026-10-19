<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'category_id',
        'title',
        'slug',
        'summary',
        'description',
        'business_model',
        'min_investment',
        'max_investment',
        'franchise_fee',
        'royalty_percentage',
        'min_space_sqft',
        'max_space_sqft',
        'min_term_years',
        'publication_status',
        'is_featured',
        'published_at',
        'last_verified_at',
        'verification_valid_until',
        'disclaimer',
    ];

    protected $casts = [
        'min_investment' => 'integer',
        'max_investment' => 'integer',
        'franchise_fee' => 'integer',
        'royalty_percentage' => 'decimal:2',
        'min_space_sqft' => 'integer',
        'max_space_sqft' => 'integer',
        'min_term_years' => 'integer',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'last_verified_at' => 'datetime',
        'verification_valid_until' => 'datetime',
    ];

    /**
     * Get the owning company.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the sector category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get itemized financial line items.
     */
    public function financialBreakdowns(): HasMany
    {
        return $this->hasMany(OpportunityFinancialBreakdown::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Get geographic availability territories.
     */
    public function locations(): HasMany
    {
        return $this->hasMany(OpportunityLocation::class);
    }

    /**
     * Get operational and infrastructure requirements.
     */
    public function requirements(): HasMany
    {
        return $this->hasMany(OpportunityRequirement::class);
    }

    /**
     * Scope for published and verified opportunities.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('publication_status', 'published')
            ->whereNotNull('last_verified_at')
            ->where(function (Builder $subQuery) {
                $subQuery->whereNull('verification_valid_until')
                    ->orWhere('verification_valid_until', '>=', now());
            });
    }

    /**
     * Scope for budget range filter.
     */
    public function scopeInBudget(Builder $query, ?int $minBudget = null, ?int $maxBudget = null): Builder
    {
        if ($minBudget !== null) {
            $query->where('max_investment', '>=', $minBudget);
        }

        if ($maxBudget !== null) {
            $query->where('min_investment', '<=', $maxBudget);
        }

        return $query;
    }

    /**
     * Determine whether the opportunity currently has a verified audit badge.
     */
    public function isVerified(): bool
    {
        return $this->publication_status === 'published'
            && $this->last_verified_at !== null
            && ($this->verification_valid_until === null || $this->verification_valid_until->isFuture());
    }

    /**
     * Format Indian Rupee currency amount for presentation.
     */
    public static function formatInr(int $amount): string
    {
        if ($amount >= 10000000) {
            return '₹'.round($amount / 10000000, 2).' Crore';
        }

        if ($amount >= 100000) {
            return '₹'.round($amount / 100000, 2).' Lakh';
        }

        return '₹'.number_format($amount);
    }

    /**
     * Helper to render formatted investment outlay range.
     */
    public function formattedInvestmentRange(): string
    {
        return self::formatInr($this->min_investment).' - '.self::formatInr($this->max_investment);
    }
}
