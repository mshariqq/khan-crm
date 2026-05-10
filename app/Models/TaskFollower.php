<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskFollower extends Model
{
    protected $table = 'tbltask_followers';
    public $timestamps = true;
    protected $fillable = ['id', 'staffid', 'taskid'];
}
