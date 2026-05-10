<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'tbltaxes';
    public $timestamps = true;
    protected $fillable = ['id', 'name', 'taxrate'];
}
