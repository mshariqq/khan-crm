<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'tblmodules';
    public $timestamps = true;
    protected $fillable = ['id', 'module_name', 'installed_version', 'active'];
}
