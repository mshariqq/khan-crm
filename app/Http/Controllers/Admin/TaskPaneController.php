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
use App\Models\TaskChecklistTemplate;
use App\Services\FileService;
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
        $checklistTemplates = TaskChecklistTemplate::all();

        return view('admin.tasks.pane.create', compact(
            'staff', 'milestones', 'projects', 'invoices', 'estimates', 'proposals', 'leads', 'tickets', 'clients', 'checklistTemplates'
        ));
    }

    public function edit($id)
    {
        if (!auth()->user()->can('tasks_edit')) {
            abort(403);
        }

        $task = Task::with('assignedStaff', 'followers')->findOrFail($id);
        $staff = Staff::all();
        $milestones = Milestone::all();
        $projects = Project::with('staff')->get();
        $invoices = Invoice::all();
        $estimates = Estimate::all();
        $proposals = Proposal::all();
        $leads = Lead::select('id', 'name')->get();
        $tickets = Ticket::select('ticketid', 'subject')->get();
        $clients = Client::select('userid', 'company')->get();
        $checklistTemplates = TaskChecklistTemplate::all();

        return view('admin.tasks.pane.edit', compact(
            'task', 'staff', 'milestones', 'projects', 'invoices', 'estimates', 'proposals', 'leads', 'tickets', 'clients', 'checklistTemplates'
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
            'attachments.staff',
        ])->findOrFail($id);

        $fileService = app(FileService::class);
        foreach ($task->attachments as $att) {
            $att->fileurl = $fileService->url($att->file_name);
        }

        return view('admin.tasks.pane.show', compact('task'));
    }
}
