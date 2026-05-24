<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\ProjectFile;
use App\Models\ProjectNote;
use App\Models\ProjectDiscussion;
use App\Models\ProjectDiscussionComment;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Notification;
use App\Models\Task;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Itemable;
use App\Services\ProjectService;
use App\Services\EmailService;
use App\Services\ProjectNotificationService;
use App\Services\AuditService;
use App\Services\FileService;
use App\Services\DiscussionService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\StoreProjectNoteRequest;
use App\Http\Requests\StoreDiscussionRequest;
use App\Http\Requests\StoreDiscussionCommentRequest;
use App\Http\Requests\StoreProjectFileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ProjectController extends Controller
{
    protected $projectService;
    protected $emailService;
    protected $projectNotification;
    protected $auditService;
    protected $fileService;
    protected $discussionService;

    public function __construct(
        ProjectService $projectService,
        EmailService $emailService,
        ProjectNotificationService $projectNotification,
        AuditService $auditService,
        FileService $fileService,
        DiscussionService $discussionService
    ) {
        $this->projectService = $projectService;
        $this->emailService = $emailService;
        $this->projectNotification = $projectNotification;
        $this->auditService = $auditService;
        $this->fileService = $fileService;
        $this->discussionService = $discussionService;
    }

    public function index(Request $request)
    {
        if (!Auth::user()->can('projects_view')) abort(403);

        $query = Project::with(['client']);

        if ($request->filled('client')) $query->where('clientid', $request->client);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('search')) $query->where('name', 'like', '%' . $request->search . '%');
        if ($request->boolean('my_projects')) {
            $query->whereHas('staff', fn($q) => $q->where('staff_id', Auth::id()));
        }
        if ($request->filled('sort')) {
            match ($request->sort) {
                'name_asc' => $query->orderBy('name'),
                'name_desc' => $query->orderBy('name', 'desc'),
                'deadline_asc' => $query->orderBy('deadline'),
                'deadline_desc' => $query->orderBy('deadline', 'desc'),
                'progress_asc' => $query->orderBy('progress'),
                'progress_desc' => $query->orderBy('progress', 'desc'),
                'status' => $query->orderBy('status'),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $total = $query->count();
        $projects = $query->paginate(15)->withQueryString();
        $clients = Client::all();
        $categories = \App\Models\ProjectCategory::all();
        $activeCount = Project::where('status', Project::STATUS_ACTIVE)->count();
        $completedCount = Project::where('status', Project::STATUS_COMPLETED)->count();
        $onHoldCount = Project::where('status', Project::STATUS_ON_HOLD)->count();

        return view('admin.projects.index', compact('projects', 'total', 'clients', 'categories', 'activeCount', 'completedCount', 'onHoldCount'));
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

        try {
            DB::beginTransaction();

            $project = Project::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'] ?? Project::STATUS_ACTIVE,
                'clientid' => $validated['clientid'],
                'billing_type' => $validated['billing_type'] ?? 1,
                'start_date' => $validated['start_date'] ?? now(),
                'deadline' => $validated['deadline'] ?? null,
                'project_cost' => $validated['project_cost'] ?? null,
                'project_rate_per_hour' => $validated['project_rate_per_hour'] ?? null,
                'estimated_hours' => $validated['estimated_hours'] ?? null,
                'project_created' => now(),
                'addedfrom' => Auth::id(),
                'progress' => (isset($validated['status']) && (int)$validated['status'] === Project::STATUS_COMPLETED) ? 100 : 0,
                'contact_notification' => $validated['contact_notification'] ?? true,
                'notify_contacts' => $validated['notify_contacts'] ?? null,
                'priority' => $validated['priority'] ?? 'medium',
                'category_id' => $validated['category_id'] ?? null,
            ]);

            if (!empty($validated['staff_ids'])) {
                $project->staff()->sync($validated['staff_ids']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to create project: ' . $e->getMessage());
        }

        $this->projectNotification->send('project-created', $project->id);

        $staffIds = $project->staff()->pluck('staffid')->toArray();
        $creatorId = (int)($project->addedfrom ?? Auth::id());
        $allRecipients = array_unique(array_merge($staffIds, [$creatorId]));
        $desc = Auth::user()->full_name . ' created project <strong>' . e($project->name) . '</strong>';
        $link = route('admin.projects.show', $project->id);
        Notification::createForMultipleStaff($allRecipients, $desc, $link);

        $this->auditService->log('project_created', 'Project created: ' . $project->name, 'Project', $project->id, null, $project->toArray());

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('projects_edit')) abort(403);
        $project = Project::with('staff')->findOrFail($id);
        if (!$this->canAccessProject($project)) abort(403);
        $clients = Client::all();
        $staff = Staff::all();
        return view('admin.projects.edit', compact('project', 'clients', 'staff'));
    }

    public function update(UpdateProjectRequest $request, $id)
    {
        $project = Project::findOrFail($id);
        if (!$this->canAccessProject($project)) abort(403);

        if (in_array($project->status, [Project::STATUS_COMPLETED, Project::STATUS_CANCELLED])) {
            return redirect()->back()->with('error', 'Cannot edit a completed or cancelled project.');
        }

        $validated = $request->validated();
        $oldAttributes = $project->getOriginal();

        try {
            DB::beginTransaction();

            $updateData = [];
            foreach (['name', 'description', 'status', 'clientid', 'billing_type', 'start_date', 'deadline', 'project_cost', 'project_rate_per_hour', 'estimated_hours', 'contact_notification', 'notify_contacts', 'priority', 'category_id'] as $field) {
                if (array_key_exists($field, $validated)) {
                    $updateData[$field] = $validated[$field];
                }
            }

            if (isset($validated['status']) && (int)$validated['status'] === Project::STATUS_COMPLETED) {
                $updateData['date_finished'] = now();
                $updateData['progress'] = 100;
            } elseif (isset($validated['status']) && (int)$validated['status'] !== Project::STATUS_COMPLETED && $project->getOriginal('status') == Project::STATUS_COMPLETED) {
                $updateData['date_finished'] = null;
            }

            $project->update($updateData);

            if ($request->has('staff_ids')) {
                $this->projectService->syncStaffAndNotify($project, $validated['staff_ids'] ?? []);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to update project: ' . $e->getMessage());
        }

        $oldStatus = (int)($oldAttributes['status'] ?? $project->status);
        $notified = false;
        if ($oldStatus !== (int)$project->status) {
            try {
                $notified = $this->projectService->handleStatusChange($project, $oldStatus, (int)$project->status) || $notified;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Status change notification failed: ' . $e->getMessage());
            }
        }

        if (!$notified && isset($oldAttributes['deadline'])) {
            $notified = $this->projectService->handleDeadlineChange($project, $oldAttributes['deadline'], $project->deadline) || $notified;
        }

        if (!$notified) {
            $this->projectService->notifyGeneralUpdate($project);
        }

        $this->projectService->logProjectActivity($project);

        $this->auditService->log('project_updated', 'Project updated: ' . $project->name, 'Project', $project->id, $oldAttributes, $project->getChanges());

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully!');
    }

    public function show($id)
    {
        $project = Project::with([
            'client',
            'staff',
            'milestones',
            'category',
        ])->findOrFail($id);

        if (!$this->canAccessProject($project)) abort(403);

        $default_tab = \App\Helpers\SettingsHelper::get('default_project_tab', 'overview');

        return view('admin.projects.show', compact('project', 'default_tab'));
    }

    public function dashboard($id)
    {
        $project = Project::with(['client', 'staff', 'tasks.assignedStaff', 'invoices', 'expenses', 'files', 'milestones', 'activities.staff'])->findOrFail($id);
        if (!Auth::user()->canViewProject($project)) abort(403);

        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 3)->count();
        $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        $expenseCount = $project->expenses->count();
        $totalExpenses = $project->expenses->sum('amount');
        $paidInvoicesAmount = $project->invoices->where('status', 'paid')->sum('total');
        $totalInvoicesAmount = $project->invoices->sum('total');
        $passedMilestones = $project->milestones->filter(fn($m) => $m->due_date && $m->due_date->isPast())->count();
        $filesCount = $project->files->count();
        $clientVisibleFiles = $project->files->where('visible_to_customer', 1)->count();

        $tasksByStatus = $project->tasks->groupBy('status');
        $kanbanStatuses = [0, 1, 2, 3];
        $recentTasks = $project->tasks->sortByDesc('id')->take(5);
        $upcomingTasks = $project->tasks->where('status', '!=', 3)->sortBy('duedate')->take(5);

        return view('admin.projects.dashboard', compact(
            'project', 'totalTasks', 'completedTasks', 'taskProgress',
            'expenseCount', 'totalExpenses', 'paidInvoicesAmount', 'totalInvoicesAmount',
            'passedMilestones', 'filesCount', 'clientVisibleFiles',
            'tasksByStatus', 'kanbanStatuses', 'recentTasks', 'upcomingTasks'
        ));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('projects_delete')) abort(403);

        $project = Project::with(['invoices', 'tasks'])->findOrFail($id);

        $unpaidInvoices = $project->invoices->where('status', '!=', 2); // Assuming status 2 = paid
        if ($unpaidInvoices->isNotEmpty()) {
            return redirect()->back()->with('error', 'Cannot delete project with unpaid invoices. Settle all invoices first.');
        }

        $projectName = $project->name;
        $projectSnapshot = $project->toArray();

        try {
            DB::beginTransaction();

            $project->staff()->detach();
            $project->tasks->each(function ($task) {
                $task->assignedStaff()->detach();
                $task->followers()->detach();
                $task->delete();
            });

            $project->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete project: ' . $e->getMessage());
        }

        $this->auditService->log('project_deleted', 'Project deleted: ' . $projectName . ' by ' . Auth::user()->full_name, 'Project', $id, ['name' => $projectName, 'deleted_by' => Auth::user()->full_name, 'deleted_by_id' => Auth::id(), 'project_snapshot' => $projectSnapshot], null);

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully!');
    }

    private function canAccessProject(Project $project): bool
    {
        $user = Auth::user();
        if ($user->can('projects_view')) return true;
        return $project->staff()->where('staff_id', $user->staffid)->exists();
    }

    // ===== Status & Progress AJAX Endpoints =====

    public function updateStatus(Request $request, $id)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $request->validate(['status' => 'required|integer|in:1,2,3,4']);

        $project = Project::findOrFail($id);
        $oldStatus = $project->status;

        if ($oldStatus === (int)$request->status) {
            return response()->json(['success' => true, 'message' => 'No change']);
        }

        $updateData = ['status' => $request->status];
        if ((int)$request->status === Project::STATUS_COMPLETED) {
            $updateData['date_finished'] = now();
            $updateData['progress'] = 100;
        } elseif ($oldStatus === Project::STATUS_COMPLETED) {
            $updateData['date_finished'] = null;
        }

        $project->update($updateData);
        $this->projectService->handleStatusChange($project, $oldStatus, (int)$request->status);

        return response()->json(['success' => true]);
    }

    public function updateProgress(Request $request, $id)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $request->validate(['progress' => 'required|integer|min:0|max:100']);

        $project = Project::findOrFail($id);
        $project->update(['progress' => $request->progress]);

        if ($request->progress >= 100 && $project->status !== Project::STATUS_COMPLETED) {
            $project->update(['status' => Project::STATUS_COMPLETED, 'date_finished' => now()]);
            $this->projectService->handleStatusChange($project, $project->status, Project::STATUS_COMPLETED);
        }

        return response()->json(['success' => true]);
    }

    // ===== Notes =====

    public function notesCreate($projectId)
    {
        if (!Auth::user()->can('projects_edit')) abort(403);
        $project = Project::findOrFail($projectId);
        return redirect()->route('admin.projects.show', $project) . '#notes';
    }

    public function notesStore(StoreProjectNoteRequest $request, $projectId)
    {
        // Security verified: ProjectNote created with project_id scoped to the project.
        // No rel_type check needed — notes are always tied to the project via project_id.
        $project = Project::findOrFail($projectId);
        $validated = $request->validated();

        $note = ProjectNote::create([
            'project_id' => $project->id,
            'title' => $validated['title'] ?? null,
            'content' => $validated['description'],
            'staff_id' => Auth::id(),
            'dateadded' => now(),
        ]);

        ProjectActivity::create([
            'project_id' => $project->id,
            'staff_id' => Auth::id(),
            'description_key' => 'project_activity_note_created',
            'additional_data' => json_encode(['note_title' => $note->title]),
            'dateadded' => now(),
        ]);

        Notification::createForMultipleStaff(
            $project->staff()->pluck('staffid')->toArray(),
            Auth::user()->full_name . ' added a note to <strong>' . e($project->name) . '</strong>',
            route('admin.projects.show', $project->id),
            notificationType: 'note_added',
            groupingKey: 'note_' . $project->id
        );

        return response()->json(['success' => true, 'note' => $note]);
    }

    public function notesUpdate(Request $request, $projectId, $noteId)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $request->validate([
            'description' => 'required|string',
            'title' => 'nullable|string|max:255'
        ]);

        $note = ProjectNote::where('project_id', $projectId)->findOrFail($noteId);
        $note->update([
            'title' => $request->title,
            'content' => $request->description,
        ]);

        ProjectActivity::create([
            'project_id' => $projectId,
            'staff_id' => Auth::id(),
            'description_key' => 'project_activity_note_updated',
            'additional_data' => json_encode(['note_title' => $request->title]),
            'dateadded' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function notesDelete($projectId, $noteId)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $note = ProjectNote::where('project_id', $projectId)->findOrFail($noteId);
        $note->delete();

        return response()->json(['success' => true]);
    }

    // ===== Discussions =====

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

    public function storeDiscussion(StoreDiscussionRequest $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validated();

        $discussion = ProjectDiscussion::create([
            'project_id' => $project->id,
            'subject' => $validated['subject'],
            'description' => $validated['description'] ?? '',
            'show_to_customer' => $validated['show_to_customer'] ?? false,
            'datecreated' => now(),
            'staff_id' => Auth::id(),
        ]);

        ProjectActivity::create([
            'project_id' => $project->id,
            'staff_id' => Auth::id(),
            'description_key' => 'project_activity_discussion_created',
            'additional_data' => json_encode(['discussion_subject' => $discussion->subject]),
            'dateadded' => now(),
        ]);

        Notification::createForMultipleStaff(
            $project->staff()->pluck('staffid')->toArray(),
            Auth::user()->full_name . ' created a new discussion <strong>' . e($discussion->subject) . '</strong> in <strong>' . e($project->name) . '</strong>',
            route('admin.projects.discussions', $project->id),
            notificationType: 'discussion_comment',
            groupingKey: 'discussion_' . $discussion->id
        );

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.projects.discussions', $project)->with('success', 'Discussion created successfully!');
    }

    public function updateDiscussion(StoreDiscussionRequest $request, $projectId, $discussionId)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $project = Project::findOrFail($projectId);
        $discussion = ProjectDiscussion::where('project_id', $projectId)->findOrFail($discussionId);
        $validated = $request->validated();

        $this->discussionService->updateDiscussion($project, $discussion, $validated);

        return response()->json(['success' => true]);
    }

    public function deleteDiscussion($projectId, $discussionId)
    {
        if (!Auth::user()->can('projects_delete')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $discussion = ProjectDiscussion::where('project_id', $projectId)->findOrFail($discussionId);
        $discussion->delete();

        return response()->json(['success' => true]);
    }

    public function togglePinDiscussion($projectId, $discussionId)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $discussion = ProjectDiscussion::where('project_id', $projectId)->findOrFail($discussionId);
        $discussion->update(['is_pinned' => !$discussion->is_pinned]);

        return response()->json(['success' => true, 'is_pinned' => (bool)$discussion->fresh()->is_pinned]);
    }

    public function addDiscussionComment(StoreDiscussionCommentRequest $request, $projectId, $discussionId)
    {
        $project = Project::findOrFail($projectId);
        $discussion = $project->discussions()->findOrFail($discussionId);
        $validated = $request->validated();

        $comment = $this->discussionService->addCommentWithFile(
            $project,
            $discussion,
            $validated,
            $request->file('file')
        );

        $comment->load('staff');

        return response()->json(['success' => true, 'comment' => $comment]);
    }

    public function updateDiscussionComment(Request $request, $projectId, $discussionId, $commentId)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $comment = ProjectDiscussionComment::where('discussion_id', $discussionId)->findOrFail($commentId);
        $validated = $request->validate(['content' => 'required|string']);

        $this->discussionService->updateComment($comment, $validated);

        return response()->json(['success' => true]);
    }

    public function deleteDiscussionComment($projectId, $discussionId, $commentId)
    {
        if (!Auth::user()->can('projects_delete')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $comment = ProjectDiscussionComment::where('discussion_id', $discussionId)->findOrFail($commentId);
        $comment->delete();

        return response()->json(['success' => true]);
    }

    // ===== Discussion Categories =====

    public function discussionCategories()
    {
        $categories = ProjectDiscussionCategory::all();
        return response()->json($categories);
    }

    public function storeDiscussionCategory(Request $request)
    {
        if (!Auth::user()->can('projects_edit')) abort(403);
        $validated = $request->validate(['name' => 'required|string|max:255', 'color' => 'nullable|string|max:20']);
        $category = ProjectDiscussionCategory::create($validated);
        return response()->json(['success' => true, 'category' => $category]);
    }

    public function deleteDiscussionCategory($id)
    {
        if (!Auth::user()->can('projects_edit')) abort(403);
        ProjectDiscussionCategory::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // ===== Discussion Search =====

    public function searchDiscussions(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'q' => 'required|string|min:1',
            'category_id' => 'nullable|integer|exists:tblproject_discussion_categories,id',
        ]);

        $discussions = $this->discussionService->searchDiscussions(
            $project,
            $validated['q'],
            $validated['category_id'] ?? null
        );

        return response()->json(['success' => true, 'discussions' => $discussions]);
    }

    // ===== Discussion Email Integration (stub) =====

    public function discussionEmailSettings()
    {
        return response()->json([
            'success' => true,
            'enabled' => (bool) \App\Helpers\SettingsHelper::get('discussion_email_integration_enabled', false),
            'mailbox' => \App\Helpers\SettingsHelper::get('discussion_email_mailbox', '{imap.example.com:993/imap/ssl}INBOX'),
            'command' => 'php artisan discussions:fetch-emails',
            'setup_instructions' => [
                '1. Set MAIL_DISCUSSION_REPLY_DOMAIN in .env for reply-to addressing',
                '2. Configure IMAP mailbox via admin settings: discussion_email_mailbox, discussion_email_username, discussion_email_password',
                '3. Enable integration: Set discussion_email_integration_enabled to true',
                '4. Run: php artisan discussions:fetch-emails (or wait for scheduler)',
                '5. Email subject must contain [DISC-{id}] to match a discussion',
            ],
        ]);
    }

    // ===== Team Members =====

    public function updateTeamMembers(Request $request, $id)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $request->validate([
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'integer|exists:tblstaff,staffid',
        ]);

        $project = Project::findOrFail($id);
        $this->projectService->syncStaffAndNotify($project, $request->staff_ids ?? []);

        return response()->json(['success' => true]);
    }

    // ===== Duplicate & Archive =====

    public function duplicate($id)
    {
        if (!Auth::user()->can('projects_create')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $original = Project::with('staff', 'milestones')->findOrFail($id);

        try {
            DB::beginTransaction();

            $copy = Project::create([
                'name' => $original->name . ' (Copy)',
                'description' => $original->description,
                'status' => Project::STATUS_ACTIVE,
                'clientid' => $original->clientid,
                'billing_type' => $original->billing_type,
                'start_date' => now(),
                'project_cost' => $original->project_cost,
                'project_rate_per_hour' => $original->project_rate_per_hour,
                'estimated_hours' => $original->estimated_hours,
                'project_created' => now(),
                'addedfrom' => Auth::id(),
                'progress' => 0,
            ]);

            $staffIds = $original->staff->pluck('staffid')->toArray();
            if (!empty($staffIds)) {
                $copy->staff()->sync($staffIds);
            }

            foreach ($original->milestones as $milestone) {
                $copy->milestones()->create([
                    'name' => $milestone->name,
                    'description' => $milestone->description,
                    'due_date' => $milestone->due_date,
                    'milestone_order' => $milestone->milestone_order,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'project_id' => $copy->id]);
    }

    public function archive($id)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $project = Project::findOrFail($id);
        $project->update(['status' => Project::STATUS_ON_HOLD]);

        return response()->json(['success' => true]);
    }

    public function restoreProject($id)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $project = Project::findOrFail($id);
        $project->update(['status' => Project::STATUS_ACTIVE]);

        return response()->json(['success' => true]);
    }

    // ===== Export =====

    public function export(Request $request)
    {
        if (!Auth::user()->can('projects_view')) abort(403);

        $query = Project::with('client');

        if ($request->filled('client')) $query->where('clientid', $request->client);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where('name', 'like', '%' . $request->search . '%');

        $projects = $query->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="projects_export_' . now()->format('YmdHis') . '.csv"',
        ];

        $callback = function () use ($projects) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Project Name', 'Client', 'Status', 'Progress', 'Start Date', 'Deadline', 'Project Cost', 'Billing Type']);

            foreach ($projects as $project) {
                $statusLabel = Project::statusLabels()[$project->status] ?? 'Unknown';
                $billingLabel = Project::billingTypeLabels()[$project->billing_type] ?? 'Unknown';

                fputcsv($handle, [
                    $project->name,
                    $project->client->company ?? 'N/A',
                    $statusLabel,
                    $project->progress . '%',
                    $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '',
                    $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : '',
                    number_format($project->project_cost ?? 0, 2),
                    $billingLabel,
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    // ===== File Upload =====

    public function uploadFile(StoreProjectFileRequest $request, $id)
    {
        $project = Project::findOrFail($id);
        $uploadedFile = $request->file('file');

        $uploaded = $this->fileService->upload($uploadedFile, 'project');

        $projectFile = ProjectFile::create([
            'project_id' => $project->id,
            'file_name' => $uploaded['file_name'],
            'original_file_name' => $uploaded['original_file_name'],
            'filetype' => $uploaded['filetype'],
            'file_size' => $uploaded['file_size'],
            'subject' => $request->subject,
            'visible_to_customer' => $request->boolean('visible_to_customer', true),
            'staffid' => Auth::id(),
            'dateadded' => now(),
            'document_status' => $request->document_status ?? ProjectFile::DOCUMENT_STATUS_DRAFT,
        ]);

        ProjectActivity::create([
            'project_id' => $project->id,
            'staff_id' => Auth::id(),
            'description_key' => 'project_activity_file_uploaded',
            'additional_data' => json_encode(['file_name' => $uploaded['original_file_name']]),
            'dateadded' => now(),
        ]);

        Notification::createForMultipleStaff(
            $project->staff()->pluck('staffid')->toArray(),
            Auth::user()->full_name . ' uploaded file <strong>' . e($uploaded['original_file_name']) . '</strong> to <strong>' . e($project->name) . '</strong>',
            route('admin.projects.show', $project->id),
            notificationType: 'file_upload',
            groupingKey: 'file_upload_' . $project->id
        );

        return response()->json(['success' => true, 'file' => $projectFile]);
    }

    // ===== Convert Billable Expenses to Invoice =====

    public function convertBillableToInvoice(Request $request, $projectId)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $project = Project::findOrFail($projectId);
        $expenses = $project->expenses()->where('billable', true)->whereNull('invoiceid')->get();

        if ($expenses->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No unbilled billable expenses found']);
        }

        $totalAmount = $expenses->sum('amount');
        $currencyId = $expenses->first()->currency ?: 1;

        $invoice = Invoice::create([
            'clientid' => $project->clientid,
            'project_id' => $project->id,
            'date' => now(),
            'duedate' => now()->addDays(30),
            'status' => Invoice::STATUS_UNPAID,
            'currency' => $currencyId,
            'addedfrom' => Auth::id(),
            'total' => $totalAmount,
            'subtotal' => $totalAmount,
            'datecreated' => now(),
        ]);

        foreach ($expenses as $expense) {
            Itemable::create([
                'rel_id' => $invoice->id,
                'rel_type' => 'invoice',
                'description' => $expense->expense_name,
                'qty' => 1,
                'rate' => $expense->amount,
                'amount' => $expense->amount,
            ]);
            $expense->update(['invoiceid' => $invoice->id]);
        }

        return response()->json([
            'success' => true,
            'invoice_id' => $invoice->id,
            'message' => 'Invoice created from ' . $expenses->count() . ' billable expenses',
        ]);
    }

    // ===== GDPR Export =====

    public function exportGdpr($id)
    {
        if (!Auth::user()->can('projects_view')) abort(403);

        $project = Project::findOrFail($id);
        $data = $this->projectService->exportProjectData($project);

        return response()->json($data);
    }

    // ===== GDPR Deletion =====

    public function deleteGdpr($id)
    {
        if (!Auth::user()->can('projects_delete')) abort(403);

        $project = Project::findOrFail($id);
        $this->projectService->deleteProjectData($project);

        return response()->json(['success' => true, 'message' => 'Project data anonymized for GDPR compliance']);
    }

    // ===== Notifications =====

    public function sendNotification(Request $request, $id)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['slug' => 'required|string']);

        $lockKey = 'project-notify-' . $id . '-' . md5($request->slug);
        if (!Cache::add($lockKey, true, 30)) {
            return response()->json(['success' => false, 'message' => 'Please wait before sending another notification.'], 429);
        }

        $options = $request->except('slug');
        $result = $this->projectNotification->send($request->slug, $id, $options);

        return response()->json($result);
    }

    public function sendProject($id)
    {
        if (!Auth::user()->can('projects_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $lockKey = 'project-send-' . $id;
        if (!Cache::add($lockKey, true, 60)) {
            return redirect()->back()->with('error', 'Please wait before sending another notification.');
        }

        $project = Project::findOrFail($id);
        $this->projectNotification->send('project-kickoff', $project->id);

        return redirect()->back()->with('success', 'Project sent to client!');
    }

    // ===== Timesheet Export =====

    public function exportTimesheets($id)
    {
        if (!Auth::user()->can('projects_view')) abort(403);

        $project = Project::with('tasks.timers.staff', 'tasks.timers.task')->findOrFail($id);
        $allTimers = $project->tasks->flatMap(function ($task) {
            return $task->timers->map(function ($timer) use ($task) {
                $timer->task_name = $task->name;
                return $timer;
            });
        })->sortByDesc('start_time');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="timesheets_' . $project->id . '_' . now()->format('YmdHis') . '.csv"',
        ];

        $callback = function () use ($allTimers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Staff', 'Task', 'Date', 'Start', 'End', 'Duration (h)', 'Rate', 'Billable', 'Status', 'Note']);

            foreach ($allTimers as $timer) {
                $start = \Carbon\Carbon::parse($timer->start_time);
                $end = $timer->end_time ? \Carbon\Carbon::parse($timer->end_time) : null;
                $duration = $end ? $start->diffInMinutes($end) : now()->diffInMinutes($start);
                $staf = $timer->staff;
                fputcsv($handle, [
                    ($staf->firstname ?? '') . ' ' . ($staf->lastname ?? ''),
                    $timer->task_name ?? '',
                    $start->format('Y-m-d'),
                    $start->format('H:i'),
                    $end ? $end->format('H:i') : 'Running',
                    number_format($duration / 60, 2),
                    $timer->is_billable ? number_format($timer->hourly_rate ?? 0, 2) : '0.00',
                    $timer->is_billable ? 'Billable' : 'Non-billable',
                    $timer->status ?? 'pending',
                    $timer->note ?? '',
                ]);
            }

            fclose($handle);
        };

        return \Illuminate\Support\Facades\Response::stream($callback, 200, $headers);
    }

    public function timesheetSummary($id)
    {
        if (!Auth::user()->can('projects_view')) abort(403);

        $project = Project::with('tasks.timers.staff', 'tasks.timers.task')->findOrFail($id);
        $allTimers = $project->tasks->flatMap(function ($task) {
            return $task->timers->map(function ($timer) use ($task) {
                $timer->task_name = $task->name;
                return $timer;
            });
        });

        $dailySummary = $allTimers->whereNotNull('end_time')
            ->groupBy(fn($t) => \Carbon\Carbon::parse($t->start_time)->format('Y-m-d'))
            ->map(function ($timers, $date) {
                $totalMin = $timers->sum(fn($t) => \Carbon\Carbon::parse($t->start_time)->diffInMinutes(\Carbon\Carbon::parse($t->end_time)));
                $billableMin = $timers->where('is_billable', true)->sum(fn($t) => \Carbon\Carbon::parse($t->start_time)->diffInMinutes(\Carbon\Carbon::parse($t->end_time)));
                $staffCount = $timers->pluck('staff_id')->unique()->count();
                return [
                    'date' => $date,
                    'total_hours' => round($totalMin / 60, 1),
                    'billable_hours' => round($billableMin / 60, 1),
                    'non_billable_hours' => round(($totalMin - $billableMin) / 60, 1),
                    'entries' => $timers->count(),
                    'staff_count' => $staffCount,
                ];
            })->sortKeysDesc();

        $weeklySummary = $allTimers->whereNotNull('end_time')
            ->groupBy(fn($t) => \Carbon\Carbon::parse($t->start_time)->startOfWeek()->format('Y-m-d'))
            ->map(function ($timers, $weekStart) {
                $totalMin = $timers->sum(fn($t) => \Carbon\Carbon::parse($t->start_time)->diffInMinutes(\Carbon\Carbon::parse($t->end_time)));
                $billableMin = $timers->where('is_billable', true)->sum(fn($t) => \Carbon\Carbon::parse($t->start_time)->diffInMinutes(\Carbon\Carbon::parse($t->end_time)));
                $overtimeMin = $timers->groupBy('staff_id')->sum(function ($staffTimers) {
                    $staffDaily = $staffTimers->groupBy(fn($t) => \Carbon\Carbon::parse($t->start_time)->format('Y-m-d'));
                    return $staffDaily->sum(fn($day) => max(0, $day->sum(fn($t) => \Carbon\Carbon::parse($t->start_time)->diffInMinutes(\Carbon\Carbon::parse($t->end_time))) - 480));
                });
                return [
                    'week_start' => $weekStart,
                    'total_hours' => round($totalMin / 60, 1),
                    'billable_hours' => round($billableMin / 60, 1),
                    'overtime_hours' => round($overtimeMin / 60, 1),
                    'entries' => $timers->count(),
                ];
            })->sortKeysDesc();

        return response()->json([
            'success' => true,
            'daily' => $dailySummary->take(30)->values(),
            'weekly' => $weeklySummary->take(12)->values(),
            'totals' => [
                'total_hours' => round($allTimers->whereNotNull('end_time')->sum(fn($t) => \Carbon\Carbon::parse($t->start_time)->diffInMinutes(\Carbon\Carbon::parse($t->end_time))) / 60, 1),
                'billable_hours' => round($allTimers->whereNotNull('end_time')->where('is_billable', true)->sum(fn($t) => \Carbon\Carbon::parse($t->start_time)->diffInMinutes(\Carbon\Carbon::parse($t->end_time))) / 60, 1),
            ],
        ]);
    }

    // ===== Standalone Pages =====

    public function timesheets($id)
    {
        if (!Auth::user()->can('projects_view')) abort(403);
        $project = Project::with('tasks.timers.staff', 'tasks.timers.task')->findOrFail($id);
        return view('admin.projects.timesheets', compact('project'));
    }

    public function gantt($id)
    {
        if (!Auth::user()->can('projects_view')) abort(403);
        $project = Project::with('tasks', 'milestones')->findOrFail($id);
        $tasks = $project->tasks()->with(['milestoneRelation'])->orderBy('milestone')->orderBy('startdate')->get();
        $milestones = $project->milestones()->orderBy('due_date')->get();
        $criticalPath = $this->projectService->calculateCriticalPath($project);
        $criticalTaskIds = $criticalPath['critical_path_task_ids'] ?? [];
        $baselines = $project->baselines()->where('is_active', true)->get();
        return view('admin.projects.gantt', compact('project', 'tasks', 'milestones', 'criticalPath', 'criticalTaskIds', 'baselines'));
    }

    // ===== Tab Content =====

    public function tabContent(Request $request, $id, $tab)
    {
        $project = Project::findOrFail($id);
        if (!Auth::user()->canViewProject($project)) abort(403);

        $html = match ($tab) {
            'overview' => $this->renderOverview($project),
            'tasks' => $this->renderTasks($project),
            'invoices' => $this->renderInvoices($project),
            'expenses' => $this->renderExpenses($project),
            'milestones' => $this->renderMilestones($project),
            'files' => $this->renderFiles($project, request('folder_id')),
            'discussions' => $this->renderDiscussions($project),
            'activity' => $this->renderActivity($project),
            'timesheets' => $this->renderTimesheets($project),
            'gantt' => $this->renderGantt($project),
            'tickets' => $this->renderTickets($project),
            'estimates' => $this->renderEstimates($project),
            'proposals' => $this->renderProposals($project),
            'contracts' => $this->renderContracts($project),
            'notes' => $this->renderNotes($project),
            'phases' => view('admin.projects.partials.phases', [
                'project' => $project->load('phases')
            ])->render(),
            'risks' => view('admin.projects.partials.risks', [
                'project' => $project->load('risks.assignedTo'),
                'staff' => \App\Models\Staff::all(),
            ])->render(),
            'change_requests' => view('admin.projects.partials.change-requests', [
                'project' => $project->load('changeRequests.submitter'),
            ])->render(),
            'resources' => view('admin.projects.partials.resources', [
                'project' => $project->load('resourceAllocations.staff'),
                'staff' => \App\Models\Staff::all(),
                'utilization' => $this->projectService->getResourceUtilization($project),
            ])->render(),
            'baselines' => view('admin.projects.partials.baselines', [
                'project' => $project->load('baselines'),
                'evmMetrics' => $this->projectService->calculateEarnedValueMetrics($project),
            ])->render(),
            'closure_checklist' => view('admin.projects.partials.closure-checklist', [
                'project' => tap($project)->load('closureChecklist.completedBy'),
            ])->render(),
            'lessons_learned' => view('admin.projects.partials.lessons-learned', [
                'project' => $project->load('lessonsLearned.creator'),
            ])->render(),
            'locations' => view('admin.projects.partials.locations', [
                'project' => $project->load('locations'),
            ])->render(),
            'shares' => view('admin.projects.partials.shares', [
                'project' => $project->load('activeShares.creator'),
            ])->render(),
            'feedback' => view('admin.projects.partials.feedback', [
                'project' => $project->load('feedback.contact', 'feedback.staff'),
            ])->render(),
            'recurring' => view('admin.projects.partials.recurring', [
                'project' => $project->load('recurringConfig'),
            ])->render(),
            'critical_path' => view('admin.projects.partials.critical-path', [
                'project' => $project,
                'criticalPath' => $this->projectService->calculateCriticalPath($project),
                'tasks' => Task::whereIn('id', $this->projectService->calculateCriticalPath($project)['critical_path_task_ids'])->get(),
            ])->render(),
            default => '<div class="text-center py-12 text-slate-400">Tab not found</div>',
        };

        return response()->json(['html' => $html]);
    }

    private function renderOverview($project)
    {
        $project->loadCount(['tasks', 'tasks as completed_tasks_count' => fn($q) => $q->where('status', \App\Models\Task::STATUS_COMPLETED)]);
        return view('admin.projects.partials.overview', compact('project'))->render();
    }

    private function renderTasks($project)
    {
        $project->load(['tasks.milestoneRelation', 'tasks.assignedStaff', 'milestones']);
        $tasks = $project->tasks;
        $taskCount = $tasks->count();
        $groupedTasks = $tasks->groupBy(function ($task) {
            return $task->milestone ?? 'no_milestone';
        });
        return view('admin.projects.partials.tasks', compact('project', 'groupedTasks', 'taskCount'))->render();
    }

    private function renderInvoices($project)
    {
        $invoices = $project->invoices()->orderBy('date', 'desc')->paginate(10);
        return view('admin.projects.partials.invoices', compact('project', 'invoices'))->render();
    }

    private function renderExpenses($project)
    {
        $expenses = $project->expenses()->orderBy('date', 'desc')->paginate(10);
        return view('admin.projects.partials.expenses', compact('project', 'expenses'))->render();
    }

    private function renderMilestones($project)
    {
        $milestones = $project->milestones()->orderBy('due_date')->get();
        return view('admin.projects.partials.milestones', compact('project', 'milestones'))->render();
    }

    private function renderFiles($project, $folderId = null)
    {
        $project->load('files.staff', 'files.folder', 'files.downloads');
        $query = $project->files();
        if ($folderId) {
            $query->where('folder_id', $folderId);
        } else {
            $query->whereNull('folder_id');
        }
        $files = $query->orderBy('dateadded', 'desc')->paginate(20);
        $folders = \App\Models\ProjectFileFolder::where('project_id', $project->id)
            ->withCount('files')->orderBy('name')->get();
        $totalSize = $project->files()->sum('file_size');
        return view('admin.projects.partials.files', compact('project', 'files', 'folders', 'totalSize'))->render();
    }

    private function renderDiscussions($project)
    {
        $discussions = $project->discussions()
            ->with(['staff', 'comments.staff'])
            ->orderBy('last_activity', 'desc')
            ->paginate(15);
        return view('admin.projects.partials.discussions', compact('project', 'discussions'))->render();
    }

    private function renderActivity($project)
    {
        $activities = $project->activities()->with('staff')->orderBy('dateadded', 'desc')->paginate(20);
        return view('admin.projects.partials.activity', compact('project', 'activities'))->render();
    }

    private function renderTimesheets($project)
    {
        $project->load(['tasks.timers.staff']);
        $allTimers = $project->tasks->flatMap(function ($task) {
            return $task->timers->map(function ($timer) use ($task) {
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

        $criticalPath = $this->projectService->calculateCriticalPath($project);
        $criticalTaskIds = $criticalPath['critical_path_task_ids'] ?? [];
        $baselines = $project->baselines()->where('is_active', true)->get();

        return view('admin.projects.partials.gantt', compact('project', 'tasks', 'milestones', 'criticalPath', 'criticalTaskIds', 'baselines'))->render();
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
        $estimates = $project->estimates()->orderBy('date', 'desc')->paginate(10);
        return view('admin.projects.partials.estimates', compact('project', 'estimates'))->render();
    }

    private function renderProposals($project)
    {
        $proposals = $project->proposals()->orderBy('datecreated', 'desc')->paginate(10);
        return view('admin.projects.partials.proposals', compact('project', 'proposals'))->render();
    }

    private function renderContracts($project)
    {
        $project->load(['contracts']);
        $contracts = $project->contracts;
        return view('admin.projects.partials.contracts', compact('project', 'contracts'))->render();
    }

    private function renderNotes($project)
    {
        $notes = ProjectNote::where('project_id', $project->id)->with('staff')->orderBy('dateadded', 'desc')->get();
        return view('admin.projects.partials.notes', compact('project', 'notes'))->render();
    }
}
