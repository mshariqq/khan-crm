<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $table = 'tblnotes';
    public $timestamps = true;
    protected $fillable = ['id', 'rel_id', 'rel_type', 'description', 'title', 'date_contacted', 'addedfrom', 'dateadded'];

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'addedfrom', 'staffid');
    }

    public function relatable()
    {
        $type = $this->rel_type;
        if ($type === 'client') return $this->belongsTo(Client::class, 'rel_id', 'userid');
        if ($type === 'contact') return $this->belongsTo(Contact::class, 'rel_id', 'id');
        if ($type === 'lead') return $this->belongsTo(Lead::class, 'rel_id', 'id');
        if ($type === 'project') return $this->belongsTo(Project::class, 'rel_id', 'id');
        return null;
    }
}
