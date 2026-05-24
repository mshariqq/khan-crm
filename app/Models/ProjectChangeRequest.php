<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectChangeRequest extends Model
{
    protected $table = 'tblproject_change_requests';

    protected $fillable = [
        'project_id', 'title', 'description', 'status', 'priority',
        'estimated_cost', 'estimated_days', 'impact_analysis',
        'submitted_by', 'submitted_at', 'approved_by', 'approved_at',
        'rejection_reason'
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function submitter()
    {
        return $this->belongsTo(Staff::class, 'submitted_by', 'staffid');
    }

    public function approver()
    {
        return $this->belongsTo(Staff::class, 'approved_by', 'staffid');
    }

    public static function statusLabels(): array
    {
        return [
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'implemented' => 'Implemented',
        ];
    }
}
