<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $table = 'tbltodos';
    protected $primaryKey = 'todoid';
    public $timestamps = true;
    protected $fillable = ['todoid', 'description', 'staffid', 'dateadded', 'finished', 'datefinished', 'item_order'];
}
