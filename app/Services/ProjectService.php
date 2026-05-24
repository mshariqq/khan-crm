<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\ProjectBaseline;
use App\Models\ProjectClosureChecklist;
use App\Models\Staff;
use App\Models\StaffLeave;
use App\Models\Notification;
use App\Models\Task;
use App\Models\TaskTimer;
use App\Models\Ticket;
use App\Services\ProjectNotificationService;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Auth;

class ProjectService extends BaseService
{
    protected ProjectNotificationService $projectNotification;
    protected ?WebhookService $webhookService;

    public function __construct(ProjectNotificationService $projectNotification, ?WebhookService $webhookService = null)
    {
        $this->projectNotification = $projectNotification;
        $this->webhookService = $webhookService;
    }

    public function syncStaffAndNotify(Project $project, array $newStaffIds, bool $autoReassignToCreator = true): void
    {
        $oldStaffIds = $project->staff()->pluck('staffid')->toArray();
        $oldStaff = Staff::whereIn('staffid', $oldStaffIds)->get()->keyBy('staffid');

        $project->staff()->sync($newStaffIds);

        $addedStaffIds = array_diff($newStaffIds, $oldStaffIds);
        $removedStaffIds = array_diff($oldStaffIds, $newStaffIds);

        foreach ($addedStaffIds as $staffId) {
            $staffMember = Staff::find($staffId);
            if ($staffMember) {
                $this->projectNotification->sendToOneStaff('project-member-added', $project->id, $staffMember, [
                    'member_name' => $staffMember->firstname . ' ' . $staffMember->lastname,
                    'member_email' => $staffMember->email,
                    'added_by' => (Auth::user()->firstname ?? '') . ' ' . (Auth::user()->lastname ?? ''),
                ]);

                Notification::createForStaff(
                    $staffMember->staffid,
                    Auth::user()->full_name . ' added you to project ' . e($project->name),
                    route('admin.projects.show', $project->id),
                    notificationType: 'project_member',
                    groupingKey: 'project_member_added_' . $project->id
                );
            }
        }

        foreach ($removedStaffIds as $staffId) {
            $staffMember = Staff::find($staffId);
            if ($staffMember) {
                $this->projectNotification->sendToOneStaff('project-member-removed', $project->id, $staffMember, [
                    'member_name' => $staffMember->firstname . ' ' . $staffMember->lastname,
                    'removed_by' => (Auth::user()->firstname ?? '') . ' ' . (Auth::user()->lastname ?? ''),
                ]);

                Notification::createForStaff(
                    $staffMember->staffid,
                    Auth::user()->full_name . ' removed you from project ' . e($project->name),
                    route('admin.projects.show', $project->id),
                    notificationType: 'project_member',
                    groupingKey: 'project_member_removed_' . $project->id
                );

                if ($autoReassignToCreator) {
                    $assignedTasks = Task::where('rel_id', $project->id)
                        ->where('rel_type', 'project')
                        ->whereHas('assignedStaff', fn($q) => $q->where('staffid', $staffId))
                        ->get();
                    foreach ($assignedTasks as $task) {
                        $task->assignedStaff()->detach($staffId);
                        $creatorId = $project->addedfrom ?: 1;
                        $task->assignedStaff()->attach($creatorId);
                    }
                }
            }
        }

        if ($oldStaffIds !== $newStaffIds) {
            ProjectActivity::create([
                'project_id' => $project->id,
                'staff_id' => Auth::id(),
                'description_key' => 'project_activity_team_updated',
                'additional_data' => json_encode([
                    'added' => $addedStaffIds,
                    'removed' => $removedStaffIds,
                ]),
                'dateadded' => now(),
            ]);
        }
    }

