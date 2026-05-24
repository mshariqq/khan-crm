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
use App\Services\EstimateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EstimateController extends Controller
{
    protected $estimateService;

    public function __construct(EstimateService $estimateService)
    {
        $this->estimateService = $estimateService;
    }

    public function index(Request $request)
    {
        if (!Auth::user()->can('estimates_view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Estimate::query()->with(['client', 'currency']);

        if ($request->filled('client')) {
            $query->where('clientid', $request->client);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $total = $query->count();
        $draft = Estimate::where('status', Estimate::STATUS_DRAFT)->count();
        $sent = Estimate::where('status', Estimate::STATUS_SENT)->count();
        $accepted = Estimate::where('status', Estimate::STATUS_ACCEPTED)->count();
        $declined = Estimate::where('status', Estimate::STATUS_DECLINED)->count();
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

        $lastEstimate = \App\Models\Estimate::orderBy('id', 'desc')->first();
        $next_estimate_number = $lastEstimate ? (int)preg_replace('/[^0-9]/', '', $lastEstimate->number) + 1 : (int)\App\Helpers\SettingsHelper::get('next_estimate_number', 1);

        return view('admin.estimates.create', compact('clients', 'currencies', 'staff', 'projects', 'items', 'taxes', 'next_estimate_number'));
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

        DB::beginTransaction();
        try {
            $estimateData = $this->estimateService->prepareEstimateData($validated, $request);
            $estimateData['number'] = $this->estimateService->generateNextNumber();

            $estimate = Estimate::create($estimateData);

            $totals = $this->estimateService->saveItems($estimate, $request);
            $this->estimateService->recalculateTotals($estimate, $totals['subtotal'], $totals['total_tax'], (float) ($validated['adjustment'] ?? 0), (float) ($validated['discount_total'] ?? 0));

            DB::commit();

            return redirect()->route('admin.estimates.index')->with('success', 'Estimate created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create estimate: ' . $e->getMessage()])->withInput();
        }
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

        if (in_array($estimate->status, [Estimate::STATUS_ACCEPTED, Estimate::STATUS_DECLINED, Estimate::STATUS_INVOICED])) {
            return back()->withErrors(['error' => 'Cannot modify an accepted, declined, or invoiced estimate.']);
        }

        $validated['include_shipping'] = $validated['include_shipping'] ?? 0;
        $validated['show_shipping_on_estimate'] = $validated['show_shipping_on_estimate'] ?? 0;
        $validated['discount_percent'] = $validated['discount_percent'] ?? 0;
        $validated['discount_total'] = $validated['discount_total'] ?? 0;
        $validated['adjustment'] = $validated['adjustment'] ?? 0;
        $validated['subtotal'] = $validated['subtotal'] ?? 0;
        $validated['total_tax'] = $validated['total_tax'] ?? 0;
        $validated['sale_agent'] = $validated['sale_agent'] ?? 0;
        $validated['project_id'] = $validated['project_id'] ?? 0;
        $validated['billing_country'] = $validated['billing_country'] ?? 0;
        $validated['shipping_country'] = $validated['shipping_country'] ?? 0;
        $validated['show_quantity_as'] = $validated['show_quantity_as'] ?? 1;

        $estimate->update($validated);

        $totals = $this->estimateService->syncItemsFromRequest($estimate, $request);
        $this->estimateService->recalculateTotals($estimate, $totals['subtotal'], $totals['total_tax'], (float) ($validated['adjustment'] ?? 0), (float) ($validated['discount_total'] ?? 0));

        return redirect()->route('admin.estimates.index')->with('success', 'Estimate updated successfully!');
    }

    public function print($id)
    {
        $estimate = Estimate::with(['client', 'currency', 'items'])->findOrFail($id);
        return view('admin.estimates.pdf', compact('estimate'));
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
            'description' => $request->content,
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
            'isnotified' => 0,
        ]);

        return redirect()->route('admin.estimates.show', $estimate->id)->with('success', 'Reminder added!');
    }

    public function markAccepted($id)
    {
        if (!Auth::user()->can('estimates_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $estimate = Estimate::findOrFail($id);
        $estimate->update(['status' => Estimate::STATUS_ACCEPTED]);

        return redirect()->route('admin.estimates.show', $estimate->id)->with('success', 'Estimate accepted!');
    }

    public function markDeclined($id)
    {
        if (!Auth::user()->can('estimates_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $estimate = Estimate::findOrFail($id);
        $estimate->update(['status' => Estimate::STATUS_DECLINED]);

        return redirect()->route('admin.estimates.show', $estimate->id)->with('success', 'Estimate declined!');
    }

    public function convertToInvoice($id)
    {
        if (!Auth::user()->can('estimates_edit') || !Auth::user()->can('invoices_create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $estimate = Estimate::with('items')->findOrFail($id);

            $invoice = Invoice::create([
                'clientid' => $estimate->clientid,
                'number' => $estimate->number,
                'prefix' => $estimate->prefix,
                'number_format' => $estimate->number_format,
                'date' => now(),
                'duedate' => now()->addDays(30),
                'currency' => $estimate->currency,
                'subtotal' => $estimate->subtotal,
                'total_tax' => $estimate->total_tax,
                'total' => $estimate->total,
                'adjustment' => $estimate->adjustment,
                'status' => Estimate::STATUS_DRAFT,
                'datecreated' => now(),
                'addedfrom' => Auth::id(),
                'hash' => Str::random(32),
                'billing_street' => $estimate->billing_street,
                'billing_city' => $estimate->billing_city,
                'billing_state' => $estimate->billing_state,
                'billing_zip' => $estimate->billing_zip,
                'billing_country' => $estimate->billing_country,
                'shipping_street' => $estimate->shipping_street,
                'shipping_city' => $estimate->shipping_city,
                'shipping_state' => $estimate->shipping_state,
                'shipping_zip' => $estimate->shipping_zip,
                'shipping_country' => $estimate->shipping_country,
                'include_shipping' => $estimate->include_shipping,
                'show_shipping_on_invoice' => $estimate->show_shipping_on_estimate,
                'show_quantity_as' => $estimate->show_quantity_as,
                'project_id' => $estimate->project_id,
                'sale_agent' => $estimate->sale_agent,
                'discount_percent' => $estimate->discount_percent,
                'discount_total' => $estimate->discount_total,
                'discount_type' => $estimate->discount_type,
                'clientnote' => $estimate->clientnote,
                'adminnote' => $estimate->adminnote,
                'terms' => $estimate->terms,
                'reference_no' => $estimate->reference_no,
            ]);

            foreach ($estimate->items as $item) {
                Itemable::create([
                    'rel_id' => $invoice->id,
                    'rel_type' => 'invoice',
                    'description' => $item->description,
                    'qty' => $item->qty ?? 1,
                    'rate' => $item->rate ?? 0,
                    'amount' => $item->amount ?? 0,
                    'tax_rate' => $item->tax_rate ?? 0,
                    'item_order' => $item->item_order ?? 0,
                ]);
            }

            $estimate->update(['status' => Estimate::STATUS_INVOICED, 'invoiceid' => $invoice->id, 'invoiced_date' => now()]);

            DB::commit();

            return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Invoice created from estimate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error converting estimate to invoice: ' . $e->getMessage());
        }
    }
}