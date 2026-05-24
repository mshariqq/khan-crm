<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'tblmodules';
    public $timestamps = true;
    protected $fillable = ['id', 'module_name', 'installed_version', 'active'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'active' => 'boolean'
    ];

    protected $attributes = [
        'active' => true
    ];
}
