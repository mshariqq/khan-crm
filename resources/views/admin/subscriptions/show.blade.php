@extends('layouts.admin')

@section('breadcrumb', 'Subscription Details')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $subscription->name }}</h1>
            <p class="text-slate-500 text-sm">Viewing subscription details for <a href="{{ route('admin.clients.show', $subscription->client->userid) }}" class="hover:text-blue-600">{{ $subscription->client->company ?? 'Unknown Client' }}</a>.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('subscriptions_edit')
            <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="bi bi-pencil-square"></i> Edit Subscription
            </a>
            @endcan
            <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Description</h3>
                <div class="text-slate-600 leading-relaxed whitespace-pre-wrap">
                    {{ $subscription->description ?? 'No detailed description provided.' }}
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Terms</h3>
                <div class="text-slate-600 leading-relaxed whitespace-pre-wrap">
                    {{ $subscription->terms ?? 'No terms specified.' }}
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Related Invoices</h3>
                @if($subscription->invoices->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-2 text-sm font-semibold text-slate-600">Invoice #</th>
                                    <th class="px-4 py-2 text-sm font-semibold text-slate-600">Date</th>
                                    <th class="px-4 py-2 text-sm font-semibold text-slate-600">Status</th>
                                    <th class="px-4 py-2 text-sm font-semibold text-slate-600 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($subscription->invoices as $invoice)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-slate-800">{{ $invoice->number ?? 'INV-'.$invoice->id }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600">{{ \Carbon\Carbon::parse($invoice->date)->format('M j, Y') }}</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusMap = [1 => 'Unpaid', 2 => 'Paid', 3 => 'Overdue', 4 => 'Cancelled'];
                                                $status = $statusMap[$invoice->status] ?? 'Unknown';
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-bold rounded-full
                                                {{ $invoice->status == 2 ? 'bg-green-100 text-green-700' : ($invoice->status == 3 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-800 font-medium text-right">{{ app_format_money($invoice->total, $invoice->currency) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 border-t border-slate-200">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-sm font-semibold text-slate-700 text-right">Total</td>
                                    <td class="px-4 py-3 text-sm font-bold text-slate-800 text-right">{{ app_format_money($subscription->invoices->sum('total'), $subscription->currency) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-slate-500 text-sm">No invoices associated with this subscription.</p>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Summary</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status</span>
                        <span>
                            @if($subscription->status)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Active</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Inactive</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Client</span>
                        <a href="{{ route('admin.clients.show', $subscription->client->userid) }}" class="text-slate-800 font-medium text-right hover:text-blue-600">{{ $subscription->client->company ?? 'N/A' }}</a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Start Date</span>
                        <span class="text-slate-800 font-medium">{{ \Carbon\Carbon::parse($subscription->date)->format('M j, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Next Billing</span>
                        <span class="text-slate-800 font-medium">{{ $subscription->next_billing_cycle ? \Carbon\Carbon::parse($subscription->next_billing_cycle)->format('M j, Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Ends At</span>
                        <span class="text-slate-800 font-medium">{{ $subscription->ends_at ? \Carbon\Carbon::parse($subscription->ends_at)->format('M j, Y') : 'Ongoing' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Project</span>
                        <a href="{{ route('admin.projects.show', $subscription->project->id) }}" class="text-slate-800 font-medium hover:text-blue-600">{{ $subscription->project->name ?? 'N/A' }}</a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Currency</span>
                        <span class="text-slate-800 font-medium">{{ $subscription->currency->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Quantity</span>
                        <span class="text-slate-800 font-medium">{{ $subscription->quantity ?? 1 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Total Invoiced</span>
                        <span class="text-slate-800 font-bold">{{ app_format_money($subscription->invoices->sum('total'), $subscription->currency) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Audit</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Created</span>
                        <span class="text-slate-800 font-medium">{{ $subscription->created ? \Carbon\Carbon::parse($subscription->created)->format('M j, Y H:i') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Created By</span>
                        <span class="text-slate-800 font-medium">{{ $subscription->creator->firstname ?? 'N/A' }} {{ $subscription->creator->lastname ?? '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Date Subscribed</span>
                        <span class="text-slate-800 font-medium">{{ $subscription->date_subscribed ? \Carbon\Carbon::parse($subscription->date_subscribed)->format('M j, Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endSection