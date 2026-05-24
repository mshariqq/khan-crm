<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimateRequestStatus extends Model
{
    protected $table = 'tblestimate_request_status';
    public $timestamps = true;
    protected $fillable = ['id', 'name', 'statusorder', 'color', 'flag'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
