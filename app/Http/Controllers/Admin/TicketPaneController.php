<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Department;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\Staff;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Project;
use Illuminate\Http\Request;

class TicketPaneController extends Controller
{
    public function create(Request $request)
    {
        if (!auth()->user()->can('tickets_create')) {
            abort(403);
        }

        $departments = Department::all();
        $priorities = TicketPriority::all();
        $statuses = TicketStatus::all();
        $staff = Staff::all();
        $clients = Client::all();
        $contacts = Contact::all();
        $projects = Project::all();
        $preselectedClient = $request->userid;
        $preselectedContact = $request->contactid;
        $preselectedProject = $request->project_id;

        return view('admin.tickets.pane.create', compact(
            'departments', 'priorities', 'statuses', 'staff', 'clients', 'contacts', 'projects',
            'preselectedClient', 'preselectedContact', 'preselectedProject'
        ));
    }

    public function edit($id)
    {
        if (!auth()->user()->can('tickets_edit')) {
            abort(403);
        }

        $ticket = Ticket::findOrFail($id);
        $departments = Department::all();
        $priorities = TicketPriority::all();
        $statuses = TicketStatus::all();
        $staff = Staff::all();
        $clients = Client::all();
        $contacts = Contact::all();
        $projects = Project::all();

        return view('admin.tickets.pane.edit', compact('ticket', 'departments', 'priorities', 'statuses', 'staff', 'clients', 'contacts', 'projects'));
    }

    public function show($id)
    {
        if (!auth()->user()->can('tickets_view')) {
            abort(403);
        }

        $ticket = Ticket::with([
            'departmentRel', 'priorityRel', 'statusRel',
            'assignedStaff', 'replyingStaff', 'client', 'contact', 'project',
            'replies', 'attachments'
        ])->findOrFail($id);

        $statuses = TicketStatus::all();
        $staff = Staff::all();

        return view('admin.tickets.pane.show', compact('ticket', 'statuses', 'staff'));
    }
}
