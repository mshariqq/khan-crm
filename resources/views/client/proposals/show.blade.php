@extends('layouts.client')

@section('title', 'Proposal: ' . $proposal->subject)
@section('breadcrumb', 'Proposal Details')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.proposals.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-blue-600 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $proposal->subject }}</h1>
                <p class="text-sm text-slate-500">Proposal Date: {{ \Carbon\Carbon::parse($proposal->date)->format('M d, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @php
                $statusMap = [
                    1 => ['label' => 'Open', 'class' => 'bg-blue-50 text-blue-600'],
                    2 => ['label' => 'Accepted', 'class' => 'bg-emerald-50 text-emerald-600'],
                    3 => ['label' => 'Declined', 'class' => 'bg-rose-50 text-rose-600'],
                ];
                $s = $statusMap[$proposal->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-600'];
            @endphp
            <span class="px-4 py-1.5 rounded-xl text-xs font-bold {{ $s['class'] }}">
                {{ $s['label'] }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Proposal Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Proposal Items</h3>
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
                            @foreach($proposal->items as $item)
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
                                    {{ $proposal->currencyRel->symbol ?? '$' }}{{ number_format($item->rate, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-slate-800">
                                        {{ $proposal->currencyRel->symbol ?? '$' }}{{ number_format($item->amount, 2) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50/50">
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Subtotal</td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-slate-800">{{ $proposal->currencyRel->symbol ?? '$' }}{{ number_format($proposal->subtotal, 2) }}</td>
                            </tr>
                            @if($proposal->total_tax > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Tax</td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-slate-800">{{ $proposal->currencyRel->symbol ?? '$' }}{{ number_format($proposal->total_tax, 2) }}</td>
                            </tr>
                            @endif
                            @if($proposal->discount_total > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Discount</td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-rose-600">-{{ $proposal->currencyRel->symbol ?? '$' }}{{ number_format($proposal->discount_total, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="bg-slate-100/50">
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-slate-800 uppercase tracking-wider">Total Amount</td>
                                <td class="px-6 py-4 text-right text-xl font-black text-blue-600">{{ $proposal->currencyRel->symbol ?? '$' }}{{ number_format($proposal->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($proposal->content)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Proposal Content</h3>
                <div class="prose prose-slate max-w-none text-slate-600 text-sm">
                    {!! $proposal->content !!}
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 text-center">
                <h3 class="text-sm font-bold text-slate-800 mb-6 uppercase tracking-wider">Total Value</h3>
                <p class="text-3xl font-black text-blue-600 mb-8">{{ $proposal->currencyRel->symbol ?? '$' }}{{ number_format($proposal->total, 2) }}</p>
                
                @if($proposal->status == 1)
                <div class="space-y-3">
                    <button class="w-full py-3 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2">
                        <i class="bi bi-check-lg"></i> Accept Proposal
                    </button>
                    <button class="w-full py-3 bg-rose-50 text-rose-600 rounded-xl font-bold hover:bg-rose-100 transition-colors flex items-center justify-center gap-2">
                        <i class="bi bi-x-lg"></i> Decline Proposal
                    </button>
                </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider text-center">Timeline</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Proposal Date</span>
                        <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($proposal->date)->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Open Till</span>
                        <span class="font-bold {{ $proposal->open_till && \Carbon\Carbon::parse($proposal->open_till)->isPast() && $proposal->status == 1 ? 'text-rose-600' : 'text-slate-800' }}">
                            {{ $proposal->open_till ? \Carbon\Carbon::parse($proposal->open_till)->format('M d, Y') : '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
