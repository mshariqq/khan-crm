<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Project;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('subscriptions_view')) abort(403);

        $query = Subscription::with(['client', 'currencyRel', 'invoices']);

        if ($request->filled('client')) {
            $query->where('clientid', $request->client);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $total = $query->count();
        $subscriptions = $query->paginate(15)->withQueryString();
        $clients = Client::all();
        $activeCount = Subscription::where('status', 1)->count();
        $totalRevenue = Invoice::whereIn('subscription_id', Subscription::pluck('id'))->sum('total');

        return view('admin.subscriptions.index', compact('subscriptions', 'total', 'clients', 'activeCount', 'totalRevenue'));
    }

    public function create()
    {
        if (!Auth::user()->can('subscriptions_create')) abort(403);
        $clients = Client::all();
        $currencies = Currency::all();
        $projects = Project::all();
        return view('admin.subscriptions.create', compact('clients', 'currencies', 'projects'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('subscriptions_create')) abort(403);

        $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'terms' => 'nullable|string',
            'clientid' => 'required|integer',
            'date' => 'required|date',
            'currency' => 'required|integer',
            'status' => 'nullable|integer',
            'quantity' => 'nullable|numeric',
            'project_id' => 'nullable|integer',
            'stripe_plan_id' => 'nullable|string|max:255',
            'stripe_subscription_id' => 'nullable|string|max:255',
        ]);

        Subscription::create([
            'name' => $request->name,
            'description' => $request->description,
            'terms' => $request->terms,
            'clientid' => $request->clientid,
            'date' => $request->date,
            'currency' => $request->currency,
            'status' => $request->status ?? 1,
            'quantity' => $request->quantity,
            'project_id' => $request->project_id,
            'stripe_plan_id' => $request->stripe_plan_id,
            'stripe_subscription_id' => $request->stripe_subscription_id,
            'hash' => Str::random(32),
            'created' => now(),
            'created_from' => Auth::id(),
        ]);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('subscriptions_edit')) abort(403);
        $subscription = Subscription::findOrFail($id);
        $clients = Client::all();
        $currencies = Currency::all();
        $projects = Project::all();
        return view('admin.subscriptions.edit', compact('subscription', 'clients', 'currencies', 'projects'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('subscriptions_edit')) abort(403);

        $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'terms' => 'nullable|string',
            'clientid' => 'required|integer',
            'date' => 'required|date',
            'currency' => 'required|integer',
            'status' => 'nullable|integer',
            'quantity' => 'nullable|numeric',
            'project_id' => 'nullable|integer',
            'stripe_plan_id' => 'nullable|string|max:255',
            'stripe_subscription_id' => 'nullable|string|max:255',
        ]);

        $subscription = Subscription::findOrFail($id);
        $subscription->update($request->all());

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription updated successfully!');
    }

    public function show($id)
    {
        if (!Auth::user()->can('subscriptions_view')) abort(403);
        $subscription = Subscription::with(['client', 'currency', 'project', 'invoices'])->findOrFail($id);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('subscriptions_delete')) abort(403);
        Subscription::findOrFail($id)->delete();
        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription deleted successfully!');
    }
}