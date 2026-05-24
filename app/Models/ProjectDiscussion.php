<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectDiscussion extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'tblprojectdiscussions';
    public $timestamps = true;
    protected $fillable = [
        'project_id', 'subject', 'description', 'show_to_customer',
        'datecreated', 'last_activity', 'staff_id', 'contact_id',
        'is_pinned', 'category_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'datecreated' => 'datetime',
        'last_activity' => 'datetime',
        'show_to_customer' => 'boolean',
        'is_pinned' => 'boolean',
        'project_id' => 'integer',
        'staff_id' => 'integer',
        'contact_id' => 'integer',
        'category_id' => 'integer',
    ];

    protected $attributes = [
        'show_to_customer' => false,
        'is_pinned' => false,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProjectDiscussionComment::class, 'discussion_id', 'id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(ProjectDiscussionCategory::class, 'category_id', 'id');
    }

    public function topLevelComments()
    {
        return $this->hasMany(ProjectDiscussionComment::class, 'discussion_id', 'id')
            ->whereNull('parent');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeUnpinned($query)
    {
        return $query->where('is_pinned', false);
    }
}
