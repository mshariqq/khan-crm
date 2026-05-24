<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimesheetAuditLog extends Model
{
    protected $table = 'tbltimesheet_audit_log';

    protected $fillable = [
        'timer_id', 'staff_id', 'action', 'changes'
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function timer(): BelongsTo
    {
        return $this->belongsTo(TaskTimer::class, 'timer_id', 'id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }
}
