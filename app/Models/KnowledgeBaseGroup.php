<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseGroup extends Model
{
    protected $table = 'tblknowledge_base_groups';
    protected $primaryKey = 'groupid';
    public $timestamps = true;
    protected $fillable = ['groupid', 'name', 'group_slug', 'description', 'active', 'color', 'group_order'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'active' => 'boolean'
    ];

    protected $attributes = [
        'active' => true
    ];
    public function articles()
    {
        return $this->hasMany(KnowledgeBase::class, 'articlegroup', 'groupid');
    }
}
