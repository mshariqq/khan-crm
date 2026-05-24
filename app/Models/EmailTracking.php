<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTracking extends Model
{
    protected $table = 'tblemail_tracking';

    protected $fillable = [
        'message_id', 'recipient_email', 'subject', 'slug',
        'trackable_type', 'trackable_id',
        'sent_at', 'opened_at', 'open_count', 'click_count',
        'user_agent', 'ip_address', 'status', 'bounce_reason',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'open_count' => 'integer',
        'click_count' => 'integer',
    ];

    public function trackable()
    {
        return $this->morphTo();
    }

    public function clicks()
    {
        return $this->hasMany(EmailClickTracking::class, 'email_tracking_id');
    }

    public function markOpened(): void
    {
        if (!$this->opened_at) {
            $this->update([
                'opened_at' => now(),
                'open_count' => $this->open_count + 1,
            ]);
        } else {
            $this->increment('open_count');
        }
    }
}
