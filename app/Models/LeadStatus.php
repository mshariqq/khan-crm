<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{
    protected $table = 'tblleads_status';
    public $timestamps = true;
    protected $fillable = ['id', 'name', 'statusorder', 'color', 'isdefault'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
