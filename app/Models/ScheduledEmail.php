<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledEmail extends Model
{
    protected $table = 'tblscheduled_emails';
    public $timestamps = true;
    protected $fillable = ['id', 'rel_id', 'rel_type', 'scheduled_at', 'contacts', 'cc', 'attach_pdf', 'template'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'scheduled_at' => 'datetime'
    ];
}
