<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'event_id',
        'event_type',
        'payload',
        'signature_verified',
        'is_processed',
        'processed_at',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'signature_verified' => 'boolean',
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
    ];

    public static function isDuplicate(string $gateway, string $eventId): bool
    {
        return self::where('gateway', $gateway)
            ->where('event_id', $eventId)
            ->where('is_processed', true)
            ->exists();
    }
}
