<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estimate;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Staff;
use App\Models\Project;
use App\Models\Item;
use App\Models\Tax;
use App\Models\Note;
use App\Models\Reminder;
use App\Models\Invoice;
use App\Models\Itemable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EstimateController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('estimates_view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Estimate::query()->with(['client', 'currencyRel']);

        if ($request->filled('client')) {
            $query->where('clientid', $request->client);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $total = $query->count();
        $draft = Estimate::where('status', 1)->count();
        $sent = Estimate::where('status', 2)->count();
        $accepted = Estimate::where('status', 3)->count();
        $declined = Estimate::where('status', 4)->count();
        $estimates = $query->paginate(15)->withQueryString();
        $clients = Client::all();

        return view('admin.estimates.index', compact('estimates', 'total', 'clients', 'draft', 'sent', 'accepted', 'declined'));
    }

    public function create()
    {
        if (!Auth::user()->can('estimates_create')) {
            abort(403, 'Unauthorized action.');
        }

        $clients = Client::all();
        $currencies = Currency::all();
        $staff = Staff::all();
        $projects = Project::all();
        $items = Item::all();
        $taxes = Tax::all();

        return view('admin.estimates.create', compact('clients', 'currencies', 'staff', 'projects', 'items', 'taxes'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('estimates_create')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'number' => 'required|string|max:255',
            'clientid' => 'required|integer',
            'currency' => 'required|integer',
            'date' => 'required|date',
            'datecreated' => 'nullable|date',
            'expirydate' => 'nullable|date',
            'subtotal' => 'nullable|numeric',
            'total_tax' => 'nullable|numeric',
            'total' => 'required|numeric',
            'adjustment' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric',
            'discount_total' => 'nullable|numeric',
            'discount_type' => 'nullable|string|max:30',
            'status' => 'required|integer',
            'prefix' => 'nullable|string|max:50',
            'number_format' => 'nullable|integer',
            'project_id' => 'nullable|integer',
            'sale_agent' => 'nullable|integer',
            'reference_no' => 'nullable|string|max:255',
            'billing_street' => 'nullable|string',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_zip' => 'nullable|string|max:50',
            'billing_country' => 'nullable|integer',
            'shipping_street' => 'nullable|string',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_zip' => 'nullable|string|max:50',
            'shipping_country' => 'nullable|integer',
            'include_shipping' => 'nullable|boolean',
            'show_shipping_on_estimate' => 'nullable|boolean',
            'show_quantity_as' => 'nullable|integer',
            'clientnote' => 'nullable|string',
            'adminnote' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        // Set defaults
        $validated['subtotal'] = $validated['subtotal'] ?? 0;
        $validated['total_tax'] = $validated['total_tax'] ?? 0;
        $validated['adjustment'] = $validated['adjustment'] ?? 0;
        $validated['discount_percent'] = $validated['discount_percent'] ?? 0;
        $validated['discount_total'] = $validated['discount_total'] ?? 0;
        $validated['include_shipping'] = $validated['include_shipping'] ?? 0;
        $validated['show_shipping_on_estimate'] = $validated['show_shipping_on_estimate'] ?? 0;
        
        $estimateData = $validated;
        $estimateData['date'] = $request->date ?? date('Y-m-d');
        $estimateData['datecreated'] = now();
        $estimateData['addedfrom'] = Auth::id();
        $estimateData['hash'] = Str::random(32);
        $estimateData['sent'] = 0;
        
        // Required fields with defaults from migration
        $estimateData['show_quantity_as'] = $request->show_quantity_as ?? 1;
        $estimateData['discount_type'] = $request->discount_type ?? 'percent';
        $estimateData['sale_agent'] = $request->sale_agent ?? 0;
        $estimateData['project_id'] = $request->project_id ?? 0;
        $estimateData['billing_country'] = $request->billing_country ?? 0;
        $estimateData['shipping_country'] = $request->shipping_country ?? 0;
        $estimateData['prefix'] = $request->prefix ?? '';
        $estimateData['number_format'] = $request->number_format ?? 0;
        
        // Only add nullable fields if they have values
        if (!empty($request->reference_no)) $estimateData['reference_no'] = $request->reference_no;
        if (!empty($request->expirydate)) $estimateData['expirydate'] = $request->expirydate;
        
        $estimate = Estimate::create($estimateData);

        // Save items
        if ($request->has('item_description')) {
            $descriptions = $request->item_description;
            $qtys = $request->item_qty ?? [];
            $rates = $request->item_rate ?? [];
            $amounts = $request->item_amount ?? [];

            foreach ($descriptions as $index => $description) {
                if (!empty($description)) {
                    Itemable::create([
                        'rel_id' => $estimate->id,
                        'rel_type' => 'estimate',
                        'description' => $description,
                        'qty' => $qtys[$index] ?? 1,
                        'rate' => $rates[$index] ?? 0,
                        'amount' => $amounts[$index] ?? 0,
                        'item_order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('admin.estimates.index')->with('success', 'Estimate created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('estimates_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $estimate = Estimate::findOrFail($id);
        $clients = Client::all();
        $currencies = Currency::all();
        $staff = Staff::all();
        $projects = Project::all();
        $items = Item::all();
        $taxes = Tax::all();

        return view('admin.estimates.edit', compact('estimate', 'clients', 'currencies', 'staff', 'projects', 'items', 'taxes'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('estimates_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'number' => 'required|string|max:255',
            'clientid' => 'required|integer',
            'currency' => 'required|integer',
            'date' => 'required|date',
            'expirydate' => 'nullable|date',
            'subtotal' => 'nullable|numeric',
            'total_tax' => 'nullable|numeric',
            'total' => 'required|numeric',
            'adjustment' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric',
            'discount_total' => 'nullable|numeric',
            'discount_type' => 'nullable|string|max:30',
            'status' => 'required|integer',
            'prefix' => 'nullable|string|max:50',
            'number_format' => 'nullable|integer',
            'project_id' => 'nullable|integer',
            'sale_agent' => 'nullable|integer',
            'reference_no' => 'nullable|string|max:255',
            'billing_street' => 'nullable|string',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_zip' => 'nullable|string|max:50',
            'billing_country' => 'nullable|integer',
            'shipping_street' => 'nullable|string',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_zip' => 'nullable|string|max:50',
            'shipping_country' => 'nullable|integer',
            'include_shipping' => 'nullable|boolean',
            'show_shipping_on_estimate' => 'nullable|boolean',
            'show_quantity_as' => 'nullable|integer',
            'clientnote' => 'nullable|string',
            'adminnote' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        $estimate = Estimate::findOrFail($id);
        $estimate->update($validated);

        return redirect()->route('admin.estimates.index')->with('success', 'Estimate updated successfully!');
    }

    public function show($id)
    {
        if (!Auth::user()->can('estimates_view')) {
            abort(403, 'Unauthorized action.');
        }

        $estimate = Estimate::with(['client', 'currency', 'creator', 'saleAgent', 'project', 'items', 'notes', 'reminders'])->findOrFail($id);
        return view('admin.estimates.show', compact('estimate'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('estimates_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $estimate = Estimate::findOrFail($id);
        $estimate->delete();

        return redirect()->route('admin.estimates.index')->with('success', 'Estimate deleted successfully!');
    }

    public function storeNote(Request $request, $id)
    {
        if (!Auth::user()->can('estimates_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate(['content' => 'required|string']);

        $estimate = Estimate::findOrFail($id);
        Note::create([
            'content' => $request->content,
            'rel_id' => $estimate->id,
            'rel_type' => 'estimate',
            'dateadded' => now(),
            'addedfrom' => Auth::id(),
        ]);

        return redirect()->route('admin.estimates.show', $estimate->id)->with('success', 'Note added!');
    }

    public function storeReminder(Request $request, $id)
    {
        if (!Auth::user()->can('estimates_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'content' => 'required|string',
            'date' => 'required|date',
        ]);

        $estimate = Estimate::findOrFail($id);
        Reminder::create([
            'description' => $request->content,
            'rel_id' => $estimate->id,
            'rel_type' => 'estimate',
            'date' => $request->date,
            'staff' => Auth::id(),
            'creator' => Auth::id(),
            'is_notified' => 0,
        ]);

        return redirect()->route('admin.estimates.show', $estimate->id)->with('success', 'Reminder added!');
    }

    public function markAccepted($id)
    {
        if (!Auth::user()->can('estimates_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $estimate = Estimate::findOrFail($id);
        $estimate->update(['status' => 2]);

        return redirect()->route('admin.estimates.show', $estimate->id)->with('success', 'Estimate accepted!');
    }

    public function markDeclined($id)
    {
        if (!Auth::user()->can('estimates_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $estimate = Estimate::findOrFail($id);
        $estimate->update(['status' => 3]);

        return redirect()->route('admin.estimates.show', $estimate->id)->with('success', 'Estimate declined!');
    }

    public function convertToInvoice($id)
    {
        if (!Auth::user()->can('estimates_edit') || !Auth::user()->can('invoices_create')) {
            abort(403, 'Unauthorized action.');
        }

        $estimate = Estimate::findOrFail($id);

        $invoice = Invoice::create([
            'clientid' => $estimate->clientid,
            'number' => $estimate->number,
            'prefix' => $estimate->prefix,
            'date' => now(),
            'duedate' => now()->addDays(30),
            'currency' => $estimate->currency,
            'subtotal' => $estimate->subtotal,
            'total_tax' => $estimate->total_tax,
            'total' => $estimate->total,
            'status' => 1,
            'datecreated' => now(),
            'addedfrom' => Auth::id(),
            'hash' => Str::random(32),
            'billing_street' => $estimate->billing_street,
            'billing_city' => $estimate->billing_city,
            'billing_state' => $estimate->billing_state,
            'billing_zip' => $estimate->billing_zip,
            'billing_country' => $estimate->billing_country,
        ]);

        $estimate->update(['status' => 4, 'invoice_id' => $invoice->id]);

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Invoice created from estimate!');
    }
}