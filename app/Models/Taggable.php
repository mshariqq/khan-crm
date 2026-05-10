<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taggable extends Model
{
    protected $table = 'tbltaggables';
    public $timestamps = true;
    protected $fillable = ['rel_id', 'rel_type', 'tag_id', 'tag_order'];

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_id', 'id');
    }
}
