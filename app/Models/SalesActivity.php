<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesActivity extends Model
{
    protected $table = 'tblsales_activity';
    public $timestamps = true;
    protected $fillable = ['id', 'rel_type', 'rel_id', 'description', 'additional_data', 'staffid', 'full_name', 'date'];
}
