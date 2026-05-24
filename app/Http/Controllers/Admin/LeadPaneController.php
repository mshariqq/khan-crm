<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Proposal;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Item;
use App\Models\Tax;
use Illuminate\Http\Request;

class LeadPaneController extends Controller
{
    public function showProposals($leadId)
    {
        if (!auth()->user()->can('proposals_view')) {
            abort(403);
        }

        $lead = Lead::with('proposals')->findOrFail($leadId);

        return view('admin.leads.pane.proposals', compact('lead'));
    }

    public function createProposal($leadId)
    {
        if (!auth()->user()->can('proposals_create')) {
            abort(403);
        }

        $lead = Lead::findOrFail($leadId);
        $countries = Country::all();
        $currencies = Currency::all();
        $projects = Project::all();
        $leads = Lead::all();
        $staff = Staff::all();
        $items = Item::all();
        $taxes = Tax::all();

        return view('admin.proposals.pane.create', compact('countries', 'currencies', 'projects', 'leads', 'staff', 'items', 'taxes', 'lead'));
    }
}
