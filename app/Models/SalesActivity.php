<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesActivity extends Model
{
    protected $table = 'tblsales_activity';
    public $timestamps = true;
    protected $fillable = ['id', 'rel_type', 'rel_id', 'description', 'additional_data', 'staffid', 'full_name', 'date'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime'
    ];
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'rel_id', 'id')->where('rel_type', 'invoice');
    }
}
