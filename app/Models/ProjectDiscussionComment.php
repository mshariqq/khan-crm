<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectDiscussionComment extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'tblprojectdiscussioncomments';
    public $timestamps = true;
    protected $fillable = [
        'discussion_id', 'discussion_type', 'parent', 'created',
        'modified', 'content', 'staff_id', 'contact_id', 'fullname',
        'file_name', 'file_mime_type', 'original_file_name', 'file_size',
        'mentions'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'created' => 'datetime',
        'modified' => 'datetime',
        'discussion_id' => 'integer',
        'parent' => 'integer',
        'staff_id' => 'integer',
        'contact_id' => 'integer',
    ];

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(ProjectDiscussion::class, 'discussion_id', 'id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function parentComment(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent', 'id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent', 'id');
    }
}
