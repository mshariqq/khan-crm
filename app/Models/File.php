<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'tblfiles';
    public $timestamps = true;
    protected $fillable = [
        'rel_id', 'rel_type', 'file_name', 'original_file_name', 'filetype',
        'visible_to_customer', 'attachment_key', 'external', 'external_link',
        'thumbnail_link', 'staffid', 'contact_id', 'task_comment_id', 'dateadded'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'dateadded' => 'datetime'
    ];
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }
}
