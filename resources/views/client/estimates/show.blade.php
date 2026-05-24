@extends('layouts.client')

@section('title', 'Estimate #' . $estimate->number)
@section('breadcrumb', 'Estimate Details')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.estimates.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-blue-600 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Estimate #{{ $estimate->number }}</h1>
                <p class="text-sm text-slate-500">Issued on {{ \Carbon\Carbon::parse($estimate->date)->format('M d, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @php
                $statusMap = [
                    1 => ['label' => 'Draft', 'class' => 'bg-slate-100 text-slate-600'],
                    2 => ['label' => 'Sent', 'class' => 'bg-blue-50 text-blue-600'],
                    3 => ['label' => 'Accepted', 'class' => 'bg-emerald-50 text-emerald-600'],
                    4 => ['label' => 'Declined', 'class' => 'bg-rose-50 text-rose-600'],
                ];
                $s = $statusMap[$estimate->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-600'];
            @endphp
            <span class="px-4 py-1.5 rounded-xl text-xs font-bold {{ $s['class'] }}">
                {{ $s['label'] }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Estimate Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Estimate Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-wider font-bold text-slate-400">
                                <th class="px-6 py-3">Description</th>
                                <th class="px-6 py-3 text-center">Qty</th>
                                <th class="px-6 py-3 text-right">Rate</th>
                                <th class="px-6 py-3 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($estimate->items as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-slate-800">{{ $item->description }}</span>
                                    @if($item->long_description)
                                        <p class="text-xs text-slate-500 mt-1">{{ $item->long_description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-slate-600">
                                    {{ $item->qty }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-slate-600">
                                    {{ app_format_money($item->rate, $estimate->currency) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-slate-800">
                                        {{ app_format_money($item->amount, $estimate->currency) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50/50">
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Subtotal</td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-slate-800">{{ app_format_money($estimate->subtotal, $estimate->currency) }}</td>
                            </tr>
                            @if($estimate->total_tax > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Tax</td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-slate-800">{{ app_format_money($estimate->total_tax, $estimate->currency) }}</td>
                            </tr>
                            @endif
                            @if($estimate->discount_total > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Discount</td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-rose-600">-{{ app_format_money($estimate->discount_total, $estimate->currency) }}</td>
                            </tr>
                            @endif
                            <tr class="bg-slate-100/50">
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-slate-800 uppercase tracking-wider">Total Amount</td>
                                <td class="px-6 py-4 text-right text-xl font-black text-blue-600">{{ app_format_money($estimate->total, $estimate->currency) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($estimate->clientnote)
            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
                <h3 class="text-sm font-bold text-blue-800 mb-2 uppercase tracking-wider">Note from Billing</h3>
                <p class="text-sm text-blue-700 leading-relaxed">{{ $estimate->clientnote }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 text-center">
                <h3 class="text-sm font-bold text-slate-800 mb-6 uppercase tracking-wider text-center">Total Value</h3>
                <p class="text-3xl font-black text-blue-600 mb-8">{{ app_format_money($estimate->total, $estimate->currency) }}</p>
                
                @if($estimate->status == 2)
                <div class="space-y-3">
                    <button class="w-full py-3 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2">
                        <i class="bi bi-check-lg"></i> Accept Estimate
                    </button>
                    <button class="w-full py-3 bg-rose-50 text-rose-600 rounded-xl font-bold hover:bg-rose-100 transition-colors flex items-center justify-center gap-2">
                        <i class="bi bi-x-lg"></i> Decline Estimate
                    </button>
                </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider text-center">Timeline</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Estimate Date</span>
                        <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($estimate->date)->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Expiry Date</span>
                        <span class="font-bold {{ $estimate->expirydate && \Carbon\Carbon::parse($estimate->expirydate)->isPast() && $estimate->status == 2 ? 'text-rose-600' : 'text-slate-800' }}">
                            {{ $estimate->expirydate ? \Carbon\Carbon::parse($estimate->expirydate)->format('M d, Y') : '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
