<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Staff;
use App\Models\Milestone;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\Notification;
use App\Models\Estimate;
use App\Models\Proposal;
use App\Models\Lead;
use App\Models\Ticket;
use App\Models\Client;
use App\Models\File;
use App\Events\TaskCompleted;
use App\Services\TaskService;
use App\Services\EmailService;
use App\Services\FileService;
use App\Services\ProjectNotificationService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    protected $taskService;
    protected $emailService;
    protected $projectNotification;

    public function __construct(TaskService $taskService, EmailService $emailService, ProjectNotificationService $projectNotification)
    {
        $this->taskService = $taskService;
        $this->emailService = $emailService;
        $this->projectNotification = $projectNotification;
    }

    public function storeAttachment(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        if (!Auth::user()->can('tasks_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file' => 'required|file|max:25600|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv,txt,zip,rar,mp4,avi,mov,mp3,wav',
        ]);

        try {
            $fileService = app(FileService::class);
            $uploaded = $fileService->upload($request->file('file'), 'task');

            File::create([
                'rel_id' => $task->id,
                'rel_type' => 'task',
                'file_name' => $uploaded['file_name'],
                'original_file_name' => $uploaded['original_file_name'],
                'filetype' => $uploaded['filetype'],
                'staffid' => Auth::id(),
                'dateadded' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'reload' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyAttachment($fileId)
    {
        $file = File::where('rel_type', 'task')->findOrFail($fileId);
        if (!Auth::user()->can('tasks_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $fileService = app(FileService::class);
            $fileService->delete($file->file_name);
            $file->delete();

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully',
                'reload' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        if (!Auth::user()->can('tasks_view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Task::query()->with(['creator', 'milestoneRelation', 'relatable', 'assignedStaff']);

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('rel_type')) {
            $query->where('rel_type', $request->rel_type);
        }

        if ($request->filled('assigned_to')) {
            $query->whereHas('assignedStaff', fn($q) => $q->where('staffid', $request->assigned_to));
        }

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'oldest' => $query->orderBy('id', 'asc'),
            'due_date_asc' => $query->orderBy('duedate', 'asc'),
            'due_date_desc' => $query->orderBy('duedate', 'desc'),
            'priority_high' => $query->orderBy('priority', 'desc'),
            'priority_low' => $query->orderBy('priority', 'asc'),
            default => $query->orderBy('id', 'desc'),
        };

        $total = $query->count();
        $tasks = $query->orderBy('milestone', 'asc')->paginate(15)->withQueryString();

        $groupedTasks = $tasks->groupBy(function($task) {
            if ($task->milestone && $task->milestoneRelation) {
                return 'milestone_' . $task->milestone;
            }
            return 'no_milestone';
        });

        $milestones = Milestone::with('project')->get()->keyBy('id');
        $allStaff = Staff::all();

        return view('admin.tasks.index', compact('tasks', 'total', 'groupedTasks', 'milestones', 'allStaff'));
    }

    public function kanban(Request $request)
    {
        if (!Auth::user()->can('tasks_view')) {
            abort(403, 'Unauthorized action.');
        }
        
        $projectId = $request->get('project');
        $relType = $request->get('rel_type');
        $project = null;
        $milestones = collect();
        
        $query = Task::with(['creator', 'relatable', 'assignedStaff', 'timers', 'checklistItems', 'comments', 'attachments'])
            ->withCount('comments');
        
        if ($projectId) {
            $project = Project::findOrFail($projectId);
            $milestones = Milestone::where('project_id', $projectId)->get();
            $query->where('rel_type', 'project')->where('rel_id', $projectId);
        } elseif ($relType) {
            $query->where('rel_type', $relType);
        }
        
        $tasks = $query
            ->orderByRaw("CASE WHEN status IS NULL OR status = '' THEN 1 ELSE 0 END")
            ->orderBy('status')
            ->get()
            ->groupBy(function($task) {
                $statusMap = [Task::STATUS_BACKLOG => 'backlog', Task::STATUS_PENDING => 'pending', Task::STATUS_IN_PROGRESS => 'in_progress', Task::STATUS_COMPLETED => 'completed'];
                return $statusMap[(int)$task->status] ?? 'backlog';
            });
        
        $projects = Project::orderBy('name')->get();
        
        // Ensure all column keys exist even if empty
        $tasks = collect([
            'backlog' => collect($tasks->get('backlog', [])),
            'pending' => collect($tasks->get('pending', [])),
            'in_progress' => collect($tasks->get('in_progress', [])),
            'completed' => collect($tasks->get('completed', [])),
        ]);
        
        // Calculate progress and timers for each task
        $tasks->each(function ($taskGroup) {
            $taskGroup->each(function ($task) {
                $completedChecklists = $task->checklistItems->where('finished', 1)->count();
                $totalChecklists = $task->checklistItems->count();
                $task->progress = $totalChecklists > 0 ? round(($completedChecklists / $totalChecklists) * 100) : 0;
                
                $activeTimer = $task->timers->whereNull('end_time')->first();
                $task->timer_active = !is_null($activeTimer);
                $task->total_hours_today = $task->timers
                    ->filter(fn($t) => $t->start_time && $t->start_time->isToday())
                    ->sum(fn($t) => $t->end_time ? $t->start_time->diffInHours($t->end_time) : 0);
                
                $task->attachments_count = $task->attachments->count();
            });
        });
        
        return view('admin.tasks.kanban', compact('tasks', 'project', 'projects', 'milestones'));
    }

    public function create()
    {
        if (!Auth::user()->can('tasks_create')) {
            abort(403, 'Unauthorized action.');
        }

        $staff = Staff::all();
        $milestones = Milestone::all();
        $projects = Project::with('staff')->get();
        $invoices = Invoice::all();
        $estimates = Estimate::all();
        $proposals = Proposal::all();
        $leads = Lead::select('id', 'name')->get();
        $tickets = Ticket::select('ticketid', 'subject')->get();
        $clients = Client::select('userid', 'company')->get();

        return view('admin.tasks.create', compact('staff', 'milestones', 'projects', 'invoices', 'estimates', 'proposals', 'leads', 'tickets', 'clients'));
    }

    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();

        $tags = $request->tags;
        if (is_array($tags)) {
            $validated['tags'] = implode(',', $tags);
        }

        $taskData = $this->taskService->prepareTaskData($validated, $request);
        $task = Task::create($taskData);

        $staffIds = $request->filled('assigned_staff') ? (is_array($request->assigned_staff) ? $request->assigned_staff : explode(',', $request->assigned_staff)) : [];
        if (!empty($staffIds)) {
            $this->taskService->syncAssignedStaff($task, $staffIds);
            $this->taskService->notifyAssignedStaff($task, $staffIds);
        }

        if ($request->filled('checklist_template_id')) {
            $this->taskService->applyChecklistTemplate($task, $request->checklist_template_id);
        }

        if ($request->filled('followers')) {
            $followerIds = is_array($request->followers) ? $request->followers : explode(',', $request->followers);
            $task->followers()->sync(array_map('trim', $followerIds));
        }

        $this->taskService->logProjectActivity($task, 'project_activity_task_created');

        if ($request->wantsJson() || $request->ajax()) {
            $createAnother = $request->boolean('create_another');
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!',
                'task' => $task,
                'reload' => true,
                'create_another' => $createAnother,
                'pane_refresh_url' => route('admin.tasks.pane.show', $task->id),
            ]);
        }

        if ($request->filled('referer')) {
            return redirect($request->referer)->with('success', 'Task created successfully!');
        }

        return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('tasks_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $task = Task::with('assignedStaff')->findOrFail($id);
        $staff = Staff::all();
        $milestones = Milestone::all();
        $projects = Project::with('staff')->get();
        $invoices = Invoice::all();
        $estimates = Estimate::all();
        $proposals = Proposal::all();
        $leads = Lead::select('id', 'name')->get();
        $tickets = Ticket::select('ticketid', 'subject')->get();
        $clients = Client::select('userid', 'company')->get();

        return view('admin.tasks.edit', compact('task', 'staff', 'milestones', 'projects', 'invoices', 'estimates', 'proposals', 'leads', 'tickets', 'clients'));
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $validated = $request->validated();

        $task = Task::findOrFail($id);

        $oldTaskStatus = $task->status;

        $tags = $request->tags;
        if (is_array($tags)) {
            $validated['tags'] = implode(',', $tags);
        } elseif (is_string($tags)) {
            $validated['tags'] = $tags;
        }

        $request->merge(['tags' => $validated['tags'] ?? $task->tags]);

        $updateData = $this->taskService->prepareUpdateData($request);
        $task->update($updateData);

        $staffIds = $request->filled('assigned_staff') ? (is_array($request->assigned_staff) ? $request->assigned_staff : explode(',', $request->assigned_staff)) : [];
        if (!empty($staffIds)) {
            $this->taskService->syncAssignedStaff($task, $staffIds);
        }

        if ($request->filled('followers')) {
            $task->followers()->sync(is_array($request->followers) ? $request->followers : explode(',', $request->followers));
        }

        $this->taskService->handleTaskCompleted($task, (int)$oldTaskStatus);
        $this->taskService->updateDateFinished($task, (int)$oldTaskStatus, (int)$task->status);

        $this->taskService->logProjectActivity($task, 'project_activity_task_updated', [
            'old_status' => $oldTaskStatus,
            'new_status' => $task->status,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!',
                'task' => $task,
                'reload' => true,
                'pane_refresh_url' => route('admin.tasks.pane.show', $task->id)
            ]);
        }

        return redirect()->route('admin.tasks.index')->with('success', 'Task updated successfully!');
    }

    public function show($id)
    {
        if (!Auth::user()->can('tasks_view')) {
            abort(403, 'Unauthorized action.');
        }

        $task = Task::with([
            'creator',
            'milestoneRelation',
            'relatable',
            'assignedStaff',
            'followers',
            'comments.addedFrom',
            'timers.staff',
            'checklistItems',
            'attachments.staff',
        ])->findOrFail($id);

        $fileService = app(FileService::class);
        foreach ($task->attachments as $att) {
            $att->fileurl = $fileService->url($att->file_name);
        }

        \Log::info('Task comments count: ' . $task->comments->count());

        return view('admin.tasks.show', compact('task'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('tasks_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $task = Task::findOrFail($id);

        DB::transaction(function () use ($task) {
            DB::table('tbltask_assigned')->where('taskid', $task->id)->delete();
            DB::table('tbltask_followers')->where('taskid', $task->id)->delete();
            DB::table('tbltask_comments')->where('taskid', $task->id)->delete();
            DB::table('tbltaskstimers')->where('task_id', $task->id)->delete();
            DB::table('tbltask_checklist_items')->where('taskid', $task->id)->delete();

            $files = File::where('rel_type', 'task')->where('rel_id', $task->id)->get();
            $fileService = app(FileService::class);
            foreach ($files as $file) {
                $fileService->delete($file->file_name);
                $file->delete();
            }

            $task->delete();
        });

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Task deleted successfully']);
        }

        return redirect()->route('admin.tasks.index')->with('success', 'Task deleted successfully!');
    }

    /**
     * Quick view modal content via AJAX
     */
    public function quickView($id)
    {
        if (!Auth::user()->can('tasks_view')) {
            abort(403, 'Unauthorized action.');
        }

        $task = Task::with(['creator', 'milestoneRelation', 'relatable', 'assignedStaff', 'comments', 'timers'])->findOrFail($id);
        
        $html = view('admin.tasks.partials.quick-view', compact('task'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Update task status via AJAX (drag & drop)
     */
    public function updateStatus(Request $request, $id)
    {
        if (!Auth::user()->can('tasks_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $task = Task::findOrFail($id);
        $oldStatus = $task->status;
        $newStatus = $request->input('status');

        $statusMap = [
            'backlog' => 0,
            'pending' => 1,
            'in_progress' => 2,
            'completed' => 3
        ];

        $task->status = $statusMap[$newStatus] ?? $newStatus;
        $task->save();

        $this->taskService->updateDateFinished($task, (int)$oldStatus, (int)$task->status);

        if ((int)$task->status === Task::STATUS_COMPLETED && (int)$oldStatus !== Task::STATUS_COMPLETED) {
            TaskCompleted::dispatch($task);
        }

        if ($task->rel_type === 'project' && $task->rel_id) {
            ProjectActivity::create([
                'project_id' => $task->rel_id,
                'staff_id' => Auth::id(),
                'description_key' => 'project_activity_task_status_updated',
                'additional_data' => json_encode([
                    'task_name' => $task->name,
                    'old_status' => $oldStatus,
                    'new_status' => $task->status,
                ]),
                'dateadded' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task status updated',
            'newStatus' => $newStatus
        ]);
    }

    /**
     * Update task priority via AJAX
     */
    public function updatePriority(Request $request, $id)
    {
        if (!Auth::user()->can('tasks_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['priority' => 'required|integer|in:1,2,3,4']);

        $task = Task::findOrFail($id);
        $task->priority = $request->priority;
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Priority updated successfully'
        ]);
    }

    /**
     * Toggle follow/unfollow for current user
     */
    public function toggleFollow(Request $request, $id)
    {
        if (!Auth::user()->can('tasks_view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $task = Task::findOrFail($id);
        $following = $this->taskService->toggleFollower($task, Auth::id());

        return response()->json([
            'success' => true,
            'message' => $following ? 'Now following this task' : 'No longer following this task',
            'following' => $following
        ]);
    }

    /**
     * Duplicate a task
     */
    public function duplicate($id)
    {
        if (!Auth::user()->can('tasks_create')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $task = Task::findOrFail($id);
        $newTask = $this->taskService->duplicateTask($task);

        return response()->json([
            'success' => true,
            'message' => 'Task duplicated successfully',
            'new_task_id' => $newTask->id
        ]);
    }

    /**
     * Quick create task via AJAX
     */
    public function quickCreate(Request $request)
    {
        if (!Auth::user()->can('tasks_create')) {
            abort(403, 'Unauthorized action.');
        }

        $token = $request->header('X-CSRF-TOKEN') ?: $request->input('_token');
        if ($token && $token !== csrf_token()) {
            return response()->json(['success' => false, 'message' => 'CSRF token mismatch'], 419);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'required|integer',
            'status' => 'required|integer',
            'duedate' => 'nullable|date'
        ]);

        $task = Task::create([
            'name' => $request->name,
            'priority' => $request->priority,
            'status' => $request->status,
            'duedate' => $request->duedate,
            'dateadded' => now(),
            'startdate' => now(),
            'addedfrom' => Auth::id(),
            'rel_id' => $request->rel_id ?: null, // Ensure rel_id and rel_type are captured
            'rel_type' => $request->rel_type ?: null,
        ]);

        $this->taskService->logProjectActivity($task, 'project_activity_task_quick_created');

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'task_id' => $task->id,
            'task' => $task->load('assignedStaff', 'relatable')
        ]);
    }

    /**
     * Assign staff to task
     */
    public function assignStaff(Request $request, $id)
    {
        if (!Auth::user()->can('tasks_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $task = Task::findOrFail($id);
        $staffIds = $request->input('staff_ids', []);

        // Sync staff assignments
        $task->assignedStaff()->sync($staffIds);

        return response()->json([
            'success' => true,
            'message' => 'Staff assigned successfully'
        ]);
    }
}