    public function handleStatusChange(Project $project, int $oldStatus, int $newStatus): bool
    {
        $notified = false;
        $statusLabels = Project::statusLabels();
        $statusSlugs = [
            Project::STATUS_COMPLETED => 'project-completed',
            Project::STATUS_ON_HOLD => 'project-on-hold',
            Project::STATUS_CANCELLED => 'project-cancelled',
        ];

        if ($oldStatus !== $newStatus) {
            try {
                if (isset($statusSlugs[$newStatus])) {
                    $this->projectNotification->send($statusSlugs[$newStatus], $project->id);
                    $notified = true;
                } elseif ($newStatus === Project::STATUS_ACTIVE && in_array($oldStatus, [Project::STATUS_ON_HOLD, Project::STATUS_CANCELLED])) {
                    $this->projectNotification->send('project-resumed', $project->id);
                    $notified = true;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Status change notification skipped: ' . $e->getMessage());
            }

            if ($newStatus === Project::STATUS_COMPLETED) {
                $project->update(['date_finished' => now()]);
                $this->autoFinalizeInvoices($project);
            } elseif ($oldStatus === Project::STATUS_COMPLETED && $newStatus !== Project::STATUS_COMPLETED) {
                $project->update(['date_finished' => null]);
            }

            $this->syncTicketStatusWithProject($project);

            if ($newStatus === Project::STATUS_COMPLETED) {
                $project->update(['progress' => 100]);
            }

            if ($this->webhookService) {
                $this->webhookService->dispatchProjectEvent('project.status_changed', $project);
            }

            $oldLabel = $statusLabels[$oldStatus] ?? 'Unknown';
            $newLabel = $statusLabels[$newStatus] ?? 'Unknown';
            $this->notifyStaffExceptSelf(
                $project,
                'Project ' . e($project->name) . ' moved from ' . $oldLabel . ' to ' . $newLabel,
                'project_status'
            );
        }

        return $notified;
    }

    public function handleDeadlineChange(Project $project, ?string $oldDeadline, ?string $newDeadline): bool
    {
        if ($oldDeadline === $newDeadline) {
            return false;
        }

        $this->projectNotification->send('project-deadline-changed', $project->id);
        $newDate = $newDeadline ? \Carbon\Carbon::parse($newDeadline)->format('M j, Y') : 'Not set';
        $this->notifyStaffExceptSelf(
            $project,
            'Deadline for ' . e($project->name) . ' changed to ' . $newDate,
            'project_status',
            'deadline_change_' . $project->id
        );
        return true;
    }

    public function notifyGeneralUpdate(Project $project): void
    {
        $this->projectNotification->send('project-updated', $project->id);
        $this->notifyStaffExceptSelf(
            $project,
            Auth::user()->full_name . ' updated project ' . e($project->name),
            'project_updated',
            'project_updated_' . $project->id
        );
    }

    public function logProjectActivity(Project $project): void
    {
        $changedAttributes = $project->getChanges();
        unset($changedAttributes['updated_at']);

        if (!empty($changedAttributes)) {
            ProjectActivity::create([
                'project_id' => $project->id,
                'staff_id' => Auth::id(),
                'description_key' => 'project_activity_project_updated',
                'additional_data' => json_encode([
                    'project_name' => $project->name,
                    'changes' => array_map(function ($key) use ($project) {
                        return ['old' => $project->getOriginal($key), 'new' => $project->{$key}];
                    }, array_keys($changedAttributes)),
                ]),
                'dateadded' => now(),
            ]);
        }
    }

    public function recalculateProgress(Project $project): int
    {
        $total = $project->tasks()->count();
        if ($total === 0) {
            $progress = $project->progress_from_tasks ? 0 : $project->progress;
        } else {
            $completed = $project->tasks()->where('status', Task::STATUS_COMPLETED)->count();
            $progress = (int) round(($completed / $total) * 100);
        }

        $project->update(['progress' => $progress]);

        if ($progress >= 100 && $project->status !== Project::STATUS_COMPLETED) {
            $project->update(['status' => Project::STATUS_COMPLETED, 'date_finished' => now()]);
        }

        return $progress;
    }

    public function handleMilestoneAutoComplete(Project $project, int $milestoneId): void
    {
        $milestone = $project->milestones()->find($milestoneId);
        if (!$milestone) return;

        $incompleteTasks = $milestone->tasks()->where('status', '!=', Task::STATUS_COMPLETED)->count();
        if ($incompleteTasks === 0) {
            $this->projectNotification->send('milestone-completed', $project->id, [
                'milestone_name' => $milestone->name,
            ]);
        }
    }

    public function handleBudgetTracking(Project $project, ?float $newAmount = null): ?string
    {
        if (!$project->project_cost || $project->project_cost <= 0) {
            return null;
        }

        $totalInvoiced = $project->invoices()->sum('total');
        $totalExpensed = $project->expenses()->sum('amount');
        $totalSpent = $totalInvoiced + $totalExpensed;

        if ($newAmount !== null) {
            $budgetCheck = $this->validateBudgetBeforeInvoice($project, $newAmount);
            if (!$budgetCheck['allowed']) {
                $this->projectNotification->send('budget-exceeded', $project->id, [
                    'budget' => $project->project_cost,
                    'spent' => $totalSpent,
                    'new_amount' => $newAmount,
                    'message' => $budgetCheck['message'],
                ]);
                return 'budget-exceeded';
            }
        }

        $percentage = ($totalSpent / $project->project_cost) * 100;

        $threshold = \App\Helpers\SettingsHelper::get('budget_threshold_percentage', 80);

        if ($percentage >= (float) $threshold) {
            $this->projectNotification->send('budget-threshold-reached', $project->id, [
                'budget' => $project->project_cost,
                'spent' => $totalSpent,
                'percentage' => round($percentage, 2),
                'threshold' => $threshold,
            ]);

            return 'budget-threshold-reached';
        }

        return null;
    }

    // ===== Critical Path Analysis =====

    public function calculateCriticalPath(Project $project): array
    {
        $tasks = $project->tasks()->with('dependencies.dependsOn')->get();
        $dependencies = \App\Models\TaskDependency::whereIn('task_id', $tasks->pluck('id'))->get();

        $graph = [];
        $inDegree = [];
        foreach ($tasks as $task) {
            $graph[$task->id] = [];
            $inDegree[$task->id] = 0;
        }
        foreach ($dependencies as $dep) {
            if (isset($graph[$dep->depends_on_task_id])) {
                $graph[$dep->depends_on_task_id][] = $dep->task_id;
                $inDegree[$dep->task_id] = ($inDegree[$dep->task_id] ?? 0) + 1;
            }
        }

        $queue = [];
        foreach ($inDegree as $id => $deg) {
            if ($deg === 0) $queue[] = $id;
        }

        $topological = [];
        while (!empty($queue)) {
            $current = array_shift($queue);
            $topological[] = $current;
            foreach ($graph[$current] ?? [] as $neighbor) {
                $inDegree[$neighbor]--;
                if ($inDegree[$neighbor] === 0) $queue[] = $neighbor;
            }
        }

        $earliestStart = array_fill_keys($tasks->pluck('id')->toArray(), 0);
        $earliestFinish = array_fill_keys($tasks->pluck('id')->toArray(), 0);
        $taskDurations = [];
        foreach ($tasks as $task) {
            $duration = $task->duedate && $task->startdate
                ? max(1, \Carbon\Carbon::parse($task->startdate)->diffInDays(\Carbon\Carbon::parse($task->duedate)))
                : 1;
            $taskDurations[$task->id] = $duration;
        }

        foreach ($topological as $taskId) {
            $maxPred = 0;
            foreach ($dependencies as $dep) {
                if ((int)$dep->task_id === $taskId) {
                    $predId = (int)$dep->depends_on_task_id;
                    $maxPred = max($maxPred, $earliestFinish[$predId] + $dep->lag_days);
                }
            }
            $earliestStart[$taskId] = $maxPred;
            $earliestFinish[$taskId] = $maxPred + ($taskDurations[$taskId] ?? 1);
        }

        $projectDuration = max($earliestFinish);
        $latestFinish = array_fill_keys($tasks->pluck('id')->toArray(), $projectDuration);
        $latestStart = array_fill_keys($tasks->pluck('id')->toArray(), $projectDuration);

        foreach (array_reverse($topological) as $taskId) {
            $minSucc = $latestFinish[$taskId];
            foreach ($dependencies as $dep) {
                if ((int)$dep->depends_on_task_id === $taskId) {
                    $succId = (int)$dep->task_id;
                    $minSucc = min($minSucc, $latestStart[$succId] - $dep->lag_days);
                }
            }
            $latestFinish[$taskId] = $minSucc;
            $latestStart[$taskId] = $minSucc - ($taskDurations[$taskId] ?? 1);
        }

        $criticalPath = [];
        foreach ($tasks as $task) {
            $float = $latestStart[$task->id] - $earliestStart[$task->id];
            if ($float <= 0) {
                $criticalPath[] = $task->id;
            }
        }

        return [
            'critical_path_task_ids' => $criticalPath,
            'project_duration_days' => $projectDuration,
            'earliest_start' => $earliestStart,
            'earliest_finish' => $earliestFinish,
            'latest_start' => $latestStart,
            'latest_finish' => $latestFinish,
            'total_float' => array_combine(
                $tasks->pluck('id')->toArray(),
                array_map(fn($i) => $latestStart[$i] - $earliestStart[$i], array_keys($earliestStart))
            ),
        ];
    }

    // ===== Baseline/Capture =====

    public function captureBaseline(Project $project, string $name, ?string $description = null): ProjectBaseline
    {
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status', Task::STATUS_COMPLETED)->count();
        $plannedValue = $project->project_cost ?? 0;
        $plannedCost = $project->expenses()->sum('amount') + ($project->invoices()->sum('total') ?? 0);
        $duration = $project->start_date && $project->deadline
            ? \Carbon\Carbon::parse($project->start_date)->diffInDays(\Carbon\Carbon::parse($project->deadline))
            : 0;

        $baseline = ProjectBaseline::create([
            'project_id' => $project->id,
            'name' => $name,
            'description' => $description,
            'planned_value' => $plannedValue,
            'planned_cost' => $plannedCost,
            'planned_duration_days' => $duration,
            'baseline_date' => now(),
            'scope_snapshot' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'task_completion_pct' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
                'milestones' => $project->milestones()->count(),
                'completed_milestones' => $project->milestones()->whereHas('tasks', fn($q) => $q->where('status', Task::STATUS_COMPLETED), '=', \DB::raw('(SELECT COUNT(*) FROM tbltasks WHERE tbltasks.milestone = tblmilestones.id)'))->count(),
            ],
            'schedule_snapshot' => [
                'start_date' => $project->start_date?->format('Y-m-d'),
                'deadline' => $project->deadline?->format('Y-m-d'),
                'progress' => $project->progress,
            ],
            'cost_snapshot' => [
                'budget' => $project->project_cost,
                'invoiced' => $project->invoices()->sum('total'),
                'expensed' => $project->expenses()->sum('amount'),
            ],
            'is_active' => true,
        ]);

        ProjectBaseline::where('project_id', $project->id)->where('id', '!=', $baseline->id)->update(['is_active' => false]);

        ProjectActivity::create([
            'project_id' => $project->id,
            'staff_id' => Auth::id(),
            'description_key' => 'project_activity_baseline_captured',
            'additional_data' => json_encode(['baseline_name' => $name]),
            'dateadded' => now(),
        ]);

        return $baseline;
    }

