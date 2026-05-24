<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFeedback extends Model
{
    protected $table = 'tblproject_feedback';

    protected $fillable = [
        'project_id', 'contact_id', 'staff_id', 'rating',
        'nps_score', 'comment', 'survey_responses', 'source'
    ];

    protected $casts = [
        'rating' => 'integer',
        'nps_score' => 'integer',
        'survey_responses' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }
}
