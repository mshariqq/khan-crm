<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormResult extends Model
{
    protected $table = 'tblform_results';
    protected $primaryKey = 'resultid';
    public $timestamps = true;
    protected $fillable = ['resultid', 'boxid', 'boxdescriptionid', 'rel_id', 'rel_type', 'questionid', 'answer', 'resultsetid'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    public function question()
    {
        return $this->belongsTo(FormQuestion::class, 'questionid', 'questionid');
    }

    public function box()
    {
        return $this->belongsTo(FormQuestionBox::class, 'boxid', 'boxid');
    }
}
