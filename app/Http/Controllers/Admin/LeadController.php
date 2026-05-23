<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Staff;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\Client;
use App\Models\Country;
use App\Models\Note;
use App\Models\Reminder;
use App\Models\GdprRequest;
use App\Services\EmailService;
use App\Services\LeadActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index(Request $request)
    {
        if (!Auth::user()->can('leads_view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Lead::query()->with(['assignedStaff', 'statusRel', 'sourceRel', 'client', 'countryRel']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        $total = $query->count();
        $leads = $query->paginate(15)->withQueryString();
        $statuses = LeadStatus::all();
        $sources = LeadSource::all();
        $countries = Country::all();

        return view('admin.leads.index', compact('leads', 'total', 'statuses', 'sources', 'countries'));
    }

    public function create()
    {
        if (!Auth::user()->can('leads_create')) {
            abort(403, 'Unauthorized action.');
        }

        $staff = Staff::all();
        $statuses = LeadStatus::all();
        $sources = LeadSource::all();
        $countries = Country::all();
        $clients = Client::all();

        // Get defaults from settings
        $default_status = \App\Helpers\SettingsHelper::get('leads_default_status');
        $default_source = \App\Helpers\SettingsHelper::get('leads_default_source');

        return view('admin.leads.create', compact('staff', 'statuses', 'sources', 'countries', 'clients', 'default_status', 'default_source'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('leads_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phonenumber' => 'nullable|string|max:30',
            'company' => 'nullable|string|max:255',
            'status' => 'required|integer',
            'source' => 'required|integer',
            'country' => 'nullable|integer',
            'assigned' => 'nullable|integer',
            'client_id' => 'nullable|integer',
            'lead_value' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $lead = Lead::create([
            'name' => $request->name,
            'email' => $request->email,
            'phonenumber' => $request->phonenumber,
            'company' => $request->company,
            'status' => $request->status,
            'source' => $request->source,
            'country' => $request->country,
            'assigned' => $request->assigned,
            'client_id' => $request->client_id,
            'lead_value' => $request->lead_value,
            'description' => $request->description,
            'dateadded' => now(),
            'addedfrom' => Auth::id(),
        ]);

        LeadActivityLogService::leadCreated($lead);

        // Notify assigned staff
        if ($lead->assigned) {
            $staff = Staff::find($lead->assigned);
            if ($staff && $staff->email) {
                $subject = "New Lead Assigned: " . $lead->name;
                $messageBody = "
                    <p>Hi {$staff->firstname},</p>
                    <p>A new lead has been assigned to you.</p>
                    <p><strong>Name:</strong> {$lead->name}</p>
                    <p><strong>Company:</strong> " . ($lead->company ?? 'N/A') . "</p>
                    <p><strong>Status:</strong> " . ($lead->statusRel->name ?? 'N/A') . "</p>
                    <p><a href='" . route('admin.leads.show', $lead->id) . "' class='btn'>View Lead</a></p>
                ";
                $this->emailService->sendCustom($staff->email, $subject, $messageBody);
            }
        }

        return redirect()->route('admin.leads.index')->with('success', 'Lead created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('leads_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $lead = Lead::findOrFail($id);
        $staff = Staff::all();
        $statuses = LeadStatus::all();
        $sources = LeadSource::all();
        $countries = Country::all();
        $clients = Client::all();

        return view('admin.leads.edit', compact('lead', 'staff', 'statuses', 'sources', 'countries', 'clients'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('leads_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phonenumber' => 'nullable|string|max:30',
            'company' => 'nullable|string|max:255',
            'status' => 'required|integer',
            'source' => 'required|integer',
            'country' => 'nullable|integer',
            'assigned' => 'nullable|integer',
            'client_id' => 'nullable|integer',
            'lead_value' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $lead = Lead::findOrFail($id);
        $oldAssigned = $lead->assigned;
        $lead->update($request->all());

        LeadActivityLogService::leadUpdated($lead, $request->only(['name', 'status', 'assigned', 'lead_value']));

        // Notify new staff if assignee changed
        if ($lead->assigned && $lead->assigned != $oldAssigned) {
            $staff = Staff::find($lead->assigned);
            if ($staff && $staff->email) {
                $subject = "Lead Assigned to You: " . $lead->name;
                $messageBody = "
                    <p>Hi {$staff->firstname},</p>
                    <p>The following lead has been assigned to you.</p>
                    <p><strong>Name:</strong> {$lead->name}</p>
                    <p><strong>Company:</strong> " . ($lead->company ?? 'N/A') . "</p>
                    <p><a href='" . route('admin.leads.show', $lead->id) . "' class='btn'>View Lead</a></p>
                ";
                $this->emailService->sendCustom($staff->email, $subject, $messageBody);
            }
        }

        return redirect()->route('admin.leads.index')->with('success', 'Lead updated successfully!');
    }

    public function show($id)
    {
        if (!Auth::user()->can('leads_view')) {
            abort(403, 'Unauthorized action.');
        }

        $lead = Lead::with(['assignedStaff', 'statusRel', 'sourceRel', 'client', 'countryRel'])->findOrFail($id);
        return view('admin.leads.show', compact('lead'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('leads_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $lead = Lead::findOrFail($id);
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('success', 'Lead deleted successfully!');
    }

    public function storeNote(Request $request, $id)
    {
        if (!Auth::user()->can('leads_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'description' => 'required|string',
        ]);

        $lead = Lead::findOrFail($id);
        $note = Note::create([
            'description' => $request->description,
            'rel_id' => $lead->id,
            'rel_type' => 'lead',
            'dateadded' => now(),
            'addedfrom' => Auth::id(),
        ]);

        LeadActivityLogService::noteAdded($lead, $note->id);

        return redirect()->route('admin.leads.show', $lead->id)->with('success', 'Note added successfully!');
    }

    public function storeReminder(Request $request, $id)
    {
        if (!Auth::user()->can('leads_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'description' => 'required|string',
            'date' => 'required|date',
            'staff' => 'nullable|integer',
        ]);

        $lead = Lead::findOrFail($id);
        $reminder = Reminder::create([
            'description' => $request->description,
            'date' => $request->date,
            'rel_id' => $lead->id,
            'rel_type' => 'lead',
            'staff' => $request->staff ?? Auth::id(),
            'isnotify' => 1,
            'creator' => Auth::id(),
        ]);

        LeadActivityLogService::reminderSet($lead, $reminder->id);

        return redirect()->route('admin.leads.show', $lead->id)->with('success', 'Reminder set successfully!');
    }

    public function convertToClient(Request $request, $id)
    {
        if (!Auth::user()->can('leads_create') || !Auth::user()->can('clients_create')) {
            abort(403, 'Unauthorized action.');
        }

        $lead = Lead::findOrFail($id);

        $request->validate([
            'company' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $client = Client::create([
            'company' => $request->company,
            'email' => $request->email,
            'phonenumber' => $lead->phonenumber ?? '',
            'address' => $lead->address ?? '',
            'city' => $lead->city ?? '',
            'state' => $lead->state ?? '',
            'zip' => $lead->zip ?? '',
            'country' => $lead->country ?? 0,
            'datecreated' => now(),
            'active' => 1,
        ]);

        $lead->update([
            'client_id' => $client->userid,
            'date_converted' => now(),
            'status' => 5,
        ]);

        LeadActivityLogService::convertedToClient($lead, $client->userid, $client->company);

        // Send Welcome Email to the new client
        if ($client->email) {
            $this->emailService->send($client->email, 'new-client-created', [
                'contact_firstname' => $client->company,
                'contact_lastname' => '',
                'crm_url' => config('app.url') . '/login/client',
            ]);
        }

        return redirect()->route('admin.clients.show', $client->userid)->with('success', 'Lead converted to client successfully!');
    }

    public function markAsLost($id)
    {
        if (!Auth::user()->can('leads_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $lead = Lead::findOrFail($id);
        $lead->update(['lost' => 1, 'status' => 6]);

        LeadActivityLogService::markedAsLost($lead);

        return redirect()->route('admin.leads.show', $lead->id)->with('success', 'Lead marked as lost!');
    }

    public function markAsJunk($id)
    {
        if (!Auth::user()->can('leads_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $lead = Lead::findOrFail($id);
        $lead->update(['junk' => 1, 'status' => 7]);

        LeadActivityLogService::markedAsJunk($lead);

        return redirect()->route('admin.leads.show', $lead->id)->with('success', 'Lead marked as junk!');
    }

    public function storeGdpr(Request $request, $id)
    {
        if (!Auth::user()->can('leads_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'type' => 'required|in:export,delete,access,rectification',
            'description' => 'nullable|string',
        ]);

        $lead = Lead::findOrFail($id);
        GdprRequest::create([
            'lead_id' => $lead->id,
            'request_type' => $request->type,
            'description' => $request->description ?? '',
            'status' => 'pending',
            'request_date' => now(),
            'request_from' => 'staff',
        ]);

        LeadActivityLogService::gdprRequestSubmitted($lead, $request->type);

        return redirect()->route('admin.leads.show', $lead->id)->with('success', 'GDPR request submitted!');
    }
}
