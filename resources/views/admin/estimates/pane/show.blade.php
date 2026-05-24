@extends('layouts.pane')

@php
$statusMap = [
    1 => ['label' => 'Draft', 'class' => 'bg-slate-100 text-slate-700'],
    2 => ['label' => 'Sent', 'class' => 'bg-blue-100 text-blue-700'],
    3 => ['label' => 'Accepted', 'class' => 'bg-emerald-100 text-emerald-700'],
    4 => ['label' => 'Declined', 'class' => 'bg-red-100 text-red-700'],
    5 => ['label' => 'Converted', 'class' => 'bg-purple-100 text-purple-700'],
];
$s = $statusMap[$estimate->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-600'];
@endphp

@section('pane-content')
<div class="flex items-start justify-between mb-6">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <h1 class="text-xl font-bold text-slate-800">#{{ $estimate->prefix ?? '' }}{{ $estimate->number }}</h1>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $s['class'] }}">{{ $s['label'] }}</span>
        </div>
        <p class="text-sm text-slate-400">{{ optional($estimate->client)->company ?? 'No Client' }} &middot; {{ \Carbon\Carbon::parse($estimate->datecreated)->format('M d, Y') }}</p>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <button onclick="parent.openTaskPane('{{ route('admin.estimates.pane.edit', $estimate->id) }}', 'Estimate')" class="px-3 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-1.5">
            <i class="bi bi-pencil"></i> Edit
        </button>
    </div>
</div>

<div class="flex border-b border-slate-200 overflow-x-auto -mx-5 px-5 mb-5">
    <button class="tab-btn active px-4 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600 whitespace-nowrap" data-tab="overview">Overview</button>
    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="items">Items ({{ $estimate->items->count() }})</button>
    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="notes">Notes ({{ $estimate->notes->count() }})</button>
    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="reminders">Reminders ({{ $estimate->reminders->count() }})</button>
</div>

<div class="grid grid-cols-1 gap-5">
    {{-- Overview Tab --}}
    <div id="tab-overview" class="tab-content">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
            <div class="lg:col-span-3 space-y-5">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                        <i class="bi bi-info-circle text-slate-400"></i>
                        <h3 class="text-sm font-semibold text-slate-800">Estimate Details</h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estimate #</span>
                                <p class="mt-1 text-sm font-medium text-slate-800">{{ $estimate->prefix ?? '' }}{{ $estimate->number }}</p>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Reference</span>
                                <p class="mt-1 text-sm font-medium text-slate-800">{{ $estimate->reference_no ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date</span>
                                <p class="mt-1 text-sm font-medium text-slate-800">{{ \Carbon\Carbon::parse($estimate->date)->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Expiry Date</span>
                                <p class="mt-1 text-sm font-medium text-slate-800">{{ $estimate->expirydate ? \Carbon\Carbon::parse($estimate->expirydate)->format('M d, Y') : '-' }}</p>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Created By</span>
                                <p class="mt-1 text-sm font-medium text-slate-800">{{ optional($estimate->creator)->firstname ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sale Agent</span>
                                <p class="mt-1 text-sm font-medium text-slate-800">{{ optional($estimate->saleAgent)->firstname ?? 'Unassigned' }}</p>
                            </div>
                        </div>
                        @if($estimate->project)
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Project</span>
                            <p class="mt-1 text-sm font-medium text-blue-600">{{ $estimate->project->name }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                        <i class="bi bi-cash text-emerald-500"></i>
                        <h3 class="text-sm font-semibold text-slate-800">Financial</h3>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-500">Subtotal</span>
                            <span class="text-sm font-medium text-slate-800">{{ app_format_money($estimate->subtotal ?? 0, $estimate->currency) }}</span>
                        </div>
                        @if($estimate->discount_total > 0)
                        <div class="flex justify-between">
                            <span class="text-sm text-emerald-600">Discount</span>
                            <span class="text-sm font-medium text-emerald-600">-{{ app_format_money($estimate->discount_total ?? 0, $estimate->currency) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-500">Tax</span>
                            <span class="text-sm font-medium text-slate-800">{{ app_format_money($estimate->total_tax ?? 0, $estimate->currency) }}</span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-3">
                            <span class="text-base font-bold text-slate-800">Total</span>
                            <span class="text-base font-bold text-amber-600">{{ app_format_money($estimate->total ?? 0, $estimate->currency) }}</span>
                        </div>
                    </div>
                </div>

                @if($estimate->adminnote || $estimate->clientnote || $estimate->terms)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                        <i class="bi bi-sticky text-slate-400"></i>
                        <h3 class="text-sm font-semibold text-slate-800">Notes & Terms</h3>
                    </div>
                    <div class="p-5 space-y-4 text-sm">
                        @if($estimate->clientnote)
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Client Note</span>
                            <p class="text-slate-600">{{ $estimate->clientnote }}</p>
                        </div>
                        @endif
                        @if($estimate->adminnote)
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Admin Note</span>
                            <p class="text-slate-600">{{ $estimate->adminnote }}</p>
                        </div>
                        @endif
                        @if($estimate->terms)
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Terms</span>
                            <p class="text-slate-600">{{ $estimate->terms }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                        <i class="bi bi-bar-chart text-slate-400"></i>
                        <h3 class="text-sm font-semibold text-slate-800">Status</h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Current Status</span>
                            <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $estimate->status == 3 ? 'bg-emerald-500' : ($estimate->status == 4 ? 'bg-red-500' : ($estimate->status == 5 ? 'bg-purple-500' : ($estimate->status == 2 ? 'bg-blue-500' : 'bg-slate-400'))) }}"></span>
                                <span class="text-sm font-bold text-slate-700">{{ $s['label'] }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Sale Agent</span>
                            <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 text-[10px] font-bold">
                                    {{ strtoupper(substr(optional($estimate->saleAgent)->firstname ?? 'U', 0, 1)) }}
                                </div>
                                <span class="text-sm font-bold text-slate-700">{{ optional($estimate->saleAgent)->firstname ?? 'Unassigned' }}</span>
                            </div>
                        </div>
                        <div class="space-y-2 pt-2">
                            @if(in_array($estimate->status, [1, 2]))
                            <form action="{{ route('admin.estimates.mark_accepted', $estimate->id) }}" method="POST" data-pane-submit="1" data-pane-refresh="{{ route('admin.estimates.pane.show', $estimate->id) }}">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 flex items-center gap-2 justify-center">
                                    <i class="bi bi-check-circle"></i> Mark Accepted
                                </button>
                            </form>
                            <form action="{{ route('admin.estimates.mark_declined', $estimate->id) }}" method="POST" data-pane-submit="1" data-pane-refresh="{{ route('admin.estimates.pane.show', $estimate->id) }}">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 bg-red-500 text-white text-sm font-bold rounded-xl hover:bg-red-600 transition-all shadow-lg shadow-red-200 flex items-center gap-2 justify-center">
                                    <i class="bi bi-x-circle"></i> Mark Declined
                                </button>
                            </form>
                            @if(!$estimate->invoiceid)
                            <form action="{{ route('admin.estimates.convert_to_invoice', $estimate->id) }}" method="POST" data-pane-submit="1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 bg-amber-600 text-white text-sm font-bold rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-200 flex items-center gap-2 justify-center">
                                    <i class="bi bi-receipt"></i> Convert to Invoice
                                </button>
                            </form>
                            @endif
                            @endif
                            <a href="{{ route('admin.estimates.print', $estimate->id) }}" target="_blank" class="w-full px-4 py-2.5 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2 justify-center">
                                <i class="bi bi-printer"></i> Print / PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                        <i class="bi bi-building text-slate-400"></i>
                        <h3 class="text-sm font-semibold text-slate-800">Client</h3>
                    </div>
                    <div class="p-5">
                        @if($estimate->client)
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600"><i class="bi bi-building"></i></div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $estimate->client->company }}</p>
                                <p class="text-xs text-slate-400">{{ $estimate->client->email }}</p>
                            </div>
                        </div>
                        @if($estimate->client->phonenumber || $estimate->client->address)
                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            @if($estimate->client->phonenumber)
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">Phone</span>
                                <span class="text-xs font-medium text-slate-700">{{ $estimate->client->phonenumber }}</span>
                            </div>
                            @endif
                            @if($estimate->client->address)
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">Address</span>
                                <span class="text-xs font-medium text-slate-700 text-right max-w-[200px]">{{ $estimate->client->address }}</span>
                            </div>
                            @endif
                        </div>
                        @endif
                        @else
                        <p class="text-sm text-slate-400">No client linked</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                        <i class="bi bi-geo-alt text-slate-400"></i>
                        <h3 class="text-sm font-semibold text-slate-800">Shipping</h3>
                    </div>
                    <div class="p-5 space-y-3">
                        @if($estimate->billing_street || $estimate->shipping_street)
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Billing Address</span>
                            <div class="text-xs text-slate-600 space-y-0.5">
                                @if($estimate->billing_street)<p>{{ $estimate->billing_street }}</p>@endif
                                <p>{{ $estimate->billing_city ?? '' }}{{ $estimate->billing_city && $estimate->billing_state ? ', ' : '' }}{{ $estimate->billing_state ?? '' }} {{ $estimate->billing_zip ?? '' }}</p>
                                @if($estimate->billing_country)<p class="text-xs text-slate-400 mt-1">{{ $estimate->billing_country }}</p>@endif
                            </div>
                        </div>
                        <div class="pt-3 border-t border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Shipping Address</span>
                            <div class="text-xs text-slate-600 space-y-0.5">
                                @if($estimate->shipping_street)<p>{{ $estimate->shipping_street }}</p>@endif
                                <p>{{ $estimate->shipping_city ?? '' }}{{ $estimate->shipping_city && $estimate->shipping_state ? ', ' : '' }}{{ $estimate->shipping_state ?? '' }} {{ $estimate->shipping_zip ?? '' }}</p>
                                @if($estimate->shipping_country)<p class="text-xs text-slate-400 mt-1">{{ $estimate->shipping_country }}</p>@endif
                            </div>
                        </div>
                        <div class="pt-3 border-t border-slate-100 space-y-1.5">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400">Include Shipping</span>
                                <span class="font-medium {{ $estimate->include_shipping ? 'text-emerald-600' : 'text-slate-400' }}">{{ $estimate->include_shipping ? 'Yes' : 'No' }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400">Show on Estimate</span>
                                <span class="font-medium {{ $estimate->show_shipping_on_estimate ? 'text-emerald-600' : 'text-slate-400' }}">{{ $estimate->show_shipping_on_estimate ? 'Yes' : 'No' }}</span>
                            </div>
                        </div>
                        @else
                        <p class="text-xs text-slate-400">No address information</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                        <i class="bi bi-info-circle text-slate-400"></i>
                        <h3 class="text-sm font-semibold text-slate-800">Details</h3>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-xs text-slate-400">Estimate #</span>
                            <span class="text-xs font-bold text-slate-700">{{ $estimate->prefix ?? '' }}{{ $estimate->number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-slate-400">Date Created</span>
                            <span class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($estimate->datecreated)->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-slate-400">Expiry Date</span>
                            <span class="text-xs font-bold text-slate-700">{{ $estimate->expirydate ? \Carbon\Carbon::parse($estimate->expirydate)->format('M d, Y') : '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-slate-400">Reference</span>
                            <span class="text-xs font-bold text-slate-700">{{ $estimate->reference_no ?? '-' }}</span>
                        </div>
                        @if($estimate->project)
                        <div class="flex justify-between">
                            <span class="text-xs text-slate-400">Project</span>
                            <a href="{{ route('admin.projects.show', $estimate->project_id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">{{ $estimate->project->name }}</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Items Tab --}}
    <div id="tab-items" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-list-check text-slate-400"></i>
                    <h3 class="text-sm font-semibold text-slate-800">Line Items</h3>
                </div>
                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold">{{ $estimate->items->count() }}</span>
            </div>
            @if($estimate->items->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/50">
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold text-slate-400 uppercase w-10">#</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold text-slate-400 uppercase">Description</th>
                            <th class="px-4 py-2.5 text-center text-[10px] font-bold text-slate-400 uppercase w-16">Qty</th>
                            <th class="px-4 py-2.5 text-right text-[10px] font-bold text-slate-400 uppercase w-24">Rate</th>
                            <th class="px-4 py-2.5 text-right text-[10px] font-bold text-slate-400 uppercase w-24">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($estimate->items as $idx => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-4 py-3 text-center text-slate-400 text-xs">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 text-slate-700 font-medium">{{ $item->description }}</td>
                            <td class="px-4 py-3 text-center text-slate-600">{{ $item->qty }}</td>
                            <td class="px-4 py-3 text-right text-slate-600">{{ app_format_money($item->rate, $estimate->currency) }}</td>
                            <td class="px-4 py-3 text-right font-medium text-slate-800">{{ app_format_money($item->amount, $estimate->currency) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50/50 border-t-2 border-slate-200">
                        <tr><td colspan="4" class="px-4 py-2.5 text-right text-sm text-slate-500">Subtotal</td>
                            <td class="px-4 py-2.5 text-right font-medium">{{ app_format_money($estimate->subtotal, $estimate->currency) }}</td></tr>
                        @if($estimate->discount_total > 0)
                        <tr><td colspan="4" class="px-4 py-2.5 text-right text-sm text-emerald-600">Discount</td>
                            <td class="px-4 py-2.5 text-right font-medium text-emerald-600">-{{ app_format_money($estimate->discount_total, $estimate->currency) }}</td></tr>
                        @endif
                        @if($estimate->total_tax > 0)
                        <tr><td colspan="4" class="px-4 py-2.5 text-right text-sm text-slate-500">Tax</td>
                            <td class="px-4 py-2.5 text-right font-medium">{{ app_format_money($estimate->total_tax, $estimate->currency) }}</td></tr>
                        @endif
                        <tr class="border-t border-slate-200"><td colspan="4" class="px-4 py-2.5 text-right text-sm font-bold text-slate-700">Total</td>
                            <td class="px-4 py-2.5 text-right font-bold text-amber-600 text-base">{{ app_format_money($estimate->total, $estimate->currency) }}</td></tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="p-6 text-center text-sm text-slate-400">No items on this estimate.</div>
            @endif
        </div>
    </div>

    {{-- Notes Tab --}}
    <div id="tab-notes" class="tab-content hidden space-y-4">
        <form action="{{ route('admin.estimates.store_note', $estimate->id) }}" method="POST" data-pane-submit="1" data-pane-refresh="{{ route('admin.estimates.pane.show', $estimate->id) }}" class="bg-slate-50 rounded-xl p-4">
            @csrf
            <div class="flex gap-2">
                <input type="text" name="content" placeholder="Add a note..." class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Add</button>
            </div>
        </form>
        <div class="space-y-3">
            @forelse($estimate->notes as $note)
            <div class="bg-white rounded-xl p-4 border border-slate-200 flex justify-between items-start">
                <div>
                    <p class="text-sm text-slate-700">{{ $note->description }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ optional($note->created_at)->format('M d, Y H:i') ?? '' }}</p>
                </div>
                <form action="{{ route('admin.notes.destroy', $note->id) }}" method="POST" data-pane-submit="1" data-pane-refresh="{{ route('admin.estimates.pane.show', $estimate->id) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600 text-xs font-medium">Delete</button>
                </form>
            </div>
            @empty
            <p class="text-sm text-slate-400 text-center py-8">No notes.</p>
            @endforelse
        </div>
    </div>

    {{-- Reminders Tab --}}
    <div id="tab-reminders" class="tab-content hidden space-y-4">
        <form action="{{ route('admin.estimates.store_reminder', $estimate->id) }}" method="POST" data-pane-submit="1" data-pane-refresh="{{ route('admin.estimates.pane.show', $estimate->id) }}" class="bg-slate-50 rounded-xl p-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <input type="text" name="content" placeholder="Reminder content..." class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                <input type="datetime-local" name="date" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Add Reminder</button>
            </div>
        </form>
        <div class="space-y-3">
            @forelse($estimate->reminders as $reminder)
            <div class="bg-white rounded-xl p-4 border border-slate-200 flex justify-between items-center">
                <div>
                    <p class="text-sm text-slate-700">{{ $reminder->description ?? '' }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $reminder->date ? \Carbon\Carbon::parse($reminder->date)->format('M d, Y H:i') : '-' }}</p>
                </div>
                <form action="{{ route('admin.reminders.destroy', $reminder->id) }}" method="POST" data-pane-submit="1" data-pane-refresh="{{ route('admin.estimates.pane.show', $estimate->id) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600"><i class="bi bi-trash"></i></button>
                </form>
            </div>
            @empty
            <p class="text-sm text-slate-400 text-center py-8">No reminders.</p>
            @endforelse
        </div>
    </div>
</div>

<style>
.tab-content { display: block; }
.tab-content.hidden { display: none; }
</style>
@endsection

@push('pane-scripts')
<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('active', 'border-blue-500', 'text-blue-600');
            b.classList.add('text-slate-500', 'border-transparent');
        });
        btn.classList.add('active', 'border-blue-500', 'text-blue-600');
        btn.classList.remove('text-slate-500', 'border-transparent');

        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        const target = document.getElementById('tab-' + btn.dataset.tab);
        if (target) target.classList.remove('hidden');
    });
});
</script>
@endpush
