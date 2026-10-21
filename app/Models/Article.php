<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'author_name',
        'author_title',
        'reading_time_minutes',
        'excerpt',
        'content',
        'sources_and_evidence',
        'disclaimer',
        'is_published',
        'published_at',
        'last_verified_at',
        'meta_title',
        'meta_description',
        'canonical_url',
    ];

    protected $casts = [
        'reading_time_minutes' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'last_verified_at' => 'datetime',
    ];

    /**
     * Get the sector category related to this article if applicable.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope query to published and verified articles.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(function (Builder $subQuery) {
                $subQuery->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Scope query ordered by publication or verification recency.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('published_at', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Check if article is fresh (verified within last 180 days).
     */
    public function isFresh(): bool
    {
        return $this->last_verified_at !== null && $this->last_verified_at->gte(now()->subDays(180));
    }
}
