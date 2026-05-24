<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectLessonsLearned extends Model
{
    protected $table = 'tblproject_lessons_learned';

    protected $fillable = [
        'project_id', 'title', 'description', 'category',
        'impact', 'recommendation', 'created_by'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'created_by', 'staffid');
    }

    public static function impactLabels(): array
    {
        return ['positive' => 'Positive', 'negative' => 'Negative', 'neutral' => 'Neutral'];
    }
}
