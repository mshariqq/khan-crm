<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinnedProject extends Model
{
    protected $table = 'tblpinned_projects';
    public $timestamps = true;
    protected $fillable = ['id', 'project_id', 'staff_id'];
}
