<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $table = 'tblreminders';
    public $timestamps = true;
    protected $fillable = ['id', 'description', 'date', 'isnotified', 'rel_id', 'staff', 'rel_type', 'notify_by_email', 'creator'];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff', 'staffid');
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'creator', 'staffid');
    }

    public function relatable()
    {
        $type = $this->rel_type;
        if ($type === 'project') {
            return $this->belongsTo(Project::class, 'rel_id', 'id');
        } elseif ($type === 'lead') {
            return $this->belongsTo(Lead::class, 'rel_id', 'id');
        } elseif ($type === 'task') {
            return $this->belongsTo(Task::class, 'rel_id', 'id');
        } elseif ($type === 'invoice') {
            return $this->belongsTo(Invoice::class, 'rel_id', 'id');
        }
        return null;
    }
}
