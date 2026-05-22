<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Staff;
use App\Models\Milestone;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Estimate;
use App\Models\Proposal;
use App\Models\Lead;
use App\Models\Ticket;
use App\Models\Client;
use Illuminate\Http\Request;

class TaskPaneController extends Controller
{
    public function create(Request $request)
    {
        if (!auth()->user()->can('tasks_create')) {
            abort(403);
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

        return view('admin.tasks.pane.create', compact(
            'staff', 'milestones', 'projects', 'invoices', 'estimates', 'proposals', 'leads', 'tickets', 'clients'
        ));
    }

    public function edit($id)
    {
        if (!auth()->user()->can('tasks_edit')) {
            abort(403);
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

        return view('admin.tasks.pane.edit', compact(
            'task', 'staff', 'milestones', 'projects', 'invoices', 'estimates', 'proposals', 'leads', 'tickets', 'clients'
        ));
    }

    public function show($id)
    {
        if (!auth()->user()->can('tasks_view')) {
            abort(403);
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
        ])->findOrFail($id);

        return view('admin.tasks.pane.show', compact('task'));
    }
}
