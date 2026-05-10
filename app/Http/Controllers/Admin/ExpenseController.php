<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('expenses_view')) abort(403);

        $query = Expense::with(['client', 'project', 'creator', 'categoryRel']);

        if ($request->filled('client')) $query->where('clientid', $request->client);
        if ($request->filled('project')) $query->where('project_id', $request->project);
        if ($request->filled('category')) $query->where('category', $request->category);

        $total = $query->count();
        $expenses = $query->paginate(15)->withQueryString();
        $clients = Client::all();
        $projects = Project::all();
        $categories = Expense::select('category')->distinct()->whereNotNull('category')->pluck('category');
        $billableCount = Expense::where('billable', 1)->count();
        $nonBillableCount = Expense::where('billable', 0)->count();

        return view('admin.expenses.index', compact('expenses', 'total', 'clients', 'projects', 'categories', 'billableCount', 'nonBillableCount'));
    }

    public function create()
    {
        if (!Auth::user()->can('expenses_create')) abort(403);

        $clients = Client::all();
        $projects = Project::all();
        $paymentModes = PaymentMode::all();
        $invoices = Invoice::all();
        $currencies = \App\Models\Currency::all();
        $categories = \App\Models\ExpenseCategory::all();

        return view('admin.expenses.create', compact('clients', 'projects', 'paymentModes', 'invoices', 'categories', 'currencies'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('expenses_create')) abort(403);

        $request->validate([
            'expense_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'clientid' => 'required|integer',
            'project_id' => 'nullable|integer',
            'currency' => 'required|integer',
            'category' => 'nullable|integer',
            'paymentmode' => 'nullable|string',
            'invoiceid' => 'nullable|integer',
            'tax' => 'nullable|integer',
            'tax2' => 'nullable|integer',
            'billable' => 'nullable|boolean',
            'reference_no' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'recurring' => 'nullable|boolean',
            'recurring_type' => 'nullable|string|max:10',
            'repeat_every' => 'nullable|integer',
            'cycles' => 'nullable|integer',
            'total_cycles' => 'nullable|integer',
            'custom_recurring' => 'nullable|boolean',
            'recurring_from' => 'nullable|integer',
            'create_invoice_billable' => 'nullable|boolean',
            'send_invoice_to_customer' => 'nullable|boolean',
        ]);

        Expense::create([
            'expense_name' => $request->expense_name,
            'amount' => $request->amount,
            'date' => $request->date,
            'clientid' => $request->clientid,
            'project_id' => $request->project_id,
            'currency' => $request->currency,
            'category' => $request->category,
            'paymentmode' => $request->paymentmode,
            'invoiceid' => $request->invoiceid,
            'tax' => $request->tax,
            'tax2' => $request->tax2 ?? 0,
            'billable' => $request->boolean('billable', false),
            'reference_no' => $request->reference_no,
            'note' => $request->note,
            'recurring' => $request->boolean('recurring', false),
            'recurring_type' => $request->recurring_type,
            'repeat_every' => $request->repeat_every,
            'cycles' => $request->cycles ?? 0,
            'total_cycles' => $request->total_cycles ?? 0,
            'custom_recurring' => $request->boolean('custom_recurring', false),
            'recurring_from' => $request->recurring_from,
            'create_invoice_billable' => $request->boolean('create_invoice_billable', false),
            'send_invoice_to_customer' => $request->boolean('send_invoice_to_customer', false),
            'dateadded' => now(),
            'addedfrom' => Auth::id(),
        ]);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('expenses_edit')) abort(403);

        $expense = Expense::findOrFail($id);
        $clients = Client::all();
        $projects = Project::all();
        $paymentModes = PaymentMode::all();
        $invoices = Invoice::all();
        $currencies = \App\Models\Currency::all();
        $categories = \App\Models\ExpenseCategory::all();

        return view('admin.expenses.edit', compact('expense', 'clients', 'projects', 'paymentModes', 'invoices', 'categories', 'currencies'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('expenses_edit')) abort(403);

        $request->validate([
            'expense_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'clientid' => 'required|integer',
            'project_id' => 'nullable|integer',
            'currency' => 'required|integer',
            'category' => 'nullable|integer',
            'paymentmode' => 'nullable|string',
            'invoiceid' => 'nullable|integer',
            'tax' => 'nullable|integer',
            'tax2' => 'nullable|integer',
            'billable' => 'nullable|boolean',
            'reference_no' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'recurring' => 'nullable|boolean',
            'recurring_type' => 'nullable|string|max:10',
            'repeat_every' => 'nullable|integer',
            'cycles' => 'nullable|integer',
            'total_cycles' => 'nullable|integer',
            'custom_recurring' => 'nullable|boolean',
            'recurring_from' => 'nullable|integer',
            'create_invoice_billable' => 'nullable|boolean',
            'send_invoice_to_customer' => 'nullable|boolean',
        ]);

        $expense = Expense::findOrFail($id);
        
        $data = $request->all();
        $data['billable'] = $request->boolean('billable', false);
        $data['recurring'] = $request->boolean('recurring', false);
        $data['custom_recurring'] = $request->boolean('custom_recurring', false);
        $data['create_invoice_billable'] = $request->boolean('create_invoice_billable', false);
        $data['send_invoice_to_customer'] = $request->boolean('send_invoice_to_customer', false);

        $expense->update($data);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense updated successfully!');
    }

    public function show($id)
    {
        if (!Auth::user()->can('expenses_view')) abort(403);

        $expense = Expense::with(['client', 'project', 'invoice', 'creator'])->findOrFail($id);
        return view('admin.expenses.show', compact('expense'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('expenses_delete')) abort(403);

        Expense::findOrFail($id)->delete();
        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted successfully!');
    }
}