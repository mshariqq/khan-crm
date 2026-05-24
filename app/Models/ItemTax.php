<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTax extends Model
{
    protected $table = 'tblitem_tax';
    public $timestamps = true;
    protected $fillable = ['id', 'itemid', 'rel_id', 'rel_type', 'taxrate', 'taxname'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
