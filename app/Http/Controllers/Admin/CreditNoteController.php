<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('credit_notes_view')) abort(403);

        $query = Credit::with([]);

        if ($request->filled('client')) {
            $query->whereIn('invoice_id', Invoice::where('clientid', $request->client)->pluck('id'));
        }

        $total = $query->count();
        $credits = $query->paginate(15)->withQueryString();
        $clients = Client::all();
        $totalAmount = Credit::sum('amount');

        return view('admin.creditnotes.index', compact('credits', 'total', 'clients', 'totalAmount'));
    }

    public function create()
    {
        if (!Auth::user()->can('credit_notes_create')) abort(403);
        $invoices = Invoice::all();
        $staff = Staff::all();
        return view('admin.creditnotes.create', compact('invoices', 'staff'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('credit_notes_create')) abort(403);

        $request->validate([
            'invoice_id' => 'required|integer',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'staff_id' => 'nullable|integer',
        ]);

        Credit::create([
            'invoice_id' => $request->invoice_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'date_applied' => $request->date_applied ?? now(),
            'staff_id' => $request->staff_id ?? Auth::id(),
        ]);

        return redirect()->route('admin.creditnotes.index')->with('success', 'Credit note created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('credit_notes_edit')) abort(403);
        $credit = Credit::findOrFail($id);
        $invoices = Invoice::all();
        $staff = Staff::all();
        return view('admin.creditnotes.edit', compact('credit', 'invoices', 'staff'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('credit_notes_edit')) abort(403);

        $request->validate([
            'invoice_id' => 'required|integer',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $credit = Credit::findOrFail($id);
        $credit->update($request->all());

        return redirect()->route('admin.creditnotes.index')->with('success', 'Credit note updated successfully!');
    }

    public function show($id)
    {
        if (!Auth::user()->can('credit_notes_view')) abort(403);
        $credit = Credit::findOrFail($id);
        return view('admin.creditnotes.show', compact('credit'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('credit_notes_delete')) abort(403);
        Credit::findOrFail($id)->delete();
        return redirect()->route('admin.creditnotes.index')->with('success', 'Credit note deleted successfully!');
    }
}