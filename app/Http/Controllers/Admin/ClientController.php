<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('clients_view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Client::query()->with(['country', 'currency', 'primaryContact', 'group']);

        // Filters
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('datecreated', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('datecreated', '<=', $request->date_to);
        }

        $query->withSum('invoices', 'total');

        $total = $query->count();
        $clients = $query->paginate(15)->withQueryString();
        $countries = Country::all();

        return view('admin.clients.index', compact('clients', 'total', 'countries'));
    }

    public function searchClients(Request $request)
    {
        $search = $request->input('q');
        if (strlen($search) < 3) {
            return response()->json([]);
        }

        $clients = Client::query()
            ->with('country')
            ->where(function($q) use ($search) {
                $q->where('company', 'LIKE', '%' . $search . '%')
                  ->orWhere('phonenumber', 'LIKE', '%' . $search . '%')
                  ->orWhere('address', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('country', function($cq) use ($search) {
                      $cq->where('long_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('short_name', 'LIKE', '%' . $search . '%');
                  });
            })
            ->limit(10)
            ->get(['userid', 'company', 'phonenumber', 'address', 'country']);

        return response()->json($clients);
    }

    public function exportCsv(Request $request)

    {
        if (!Auth::user()->can('clients_view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Client::query()->with(['country']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company', 'like', '%' . $search . '%')
                  ->orWhere('phonenumber', 'like', '%' . $search . '%')
                  ->orWhere('website', 'like', '%' . $search . '%')
                  ->orWhere('vat', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status);
        }

        $clients = $query->get();

        $filename = "clients_export_" . now()->format('YmdHis') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Company', 'Country', 'Phone', 'Website', 'Status', 'Created Date']);

        foreach ($clients as $client) {
            fputcsv($handle, [
                $client->company,
                $client->country->name ?? 'N/A',
                $client->phonenumber ?? 'N/A',
                $client->website ?? 'N/A',
                $client->active ? 'Active' : 'Inactive',
                $client->datecreated,
            ]);
        }

        fclose($handle);
        exit;
    }

    public function create()
    {
        if (!Auth::user()->can('clients_create')) {
            abort(403, 'Unauthorized action.');
        }

        $countries = Country::all();
        $currencies = Currency::all();
        $groups = \App\Models\CustomersGroup::all();

        return view('admin.clients.create', compact('countries', 'currencies', 'groups'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('clients_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'company' => 'required|string|max:191',
            'email' => 'nullable|email', // Not in table but common in forms, though Client model doesn't have it. I'll stick to table.
            'phonenumber' => 'nullable|string|max:30',
            'country' => 'required|integer',
            'city' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:15',
            'state' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:191',
            'website' => 'nullable|string|max:150',
            'default_currency' => 'required|integer',
            'customer_group_id' => 'nullable|integer',
        ]);

        $maxId = DB::table('tblclients')->max('userid') ?? 0;
        $userId = $maxId + 1;

        Client::create([
            'userid' => $userId,
            'company' => $request->company,
            'phonenumber' => $request->phonenumber,
            'country' => $request->country,
            'city' => $request->city,
            'zip' => $request->zip,
            'state' => $request->state,
            'address' => $request->address,
            'website' => $request->website,
            'datecreated' => now(),
            'active' => 1,
            'default_currency' => $request->default_currency,
            'customer_group_id' => $request->customer_group_id,
            'addedfrom' => Auth::id(),
        ]);

        return redirect()->route('admin.clients.index')->with('success', 'Client created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('clients_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $client = Client::findOrFail($id);
        $countries = Country::all();
        $currencies = Currency::all();
        $groups = \App\Models\CustomersGroup::all();

        return view('admin.clients.edit', compact('client', 'countries', 'currencies', 'groups'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('clients_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'company' => 'required|string|max:191',
            'phonenumber' => 'nullable|string|max:30',
            'country' => 'required|integer',
            'city' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:15',
            'state' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:191',
            'website' => 'nullable|string|max:150',
            'default_currency' => 'required|integer',
            'customer_group_id' => 'nullable|integer',
        ]);

        $client = Client::findOrFail($id);
        $client->update([
            'company' => $request->company,
            'phonenumber' => $request->phonenumber,
            'country' => $request->country,
            'city' => $request->city,
            'zip' => $request->zip,
            'state' => $request->state,
            'address' => $request->address,
            'website' => $request->website,
            'default_currency' => $request->default_currency,
            'customer_group_id' => $request->customer_group_id,
        ]);

        return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully!');
    }

    public function show($id)
    {
        if (!Auth::user()->can('clients_view')) {
            abort(403, 'Unauthorized action.');
        }

        $client = Client::findOrFail($id);
        $stats = $this->getClientStats($client);

        return view('admin.clients.show', compact('client', 'stats'));
    }

    private function getClientStats(Client $client)
    {
        return [
            'projects' => $client->projects()->count(),
            'tickets' => $client->tickets()->count(),
            'invoices' => $client->invoices()->count(),
            'estimates' => $client->estimates()->count(),
            'expenses' => $client->expenses()->count(),
            'tasks' => $client->tasks()->count(),
            'reminders' => $client->reminders()->count(),
            'notes' => $client->notes()->count(),
            'contacts' => $client->contacts()->count(),
        ];
    }

    public function projects($id)
    {
        $client = Client::findOrFail($id);
        $stats = $this->getClientStats($client);
        $data = $client->projects()->paginate(10);
        return view('admin.clients.projects', compact('client', 'stats', 'data'));
    }

    public function tickets($id)
    {
        $client = Client::findOrFail($id);
        $stats = $this->getClientStats($client);
        $data = $client->tickets()->with(['statusRel', 'priorityRel', 'departmentRel'])->paginate(10);
        return view('admin.clients.tickets', compact('client', 'stats', 'data'));
    }

    public function invoices($id)
    {
        $client = Client::findOrFail($id);
        $stats = $this->getClientStats($client);
        $data = $client->invoices()->with('currency')->paginate(10);
        return view('admin.clients.invoices', compact('client', 'stats', 'data'));
    }

    public function estimates($id)
    {
        $client = Client::findOrFail($id);
        $stats = $this->getClientStats($client);
        $data = $client->estimates()->with('currency')->paginate(10);
        return view('admin.clients.estimates', compact('client', 'stats', 'data'));
    }

    public function expenses($id)
    {
        $client = Client::findOrFail($id);
        $stats = $this->getClientStats($client);
        $data = $client->expenses()->with(['categoryRel', 'currency'])->paginate(10);
        return view('admin.clients.expenses', compact('client', 'stats', 'data'));
    }

    public function tasks($id)
    {
        $client = Client::findOrFail($id);
        $stats = $this->getClientStats($client);
        $data = $client->tasks()->paginate(10);
        return view('admin.clients.tasks', compact('client', 'stats', 'data'));
    }

    public function reminders($id)
    {
        $client = Client::findOrFail($id);
        $stats = $this->getClientStats($client);
        $data = $client->reminders()->with(['staff', 'creator'])->paginate(10);
        return view('admin.clients.reminders', compact('client', 'stats', 'data'));
    }

    public function notes($id)
    {
        $client = Client::findOrFail($id);
        $stats = $this->getClientStats($client);
        $data = $client->notes()->with('creator')->paginate(10);
        return view('admin.clients.notes', compact('client', 'stats', 'data'));
    }

    public function contacts($id)
    {
        $client = Client::findOrFail($id);
        $stats = $this->getClientStats($client);
        $data = $client->contacts()->paginate(10);
        return view('admin.clients.contacts', compact('client', 'stats', 'data'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('clients_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $client = Client::findOrFail($id);
        
        // Check for dependent records to prevent orphans
        if ($client->projects()->exists() || $client->invoices()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete client with existing projects or invoices.');
        }

        $client->delete();
        return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully!');
    }
}
