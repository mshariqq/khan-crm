<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMember extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'tblproject_members';
    public $timestamps = true;
    protected $fillable = ['project_id', 'staff_id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'project_id' => 'integer',
        'staff_id' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }
}
