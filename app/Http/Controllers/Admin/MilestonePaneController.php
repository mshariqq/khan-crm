<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class MilestonePaneController extends Controller
{
    public function create(Request $request)
    {
        if (!auth()->user()->can('milestones_create')) {
            abort(403);
        }

        $projects = Project::all();
        $preselectedProject = $request->project_id;

        return view('admin.milestones.pane.create', compact('projects', 'preselectedProject'));
    }

    public function edit($id)
    {
        if (!auth()->user()->can('milestones_edit')) {
            abort(403);
        }

        $milestone = Milestone::findOrFail($id);
        $projects = Project::all();

        return view('admin.milestones.pane.edit', compact('milestone', 'projects'));
    }

    public function show($id)
    {
        if (!auth()->user()->can('milestones_view')) {
            abort(403);
        }

        $milestone = Milestone::with('project')->findOrFail($id);
        $tasks = Task::with(['assignedStaff', 'project', 'checklistItems'])->where('milestone', $id)->get();

        return view('admin.milestones.pane.show', compact('milestone', 'tasks'));
    }
}
