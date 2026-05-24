<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'tblpermissions';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['id', 'name', 'slug'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
