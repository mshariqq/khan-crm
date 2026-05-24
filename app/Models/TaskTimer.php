<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskTimer extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $table = 'tbltaskstimers';
    public $timestamps = true;
    protected $fillable = ['task_id', 'start_time', 'end_time', 'staff_id', 'hourly_rate', 'note', 'is_billable', 'status', 'approved_by', 'approved_at', 'rounding_minutes'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'approved_at' => 'datetime',
        'is_billable' => 'boolean',
        'rounding_minutes' => 'integer',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'approved_by', 'staffid');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(TimesheetAuditLog::class, 'timer_id', 'id');
    }

    public function getDurationMinutesAttribute(): int
    {
        $start = $this->start_time;
        $end = $this->end_time ?? now();
        return (int) $start->diffInMinutes($end);
    }

    public function getDurationHoursAttribute(): float
    {
        return round($this->duration_minutes / 60, 2);
    }

    public function getRoundedDurationMinutesAttribute(): int
    {
        if ($this->rounding_minutes > 0) {
            $minutes = $this->duration_minutes;
            $roundTo = $this->rounding_minutes;
            return (int) (ceil($minutes / $roundTo) * $roundTo);
        }
        return $this->duration_minutes;
    }

    public function getOvertimeMinutesAttribute(): int
    {
        $dailyTotal = self::where('staff_id', $this->staff_id)
            ->whereDate('start_time', $this->start_time->toDateString())
            ->whereNotNull('end_time')
            ->sum(\DB::raw('TIMESTAMPDIFF(MINUTE, start_time, end_time)'));

        return max(0, $dailyTotal - 480);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeBillable($query)
    {
        return $query->where('is_billable', true);
    }

    public function scopeNonBillable($query)
    {
        return $query->where('is_billable', false);
    }
}
