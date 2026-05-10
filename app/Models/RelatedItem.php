<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelatedItem extends Model
{
    protected $table = 'tblrelated_items';
    public $timestamps = true;
    protected $fillable = ['id', 'rel_id', 'rel_type', 'item_id'];
}
