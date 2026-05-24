<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tbltags';
    public $timestamps = true;
    protected $fillable = ['id', 'name'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    public function taggables()
    {
        return $this->hasMany(Taggable::class, 'tag_id', 'id');
    }
}
