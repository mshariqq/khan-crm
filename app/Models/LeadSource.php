<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    protected $table = 'tblleads_sources';
    public $timestamps = true;
    protected $fillable = ['id', 'name'];
}
