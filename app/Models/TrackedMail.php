<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackedMail extends Model
{
    protected $table = 'tbltracked_mails';
    public $timestamps = true;
    protected $fillable = ['id', 'uid', 'rel_id', 'rel_type', 'date', 'email', 'opened', 'date_opened', 'subject'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime',
        'date_opened' => 'datetime'
    ];
}
