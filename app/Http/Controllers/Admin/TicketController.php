<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Department;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketReply;
use App\Models\Staff;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Project;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index(Request $request)
    {
        if (!Auth::user()->can('tickets_view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Ticket::query()->with(['departmentRel', 'priorityRel', 'statusRel', 'assignedStaff', 'client']);

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $total = $query->count();
        $tickets = $query->paginate(15)->withQueryString();
        $departments = Department::all();
        $priorities = TicketPriority::all();
        $statuses = TicketStatus::all();

        return view('admin.tickets.index', compact('tickets', 'total', 'departments', 'priorities', 'statuses'));
    }

    public function create()
    {
        if (!Auth::user()->can('tickets_create')) {
            abort(403, 'Unauthorized action.');
        }

        $departments = Department::all();
        $priorities = TicketPriority::all();
        $statuses = TicketStatus::all();
        $staff = Staff::all();
        $clients = Client::all();
        $contacts = Contact::all();
        $projects = Project::all();

        return view('admin.tickets.create', compact('departments', 'priorities', 'statuses', 'staff', 'clients', 'contacts', 'projects'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('tickets_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'department' => 'required|integer',
            'priority' => 'required|integer',
            'status' => 'required|integer',
            'userid' => 'nullable|integer',
            'contactid' => 'nullable|integer',
            'assigned' => 'nullable|integer',
            'project_id' => 'nullable|integer',
            'email' => 'nullable|email',
        ]);

        $name = $request->contactid ? \App\Models\Contact::find($request->contactid)->firstname . ' ' . \App\Models\Contact::find($request->contactid)->lastname : ($request->name ?? 'Unknown');

        $ticket = Ticket::create([
            'subject' => $request->subject,
            'message' => $request->message,
            'department' => $request->department,
            'priority' => $request->priority,
            'status' => $request->status,
            'userid' => $request->userid ?: 0,
            'contactid' => $request->contactid ?: 0,
            'assigned' => $request->assigned ?: 0,
            'project_id' => $request->project_id ?: 0,
            'email' => $request->email ?? '',
            'name' => $name,
            'date' => now(),
            'admin' => 1,
            'ticketkey' => Str::random(32),
        ]);

        // Notify Customer if opened by staff
        if ($ticket->admin == 1 && $ticket->email) {
            $this->emailService->send($ticket->email, 'new-ticket-opened-admin', [
                'contact_firstname' => $ticket->name,
                'contact_lastname' => '',
                'ticket_subject' => '[#' . $ticket->ticketid . '] ' . $ticket->subject,
                'ticket_department' => $ticket->departmentRel->name ?? 'Support',
                'ticket_priority' => $ticket->priorityRel->name ?? 'Medium',
                'ticket_message' => $ticket->message,
                'ticket_id' => $ticket->ticketid,
                'ticket_public_url' => config('app.url') . '/client/tickets/' . $ticket->ticketid,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Ticket created successfully!', 'reload' => true, 'pane_refresh_url' => route('admin.tickets.pane.show', $ticket->ticketid)]);
        }

        if ($request->filled('referer')) {
            return redirect($request->referer)->with('success', 'Ticket created successfully!');
        }

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('tickets_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $ticket = Ticket::findOrFail($id);
        $departments = Department::all();
        $priorities = TicketPriority::all();
        $statuses = TicketStatus::all();
        $staff = Staff::all();
        $clients = Client::all();
        $contacts = Contact::all();
        $projects = Project::all();

        return view('admin.tickets.edit', compact('ticket', 'departments', 'priorities', 'statuses', 'staff', 'clients', 'contacts', 'projects'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('tickets_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'department' => 'required|integer',
            'priority' => 'required|integer',
            'status' => 'required|integer',
            'userid' => 'nullable|integer',
            'contactid' => 'nullable|integer',
            'assigned' => 'nullable|integer',
            'project_id' => 'nullable|integer',
            'email' => 'nullable|email',
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->update(array_merge($validated, [
            'userid' => $request->userid ?: 0,
            'contactid' => $request->contactid ?: 0,
            'assigned' => $request->assigned ?: 0,
            'project_id' => $request->project_id ?: 0,
            'email' => $request->email ?? '',
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Ticket updated successfully!', 'reload' => true]);
        }

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket updated successfully!');
    }

    public function show($id)
    {
        if (!Auth::user()->can('tickets_view')) {
            abort(403, 'Unauthorized action.');
        }

        $ticket = Ticket::with([
            'departmentRel', 
            'priorityRel', 
            'statusRel', 
            'assignedStaff', 
            'client', 
            'contact', 
            'project',
            'replies',
            'attachments'
        ])->findOrFail($id);
        
        $statuses = TicketStatus::all();
        $staff = Staff::all();
        
        return view('admin.tickets.show', compact('ticket', 'statuses', 'staff'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('tickets_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket deleted successfully!');
    }

    public function storeReply(Request $request)
    {
        if (!Auth::user()->can('tickets_view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message' => 'required|string',
            'ticketid' => 'required|exists:tbltickets,ticketid'
        ]);

        $ticket = Ticket::findOrFail($request->ticketid);
        
        // Generate Message-ID for the outgoing email
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?: 'crm.local';
        $newMessageId = '<' . Str::uuid() . '@' . $domain . '>';

        // Determine In-Reply-To and References
        $inReplyTo = $ticket->message_id;
        $references = $ticket->references ? $ticket->references . ' ' . $ticket->message_id : $ticket->message_id;

        // If there are previous replies, we might want to reply to the latest one specifically for some clients
        $lastReply = $ticket->replies()->orderBy('date', 'desc')->first();
        if ($lastReply && $lastReply->message_id) {
            $inReplyTo = $lastReply->message_id;
        }

        $reply = TicketReply::create([
            'ticketid' => $ticket->ticketid,
            'userid' => $ticket->userid,
            'contactid' => $ticket->contactid,
            'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'email' => Auth::user()->email,
            'message' => $request->message,
            'date' => now(),
            'admin' => 1,
            'message_id' => $newMessageId,
            'in_reply_to' => $inReplyTo,
            'references' => $references
        ]);

        $newReferences = $references
            ? $references . ' ' . ($ticket->message_id ?? '')
            : ($ticket->message_id ?? '');

        $ticket->update([
            'lastreply' => now(),
            'adminreplying' => 1,
            'staff_id_replying' => Auth::id(),
            'message_id' => $newMessageId,
            'references' => trim($newReferences),
        ]);

        // Notify Customer about the reply
        if ($ticket->email) {
            $headers = [];
            if ($newMessageId) $headers['Message-ID'] = $newMessageId;
            if ($inReplyTo) $headers['In-Reply-To'] = $inReplyTo;
            if ($references) $headers['References'] = $references;

            $this->emailService->send($ticket->email, 'ticket-reply', [
                'contact_firstname' => $ticket->name,
                'contact_lastname' => '',
                'ticket_subject' => '[#' . $ticket->ticketid . '] ' . $ticket->subject,
                'ticket_message' => $reply->message,
                'ticket_id' => $ticket->ticketid,
                'ticket_public_url' => config('app.url') . '/client/tickets/' . $ticket->ticketid,
            ], $headers);
        }

        return response()->json(['success' => true, 'message' => 'Reply added successfully']);
    }

    public function changeStatus(Request $request, $id)
    {
        if (!Auth::user()->can('tickets_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|integer'
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function changeAssigned(Request $request, $id)
    {
        if (!Auth::user()->can('tickets_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'assigned' => 'nullable|integer'
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->update(['assigned' => $request->assigned ?: 0]);

        return response()->json(['success' => true, 'message' => 'Assignment updated successfully']);
    }
}
