<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'tblmilestones';
    public $timestamps = true;
    protected $fillable = [
        'name', 'description', 'description_visible_to_customer', 'start_date',
        'due_date', 'project_id', 'color', 'milestone_order', 'datecreated', 'hide_from_customer'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'datecreated' => 'datetime',
        'project_id' => 'integer',
        'hide_from_customer' => 'boolean',
        'description_visible_to_customer' => 'boolean',
        'milestone_order' => 'integer',
    ];

    protected $attributes = [
        'hide_from_customer' => false,
        'description_visible_to_customer' => false,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'milestone', 'id');
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->tasks()->where('status', '!=', Task::STATUS_COMPLETED)->count() === 0;
    }

    public function getCompletionPercentageAttribute(): float
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;
        $completed = $this->tasks()->where('status', Task::STATUS_COMPLETED)->count();
        return round(($completed / $total) * 100, 2);
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->due_date) return null;
        return max(0, Carbon::now()->startOfDay()->diffInDays(Carbon::parse($this->due_date)->startOfDay(), false));
    }

    public static function statusLabels(): array
    {
        return [
            0 => 'Incomplete',
            1 => 'Completed',
        ];
    }
}
