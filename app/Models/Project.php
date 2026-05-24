<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use SoftDeletes, HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_ON_HOLD = 3;
    const STATUS_CANCELLED = 4;

    const BILLING_TYPE_FIXED = 1;
    const BILLING_TYPE_HOURLY = 2;
    const BILLING_TYPE_MIXED = 3;

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    protected $table = 'tblprojects';
    public $timestamps = true;

    protected $fillable = [
        'name', 'description', 'status', 'clientid', 'billing_type', 'start_date',
        'deadline', 'project_created', 'date_finished', 'progress', 'progress_from_tasks',
        'project_cost', 'project_rate_per_hour', 'estimated_hours', 'addedfrom',
        'contact_notification', 'notify_contacts', 'priority', 'category_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'start_date' => 'datetime',
        'deadline' => 'datetime',
        'project_created' => 'datetime',
        'date_finished' => 'datetime',
        'status' => 'integer',
        'billing_type' => 'integer',
        'progress' => 'integer',
        'clientid' => 'integer',
        'addedfrom' => 'integer',
        'progress_from_tasks' => 'boolean',
        'contact_notification' => 'boolean',
        'notify_contacts' => 'array',
        'project_cost' => 'decimal:2',
        'project_rate_per_hour' => 'decimal:2',
        'estimated_hours' => 'decimal:2',
        'category_id' => 'integer',
    ];

    protected $attributes = [
        'status' => 1,
        'billing_type' => 1,
        'priority' => 'medium',
    ];

    protected $appends = [
        'deadline_passed',
        'days_remaining',
        'task_completion_percentage',
        'is_overdue',
        'budget_utilization',
        'total_logged_hours',
        'health_status',
        'profitability',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'clientid', 'userid');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'addedfrom', 'staffid');
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'tblproject_members', 'project_id', 'staff_id')
            ->withTimestamps()
            ->withPivot('id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'rel_id', 'id')->where('rel_type', 'project');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ProjectActivity::class, 'project_id', 'id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class, 'project_id', 'id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ProjectNote::class, 'project_id', 'id');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(ProjectSetting::class, 'project_id', 'id');
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(ProjectDiscussion::class, 'project_id', 'id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'project_id', 'id');
    }

    public function estimates(): HasMany
    {
        return $this->hasMany(Estimate::class, 'project_id', 'id');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'project_id', 'id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'project_id', 'id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'project_id', 'id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class, 'project_id', 'id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'project_id', 'id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'project_id', 'id');
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class, 'project_id', 'id');
    }

    public function pinnedBy(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'tblpinned_projects', 'project_id', 'staff_id')
            ->withTimestamps()
            ->withPivot('id');
    }

    public function contactsToNotify()
    {
        return $this->belongsToMany(Contact::class, 'tblproject_contacts', 'project_id', 'contact_id');
    }

    // ===== New Relationships for Advanced Features =====

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id', 'id');
    }

    public function phases()
    {
        return $this->hasMany(ProjectPhase::class, 'project_id', 'id');
    }

    public function risks()
    {
        return $this->hasMany(ProjectRisk::class, 'project_id', 'id');
    }

    public function changeRequests()
    {
        return $this->hasMany(ProjectChangeRequest::class, 'project_id', 'id');
    }

    public function resourceAllocations()
    {
        return $this->hasMany(ProjectResourceAllocation::class, 'project_id', 'id');
    }

    public function baselines()
    {
        return $this->hasMany(ProjectBaseline::class, 'project_id', 'id');
    }

    public function activeBaseline()
    {
        return $this->hasOne(ProjectBaseline::class, 'project_id', 'id')->where('is_active', true);
    }

    public function closureChecklist()
    {
        return $this->hasMany(ProjectClosureChecklist::class, 'project_id', 'id');
    }

    public function lessonsLearned()
    {
        return $this->hasMany(ProjectLessonsLearned::class, 'project_id', 'id');
    }

    public function shares()
    {
        return $this->hasMany(ProjectShare::class, 'project_id', 'id');
    }

    public function activeShares()
    {
        return $this->hasMany(ProjectShare::class, 'project_id', 'id')->active();
    }

    public function watchers()
    {
        return $this->belongsToMany(Staff::class, 'tblproject_watchers', 'project_id', 'staff_id')
            ->withTimestamps();
    }

    public function recurringConfig()
    {
        return $this->hasOne(ProjectRecurring::class, 'template_project_id', 'id');
    }

    public function feedback()
    {
        return $this->hasMany(ProjectFeedback::class, 'project_id', 'id');
    }

    public function locations()
    {
        return $this->hasMany(ProjectLocation::class, 'project_id', 'id');
    }

    public function emailCampaigns()
    {
        return $this->hasMany(\App\Models\ProjectEmailCampaign::class, 'project_id', 'id');
    }

    // ===== Scopes =====

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeForClient($query, int $clientId)
    {
        return $query->where('clientid', $clientId);
    }

    public function scopeForStaff($query, int $staffId)
    {
        return $query->whereHas('staff', fn($q) => $q->where('staff_id', $staffId));
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('deadline')->where('deadline', '<', now())->where('status', '!=', self::STATUS_COMPLETED);
    }

    public function scopeOnHold($query)
    {
        return $query->where('status', self::STATUS_ON_HOLD);
    }

    public function getDeadlinePassedAttribute(): bool
    {
        return $this->deadline && Carbon::parse($this->deadline)->isPast() && $this->status !== self::STATUS_COMPLETED;
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->deadline) return null;
        return max(0, Carbon::now()->startOfDay()->diffInDays(Carbon::parse($this->deadline)->startOfDay(), false));
    }

    public function getTaskCompletionPercentageAttribute(): float
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;
        $completed = $this->tasks()->where('status', Task::STATUS_COMPLETED)->count();
        return round(($completed / $total) * 100, 2);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline_passed;
    }

    public function getBudgetUtilizationAttribute(): ?float
    {
        if (!$this->project_cost || $this->project_cost <= 0) return null;
        $totalCost = $this->invoices()->sum('total') + $this->expenses()->sum('amount');
        return round(($totalCost / $this->project_cost) * 100, 2);
    }

    public function getTotalLoggedHoursAttribute(): float
    {
        return (float) $this->tasks()
            ->join('tbltaskstimers', 'tbltasks.id', '=', 'tbltaskstimers.task_id')
            ->whereNotNull('tbltaskstimers.end_time')
            ->sum(\DB::raw('TIMESTAMPDIFF(SECOND, tbltaskstimers.start_time, tbltaskstimers.end_time) / 3600'));
    }

    public function getHealthStatusAttribute(): string
    {
        if ($this->status === self::STATUS_COMPLETED) return 'completed';
        if ($this->status === self::STATUS_CANCELLED) return 'cancelled';
        if ($this->deadline && Carbon::parse($this->deadline)->isPast()) return 'critical';
        if ($this->progress < 25) return 'at_risk';
        if ($this->progress < 75) return 'warning';
        return 'on_track';
    }

    public function getProfitabilityAttribute(): array
    {
        $revenue = (float) $this->invoices()->sum('total');
        $expenses = (float) $this->expenses()->sum('amount');
        $loggedHours = $this->total_logged_hours;
        $hourlyRate = (float) ($this->project_rate_per_hour ?? 0);
        $laborCost = $loggedHours * $hourlyRate;
        $totalCost = $expenses + $laborCost;
        $profit = $revenue - $totalCost;
        $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0;
        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'labor_cost' => $laborCost,
            'total_cost' => $totalCost,
            'profit' => $profit,
            'margin' => $margin,
        ];
    }

    public static function priorityLabels(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    public static function priorityColors(): array
    {
        return [
            self::PRIORITY_LOW => 'bg-slate-100 text-slate-600',
            self::PRIORITY_MEDIUM => 'bg-blue-100 text-blue-700',
            self::PRIORITY_HIGH => 'bg-amber-100 text-amber-700',
            self::PRIORITY_URGENT => 'bg-red-100 text-red-700',
        ];
    }

    public static function healthLabels(): array
    {
        return [
            'on_track' => 'On Track',
            'warning' => 'Warning',
            'at_risk' => 'At Risk',
            'critical' => 'Critical',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    public static function healthColors(): array
    {
        return [
            'on_track' => 'bg-emerald-100 text-emerald-700',
            'warning' => 'bg-amber-100 text-amber-700',
            'at_risk' => 'bg-orange-100 text-orange-700',
            'critical' => 'bg-red-100 text-red-700',
            'completed' => 'bg-indigo-100 text-indigo-700',
            'cancelled' => 'bg-slate-100 text-slate-600',
        ];
    }

    public static function healthDotColors(): array
    {
        return [
            'on_track' => 'bg-emerald-500',
            'warning' => 'bg-amber-500',
            'at_risk' => 'bg-orange-500',
            'critical' => 'bg-red-500',
            'completed' => 'bg-indigo-500',
            'cancelled' => 'bg-slate-500',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_ON_HOLD => 'On Hold',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function billingTypeLabels(): array
    {
        return [
            self::BILLING_TYPE_FIXED => 'Fixed Rate',
            self::BILLING_TYPE_HOURLY => 'Hourly Rate',
            self::BILLING_TYPE_MIXED => 'Mixed',
        ];
    }

    public static function activityDescriptionLabels(): array
    {
        return [
            'project_activity_project_created' => 'created this project',
            'project_activity_project_updated' => 'updated the project details',
            'project_activity_project_deleted' => 'deleted this project',
            'project_activity_status_updated' => 'changed status from :old_status to :new_status',
            'project_activity_completed' => 'marked project as completed',
            'project_activity_member_added' => 'added :member_name to the team',
            'project_activity_member_removed' => 'removed :member_name from the team',
            'project_activity_file_uploaded' => 'uploaded a file',
            'project_activity_file_deleted' => 'deleted a file',
            'project_activity_discussion_created' => 'started a discussion',
            'project_activity_discussion_commented' => 'commented on a discussion',
            'project_activity_milestone_created' => 'created a milestone',
            'project_activity_milestone_completed' => 'completed a milestone',
            'project_activity_note_created' => 'added a note',
            'project_activity_note_updated' => 'updated a note',
            'project_activity_task_created' => 'created a new task',
            'project_activity_task_status_updated' => 'updated task status',
            'project_activity_task_quick_created' => 'quick-created a task',
            'project_activity_baseline_captured' => 'captured a baseline: :baseline_name',
            'project_activity_phase_created' => 'created a new phase: :phase_name',
            'project_activity_risk_added' => 'added a risk: :risk_title',
            'project_activity_risk_resolved' => 'resolved a risk: :risk_title',
            'project_activity_change_request_submitted' => 'submitted a change request: :cr_title',
            'project_activity_change_request_approved' => 'approved a change request: :cr_title',
            'project_activity_lesson_added' => 'added a lesson learned: :lesson_title',
            'project_activity_invoices_finalized' => 'auto-finalized invoices on project completion',
            'project_activity_invoice_from_milestone' => 'generated invoice from milestone: :milestone_name',
        ];
    }
}
