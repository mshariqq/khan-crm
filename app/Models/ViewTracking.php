<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewTracking extends Model
{
    protected $table = 'tblviews_tracking';
    public $timestamps = true;
    protected $fillable = ['id', 'rel_id', 'rel_type', 'date', 'view_ip'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime'
    ];
}
