<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\PaymentMode;
use App\Models\ExpenseCategory;
use App\Models\Currency;
use Illuminate\Http\Request;

class ExpensePaneController extends Controller
{
    public function create(Request $request)
    {
        if (!auth()->user()->can('expenses_create')) {
            abort(403);
        }

        $clients = Client::all();
        $projects = Project::all();
        $paymentModes = PaymentMode::all();
        $invoices = Invoice::all();
        $currencies = Currency::all();
        $categories = ExpenseCategory::all();

        return view('admin.expenses.pane.create', compact('clients', 'projects', 'paymentModes', 'invoices', 'categories', 'currencies'));
    }

    public function edit($id)
    {
        if (!auth()->user()->can('expenses_edit')) {
            abort(403);
        }

        $expense = Expense::findOrFail($id);
        $clients = Client::all();
        $projects = Project::all();
        $paymentModes = PaymentMode::all();
        $invoices = Invoice::all();
        $currencies = Currency::all();
        $categories = ExpenseCategory::all();

        return view('admin.expenses.pane.edit', compact('expense', 'clients', 'projects', 'paymentModes', 'invoices', 'categories', 'currencies'));
    }

    public function show($id)
    {
        if (!auth()->user()->can('expenses_view')) {
            abort(403);
        }

        $expense = Expense::with(['client', 'project', 'invoice', 'creator', 'categoryRel', 'currency'])->findOrFail($id);

        return view('admin.expenses.pane.show', compact('expense'));
    }
}