    public function calculateEarnedValueMetrics(Project $project): array
    {
        $baseline = $project->activeBaseline;
        if (!$baseline) {
            return [];
        }

        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status', Task::STATUS_COMPLETED)->count();
        $percentComplete = $totalTasks > 0 ? $completedTasks / $totalTasks : 0;

        $plannedValue = (float) $baseline->planned_value;
        $earnedValue = $plannedValue * $percentComplete;
        $actualCost = (float) ($project->expenses()->sum('amount') + $project->invoices()->sum('total'));

        $schedulePct = $project->start_date && $project->deadline
            ? min(1, max(0, \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($project->start_date), false) / max(1, $baseline->planned_duration_days)))
            : 0;

        $plannedValueSchedule = $plannedValue * $schedulePct;

        return [
            'planned_value' => $plannedValue,
            'earned_value' => round($earnedValue, 2),
            'actual_cost' => round($actualCost, 2),
            'schedule_pv' => round($plannedValueSchedule, 2),
            'schedule_variance' => round($earnedValue - $plannedValueSchedule, 2),
            'cost_variance' => round($earnedValue - $actualCost, 2),
            'schedule_performance_index' => $plannedValueSchedule > 0 ? round($earnedValue / $plannedValueSchedule, 2) : 0,
            'cost_performance_index' => $actualCost > 0 ? round($earnedValue / $actualCost, 2) : 0,
            'estimate_at_completion' => $actualCost > 0 && $percentComplete > 0
                ? round($actualCost / $percentComplete, 2) : 0,
            'estimate_to_complete' => $actualCost > 0 && $percentComplete > 0
                ? round(($actualCost / $percentComplete) - $actualCost, 2) : 0,
        ];
    }

    // ===== Closure Checklist =====

    public function initializeClosureChecklist(Project $project): void
    {
        if ($project->closureChecklist()->count() === 0) {
            foreach (ProjectClosureChecklist::defaultItems() as $index => $item) {
                ProjectClosureChecklist::create([
                    'project_id' => $project->id,
                    'item' => $item['item'],
                    'category' => $item['category'],
                    'sort_order' => $index,
                ]);
            }
        }
    }

    // ===== Template Application =====

    public function applyTemplate(Project $project, \App\Models\ProjectTemplate $template): void
    {
        if ($template->milestones) {
            foreach ($template->milestones as $milestoneData) {
                $project->milestones()->create([
                    'name' => $milestoneData['name'] ?? 'Milestone',
                    'description' => $milestoneData['description'] ?? '',
                    'due_date' => $milestoneData['due_date'] ?? null,
                    'milestone_order' => $milestoneData['order'] ?? 0,
                ]);
            }
        }
    }

    // ===== Recurring Project Generation =====

    public function generateRecurringProject(\App\Models\ProjectRecurring $config): ?Project
    {
        if (!$config->is_active) return null;
        if ($config->max_occurrences && $config->occurrences_generated >= $config->max_occurrences) {
            $config->update(['is_active' => false]);
            return null;
        }

        $template = $config->templateProject;
        if (!$template) return null;

        $project = Project::create([
            'name' => $template->name . ' (' . now()->format('M Y') . ')',
            'description' => $template->description,
            'status' => Project::STATUS_ACTIVE,
            'clientid' => $template->clientid,
            'billing_type' => $template->billing_type,
            'start_date' => now(),
            'deadline' => $config->next_generation_date->copy()->addDays(
                $template->start_date && $template->deadline
                    ? $template->start_date->diffInDays($template->deadline) : 30
            ),
            'project_cost' => $template->project_cost,
            'project_rate_per_hour' => $template->project_rate_per_hour,
            'estimated_hours' => $template->estimated_hours,
            'project_created' => now(),
            'addedfrom' => Auth::id() ?: $template->addedfrom,
            'progress' => 0,
        ]);

        if ($config->copy_team) {
            $staffIds = $template->staff()->pluck('staffid')->toArray();
            if (!empty($staffIds)) $project->staff()->sync($staffIds);
        }

        if ($config->copy_milestones) {
            foreach ($template->milestones as $milestone) {
                $project->milestones()->create([
                    'name' => $milestone->name,
                    'description' => $milestone->description,
                    'due_date' => $milestone->due_date,
                    'mileStones_order' => $milestone->milestone_order,
                ]);
            }
        }

        $nextDate = $config->next_generation_date->copy();
        match ($config->recurring_type) {
            'daily' => $nextDate->addDays($config->repeat_every),
            'weekly' => $nextDate->addWeeks($config->repeat_every),
            'monthly' => $nextDate->addMonths($config->repeat_every),
            'yearly' => $nextDate->addYears($config->repeat_every),
            default => $nextDate->addMonths($config->repeat_every),
        };

        $config->update([
            'occurrences_generated' => $config->occurrences_generated + 1,
            'last_generated_date' => now(),
            'next_generation_date' => $nextDate,
        ]);

        return $project;
    }

    // ===== Resource Utilization =====

    public function getResourceUtilization(Project $project): array
    {
        $allocations = $project->resourceAllocations;
        $totalHours = $project->total_logged_hours;
        $estimatedHours = (float) ($project->estimated_hours ?? 0);

        $staffUtilization = [];
        foreach ($allocations as $alloc) {
            $staffTasks = Task::whereHas('assignedStaff', fn($q) => $q->where('staffid', $alloc->staff_id))
                ->where('rel_id', $project->id)->where('rel_type', 'project')
                ->with(['timers' => fn($q) => $q->whereNotNull('end_time')])
                ->get();

            $loggedByStaff = 0;
            foreach ($staffTasks as $task) {
                foreach ($task->timers as $timer) {
                    $loggedByStaff += \Carbon\Carbon::parse($timer->start_time)->diffInSeconds(\Carbon\Carbon::parse($timer->end_time)) / 3600;
                }
            }

            $staffUtilization[] = [
                'staff_id' => $alloc->staff_id,
                'staff_name' => $alloc->staff->firstname . ' ' . $alloc->staff->lastname ?? 'Unknown',
                'role' => $alloc->role,
                'allocation_pct' => $alloc->allocation_percentage,
                'logged_hours' => round($loggedByStaff, 2),
                'utilization_pct' => $estimatedHours > 0 ? round(($loggedByStaff / ($estimatedHours * $alloc->allocation_percentage / 100)) * 100, 2) : 0,
            ];
        }

        return [
            'total_estimated_hours' => $estimatedHours,
            'total_logged_hours' => round($totalHours, 2),
            'overall_utilization_pct' => $estimatedHours > 0 ? round(($totalHours / $estimatedHours) * 100, 2) : 0,
            'staff' => $staffUtilization,
        ];
    }

    // ===== Portfolio Health =====

    public function getPortfolioHealth(): array
    {
        $projects = Project::withCount(['tasks', 'tasks as completed_tasks' => fn($q) => $q->where('status', Task::STATUS_COMPLETED)])
            ->get();

        $health = ['on_track' => 0, 'warning' => 0, 'at_risk' => 0, 'critical' => 0, 'completed' => 0];
        $totalBudget = 0;
        $totalSpent = 0;
        $totalTasks = 0;
        $completedTasks = 0;

        foreach ($projects as $project) {
            $h = $project->health_status;
            $health[$h] = ($health[$h] ?? 0) + 1;
            $totalBudget += (float) ($project->project_cost ?? 0);
            $totalSpent += (float) ($project->expenses()->sum('amount') + $project->invoices()->sum('total'));
            $totalTasks += $project->tasks_count ?? 0;
            $completedTasks += $project->completed_tasks ?? 0;
        }

        return [
            'total_projects' => $projects->count(),
            'health_distribution' => $health,
            'total_budget' => $totalBudget,
            'total_spent' => $totalSpent,
            'budget_utilization_pct' => $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 2) : 0,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'task_completion_pct' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
            'overdue_count' => Project::overdue()->count(),
            'active_count' => Project::active()->count(),
            'completed_count' => Project::completed()->count(),
            'on_hold_count' => Project::onHold()->count(),
        ];
    }

    // ===== Batch Operations =====

    public function batchUpdateStatus(array $projectIds, int $status): int
    {
        $count = 0;
        foreach ($projectIds as $id) {
            $project = Project::find($id);
            if ($project) {
                $oldStatus = $project->status;
                $project->update(['status' => $status]);
                if ($status === Project::STATUS_COMPLETED) {
                    $project->update(['date_finished' => now(), 'progress' => 100]);
                }
                $this->handleStatusChange($project, $oldStatus, $status);
                $count++;
            }
        }
        return $count;
    }

    public function batchDelete(array $projectIds): int
    {
        $count = 0;
        foreach ($projectIds as $id) {
            $project = Project::find($id);
            if ($project) {
                $project->staff()->detach();
                $project->tasks->each(function ($task) {
                    $task->assignedStaff()->detach();
                    $task->followers()->detach();
                    $task->delete();
                });
                $project->delete();
                $count++;
            }
        }
        return $count;
    }

    public function batchAssignTeam(array $projectIds, array $staffIds): int
    {
        $count = 0;
        foreach ($projectIds as $id) {
            $project = Project::find($id);
            if ($project) {
                $existingIds = $project->staff()->pluck('staffid')->toArray();
                $project->staff()->syncWithoutDetaching($staffIds);
                $count++;
            }
        }
        return $count;
    }

    // ===== Search Within Tabs =====

    public function searchTabContent(Project $project, string $tab, string $query): array
    {
        $results = [];
        switch ($tab) {
            case 'tasks':
                $results = $project->tasks()->where('name', 'like', "%{$query}%")->orWhere('description', 'like', "%{$query}%")->get()->toArray();
                break;
            case 'milestones':
                $results = $project->milestones()->where('name', 'like', "%{$query}%")->get()->toArray();
                break;
            case 'invoices':
                $results = $project->invoices()->where('number', 'like', "%{$query}%")->get()->toArray();
                break;
            case 'expenses':
                $results = $project->expenses()->where('name', 'like', "%{$query}%")->get()->toArray();
                break;
            case 'files':
                $results = $project->files()->where('original_file_name', 'like', "%{$query}%")->get()->toArray();
                break;
            case 'discussions':
                $results = $project->discussions()->where('subject', 'like', "%{$query}%")->get()->toArray();
                break;
            case 'notes':
                $results = $project->notes()->where('title', 'like', "%{$query}%")->orWhere('content', 'like', "%{$query}%")->get()->toArray();
                break;
        }
        return $results;
    }

    // ===== Integration: Auto-finalize Invoices =====

    public function autoFinalizeInvoices(Project $project): void
    {
        $project->invoices()
            ->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIALLY_PAID])
            ->update(['status' => Invoice::STATUS_PAID]);
    }

    // ===== Integration: Milestone Invoice =====

    public function generateMilestoneInvoice(Milestone $milestone): ?Invoice
    {
        $project = $milestone->project;
        if (!$project) return null;

        $billableTasks = $milestone->tasks()->where('billable', true)->get();
        if ($billableTasks->isEmpty()) return null;

        $totalAmount = $billableTasks->sum(function ($task) {
            return $task->hourly_rate ?: 0;
        });

        if ($totalAmount <= 0) return null;

        $invoice = Invoice::create([
            'clientid' => $project->clientid,
            'project_id' => $project->id,
            'date' => now(),
            'duedate' => now()->addDays(30),
            'status' => Invoice::STATUS_UNPAID,
            'addedfrom' => Auth::id() ?? 1,
            'total' => $totalAmount,
            'subtotal' => $totalAmount,
            'datecreated' => now(),
        ]);

        \App\Models\Itemable::create([
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'description' => 'Billable tasks from milestone: ' . $milestone->name,
            'qty' => 1,
            'rate' => $totalAmount,
            'amount' => $totalAmount,
        ]);

        ProjectActivity::create([
            'project_id' => $project->id,
            'staff_id' => Auth::id() ?? 1,
            'description_key' => 'project_activity_invoice_from_milestone',
            'additional_data' => json_encode([
                'milestone_name' => $milestone->name,
                'invoice_id' => $invoice->id,
                'amount' => $totalAmount,
            ]),
            'dateadded' => now(),
        ]);

        return $invoice;
    }

    // ===== Integration: Sync Ticket Status =====

    public function syncTicketStatusWithProject(Project $project): void
    {
        $closedStatusId = \App\Models\TicketStatus::where('name', 'like', '%Closed%')->value('ticketstatusid') ?: 1;

        if (in_array($project->status, [Project::STATUS_COMPLETED, Project::STATUS_CANCELLED])) {
            $project->tickets()->whereNotIn('status', [$closedStatusId])->update(['status' => $closedStatusId]);
        } elseif ($project->status === Project::STATUS_ACTIVE) {
            $openStatusId = \App\Models\TicketStatus::where('name', 'like', '%Open%')->value('ticketstatusid') ?: 1;
            $project->tickets()->where('status', $closedStatusId)->update(['status' => $openStatusId]);
        }
    }

    // ===== Integration: Budget Validation =====

    public function validateBudgetBeforeInvoice(Project $project, float $amount): array
    {
        if (!$project->project_cost || $project->project_cost <= 0) {
            return ['allowed' => true, 'message' => 'No budget set'];
        }

        $totalInvoiced = (float) $project->invoices()->sum('total');
        $totalExpensed = (float) $project->expenses()->sum('amount');
        $totalSpent = $totalInvoiced + $totalExpensed;
        $remaining = $project->project_cost - $totalSpent;

        if ($amount > $remaining) {
            return [
                'allowed' => false,
                'message' => "Invoice amount \${$amount} exceeds remaining budget \${$remaining}",
            ];
        }

        $threshold = \App\Helpers\SettingsHelper::get('budget_threshold_percentage', 80);
        $newPercentage = (($totalSpent + $amount) / $project->project_cost) * 100;

        return [
            'allowed' => true,
            'message' => $newPercentage >= (float) $threshold
                ? 'Warning: Budget threshold of ' . $threshold . '% will be reached'
                : 'Budget check passed',
        ];
    }

    // ===== Integration: Staff Leave Check =====

    public function checkStaffOnLeaveAndReassign(Project $project): array
    {
        $alerts = [];
        $teamIds = $project->staff()->pluck('staffid')->toArray();
        $onLeave = StaffLeave::whereIn('staffid', $teamIds)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();

        foreach ($onLeave as $leave) {
            $staffId = $leave->staffid;
            $assignedTasks = Task::where('rel_id', $project->id)
                ->where('rel_type', 'project')
                ->whereHas('assignedStaff', fn($q) => $q->where('staffid', $staffId))
                ->count();

            if ($assignedTasks > 0) {
                $alerts[] = [
                    'staff_id' => $staffId,
                    'staff_name' => $leave->staff->firstname . ' ' . $leave->staff->lastname ?? 'Unknown',
                    'leave_until' => $leave->end_date->format('Y-m-d'),
                    'assigned_tasks' => $assignedTasks,
                    'project_id' => $project->id,
                ];
            }
        }

        return $alerts;
    }

    // ===== Integration: Payroll Hours =====

    public function syncBillableHoursToPayroll(Project $project, int $staffId, int $month, int $year): array
    {
        $timers = TaskTimer::where('staff_id', $staffId)
            ->whereHas('task', fn($q) => $q->where('rel_id', $project->id)->where('rel_type', 'project'))
            ->whereMonth('start_time', $month)
            ->whereYear('start_time', $year)
            ->whereNotNull('end_time')
            ->where('is_billable', true)
            ->get();

        $totalSeconds = $timers->sum(function ($timer) {
            return $timer->start_time->diffInSeconds($timer->end_time);
        });

        $totalHours = round($totalSeconds / 3600, 2);

        return [
            'staff_id' => $staffId,
            'project_id' => $project->id,
            'month' => $month,
            'year' => $year,
            'total_billable_hours' => $totalHours,
            'total_entries' => $timers->count(),
            'timers' => $timers,
        ];
    }

    // ===== Integration: GDPR Export =====

    public function exportProjectData(Project $project): array
    {
        $project->load([
            'client', 'staff', 'tasks', 'tasks.assignedStaff',
            'invoices', 'expenses', 'milestones', 'files',
            'activities', 'notes', 'discussions', 'tickets',
        ]);

        return [
            'project' => $project->toArray(),
            'tasks' => $project->tasks->toArray(),
            'invoices' => $project->invoices->toArray(),
            'expenses' => $project->expenses->toArray(),
            'milestones' => $project->milestones->toArray(),
            'files' => $project->files->toArray(),
            'activities' => $project->activities->toArray(),
            'notes' => $project->notes->toArray(),
            'discussions' => $project->discussions->toArray(),
            'tickets' => $project->tickets->toArray(),
            'team_members' => $project->staff->toArray(),
            'client' => $project->client ? $project->client->toArray() : null,
        ];
    }

    // ===== Integration: GDPR Deletion =====

    public function deleteProjectData(Project $project): bool
    {
        $project->update([
            'name' => '[Deleted Project #' . $project->id . ']',
            'description' => null,
            'clientid' => null,
        ]);

        $project->staff()->detach();
        $project->tasks()->each(function ($task) {
            $task->assignedStaff()->detach();
            $task->followers()->detach();
            $task->update(['name' => '[Deleted Task]', 'description' => null]);
        });

        return true;
    }

    private function notifyStaffExceptSelf(Project $project, string $description, ?string $notificationType = null, ?string $groupingKey = null): void
    {
        $staffIds = $project->staff()->pluck('staffid')->toArray();
        $exclude = [(int)Auth::id()];
        $recipients = array_values(array_diff(array_unique($staffIds), $exclude));
        if (!empty($recipients)) {
            Notification::createForMultipleStaff(
                $recipients,
                $description,
                route('admin.projects.show', $project->id),
                notificationType: $notificationType,
                groupingKey: $groupingKey,
            );
        }
    }
}
