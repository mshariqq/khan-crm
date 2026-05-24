<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFileDownload extends Model
{
    protected $table = 'tblproject_file_downloads';
    public $timestamps = false;

    protected $fillable = [
        'file_id', 'staffid', 'contact_id', 'ip_address', 'user_agent', 'downloaded_at'
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(ProjectFile::class, 'file_id', 'id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }
}
