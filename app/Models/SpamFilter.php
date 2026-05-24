<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpamFilter extends Model
{
    protected $table = 'tblspam_filters';
    public $timestamps = true;
    protected $fillable = ['id', 'type', 'rel_type', 'value'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
