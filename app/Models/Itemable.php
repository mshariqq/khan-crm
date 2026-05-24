<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Itemable extends Model
{
    protected $table = 'tblitemable';
    public $timestamps = true;
    protected $fillable = ['id', 'rel_id', 'rel_type', 'description', 'long_description', 'qty', 'rate', 'unit', 'is_optional', 'is_selected', 'item_order', 'tax_rate', 'amount'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_optional' => 'boolean',
        'is_selected' => 'boolean'
    ];
    public function relatable()
    {
        return $this->morphTo(__FUNCTION__, 'rel_type', 'rel_id');
    }
}
