<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldValue extends Model
{
    protected $table = 'tblcustomfieldsvalues';
    public $timestamps = true;
    protected $fillable = ['id', 'relid', 'fieldid', 'fieldto', 'value'];

    public function field()
    {
        return $this->belongsTo(CustomField::class, 'fieldid', 'id');
    }
}
