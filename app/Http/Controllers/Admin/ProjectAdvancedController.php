<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTemplate;
use App\Models\ProjectPhase;
use App\Models\ProjectRisk;
use App\Models\ProjectChangeRequest;
use App\Models\ProjectResourceAllocation;
use App\Models\TaskDependency;
use App\Models\ProjectBaseline;
use App\Models\ProjectClosureChecklist;
use App\Models\ProjectLessonsLearned;
use App\Models\ProjectShare;
use App\Models\ProjectWatcher;
use App\Models\ProjectRecurring;
use App\Models\ProjectFeedback;
use App\Models\ProjectLocation;
use App\Models\Staff;
use App\Models\Task;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectAdvancedController extends Controller
{
    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    // ===== Project Templates =====

    public function templatesIndex()
    {
        $templates = ProjectTemplate::with('creator')->latest()->paginate(20);
        return view('admin.projects.templates.index', compact('templates'));
    }

    public function templatesCreate()
    {
        return view('admin.projects.templates.create');
    }

    public function templatesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'estimated_budget' => 'nullable|numeric|min:0',
            'milestones' => 'nullable|array',
            'tasks' => 'nullable|array',
            'team_roles' => 'nullable|array',
            'checklists' => 'nullable|array',
            'is_public' => 'nullable|boolean',
        ]);

        $template = ProjectTemplate::create(array_merge($validated, [
            'created_by' => Auth::id(),
            'is_public' => $request->boolean('is_public', true),
        ]));

        return redirect()->route('admin.projects.templates.index')
            ->with('success', 'Template created successfully!');
    }

    public function templatesApply(Request $request, $templateId)
    {
        $template = ProjectTemplate::findOrFail($templateId);
        $projectId = $request->input('project_id');
        $project = Project::findOrFail($projectId);

        $this->projectService->applyTemplate($project, $template);

        return response()->json(['success' => true, 'message' => 'Template applied successfully!']);
    }

    // ===== Project Categories =====

    public function categoriesIndex()
    {
        $categories = ProjectCategory::withCount('projects')->latest()->get();
        return view('admin.projects.categories.index', compact('categories'));
    }

    public function categoriesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        ProjectCategory::create($validated);
        return redirect()->route('admin.projects.categories.index')
            ->with('success', 'Category created!');
    }

    public function categoriesUpdate(Request $request, $id)
    {
        $category = ProjectCategory::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);
        $category->update($validated);
        return redirect()->route('admin.projects.categories.index')
            ->with('success', 'Category updated!');
    }

    public function categoriesDestroy($id)
    {
        ProjectCategory::findOrFail($id)->delete();
        return redirect()->route('admin.projects.categories.index')
            ->with('success', 'Category deleted!');
    }

    // ===== Project Phases =====

    public function phasesIndex($projectId)
    {
        $project = Project::with('phases.milestones')->findOrFail($projectId);
        return view('admin.projects.partials.phases', compact('project'));
    }

    public function phasesStore(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'color' => 'nullable|string|max:20',
            'status' => 'nullable|string|in:pending,active,completed',
        ]);

        $phase = $project->phases()->create($validated + ['phase_order' => $project->phases()->max('phase_order') + 1]);
        return response()->json(['success' => true, 'phase' => $phase]);
    }

    public function phasesUpdate(Request $request, $projectId, $phaseId)
    {
        $phase = ProjectPhase::where('project_id', $projectId)->findOrFail($phaseId);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'color' => 'nullable|string|max:20',
            'status' => 'nullable|string|in:pending,active,completed',
        ]);
        $phase->update($validated);
        return response()->json(['success' => true]);
    }

    public function phasesDestroy($projectId, $phaseId)
    {
        ProjectPhase::where('project_id', $projectId)->findOrFail($phaseId)->delete();
        return response()->json(['success' => true]);
    }

    // ===== Risk Management =====

    public function risksIndex($projectId)
    {
        $project = Project::with('risks.assignedTo')->findOrFail($projectId);
        $staff = Staff::all();
        return view('admin.projects.partials.risks', compact('project', 'staff'));
    }

    public function risksStore(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'risk_type' => 'nullable|string|max:100',
            'probability' => 'required|string|in:low,medium,high',
            'impact' => 'required|string|in:low,medium,high',
            'mitigation_plan' => 'nullable|string',
            'contingency_plan' => 'nullable|string',
            'assigned_to' => 'nullable|integer|exists:tblstaff,staffid',
        ]);

        $risk = $project->risks()->create($validated);
        return response()->json(['success' => true, 'risk' => $risk]);
    }

    public function risksUpdate(Request $request, $projectId, $riskId)
    {
        $risk = ProjectRisk::where('project_id', $projectId)->findOrFail($riskId);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'risk_type' => 'nullable|string|max:100',
            'probability' => 'required|string|in:low,medium,high',
            'impact' => 'required|string|in:low,medium,high',
            'status' => 'required|string|in:open,mitigated,closed,accepted',
            'mitigation_plan' => 'nullable|string',
            'contingency_plan' => 'nullable|string',
            'assigned_to' => 'nullable|integer|exists:tblstaff,staffid',
        ]);
        if ($validated['status'] === 'closed' || $validated['status'] === 'accepted') {
            $validated['resolved_at'] = now();
        }
        $risk->update($validated);
        return response()->json(['success' => true]);
    }

    public function risksDestroy($projectId, $riskId)
    {
        ProjectRisk::where('project_id', $projectId)->findOrFail($riskId)->delete();
        return response()->json(['success' => true]);
    }

    // ===== Change Requests =====

    public function changeRequestsIndex($projectId)
    {
        $project = Project::with('changeRequests.submitter')->findOrFail($projectId);
        return view('admin.projects.partials.change-requests', compact('project'));
    }

    public function changeRequestsStore(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'estimated_cost' => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:0',
            'impact_analysis' => 'nullable|string',
        ]);

        $cr = $project->changeRequests()->create($validated + [
            'submitted_by' => Auth::id(),
            'submitted_at' => now(),
        ]);
        return response()->json(['success' => true, 'change_request' => $cr]);
    }

    public function changeRequestsApprove(Request $request, $projectId, $crId)
    {
        $cr = ProjectChangeRequest::where('project_id', $projectId)->findOrFail($crId);
        $cr->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return response()->json(['success' => true]);
    }

    public function changeRequestsReject(Request $request, $projectId, $crId)
    {
        $cr = ProjectChangeRequest::where('project_id', $projectId)->findOrFail($crId);
        $cr->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
        ]);
        return response()->json(['success' => true]);
    }

    // ===== Resource Management =====

    public function resourcesIndex($projectId)
    {
        $project = Project::with('resourceAllocations.staff')->findOrFail($projectId);
        $staff = Staff::all();
        $utilization = $this->projectService->getResourceUtilization($project);
        return view('admin.projects.partials.resources', compact('project', 'staff', 'utilization'));
    }

    public function resourcesStore(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'staff_id' => 'required|integer|exists:tblstaff,staffid',
            'role' => 'nullable|string|max:255',
            'allocation_percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);
        $resource = $project->resourceAllocations()->create($validated);
        return response()->json(['success' => true, 'resource' => $resource]);
    }

    public function resourcesDestroy($projectId, $resourceId)
    {
        ProjectResourceAllocation::where('project_id', $projectId)->findOrFail($resourceId)->delete();
        return response()->json(['success' => true]);
    }

    // ===== Task Dependencies =====

    public function taskDependenciesStore(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|integer|exists:tbltasks,id',
            'depends_on_task_id' => 'required|integer|exists:tbltasks,id|different:task_id',
            'dependency_type' => 'required|string|in:FS,FF,SS,SF',
            'lag_days' => 'nullable|integer|min:0',
        ]);
        $dep = TaskDependency::create($validated);
        return response()->json(['success' => true, 'dependency' => $dep]);
    }

    public function taskDependenciesDestroy($id)
    {
        TaskDependency::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // ===== Critical Path =====

    public function criticalPath($projectId)
    {
        $project = Project::findOrFail($projectId);
        $criticalPath = $this->projectService->calculateCriticalPath($project);
        $tasks = Task::whereIn('id', $criticalPath['critical_path_task_ids'])->get();
        return view('admin.projects.partials.critical-path', compact('project', 'criticalPath', 'tasks'));
    }

    // ===== Baselines =====

    public function baselinesIndex($projectId)
    {
        $project = Project::with('baselines')->findOrFail($projectId);
        $evmMetrics = $this->projectService->calculateEarnedValueMetrics($project);
        return view('admin.projects.partials.baselines', compact('project', 'evmMetrics'));
    }

    public function baselinesCapture(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $baseline = $this->projectService->captureBaseline($project, $validated['name'], $validated['description'] ?? null);
        return response()->json(['success' => true, 'baseline' => $baseline]);
    }

    // ===== Closure Checklist =====

    public function closureChecklistIndex($projectId)
    {
        $project = Project::findOrFail($projectId);
        $this->projectService->initializeClosureChecklist($project);
        $project->load('closureChecklist.completedBy');
        return view('admin.projects.partials.closure-checklist', compact('project'));
    }

    public function closureChecklistToggle($projectId, $itemId)
    {
        $item = ProjectClosureChecklist::where('project_id', $projectId)->findOrFail($itemId);
        $item->update([
            'completed' => !$item->completed,
            'completed_by' => $item->completed ? null : Auth::id(),
            'completed_at' => $item->completed ? null : now(),
        ]);
        return response()->json(['success' => true, 'completed' => $item->fresh()->completed]);
    }

    public function closureChecklistAdd(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'item' => 'required|string|max:500',
            'category' => 'nullable|string|max:100',
        ]);
        $item = $project->closureChecklist()->create($validated + ['sort_order' => $project->closureChecklist()->max('sort_order') + 1]);
        return response()->json(['success' => true, 'item' => $item]);
    }

    // ===== Lessons Learned =====

    public function lessonsIndex($projectId)
    {
        $project = Project::with('lessonsLearned.creator')->findOrFail($projectId);
        return view('admin.projects.partials.lessons-learned', compact('project'));
    }

    public function lessonsStore(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'nullable|string|max:100',
            'impact' => 'required|string|in:positive,negative,neutral',
            'recommendation' => 'nullable|string',
        ]);
        $lesson = $project->lessonsLearned()->create($validated + ['created_by' => Auth::id()]);
        return response()->json(['success' => true, 'lesson' => $lesson]);
    }

    // ===== Portfolio View =====

    public function portfolio()
    {
        $health = $this->projectService->getPortfolioHealth();
        $projects = Project::with(['client', 'category'])
            ->withCount(['tasks', 'tasks as completed_tasks' => fn($q) => $q->where('status', Task::STATUS_COMPLETED)])
            ->latest()
            ->get();
        return view('admin.projects.portfolio', compact('health', 'projects'));
    }

    // ===== Calendar =====

    public function calendar()
    {
        $projects = Project::whereNotNull('deadline')->orWhereNotNull('start_date')->get();
        $milestones = \App\Models\Milestone::with('project')->whereNotNull('due_date')->get();
        $tasks = Task::whereNotNull('duedate')->orWhereNotNull('startdate')->get();
        return view('admin.projects.calendar', compact('projects', 'milestones', 'tasks'));
    }

    // ===== Locations / Map =====

    public function locationsIndex($projectId)
    {
        $project = Project::with('locations')->findOrFail($projectId);
        return view('admin.projects.partials.locations', compact('project'));
    }

    public function locationsStore(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'type' => 'nullable|string|max:50',
        ]);
        $location = $project->locations()->create($validated);
        return response()->json(['success' => true, 'location' => $location]);
    }

    public function locationsDestroy($projectId, $locationId)
    {
        ProjectLocation::where('project_id', $projectId)->findOrFail($locationId)->delete();
        return response()->json(['success' => true]);
    }

    // ===== Sharing =====

    public function sharesIndex($projectId)
    {
        $project = Project::with('activeShares.creator')->findOrFail($projectId);
        return view('admin.projects.partials.shares', compact('project'));
    }

    public function sharesStore(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'email' => 'nullable|email|max:255',
            'access_level' => 'required|string|in:view,edit,comment',
            'expires_at' => 'nullable|date|after:now',
        ]);
        $share = $project->shares()->create($validated + ['created_by' => Auth::id()]);
        return response()->json(['success' => true, 'share' => $share, 'url' => $share->url]);
    }

    public function sharesDestroy($projectId, $shareId)
    {
        ProjectShare::where('project_id', $projectId)->findOrFail($shareId)->delete();
        return response()->json(['success' => true]);
    }

    // ===== Watchers =====

    public function toggleWatcher($projectId)
    {
        $project = Project::findOrFail($projectId);
        $existing = $project->watchers()->where('staff_id', Auth::id())->exists();
        if ($existing) {
            $project->watchers()->detach(Auth::id());
            return response()->json(['success' => true, 'watching' => false]);
        }
        $project->watchers()->attach(Auth::id());
        return response()->json(['success' => true, 'watching' => true]);
    }

    // ===== Recurring Projects =====

    public function recurringConfig($projectId)
    {
        $project = Project::with('recurringConfig')->findOrFail($projectId);
        return view('admin.projects.partials.recurring', compact('project'));
    }

    public function recurringStore(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'recurring_type' => 'required|string|in:daily,weekly,monthly,yearly',
            'repeat_every' => 'required|integer|min:1',
            'max_occurrences' => 'nullable|integer|min:1',
            'next_generation_date' => 'required|date|after:now',
            'copy_team' => 'nullable|boolean',
            'copy_milestones' => 'nullable|boolean',
            'copy_tasks' => 'nullable|boolean',
        ]);
        $config = $project->recurringConfig()->updateOrCreate(
            ['template_project_id' => $project->id],
            $validated + ['is_active' => true]
        );
        return response()->json(['success' => true, 'config' => $config]);
    }

    public function recurringDeactivate($projectId)
    {
        $config = ProjectRecurring::where('template_project_id', $projectId)->firstOrFail();
        $config->update(['is_active' => false]);
        return response()->json(['success' => true]);
    }

    // ===== Feedback =====

    public function feedbackIndex($projectId)
    {
        $project = Project::with('feedback.contact', 'feedback.staff')->findOrFail($projectId);
        return view('admin.projects.partials.feedback', compact('project'));
    }

    public function feedbackStore(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'nps_score' => 'nullable|integer|min:0|max:10',
            'comment' => 'nullable|string',
            'survey_responses' => 'nullable|array',
        ]);
        $feedback = $project->feedback()->create($validated + [
            'staff_id' => Auth::id(),
            'source' => 'manual',
        ]);
        return response()->json(['success' => true, 'feedback' => $feedback]);
    }

    // ===== Search Within Tabs =====

    public function searchTab(Request $request, $projectId, $tab)
    {
        $project = Project::findOrFail($projectId);
        $query = $request->input('q');
        $results = $this->projectService->searchTabContent($project, $tab, $query);
        return response()->json(['success' => true, 'results' => $results]);
    }

    // ===== Batch Operations =====

    public function batchStatus(Request $request)
    {
        $validated = $request->validate([
            'project_ids' => 'required|array',
            'project_ids.*' => 'integer|exists:tblprojects,id',
            'status' => 'required|integer|in:1,2,3,4',
        ]);
        $count = $this->projectService->batchUpdateStatus($validated['project_ids'], $validated['status']);
        return response()->json(['success' => true, 'count' => $count]);
    }

    public function batchDelete(Request $request)
    {
        $validated = $request->validate([
            'project_ids' => 'required|array',
            'project_ids.*' => 'integer|exists:tblprojects,id',
        ]);
        $count = $this->projectService->batchDelete($validated['project_ids']);
        return response()->json(['success' => true, 'count' => $count]);
    }

    public function batchAssignTeam(Request $request)
    {
        $validated = $request->validate([
            'project_ids' => 'required|array',
            'project_ids.*' => 'integer|exists:tblprojects,id',
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'integer|exists:tblstaff,staffid',
        ]);
        $count = $this->projectService->batchAssignTeam($validated['project_ids'], $validated['staff_ids']);
        return response()->json(['success' => true, 'count' => $count]);
    }

    // ===== Profitability Report =====

    public function profitabilityReport()
    {
        $projects = Project::with(['client', 'invoices', 'expenses'])
            ->withCount(['tasks', 'tasks as completed_tasks' => fn($q) => $q->where('status', Task::STATUS_COMPLETED)])
            ->get()
            ->map(function ($project) {
                $profitability = $project->profitability;
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'client' => $project->client->company ?? 'N/A',
                    'status' => $project->status,
                    'revenue' => $profitability['revenue'],
                    'expenses' => $profitability['expenses'],
                    'labor_cost' => $profitability['labor_cost'],
                    'total_cost' => $profitability['total_cost'],
                    'profit' => $profitability['profit'],
                    'margin' => $profitability['margin'],
                    'budget' => $project->project_cost ?? 0,
                    'progress' => $project->progress,
                ];
            });

        $totals = [
            'total_revenue' => $projects->sum('revenue'),
            'total_expenses' => $projects->sum('expenses'),
            'total_labor' => $projects->sum('labor_cost'),
            'total_cost' => $projects->sum('total_cost'),
            'total_profit' => $projects->sum('profit'),
            'avg_margin' => $projects->avg('margin'),
        ];

        return view('admin.projects.profitability', compact('projects', 'totals'));
    }
}
