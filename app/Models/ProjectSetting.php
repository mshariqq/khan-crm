<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectSetting extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'tblproject_settings';
    public $timestamps = true;
    protected $fillable = ['project_id', 'name', 'value'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'project_id' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
