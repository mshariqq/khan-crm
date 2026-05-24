<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadActivityLog extends Model
{
    protected $table = 'tbllead_activity_log';
    public $timestamps = true;
    protected $fillable = ['id', 'leadid', 'description', 'additional_data', 'date', 'staffid', 'full_name', 'custom_activity'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime'
    ];
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'leadid', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }
}
