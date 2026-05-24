<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    protected $table = 'tblproject_categories';

    protected $fillable = [
        'name', 'color', 'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'category_id', 'id');
    }
}
