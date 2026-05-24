<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffNotificationPreference extends Model
{
    protected $table = 'tblstaff_notification_preferences';

    protected $fillable = [
        'staffid', 'notification_type', 'in_app', 'email',
    ];

    protected $casts = [
        'in_app' => 'boolean',
        'email' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }
}
