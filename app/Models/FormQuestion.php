<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormQuestion extends Model
{
    protected $table = 'tblform_questions';
    protected $primaryKey = 'questionid';
    public $timestamps = true;
    protected $fillable = ['questionid', 'rel_id', 'rel_type', 'question', 'required', 'question_order'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
