<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'tblservices';
    protected $primaryKey = 'serviceid';
    public $timestamps = true;
    protected $fillable = ['serviceid', 'name'];
}
