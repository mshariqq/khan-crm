<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectActivity; // Add this line
use App\Models\Client;
use App\Models\Staff;
use App\Models\Note;
use App\Models\Notification;
use App\Models\ProjectDiscussion;
use App\Models\ProjectDiscussionComment;
use App\Services\ProjectService;
use App\Services\EmailService;
use App\Services\ProjectNotificationService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    protected $projectService;
    protected $emailService;
    protected $projectNotification;

    public function __construct(ProjectService $projectService, EmailService $emailService, ProjectNotificationService $projectNotification)
    {
        $this->projectService = $projectService;
        $this->emailService = $emailService;
        $this->projectNotification = $projectNotification;
    }

    public function index(Request $request)
    {
        if (!Auth::user()->can('projects_view')) abort(403);

        $query = Project::with(['client']);

        if ($request->filled('client')) $query->where('clientid', $request->client);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where('name', 'like', '%'.$request->search.'%');

        $total = $query->count();
        $projects = $query->paginate(15)->withQueryString();
        $clients = Client::all();
        $activeCount = Project::where('status', Project::STATUS_ACTIVE)->count();
        $completedCount = Project::where('status', Project::STATUS_COMPLETED)->count();
        $onHoldCount = Project::where('status', Project::STATUS_ON_HOLD)->count();

        return view('admin.projects.index', compact('projects', 'total', 'clients', 'activeCount', 'completedCount', 'onHoldCount'));
    }

    public function create()
    {
        if (!Auth::user()->can('projects_create')) abort(403);
        $clients = Client::all();
        $staff = Staff::all();
        return view('admin.projects.create', compact('clients', 'staff'));
    }

    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'clientid' => $request->clientid,
            'billing_type' => $request->billing_type ?? 1,
            'start_date' => $request->start_date ?? now(),
            'deadline' => $request->deadline,
            'project_cost' => $request->project_cost,
            'project_rate_per_hour' => $request->project_rate_per_hour,
            'estimated_hours' => $request->estimated_hours,
            'project_created' => now(),
            'addedfrom' => Auth::id(),
            'progress' => 0,
        ]);

        if ($request->filled('staff_ids')) {
            $project->staff()->sync($request->staff_ids);
        }

        $this->projectNotification->send('project-created', $project->id);

        $staffIds = $project->staff()->pluck('staffid')->toArray();
        $creatorId = (int)($project->addedfrom ?? Auth::id());
        $allRecipients = array_unique(array_merge($staffIds, [$creatorId]));
        $desc = Auth::user()->full_name . ' created project <strong>' . e($project->name) . '</strong>';
        $link = route('admin.projects.show', $project->id);
        Notification::createForMultipleStaff($allRecipients, $desc, $link);

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('projects_edit')) abort(403);
        $project = Project::with('staff')->findOrFail($id);
        $clients = Client::all();
        $staff = Staff::all();
        return view('admin.projects.edit', compact('project', 'clients', 'staff'));
    }

    public function update(UpdateProjectRequest $request, $id)
    {
        $validated = $request->validated();

        $project = Project::findOrFail($id);

        $oldAttributes = $project->getOriginal();

        $project->update($request->only([
            'name', 'description', 'status', 'clientid', 'billing_type',
            'start_date', 'deadline', 'project_cost', 'project_rate_per_hour', 'estimated_hours'
        ]));

        $notified = false;

        if ($request->has('staff_ids')) {
            $this->projectService->syncStaffAndNotify($project, $request->staff_ids ?? []);
            $notified = true;
        }

        $oldStatus = (int)($oldAttributes['status'] ?? $project->status);
        if ($oldStatus !== (int)$project->status) {
            $notified = $this->projectService->handleStatusChange($project, $oldStatus, (int)$project->status) || $notified;
        }

        if (!$notified) {
            $oldDeadline = $oldAttributes['deadline'] ?? null;
            $notified = $this->projectService->handleDeadlineChange($project, $oldDeadline, $project->deadline) || $notified;
        }

        if (!$notified) {
            $this->projectService->notifyGeneralUpdate($project);
        }

        $this->projectService->logProjectActivity($project);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully!');
    }

    public function show($id)
    {
        if (!Auth::user()->can('projects_view')) abort(403);
        
        $project = Project::with([
            'client', 
            'staff', 
            'tasks.creator', 
            'tasks.assignedStaff',
            'tasks.milestoneRelation',
            'tasks.comments',
            'tasks.timers',
            'tasks.checklistItems',
            'invoices', 
            'estimates', 
            'expenses',
            'milestones',
            'files',
            'notes',
            'discussions',
            'activities'
        ])->findOrFail($id);

        $default_tab = \App\Helpers\SettingsHelper::get('default_project_tab', 'overview');
        
        return view('admin.projects.show', compact('project', 'default_tab'));
    }

    // Dashboard view for a single project – shows key metrics
    public function dashboard($id)
    {
        if (!Auth::user()->can('projects_view')) abort(403);
        $project = Project::with(['client', 'tasks', 'invoices', 'expenses'])->findOrFail($id);
        return view('admin.projects.dashboard', compact('project'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('projects_delete')) abort(403);
        Project::findOrFail($id)->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully!');
    }

    public function notesCreate($projectId)
    {
        if (!Auth::user()->can('projects_edit')) abort(403);
        $project = Project::findOrFail($projectId);
        
        // Redirect to project show with notes tab active
        return redirect()->route('admin.projects.show', $project) . '#notes';
    }

    public function notesStore(Request $request, $projectId)
    {
        try {
            if (!Auth::user()->can('projects_edit')) {
                return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
            }
            
            $request->validate([
                'description' => 'required|string',
                'title' => 'nullable|string|max:255',
                'rel_type' => 'required|string',
                'rel_id' => 'required|integer'
            ]);

            Note::create([
                'rel_id' => $request->rel_id,
                'rel_type' => $request->rel_type,
                'title' => $request->title,
                            'description' => $request->description,
                            'addedfrom' => Auth::id(),
                            'dateadded' => now()
                        ]);

        // Log activity for new note
        ProjectActivity::create([
            'project_id' => $projectId,
            'staff_id' => Auth::id(),
            'description_key' => 'project_activity_note_created',
            'additional_data' => json_encode(['note_title' => $request->title]),
            'dateadded' => now(),
        ]);

        return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function notesUpdate(Request $request, $projectId, $noteId)
    {
        try {
            if (!Auth::user()->can('projects_edit')) {
                return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
            }
            
            $request->validate([
                'description' => 'required|string',
                'title' => 'nullable|string|max:255'
            ]);

            $note = Note::findOrFail($noteId);
            $note->update([
                'title' => $request->title,
                'description' => $request->description
            ]);

        // Log activity for note update
        ProjectActivity::create([
            'project_id' => $projectId,
            'staff_id' => Auth::id(),
            'description_key' => 'project_activity_note_updated',
            'additional_data' => json_encode(['note_title' => $request->title]),
            'dateadded' => now(),
        ]);

        return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function discussions($projectId)
    {
        if (!Auth::user()->can('projects_view')) abort(403);
        $project = Project::with(['discussions', 'discussions.staff'])->findOrFail($projectId);
        
        return view('admin.projects.discussions', compact('project'));
    }

    public function discussionsCreate($projectId)
    {
        if (!Auth::user()->can('projects_create')) abort(403);
        $project = Project::findOrFail($projectId);
        
        return view('admin.projects.discussions-create', compact('project'));
    }

    public function storeDiscussion(Request $request, $projectId)
    {
        if (!Auth::user()->can('projects_create')) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
            }
            abort(403);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::findOrFail($projectId);

        $discussion = ProjectDiscussion::create([
            'project_id' => $project->id,
            'subject' => $request->subject,
            'description' => $request->description ?? '',
            'show_to_customer' => $request->boolean('show_to_customer'),
            'datecreated' => now(),
            'staff_id' => Auth::id(),
        ]);

        // Log activity for new discussion creation
        ProjectActivity::create([
            'project_id' => $project->id,
            'staff_id' => Auth::id(),
            'description_key' => 'project_activity_discussion_created',
            'additional_data' => json_encode(['discussion_subject' => $discussion->subject]),
            'dateadded' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.projects.discussions', $project)->with('success', 'Discussion created successfully!');
    }

    public function addDiscussionComment(Request $request, $projectId, $discussionId)
    {
        if (!Auth::user()->can('projects_view')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $project = Project::findOrFail($projectId);
        $discussion = $project->discussions()->findOrFail($discussionId);

        $comment = ProjectDiscussionComment::create([
            'discussion_id' => $discussion->id,
            'discussion_type' => 'discussion',
            'content' => $request->content,
            'staff_id' => Auth::id(),
            'created' => now(),
        ]);

        $discussion->update(['last_activity' => now()]);

        // Log activity for new discussion comment
        ProjectActivity::create([
            'project_id' => $project->id,
            'staff_id' => Auth::id(),
            'description_key' => 'project_activity_discussion_commented',
            'additional_data' => json_encode([
                'discussion_subject' => $discussion->subject,
                'comment_content' => substr($comment->content, 0, 100) . (strlen($comment->content) > 100 ? '...' : ''),
            ]),
            'dateadded' => now(),
        ]);

        $comment->load('staff');

        return response()->json(['success' => true, 'comment' => $comment]);
    }

    public function timesheets($projectId)
    {
        if (!Auth::user()->can('projects_view')) abort(403);
        $project = Project::with(['tasks', 'tasks.timers', 'tasks.timers.staff'])->findOrFail($projectId);
        
        return view('admin.projects.timesheets', compact('project'));
    }

    public function gantt($projectId)
    {
        if (!Auth::user()->can('projects_view')) abort(403);
        $project = Project::with(['tasks', 'milestones'])->findOrFail($projectId);
        
        return view('admin.projects.gantt', compact('project'));
    }

    public function sendNotification(Request $request, $id)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['slug' => 'required|string']);

        $options = $request->except('slug');
        $result = $this->projectNotification->send($request->slug, $id, $options);

        return response()->json($result);
    }

    public function sendProject($id)
    {
        if (!Auth::user()->can('projects_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $project = Project::findOrFail($id);

        $this->projectNotification->send('project-kickoff', $project->id);

        return redirect()->back()->with('success', 'Project sent to client!');
    }

    public function tabContent(Request $request, $id, $tab)
    {
        if (!Auth::user()->can('projects_view')) abort(403);

        $project = Project::findOrFail($id);

        $html = match($tab) {
            'overview' => $this->renderOverview($project),
            'tasks' => $this->renderTasks($project),
            'invoices' => $this->renderInvoices($project),
            'expenses' => $this->renderExpenses($project),
            'milestones' => $this->renderMilestones($project),
            'files' => $this->renderFiles($project),
            'discussions' => $this->renderDiscussions($project),
            'activity' => $this->renderActivity($project),
            'timesheets' => $this->renderTimesheets($project),
            'gantt' => $this->renderGantt($project),
            'tickets' => $this->renderTickets($project),
            'estimates' => $this->renderEstimates($project),
            'proposals' => $this->renderProposals($project),
            'contracts' => $this->renderContracts($project),
            'notes' => $this->renderNotes($project),
            default => '<div class="text-center py-12 text-slate-400">Tab not found</div>',
        };

        return response()->json(['html' => $html]);
    }

    private function renderOverview($project)
    {
        return view('admin.projects.partials.overview', compact('project'))->render();
    }

    private function renderTasks($project)
    {
        $project->load(['tasks.milestoneRelation', 'tasks.assignedStaff', 'milestones']);
        $tasks = $project->tasks;
        $taskCount = $tasks->count();
        $groupedTasks = $tasks->groupBy(function($task) {
            return $task->milestone ?? 'no_milestone';
        });
        return view('admin.projects.partials.tasks', compact('project', 'groupedTasks', 'taskCount'))->render();
    }

    private function renderInvoices($project)
    {
        $invoices = $project->invoices()->orderBy('date', 'desc')->get();
        return view('admin.projects.partials.invoices', compact('project', 'invoices'))->render();
    }

    private function renderExpenses($project)
    {
        $expenses = $project->expenses()->orderBy('date', 'desc')->get();
        return view('admin.projects.partials.expenses', compact('project', 'expenses'))->render();
    }

    private function renderMilestones($project)
    {
        $milestones = $project->milestones()->orderBy('due_date')->get();
        return view('admin.projects.partials.milestones', compact('project', 'milestones'))->render();
    }

    private function renderFiles($project)
    {
        $files = $project->files()->orderBy('dateadded', 'desc')->get();
        return view('admin.projects.partials.files', compact('project', 'files'))->render();
    }

    private function renderDiscussions($project)
    {
        // Load discussions with their creator (staff) and comments (with comment authors)
        $discussions = $project->discussions()
            ->with(['staff', 'comments.staff'])
            ->orderBy('last_activity', 'desc')
            ->get();
        return view('admin.projects.partials.discussions', compact('project', 'discussions'))->render();
    }

    private function renderActivity($project)
    {
        $activities = $project->activities()->with('staff')->orderBy('dateadded', 'desc')->limit(20)->get();
        return view('admin.projects.partials.activity', compact('project', 'activities'))->render();
    }

    private function renderTimesheets($project)
    {
        $project->load(['tasks.timers.staff']);
        $allTimers = $project->tasks->flatMap(function($task) {
            return $task->timers->map(function($timer) use ($task) {
                $timer->task_name = $task->name;
                return $timer;
            });
        })->sortByDesc('start_time');
        return view('admin.projects.partials.timesheets', compact('project', 'allTimers'))->render();
    }

    private function renderGantt($project)
    {
        $tasks = $project->tasks()
            ->with(['milestoneRelation'])
            ->orderBy('milestone')
            ->orderBy('startdate')
            ->get();

        $milestones = $project->milestones()->orderBy('due_date')->get();

        return view('admin.projects.partials.gantt', compact('project', 'tasks', 'milestones'))->render();
    }

    private function renderTickets($project)
    {
        $project->load(['tickets.departmentRel', 'tickets.assignedStaff', 'tickets.client', 'tickets.priorityRel']);
        $tickets = $project->tickets;
        $ticketCount = $tickets->count();
        return view('admin.projects.partials.tickets', compact('project', 'tickets', 'ticketCount'))->render();
    }

    private function renderEstimates($project)
    {
        $estimates = $project->estimates()->orderBy('date', 'desc')->get();
        return view('admin.projects.partials.estimates', compact('project', 'estimates'))->render();
    }

    private function renderProposals($project)
    {
        $proposals = $project->proposals()->orderBy('datecreated', 'desc')->get();
        return view('admin.projects.partials.proposals', compact('project', 'proposals'))->render();
    }

    private function renderContracts($project)
    {
        return view('admin.projects.partials.contracts', compact('project'))->render();
    }

    private function renderNotes($project)
    {
        $notes = Note::where('rel_type', 'project')->where('rel_id', $project->id)->with('creator')->orderBy('dateadded', 'desc')->get();
        return view('admin.projects.partials.notes', compact('project', 'notes'))->render();
    }
}
