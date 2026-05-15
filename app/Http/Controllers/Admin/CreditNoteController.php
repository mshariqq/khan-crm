<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use App\Models\Credit;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Staff;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Tax;
use App\Models\Project;
use App\Models\Itemable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('credit_notes_view')) abort(403);

        $query = CreditNote::with(['client', 'currencyRel']);

        if ($request->filled('client')) {
            $query->where('clientid', $request->client);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $total = $query->count();
        $creditNotes = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();
        $clients = Client::all();
        $totalAmount = CreditNote::sum('total');

        return view('admin.creditnotes.index', compact('creditNotes', 'total', 'clients', 'totalAmount'));
    }

    public function create()
    {
        if (!Auth::user()->can('credit_notes_create')) abort(403);
        
        $clients = Client::all();
        $currencies = Currency::all();
        $items = Item::all();
        $taxes = Tax::all();
        $projects = Project::all();
        
        return view('admin.creditnotes.create', compact('clients', 'currencies', 'items', 'taxes', 'projects'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('credit_notes_create')) abort(403);

        $validated = $request->validate([
            'clientid' => 'required|integer',
            'date' => 'required|date',
            'currency' => 'required|integer',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
            'number' => 'required|integer',
            'prefix' => 'nullable|string',
            'discount_type' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $creditNote = CreditNote::create(array_merge($validated, [
                'datecreated' => now(),
                'addedfrom' => Auth::id(),
                'status' => 1,
            ]));

            // Handle items
            if ($request->has('items')) {
                foreach ($request->items as $index => $itemData) {
                    Itemable::create([
                        'rel_id' => $creditNote->id,
                        'rel_type' => 'creditnote',
                        'description' => $itemData['description'],
                        'long_description' => $itemData['long_description'] ?? '',
                        'qty' => $itemData['qty'],
                        'rate' => $itemData['rate'],
                        'amount' => $itemData['amount'],
                        'item_order' => $index,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.creditnotes.show', $creditNote->id)->with('success', 'Credit Note created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        if (!Auth::user()->can('credit_notes_view')) abort(403);
        
        $creditNote = CreditNote::with(['client', 'currencyRel', 'items', 'credits.invoice'])->findOrFail($id);
        
        // Find unpaid invoices for this client to apply credit
        $availableInvoices = Invoice::where('clientid', $creditNote->clientid)
            ->whereIn('status', [1, 2]) // Unpaid or Partially Paid
            ->get();

        return view('admin.creditnotes.show', compact('creditNote', 'availableInvoices'));
    }

    public function edit($id)
    {
        if (!Auth::user()->can('credit_notes_edit')) abort(403);
        
        $creditNote = CreditNote::with('items')->findOrFail($id);
        $clients = Client::all();
        $currencies = Currency::all();
        $items = Item::all();
        $taxes = Tax::all();
        $projects = Project::all();

        return view('admin.creditnotes.edit', compact('creditNote', 'clients', 'currencies', 'items', 'taxes', 'projects'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('credit_notes_edit')) abort(403);

        $validated = $request->validate([
            'clientid' => 'required|integer',
            'date' => 'required|date',
            'currency' => 'required|integer',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
            'number' => 'required|integer',
            'prefix' => 'nullable|string',
        ]);

        $creditNote = CreditNote::findOrFail($id);
        $creditNote->update($validated);

        return redirect()->route('admin.creditnotes.show', $creditNote->id)->with('success', 'Credit Note updated successfully!');
    }

    public function applyToInvoice(Request $request, $id)
    {
        if (!Auth::user()->can('credit_notes_edit')) abort(403);

        $request->validate([
            'invoice_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $creditNote = CreditNote::findOrFail($id);
        $invoice = Invoice::findOrFail($request->invoice_id);

        if ($request->amount > $creditNote->remainingCredit()) {
            return redirect()->back()->with('error', 'Amount exceeds available credit.');
        }

        if ($request->amount > $invoice->amountDue()) {
            return redirect()->back()->with('error', 'Amount exceeds invoice balance.');
        }

        Credit::create([
            'credit_id' => $creditNote->id,
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'date' => now(),
            'date_applied' => now(),
            'staff_id' => Auth::id(),
        ]);

        // Update invoice status
        $totalSettled = $invoice->totalSettled();
        if ($totalSettled >= $invoice->total) {
            $invoice->update(['status' => 3]); // Paid
        } elseif ($totalSettled > 0) {
            $invoice->update(['status' => 2]); // Partially Paid
        }

        return redirect()->route('admin.creditnotes.show', $creditNote->id)->with('success', 'Credit applied to invoice successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('credit_notes_delete')) abort(403);
        
        $creditNote = CreditNote::findOrFail($id);
        
        // Before deleting, we should probably handle the applied credits
        // For simplicity in this CRM, we'll just delete them, but robust CRM might want to revert invoice statuses
        foreach ($creditNote->credits as $credit) {
            $invoice = $credit->invoice;
            $credit->delete();
            
            if ($invoice) {
                $totalSettled = $invoice->totalSettled();
                if ($totalSettled <= 0) {
                    $invoice->update(['status' => 1]); // Unpaid
                } elseif ($totalSettled < $invoice->total) {
                    $invoice->update(['status' => 2]); // Partially Paid
                }
            }
        }

        $creditNote->delete();
        return redirect()->route('admin.creditnotes.index')->with('success', 'Credit Note deleted successfully!');
    }
}
