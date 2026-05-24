<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailClickTracking extends Model
{
    protected $table = 'tblemail_click_tracking';

    protected $fillable = [
        'email_tracking_id', 'url', 'clicked_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    public function emailTracking()
    {
        return $this->belongsTo(EmailTracking::class, 'email_tracking_id');
    }
}
