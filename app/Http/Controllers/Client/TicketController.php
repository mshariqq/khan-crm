<?php

namespace App\Http\Controllers\Client;

use App\Models\Ticket;
use App\Models\Department;
use App\Models\Priority;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends ClientBaseController
{
    public function index(Request $request)
    {
        $client = $this->getClient();
        $query = $client->tickets();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', '%' . $search . '%')
                  ->orWhere('ticketid', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->orderBy('lastreply', 'desc')->paginate(10)->withQueryString();
        
        return view('client.tickets.index', compact('tickets'));
    }

    public function create()
    {
        $departments = Department::all();
        $priorities = Priority::all();
        return view('client.tickets.create', compact('departments', 'priorities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:191',
            'department' => 'required|integer',
            'priority' => 'required|integer',
            'message' => 'required|string',
        ]);

        $client = $this->getClient();
        
        $ticket = Ticket::create([
            'subject' => $request->subject,
            'department' => $request->department,
            'priority' => $request->priority,
            'message' => $request->message,
            'userid' => $client->userid,
            'contactid' => Auth::guard('client')->id(),
            'name' => Auth::guard('client')->user()->firstname . ' ' . Auth::guard('client')->user()->lastname,
            'email' => Auth::guard('client')->user()->email,
            'status' => 1, // Open
            'date' => now(),
            'lastreply' => now(),
        ]);

        return redirect()->route('client.tickets.show', $ticket->ticketid)->with('success', 'Ticket opened successfully!');
    }

    public function show($id)
    {
        $client = $this->getClient();
        $ticket = $client->tickets()->with(['replies', 'departmentRel', 'priorityRel', 'statusRel'])->findOrFail($id);
        
        return view('client.tickets.show', compact('ticket'));
    }

    public function storeReply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);
        
        $client = $this->getClient();
        $ticket = $client->tickets()->findOrFail($id);

        TicketReply::create([
            'ticketid' => $ticket->ticketid,
            'message' => $request->message,
            'userid' => $client->userid,
            'contactid' => Auth::guard('client')->id(),
            'name' => Auth::guard('client')->user()->firstname . ' ' . Auth::guard('client')->user()->lastname,
            'date' => now(),
        ]);

        $ticket->update(['lastreply' => now(), 'status' => 1]);

        return redirect()->back()->with('success', 'Reply posted successfully!');
    }

    public function export(Request $request)
    {
        $client = $this->getClient();
        $query = $client->tickets();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', '%' . $search . '%')
                  ->orWhere('ticketid', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->get();

        $filename = "tickets_export_" . now()->format('YmdHis') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Ticket #', 'Subject', 'Department', 'Priority', 'Status', 'Last Reply', 'Created Date']);

        foreach ($tickets as $ticket) {
            fputcsv($handle, [
                '#' . $ticket->ticketid,
                $ticket->subject,
                $ticket->departmentRel->name ?? '-',
                $ticket->priorityRel->name ?? '-',
                $ticket->statusRel->name ?? 'Open',
                $ticket->lastreply,
                $ticket->date,
            ]);
        }

        fclose($handle);
        exit;
    }
}
