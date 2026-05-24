<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailRateLimit extends Model
{
    protected $table = 'tblemail_rate_limits';

    protected $fillable = [
        'limitable_type', 'limitable_id', 'recipient_type',
        'emails_sent', 'max_per_hour', 'hour_bucket',
    ];

    protected $casts = [
        'hour_bucket' => 'datetime',
        'emails_sent' => 'integer',
        'max_per_hour' => 'integer',
    ];

    public function limitable()
    {
        return $this->morphTo();
    }

    public static function canSend($limitableType, $limitableId, string $recipientType = 'client', int $maxPerHour = 50): bool
    {
        $bucket = now()->startOfHour();

        $record = static::where('limitable_type', $limitableType)
            ->where('limitable_id', $limitableId)
            ->where('recipient_type', $recipientType)
            ->where('hour_bucket', $bucket)
            ->first();

        if (!$record) {
            return true;
        }

        return $record->emails_sent < ($record->max_per_hour ?: $maxPerHour);
    }

    public static function incrementCount($limitableType, $limitableId, string $recipientType = 'client', int $maxPerHour = 50): void
    {
        $bucket = now()->startOfHour();

        $record = static::firstOrCreate(
            [
                'limitable_type' => $limitableType,
                'limitable_id' => $limitableId,
                'recipient_type' => $recipientType,
                'hour_bucket' => $bucket,
            ],
            [
                'emails_sent' => 0,
                'max_per_hour' => $maxPerHour,
            ]
        );

        $record->increment('emails_sent');
    }
}
