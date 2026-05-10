<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'tblactivity_log';
    public $timestamps = true;
    protected $fillable = ['id', 'description', 'date', 'staffid'];
}
