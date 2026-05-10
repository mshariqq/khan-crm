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
use App\Models\Note;
use App\Models\Reminder;
use App\Models\ProposalComment;
use App\Models\Itemable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('proposals_view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Proposal::query()->with(['currencyRel', 'countryRel', 'project']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', '%' . $search . '%')
                  ->orWhere('proposal_to', 'like', '%' . $search . '%');
            });
        }

        $total = $query->count();
        $open = Proposal::where('status', 1)->count();
        $accepted = Proposal::where('status', 2)->count();
        $declined = Proposal::where('status', 3)->count();
        $proposals = $query->paginate(15)->withQueryString();

        return view('admin.proposals.index', compact('proposals', 'total', 'open', 'accepted', 'declined'));
    }

    public function create()
    {
        if (!Auth::user()->can('proposals_create')) {
            abort(403, 'Unauthorized action.');
        }

        $countries = Country::all();
        $currencies = Currency::all();
        $projects = Project::all();
        $leads = Lead::all();
        $staff = Staff::all();
        $items = Item::all();
        $taxes = Tax::all();

        return view('admin.proposals.create', compact('countries', 'currencies', 'projects', 'leads', 'staff', 'items', 'taxes'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('proposals_create')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:191',
            'content' => 'nullable|string',
            'proposal_to' => 'required|string|max:191',
            'currency' => 'required|integer',
            'country' => 'nullable|integer',
            'rel_id' => 'nullable|integer',
            'rel_type' => 'nullable|string|max:50',
            'assigned' => 'nullable|integer',
            'date' => 'required|date',
            'open_till' => 'nullable|date',
            'status' => 'nullable|integer',
            'project_id' => 'nullable|integer',
            'email' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:50',
            'allow_comments' => 'nullable|boolean',
            'subtotal' => 'nullable|numeric',
            'total_tax' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'adjustment' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric',
            'discount_total' => 'nullable|numeric',
            'discount_type' => 'nullable|string|max:30',
            'show_quantity_as' => 'nullable|integer',
        ]);

        // Set defaults
        $validated['subtotal'] = $validated['subtotal'] ?? 0;
        $validated['total_tax'] = $validated['total_tax'] ?? 0;
        $validated['total'] = $validated['total'] ?? 0;
        $validated['adjustment'] = $validated['adjustment'] ?? 0;
        $validated['discount_percent'] = $validated['discount_percent'] ?? 0;
        $validated['discount_total'] = $validated['discount_total'] ?? 0;
        
        $proposalData = $validated;
        $proposalData['addedfrom'] = Auth::id();
        $proposalData['datecreated'] = now();
        $proposalData['hash'] = Str::random(32);
        
        // Required fields with defaults from migration
        $proposalData['show_quantity_as'] = $validated['show_quantity_as'] ?? 1;
        $proposalData['status'] = $validated['status'] ?? 1;
        
        if (!empty($validated['assigned'])) $proposalData['assigned'] = $validated['assigned'];
        if (!empty($validated['country'])) $proposalData['country'] = $validated['country'];
        if (!empty($validated['project_id'])) $proposalData['project_id'] = $validated['project_id'];
        if (!empty($validated['rel_type'])) $proposalData['rel_type'] = $validated['rel_type'];
        if (!empty($validated['rel_id'])) $proposalData['rel_id'] = $validated['rel_id'];
        if (!empty($validated['open_till'])) $proposalData['open_till'] = $validated['open_till'];
        
        $proposal = Proposal::create($proposalData);

        // Save items
        if ($request->has('item_description')) {
            $descriptions = $request->item_description;
            $qtys = $request->item_qty ?? [];
            $rates = $request->item_rate ?? [];
            $amounts = $request->item_amount ?? [];

            foreach ($descriptions as $index => $description) {
                if (!empty($description)) {
                    Itemable::create([
                        'rel_id' => $proposal->id,
                        'rel_type' => 'proposal',
                        'description' => $description,
                        'qty' => $qtys[$index] ?? 1,
                        'rate' => $rates[$index] ?? 0,
                        'amount' => $amounts[$index] ?? 0,
                        'item_order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('admin.proposals.index')->with('success', 'Proposal created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('proposals_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $proposal = Proposal::findOrFail($id);
        $countries = Country::all();
        $currencies = Currency::all();
        $projects = Project::all();
        $leads = Lead::all();
        $staff = Staff::all();
        $items = Item::all();
        $taxes = Tax::all();

        return view('admin.proposals.edit', compact('proposal', 'countries', 'currencies', 'projects', 'leads', 'staff', 'items', 'taxes'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('proposals_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'subject' => 'required|string|max:191',
            'content' => 'nullable|string',
            'proposal_to' => 'required|string|max:191',
            'currency' => 'required|integer',
            'country' => 'nullable|integer',
            'rel_id' => 'nullable|integer',
            'rel_type' => 'nullable|string|max:50',
            'assigned' => 'nullable|integer',
            'date' => 'required|date',
            'open_till' => 'nullable|date',
            'status' => 'nullable|integer',
            'project_id' => 'nullable|integer',
            'email' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:50',
            'allow_comments' => 'nullable|boolean',
            'subtotal' => 'nullable|numeric',
            'total_tax' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'adjustment' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric',
            'discount_total' => 'nullable|numeric',
            'discount_type' => 'nullable|string|max:30',
            'show_quantity_as' => 'nullable|integer',
        ]);

        $proposal = Proposal::findOrFail($id);
        $proposal->update($request->all());

        return redirect()->route('admin.proposals.index')->with('success', 'Proposal updated successfully!');
    }

    public function show($id)
    {
        if (!Auth::user()->can('proposals_view')) {
            abort(403, 'Unauthorized action.');
        }

        $proposal = Proposal::with(['currency', 'country', 'items', 'creator', 'assignedStaff', 'project'])->findOrFail($id);

        return view('admin.proposals.show', compact('proposal'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('proposals_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $proposal = Proposal::findOrFail($id);
        $proposal->delete();

        return redirect()->route('admin.proposals.index')->with('success', 'Proposal deleted successfully!');
    }

    public function storeNote(Request $request, $id)
    {
        if (!Auth::user()->can('proposals_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate(['content' => 'required|string']);

        $proposal = Proposal::findOrFail($id);
        Note::create([
            'content' => $request->content,
            'rel_id' => $proposal->id,
            'rel_type' => 'proposal',
            'dateadded' => now(),
            'addedfrom' => Auth::id(),
        ]);

        return redirect()->route('admin.proposals.show', $proposal->id)->with('success', 'Note added!');
    }

    public function storeComment(Request $request, $id)
    {
        if (!Auth::user()->can('proposals_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate(['content' => 'required|string']);

        $proposal = Proposal::findOrFail($id);
        ProposalComment::create([
            'content' => $request->content,
            'proposalid' => $proposal->id,
            'staffid' => Auth::id(),
            'dateadded' => now(),
        ]);

        return redirect()->route('admin.proposals.show', $proposal->id)->with('success', 'Comment added!');
    }

    public function destroyComment($id)
    {
        if (!Auth::user()->can('proposals_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $comment = ProposalComment::findOrFail($id);
        $comment->delete();

        return back()->with('success', 'Comment deleted!');
    }

    public function storeReminder(Request $request, $id)
    {
        if (!Auth::user()->can('proposals_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'content' => 'required|string',
            'date' => 'required|date',
        ]);

        $proposal = Proposal::findOrFail($id);
        Reminder::create([
            'description' => $request->content,
            'rel_id' => $proposal->id,
            'rel_type' => 'proposal',
            'date' => $request->date,
            'staff' => Auth::id(),
            'creator' => Auth::id(),
            'is_notified' => 0,
        ]);

        return redirect()->route('admin.proposals.show', $proposal->id)->with('success', 'Reminder added!');
    }

    public function markAccepted($id)
    {
        if (!Auth::user()->can('proposals_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $proposal = Proposal::findOrFail($id);
        $proposal->update(['status' => 2]);

        return redirect()->route('admin.proposals.show', $proposal->id)->with('success', 'Proposal accepted!');
    }

    public function markDeclined($id)
    {
        if (!Auth::user()->can('proposals_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $proposal = Proposal::findOrFail($id);
        $proposal->update(['status' => 3]);

        return redirect()->route('admin.proposals.show', $proposal->id)->with('success', 'Proposal declined!');
    }

    public function sendProposal($id)
    {
        if (!Auth::user()->can('proposals_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $proposal = Proposal::findOrFail($id);
        $proposal->update(['status' => 1]);

        return redirect()->route('admin.proposals.show', $proposal->id)->with('success', 'Proposal sent!');
    }
}