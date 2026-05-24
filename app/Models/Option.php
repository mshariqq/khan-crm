<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = 'tbloptions';
    public $timestamps = true;
    protected $fillable = ['id', 'name', 'value', 'autoload'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
