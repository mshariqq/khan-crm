<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectEmailCampaign extends Model
{
    protected $table = 'tblproject_email_campaigns';

    protected $fillable = [
        'project_id', 'subject', 'content', 'status', 'sent_at',
        'recipient_count', 'opened_count', 'clicked_count', 'created_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'recipient_count' => 'integer',
        'opened_count' => 'integer',
        'clicked_count' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'created_by', 'staffid');
    }
}
