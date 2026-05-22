<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
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
            ]);
            
            Milestone::create($validated);

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
            ]);
            
            $milestone->update($validated);
            return redirect()->route('admin.milestones.show', $milestone)->with('success', 'Milestone updated');
    }

    public function destroy(Milestone $milestone)
    {
        if (!auth()->user()->can('milestones_delete')) {
            abort(403, 'Unauthorized action.');
        }
        $milestone->delete();
        return redirect()->route('admin.milestones.index')->with('success', 'Milestone deleted');
    }
}
