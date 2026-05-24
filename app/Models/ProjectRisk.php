<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRisk extends Model
{
    protected $table = 'tblproject_risks';

    protected $fillable = [
        'project_id', 'title', 'description', 'risk_type',
        'probability', 'impact', 'status', 'mitigation_plan',
        'contingency_plan', 'assigned_to', 'identified_at', 'resolved_at'
    ];

    protected $casts = [
        'identified_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(Staff::class, 'assigned_to', 'staffid');
    }

    public function getRiskScoreAttribute()
    {
        $probability = ['low' => 1, 'medium' => 2, 'high' => 3];
        $impact = ['low' => 1, 'medium' => 2, 'high' => 3];
        return ($probability[$this->probability] ?? 2) * ($impact[$this->impact] ?? 2);
    }

    public static function probabilityLabels(): array
    {
        return ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'];
    }

    public static function impactLabels(): array
    {
        return ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'];
    }

    public static function statusLabels(): array
    {
        return ['open' => 'Open', 'mitigated' => 'Mitigated', 'closed' => 'Closed', 'accepted' => 'Accepted'];
    }
}
