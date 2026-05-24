<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Notification;
use App\Events\MilestoneCreated;
use App\Events\MilestoneCompleted;
use App\Services\ProjectNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MilestoneController extends Controller
{
    protected $projectNotification;

    public function __construct(ProjectNotificationService $projectNotification)
    {
        $this->projectNotification = $projectNotification;
    }

    public function index()
    {
        if (!auth()->user()->can('milestones_view')) {
            abort(403, 'Unauthorized action.');
        }
        $milestones = Milestone::with('project')->orderBy('due_date', 'asc')->paginate(15);
        return view('admin.milestones.index', compact('milestones'));
    }

    public function create(Request $request)
    {
        if (!auth()->user()->can('milestones_create')) {
            abort(403, 'Unauthorized action.');
        }
        $projects = Project::all();
        $preselectedProject = $request->get('project_id');
        return view('admin.milestones.create', compact('projects', 'preselectedProject'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('milestones_create')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:tblprojects,id',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date|before_or_equal:due_date',
            'color' => 'nullable|string|max:10',
            'hide_from_customer' => 'nullable|boolean',
            'description_visible_to_customer' => 'nullable|boolean',
        ]);

        $milestone = DB::transaction(function () use ($validated) {
            return Milestone::create($validated);
        });

        MilestoneCreated::dispatch($milestone);

        $this->projectNotification->send('milestone-created', $milestone->project_id, [
            'milestone_name' => $milestone->name,
            'milestone_duedate' => $milestone->due_date ? \Carbon\Carbon::parse($milestone->due_date)->format('d M, Y') : '',
        ]);

        $project = Project::find($milestone->project_id);
        if ($project) {
            $staffIds = $project->staff()->pluck('staffid')->toArray();
            $exclude = [(int)Auth::id()];
            $recipients = array_values(array_diff($staffIds, $exclude));
            if (!empty($recipients)) {
                Notification::createForMultipleStaff(
                    $recipients,
                    Auth::user()->full_name . ' added milestone ' . e($milestone->name) . ' to ' . e($project->name),
                    route('admin.projects.show', $project->id),
                    notificationType: 'milestone',
                    groupingKey: 'milestone_created_' . $milestone->id
                );
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Milestone created successfully!',
                'reload' => true,
                'pane_refresh_url' => route('admin.milestones.pane.show', $milestone->id),
            ]);
        }

        if ($request->filled('referer')) {
            return redirect($request->referer)->with('success', 'Milestone created');
        }

        return redirect()->route('admin.milestones.index')->with('success', 'Milestone created');
    }

    public function show(Milestone $milestone)
    {
        if (!auth()->user()->can('milestones_view')) {
            abort(403, 'Unauthorized action.');
        }
        $milestone->load(['project', 'tasks']);
        return view('admin.milestones.show', compact('milestone'));
    }

    public function edit(Milestone $milestone)
    {
        if (!auth()->user()->can('milestones_edit')) {
            abort(403, 'Unauthorized action.');
        }
        $projects = Project::all();
        return view('admin.milestones.edit', compact('milestone', 'projects'));
    }

    public function update(Request $request, Milestone $milestone)
    {
        if (!auth()->user()->can('milestones_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:tblprojects,id',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date|before_or_equal:due_date',
            'color' => 'nullable|string|max:10',
            'hide_from_customer' => 'nullable|boolean',
            'description_visible_to_customer' => 'nullable|boolean',
        ]);

        $milestone->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Milestone updated successfully!', 'reload' => true]);
        }

        return redirect()->route('admin.milestones.show', $milestone)->with('success', 'Milestone updated');
    }

    public function destroy(Milestone $milestone)
    {
        if (!auth()->user()->can('milestones_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $milestone->tasks()->update(['milestone' => 0]);
        $milestone->delete();

        return redirect()->route('admin.milestones.index')->with('success', 'Milestone deleted');
    }

    public function complete(Milestone $milestone)
    {
        if (!auth()->user()->can('milestones_edit')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $milestone->update(['hide_from_customer' => false]); // placeholder for status

        MilestoneCompleted::dispatch($milestone);

        $this->projectNotification->send('milestone-completed', $milestone->project_id, [
            'milestone_name' => $milestone->name,
        ]);

        $project = Project::find($milestone->project_id);
        if ($project) {
            $staffIds = $project->staff()->pluck('staffid')->toArray();
            $exclude = [(int)Auth::id()];
            $recipients = array_values(array_diff($staffIds, $exclude));
            if (!empty($recipients)) {
                Notification::createForMultipleStaff(
                    $recipients,
                    Auth::user()->full_name . ' completed milestone ' . e($milestone->name) . ' in ' . e($project->name),
                    route('admin.projects.show', $project->id),
                    notificationType: 'milestone',
                    groupingKey: 'milestone_completed_' . $milestone->id
                );
            }
        }

        return response()->json(['success' => true]);
    }
}
