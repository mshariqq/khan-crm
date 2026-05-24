<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Staff;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\TaskChecklistItem;
use App\Models\TaskChecklistTemplate;
use Illuminate\Support\Facades\Auth;

class TaskService extends BaseService
{
    protected EmailService $emailService;
    protected ProjectNotificationService $projectNotification;

    public function __construct(EmailService $emailService, ProjectNotificationService $projectNotification)
    {
        $this->emailService = $emailService;
        $this->projectNotification = $projectNotification;
    }

    public function prepareTaskData(array $validated, $request): array
    {
        return array_merge($validated, [
            'description' => $request->description,
            'startdate' => $request->startdate ?? now()->format('Y-m-d'),
            'duedate' => $request->duedate,
            'datefinished' => $request->datefinished,
            'rel_id' => $request->rel_id ?: null,
            'rel_type' => $request->rel_type ?: null,
            'milestone' => $request->milestone ?? 0,
            'billable' => $request->billable ?? 0,
            'billed' => $request->billed ?? 0,
            'hourly_rate' => $request->hourly_rate ?? 0,
            'is_public' => $request->is_public ?? 0,
            'visible_to_client' => $request->visible_to_client ?? 0,
            'recurring' => $request->recurring ?? 0,
            'recurring_type' => $request->recurring_type,
            'repeat_every' => $request->repeat_every,
            'cycles' => $request->cycles ?? 0,
            'total_cycles' => $request->total_cycles ?? 0,
            'custom_recurring' => $request->custom_recurring ?? 0,
            'dateadded' => now(),
            'addedfrom' => Auth::id(),
        ]);
    }

    public function prepareUpdateData($request): array
    {
        return array_merge($request->only([
            'name', 'description', 'priority', 'status', 'startdate', 'duedate', 'datefinished',
            'rel_id', 'rel_type', 'milestone', 'billable', 'billed', 'hourly_rate',
            'is_public', 'visible_to_client', 'recurring', 'recurring_type',
            'repeat_every', 'cycles', 'total_cycles', 'custom_recurring', 'tags'
        ]), [
            'rel_id' => $request->rel_id ?: null,
            'rel_type' => $request->rel_type ?: null,
            'milestone' => $request->milestone ?? 0,
            'billable' => $request->billable ?? 0,
            'billed' => $request->billed ?? 0,
            'hourly_rate' => $request->hourly_rate ?? 0,
            'is_public' => $request->is_public ?? 0,
            'visible_to_client' => $request->visible_to_client ?? 0,
            'recurring' => $request->recurring ?? 0,
            'cycles' => $request->cycles ?? 0,
            'total_cycles' => $request->total_cycles ?? 0,
        ]);
    }

    public function applyChecklistTemplate(Task $task, int $templateId): void
    {
        $template = TaskChecklistTemplate::find($templateId);
        if (!$template) return;

        TaskChecklistItem::create([
            'taskid' => $task->id,
            'description' => $template->description,
            'finished' => 0,
            'dateadded' => now(),
            'addedfrom' => Auth::id(),
            'list_order' => 1,
        ]);
    }

    public function syncAssignedStaff(Task $task, array $staffIds): void
    {
        $task->assignedStaff()->sync(array_map('trim', $staffIds));
    }

