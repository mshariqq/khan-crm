<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemsGroup extends Model
{
    protected $table = 'tblitems_groups';
    public $timestamps = true;
    protected $fillable = ['id', 'name'];
}
