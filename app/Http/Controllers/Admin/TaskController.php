<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Staff;
use App\Models\Milestone;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\Estimate;
use App\Models\Proposal;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index(Request $request)
    {
        if (!Auth::user()->can('tasks_view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Task::query()->with(['creator', 'milestoneRelation', 'project', 'assignedStaff']);

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $total = $query->count();
        $tasks = $query->orderBy('milestone', 'asc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $groupedTasks = $tasks->groupBy(function($task) {
            if ($task->milestone && $task->milestoneRelation) {
                return 'milestone_' . $task->milestone;
            }
            return 'no_milestone';
        });

        $milestones = Milestone::with('project')->get()->keyBy('id');

        return view('admin.tasks.index', compact('tasks', 'total', 'groupedTasks', 'milestones'));
    }

    public function kanban(Request $request)
    {
        if (!Auth::user()->can('tasks_view')) {
            abort(403, 'Unauthorized action.');
        }
        
        $projectId = $request->get('project');
        $project = null;
        $milestones = collect();
        $tasks = collect();
        
        if ($projectId) {
            $project = Project::findOrFail($projectId);
            $milestones = Milestone::where('project_id', $projectId)->get();
            
            // Get tasks for this project grouped by milestone
            $tasks = Task::with(['creator', 'assignedStaff', 'timers', 'checklistItems', 'comments'])
                ->withCount('comments')
                ->where('rel_type', 'project')
                ->where('rel_id', $projectId)
                ->orderByRaw("CASE WHEN milestone IS NULL OR milestone = '' THEN 1 ELSE 0 END")
                ->orderBy('milestone')
                ->get()
                ->groupBy(function($task) {
                    if ($task->milestone) {
                        return 'milestone_' . $task->milestone;
                    }
                    return 'unassigned';
                });
        }
        
        // Also get available projects for the project selector
        $projects = Project::orderBy('name')->get();
        
        // Calculate progress and timers for each task
        $tasks->each(function ($taskGroup) {
            $taskGroup->each(function ($task) {
                // Simple progress calculation based on checklist completion
                $completedChecklists = $task->checklistItems()->where('finished', 1)->count();
                $totalChecklists = $task->checklistItems()->count();
                $task->progress = $totalChecklists > 0 ? round(($completedChecklists / $totalChecklists) * 100) : 0;
                
                // Check if timer is active
                $activeTimer = $task->timers()->where('end_time', null)->first();
                $task->timer_active = !is_null($activeTimer);
                $task->total_hours_today = $task->timers()->whereDate('start_time', today())->sum('total_time_hours');
                
                // Count attachments (if attachment model exists)
                $task->attachments_count = 0; // Placeholder - update if attachment model exists
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

        return view('admin.tasks.create', compact('staff', 'milestones', 'projects', 'invoices', 'estimates', 'proposals'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('tasks_create')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'required|integer',
            'status' => 'required|integer',
        ]);

        $task = Task::create(array_merge($validated, [
            'description' => $request->description,
            'startdate' => $request->startdate ?? now()->format('Y-m-d'),
            'duedate' => $request->duedate,
            'datefinished' => $request->datefinished,
            'rel_id' => $request->rel_id ?: null,
            'rel_type' => $request->rel_type ?: null,
            'milestone' => $request->milestone ?: null,
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
            'custom_recurring' => $request->custom_recurring,
            'dateadded' => now(),
            'addedfrom' => Auth::id(),
        ]));

        if ($request->has('assigned_staff')) {
            $task->assignedStaff()->sync($request->assigned_staff);

            // Notify assigned staff
            foreach ($request->assigned_staff as $staffId) {
                $staffMember = Staff::find($staffId);
                if ($staffMember && $staffMember->email) {
                    $this->emailService->send($staffMember->email, 'task-assigned', [
                        'staff_firstname' => $staffMember->firstname,
                        'task_name' => $task->name,
                        'task_startdate' => \Carbon\Carbon::parse($task->startdate)->format('d M, Y'),
                        'task_duedate' => $task->duedate ? \Carbon\Carbon::parse($task->duedate)->format('d M, Y') : 'N/A',
                        'task_priority' => $task->priority == 4 ? 'Urgent' : ($task->priority == 3 ? 'High' : ($task->priority == 2 ? 'Medium' : 'Low')),
                        'task_link' => config('app.url') . '/admin/tasks/' . $task->id,
                    ]);
                }
            }
        }

        if ($task->rel_type === 'project' && $task->rel_id) {
            ProjectActivity::create([
                'project_id' => $task->rel_id,
                'staff_id' => Auth::id(),
                'description_key' => 'project_activity_task_created',
                'additional_data' => json_encode(['task_name' => $task->name]),
                'dateadded' => now(),
            ]);
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

        return view('admin.tasks.edit', compact('task', 'staff', 'milestones', 'projects', 'invoices', 'estimates', 'proposals'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('tasks_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'required|integer',
            'status' => 'required|integer',
        ]);

        $task = Task::findOrFail($id);
        
        // Capture old values for logging if needed later
        $oldTaskName = $task->name;
        $oldTaskStatus = $task->status;

        $task->update(array_merge($request->only([
            'name', 'description', 'priority', 'status', 'startdate', 'duedate', 'datefinished',
            'rel_id', 'rel_type', 'milestone', 'billable', 'billed', 'hourly_rate',
            'is_public', 'visible_to_client', 'recurring', 'recurring_type',
            'repeat_every', 'cycles', 'total_cycles', 'custom_recurring'
        ]), [
            'rel_id' => $request->rel_id ?: null,
            'rel_type' => $request->rel_type ?: null,
            'milestone' => $request->milestone ?: null,
            'billable' => $request->billable ?? 0,
            'billed' => $request->billed ?? 0,
            'hourly_rate' => $request->hourly_rate ?? 0,
            'is_public' => $request->is_public ?? 0,
            'visible_to_client' => $request->visible_to_client ?? 0,
            'recurring' => $request->recurring ?? 0,
            'cycles' => $request->cycles ?? 0,
            'total_cycles' => $request->total_cycles ?? 0,
        ]));

        if ($request->has('assigned_staff')) {
            $task->assignedStaff()->sync($request->assigned_staff);
        }

        // Log activity for task update
        if ($task->rel_type === 'project' && $task->rel_id) {
            $changes = [];
            if ($oldTaskName !== $task->name) {
                $changes['old_name'] = $oldTaskName;
                $changes['new_name'] = $task->name;
            }
            if ($oldTaskStatus !== $task->status) {
                $changes['old_status'] = $oldTaskStatus;
                $changes['new_status'] = $task->status;
            }

            $descriptionKey = 'project_activity_task_updated';
            if (!empty($changes)) {
                 $descriptionKey = 'project_activity_task_updated_details'; // More specific key if changes are present
            }

            ProjectActivity::create([
                'project_id' => $task->rel_id,
                'staff_id' => Auth::id(),
                'description_key' => $descriptionKey,
                'additional_data' => json_encode(['task_name' => $task->name, 'changes' => $changes]),
                'dateadded' => now(),
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
            'assignedStaff',
            'followers',
            'comments.addedFrom',
            'timers.staff',
            'checklistItems'
        ])->findOrFail($id);

        \Log::info('Task comments count: ' . $task->comments->count());

        return view('admin.tasks.show', compact('task'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('tasks_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $task = Task::findOrFail($id);
        $task->delete();

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

        $task = Task::with(['creator', 'milestoneRelation', 'assignedStaff', 'comments', 'timers'])->findOrFail($id);
        
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

        // Log activity (optional)
        // Activity::log('updated task status', 'task', $task->id);

        // Log activity for task status update
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
     * Quick create task via AJAX
     */
    public function quickCreate(Request $request)
    {
        if (!Auth::user()->can('tasks_create')) {
            abort(403, 'Unauthorized action.');
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

        if ($task->rel_type === 'project' && $task->rel_id) {
            ProjectActivity::create([
                'project_id' => $task->rel_id,
                'staff_id' => Auth::id(),
                'description_key' => 'project_activity_task_quick_created',
                'additional_data' => json_encode(['task_name' => $task->name]),
                'dateadded' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'task_id' => $task->id,
            'task' => $task->load('assignedStaff', 'project')
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
