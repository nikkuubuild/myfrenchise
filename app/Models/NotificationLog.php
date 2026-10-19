<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    public const CHANNEL_MAIL = 'mail';
    public const CHANNEL_SMS_DLT = 'sms_dlt';
    public const CHANNEL_IN_APP = 'in_app';

    public const STATUS_QUEUED = 'queued';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'channel',
        'recipient',
        'event_type',
        'subject',
        'message_body',
        'dlt_template_id',
        'status',
        'provider_message_id',
        'error_details',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
