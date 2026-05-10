<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSetting extends Model
{
    protected $table = 'tblproject_settings';
    public $timestamps = true;
    protected $fillable = ['id', 'project_id', 'name', 'value'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
