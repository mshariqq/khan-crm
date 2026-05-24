<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimateRequest extends Model
{
    protected $table = 'tblestimate_requests';
    public $timestamps = true;
    protected $fillable = [
        'id', 'email', 'submission', 'last_status_change', 'date_estimated', 
        'from_form_id', 'assigned', 'status', 'default_language', 'date_added'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_status_change' => 'datetime',
        'date_estimated' => 'datetime',
        'date_added' => 'datetime'
    ];
}
