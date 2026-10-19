<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    public const CATEGORY_IDENTITY = 'identity';
    public const CATEGORY_FINANCIAL = 'financial';
    public const CATEGORY_PROPERTY = 'property';
    public const CATEGORY_BRAND_DISCLOSURE = 'brand_disclosure';
    public const CATEGORY_OTHER = 'other';

    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'enquiry_id',
        'uploaded_by_user_id',
        'category',
        'title',
        'original_filename',
        'storage_path',
        'disk',
        'mime_type',
        'file_size_bytes',
        'verification_status',
        'verified_by_user_id',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
        'verified_at' => 'datetime',
    ];

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(DocumentAccessLog::class)->latest();
    }

    /**
     * Check if file exists in private storage.
     */
    public function existsInStorage(): bool
    {
        return Storage::disk($this->disk)->exists($this->storage_path);
    }

    /**
     * Get absolute path in local storage.
     */
    public function absoluteStoragePath(): string
    {
        return Storage::disk($this->disk)->path($this->storage_path);
    }

    /**
     * Formatted human-readable file size (KB / MB).
     */
    public function formattedSize(): string
    {
        $bytes = $this->file_size_bytes;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        return number_format($bytes / 1024, 1) . ' KB';
    }

    /**
     * Check if document is verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === self::STATUS_VERIFIED;
    }

    /**
     * Check if document is PDF.
     */
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Check if document is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }
}