    public function notifyAssignedStaff(Task $task, array $staffIds): void
    {
        $taskPriority = $task->priority == 4 ? 'Urgent' : ($task->priority == 3 ? 'High' : ($task->priority == 2 ? 'Medium' : 'Low'));

        if ($task->rel_type === 'project' && $task->rel_id) {
            foreach ($staffIds as $staffId) {
                $staffMember = Staff::find($staffId);
                if ($staffMember) {
                    $this->projectNotification->sendToOneStaff('task-assigned', $task->rel_id, $staffMember, [
                        'task_name' => $task->name,
                        'task_priority' => $taskPriority,
                        'task_duedate' => $task->duedate ? \Carbon\Carbon::parse($task->duedate)->format('d M, Y') : '',
                        'task_link' => route('admin.tasks.show', $task->id),
                    ]);
                    Notification::createForStaff(
                        $staffMember->staffid,
                        Auth::user()->full_name . ' assigned task ' . e($task->name) . ' to you',
                        route('admin.tasks.show', $task->id),
                        notificationType: 'task_assigned',
                        groupingKey: 'task_assigned_' . $task->id
                    );
                }
            }
        } else {
            foreach ($staffIds as $staffId) {
                $staffMember = Staff::find($staffId);
                if ($staffMember && $staffMember->email) {
                    $this->emailService->send($staffMember->email, 'task-assigned', [
                        'staff_firstname' => $staffMember->firstname,
                        'task_name' => $task->name,
                        'task_startdate' => \Carbon\Carbon::parse($task->startdate)->format('d M, Y'),
                        'task_duedate' => $task->duedate ? \Carbon\Carbon::parse($task->duedate)->format('d M, Y') : 'N/A',
                        'task_priority' => $taskPriority,
                        'task_link' => route('admin.tasks.show', $task->id),
                    ]);
                }
            }
        }
    }

    public function logProjectActivity(Task $task, string $descriptionKey, array $additionalData = []): void
    {
        if ($task->rel_type === 'project' && $task->rel_id) {
            ProjectActivity::create([
                'project_id' => $task->rel_id,
                'staff_id' => Auth::id(),
                'description_key' => $descriptionKey,
                'additional_data' => json_encode(array_merge(['task_name' => $task->name], $additionalData)),
                'dateadded' => now(),
            ]);
        }
    }

    public function toggleFollower(Task $task, int $staffId): bool
    {
        $existing = $task->followers()->where('staffid', $staffId)->exists();
        if ($existing) {
            $task->followers()->detach($staffId);
            return false;
        } else {
            $task->followers()->attach($staffId);
            return true;
        }
    }

    public function duplicateTask(Task $task): Task
    {
        $newTask = $task->replicate();
        $newTask->name = $task->name . ' (Copy)';
        $newTask->dateadded = now();
        $newTask->addedfrom = Auth::id();
        $newTask->datefinished = null;
        $newTask->save();

        $staffIds = $task->assignedStaff()->pluck('staffid')->toArray();
        if (!empty($staffIds)) {
            $newTask->assignedStaff()->sync($staffIds);
        }

        return $newTask;
    }

    public function updateDateFinished(Task $task, int $oldStatus, int $newStatus): void
    {
        if ($oldStatus !== Task::STATUS_COMPLETED && $newStatus === Task::STATUS_COMPLETED) {
            $task->datefinished = now();
            $task->save();
        } elseif ($oldStatus === Task::STATUS_COMPLETED && $newStatus !== Task::STATUS_COMPLETED) {
            $task->datefinished = null;
            $task->save();
        }
    }

    public function handleTaskCompleted(Task $task, int $oldStatus): void
    {
        if ($task->rel_type === 'project' && $task->rel_id && $oldStatus !== Task::STATUS_COMPLETED && (int)$task->status === Task::STATUS_COMPLETED) {
            $this->projectNotification->send('task-completed', $task->rel_id, [
                'task_name' => $task->name,
                'task_link' => route('admin.tasks.show', $task->id),
            ]);

            $project = Project::find($task->rel_id);
            if ($project) {
                $staffIds = $project->staff()->pluck('staffid')->toArray();
                $exclude = [(int)Auth::id()];
                $recipients = array_values(array_diff($staffIds, $exclude));
                if (!empty($recipients)) {
                    Notification::createForMultipleStaff(
                        $recipients,
                        Auth::user()->full_name . ' completed task ' . e($task->name) . ' in ' . e($project->name),
                        route('admin.tasks.show', $task->id),
                        notificationType: 'task_completed',
                        groupingKey: 'task_completed_' . $task->id
                    );
                }
            }
        }
    }
}
