<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $table = 'tblreminders';
    public $timestamps = true;
    protected $fillable = ['id', 'description', 'date', 'isnotified', 'rel_id', 'staff', 'rel_type', 'notify_by_email', 'creator'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime',
        'isnotified' => 'boolean',
        'notify_by_email' => 'boolean'
    ];
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff', 'staffid');
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'creator', 'staffid');
    }

    public function relatable()
    {
        return $this->morphTo('relatable', 'rel_type', 'rel_id');
    }
}
