<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'tblitems';
    public $timestamps = true;
    protected $fillable = ['id', 'description', 'long_description', 'rate', 'tax', 'tax2', 'unit', 'group_id'];

    public function group()
    {
        return $this->belongsTo(ItemsGroup::class, 'group_id', 'id');
    }

    public function taxes()
    {
        return $this->hasMany(ItemTax::class, 'itemid', 'id');
    }
}
