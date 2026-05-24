<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPriority extends Model
{
    protected $table = 'tbltickets_priorities';
    protected $primaryKey = 'priorityid';
    public $timestamps = true;
    protected $fillable = ['priorityid', 'name'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
