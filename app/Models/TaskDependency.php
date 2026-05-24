<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskDependency extends Model
{
    protected $table = 'tbltask_dependencies';

    protected $fillable = [
        'task_id', 'depends_on_task_id', 'dependency_type', 'lag_days'
    ];

    protected $casts = [
        'lag_days' => 'integer',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }

    public function dependsOn()
    {
        return $this->belongsTo(Task::class, 'depends_on_task_id', 'id');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('dependency_type', $type);
    }

    public static function typeLabels(): array
    {
        return [
            'FS' => 'Finish-to-Start',
            'FF' => 'Finish-to-Finish',
            'SS' => 'Start-to-Start',
            'SF' => 'Start-to-Finish',
        ];
    }
}
