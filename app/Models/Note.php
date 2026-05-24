<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $table = 'tblnotes';
    public $timestamps = true;
    protected $fillable = ['id', 'rel_id', 'rel_type', 'description', 'title', 'date_contacted', 'addedfrom', 'dateadded'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date_contacted' => 'datetime',
        'dateadded' => 'datetime'
    ];
    public function creator()
    {
        return $this->belongsTo(Staff::class, 'addedfrom', 'staffid');
    }

    public function relatable()
    {
        return $this->morphTo('relatable', 'rel_type', 'rel_id');
    }
}
