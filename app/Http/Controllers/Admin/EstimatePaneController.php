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
use Illuminate\Http\Request;

class EstimatePaneController extends Controller
{
    public function create()
    {
        if (!auth()->user()->can('estimates_create')) {
            abort(403);
        }

        $clients = Client::select('userid', 'company')->get();
        $currencies = Currency::all();
        $staff = Staff::all();
        $projects = Project::select('id', 'name')->get();
        $items = Item::all();
        $taxes = Tax::all();

        return view('admin.estimates.pane.create', compact('clients', 'currencies', 'staff', 'projects', 'items', 'taxes'));
    }

    public function edit($id)
    {
        if (!auth()->user()->can('estimates_edit')) {
            abort(403);
        }

        $estimate = Estimate::with('items')->findOrFail($id);
        $clients = Client::select('userid', 'company')->get();
        $currencies = Currency::all();
        $staff = Staff::all();
        $projects = Project::select('id', 'name')->get();
        $items = Item::all();
        $taxes = Tax::all();

        return view('admin.estimates.pane.edit', compact('estimate', 'clients', 'currencies', 'staff', 'projects', 'items', 'taxes'));
    }

    public function show($id)
    {
        if (!auth()->user()->can('estimates_view')) {
            abort(403);
        }

        $estimate = Estimate::with([
            'client', 'currency', 'creator', 'saleAgent', 'project', 'items', 'notes', 'reminders'
        ])->findOrFail($id);

        return view('admin.estimates.pane.show', compact('estimate'));
    }
}