<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $table = 'tbltask_comments';
    public $timestamps = true;
    protected $fillable = ['content', 'taskid', 'staffid', 'contact_id', 'file_id', 'dateadded'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'dateadded' => 'datetime'
    ];
    public function addedFrom()
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }
}
