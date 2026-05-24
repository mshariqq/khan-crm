<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $table = 'tblwebhooks';

    protected $fillable = [
        'name', 'url', 'events', 'is_active', 'secret', 'last_triggered_at',
        'failure_count', 'created_by',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'created_by', 'staffid');
    }
}
