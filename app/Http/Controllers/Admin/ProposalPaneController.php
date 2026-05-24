<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Project;
use App\Models\Lead;
use App\Models\Staff;
use App\Models\Item;
use App\Models\Tax;
use Illuminate\Http\Request;

class ProposalPaneController extends Controller
{
    public function create(Request $request)
    {
        if (!auth()->user()->can('proposals_create')) {
            abort(403);
        }

        $countries = Country::all();
        $currencies = Currency::all();
        $projects = Project::all();
        $leads = Lead::all();
        $staff = Staff::all();
        $items = Item::all();
        $taxes = Tax::all();

        return view('admin.proposals.pane.create', compact('countries', 'currencies', 'projects', 'leads', 'staff', 'items', 'taxes'));
    }

    public function edit($id)
    {
        if (!auth()->user()->can('proposals_edit')) {
            abort(403);
        }

        $proposal = Proposal::with('items')->findOrFail($id);
        $countries = Country::all();
        $currencies = Currency::all();
        $projects = Project::all();
        $leads = Lead::all();
        $staff = Staff::all();
        $items = Item::all();
        $taxes = Tax::all();

        return view('admin.proposals.pane.edit', compact('proposal', 'countries', 'currencies', 'projects', 'leads', 'staff', 'items', 'taxes'));
    }

    public function show($id)
    {
        if (!auth()->user()->can('proposals_view')) {
            abort(403);
        }

        $proposal = Proposal::with(['currency', 'country', 'items', 'creator', 'assignedStaff', 'project', 'comments.staff', 'notes.creator', 'reminders'])->findOrFail($id);

        $pStatusMap = [
            Proposal::STATUS_OPEN => ['label' => 'Open', 'class' => 'bg-blue-100 text-blue-700'],
            Proposal::STATUS_ACCEPTED => ['label' => 'Accepted', 'class' => 'bg-emerald-100 text-emerald-700'],
            Proposal::STATUS_DECLINED => ['label' => 'Declined', 'class' => 'bg-red-100 text-red-700'],
        ];
        $ps = $pStatusMap[$proposal->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-600'];

        return view('admin.proposals.pane.show', compact('proposal', 'pStatusMap', 'ps'));
    }
}